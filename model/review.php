<?php

namespace Goteo\Model {

    use Goteo\Model\User;

    class Review extends \Goteo\Core\Model {

        public
            $id,
            $project,
            $checkers = array(),
            $status,
            $to_checker,
            $to_owner,
            $score,
            $max;

        /*
         *  Para conseguir el id de la review de un proyecto
         *  Devuelve datos de un review
         */
        public static function get ($project) {
                $query = static::query("
                    SELECT *
                    FROM    review
                    WHERE project = :project
                    ", array(':project' => $project));
                
                if ($review =  $query->fetchObject(__CLASS__)) {
                    $review->checkers = User\Review::checkers($review->id);
                } else {
                    $review = new self (array(
                        'project' => $project
                    ));
                }
                return $review;
        }

        public function validate (&$errors = array()) {
            if (empty($this->project))
                $errors[] = 'Falta proyecto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        /*
         *  para cuando se crea un proceso de revision para un proyecto
         */
        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'project',
                'to_checker',
                'to_owner'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO review SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }


        /**
         * Saca una lista completa de proyectos con revision o susceptibles de abrir revision
         *
         * @param string node id
         * @return array of project instances
         */
        public static function getList($filters = array(), $node = \GOTEO_NODE) {
            $projects = array();

            $sqlFilter = "";
            if (!empty($filters['status'])) {
                $status = $filters['status'] == 'open' ? '1' : '0';
                $sqlFilter .= " AND review.status = " . $status;
            }
            if (!empty($filters['checker'])) {
                $sqlFilter .= " AND review.id IN (
                    SELECT review
                    FROM user_review
                    WHERE user = '{$filters['checker']}'
                    )";
            }

            $sql = "SELECT
                        project.id as project,
                        project.name as name,
                        user.name as owner,
                        review.id as review,
                        review.status as status,
                        project.progress as progress,
                        review.score as score,
                        review.max as max
                    FROM project
                    INNER JOIN user
                        ON user.id = project.owner
                    LEFT JOIN review
                        ON review.project = project.id
                    WHERE (project.status = 2 OR review.id IS NOT NULL)
                        AND project.node = ?
                        $sqlFilter
                    ORDER BY project.progress DESC
                    ";

//            echo "$sql <br />";
            $query = self::query($sql, array($node));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {

                $checkers = array();

                $subquery = self::query("
                    SELECT
                        user.id as user,
                        user.name as name,
                        user_review.ready as ready
                    FROM user
                    JOIN user_review ON user.id = user_review.user
                    WHERE user_review.review = ?
                ", array($proj->review));
                foreach ($subquery->fetchAll(\PDO::FETCH_OBJ) as $checker) {
                    $checkers[] =  $checker;
                }
                unset($subquery);

                $proj->checkers = $checkers;

                $projects[] = $proj;
            }
            return $projects;
        }

        /**
         * Para obtener las revisiones de proyectos asignadas
         */
        public static function assigned($user) {
            $projects = array();

            $sql = "SELECT
                        project.id as project,
                        project.name as name,
                        user.name as owner,
                        review.id as review,
                        review.status as status,
                        project.progress as progress,
                        review.score as score,
                        review.max as max
                    FROM project
                    INNER JOIN user
                        ON user.id = project.owner
                    LEFT JOIN review
                        ON review.project = project.id
                    WHERE (project.status = 2 OR review.id IS NOT NULL)
                        AND project.node = ?
                        AND review.id IN (
                            SELECT review
                            FROM user_review
                            WHERE user = '{$user}'
                        )
                    ORDER BY project.name ASC
                    ";

            echo "$sql <br />";
            $query = self::query($sql, array($node));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {
                $projects[] = $proj;
            }
            return $projects;
        }


        /*
         * Metodo para contar la puntuacion
         * @TODO otro metodo para grabarla
         *
         * score es la puntuacion total
         * max es el maximo depuntuacio que podria haber obtenido
         *
         */
        public function recount ($checker, &$errors = array()) {
            try {
                $score = 0;
                $max   = 0;

                $sql = "SELECT
                            COUNT(criteria.id) as `max`,
                            COUNT(review_score.score) as score
                        FROM criteria
                        LEFT JOIN review_score
                            ON review_score.criteria = criteria.id
                            AND review_score.review = :review
                            AND review_score.user = :user
                        ";

                $query = static::query($sql, array(
                    ':review' => $this->id,
                    ':user'  => $checker
                ));
                
                return $query->fetchObject();
                
            } catch(\PDOException $e) {
                $errors[] = "No se ha aplicado la puntuacion. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Metodo para dar por cerrada una revisión
         */
        public static function close ($id, &$errors = array()) {
            try {
                $values = array(
                    ':review' => $id
                );

                $sql = "UPDATE review SET status = 0 WHERE id = :review";
                self::query($sql, $values);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha podido cerrar. " . $e->getMessage();
                return false;
            }
        }

        /*
         * metodo para añadir un comentario (evaluación o recomendacion)
         */
        public function comment ($checker, $section, $evaluate, $recommendation) {

            $values = array(
                ':review'   => $this->id,
                ':user'     => $checker,
                ':section'  => $section,
                ':evaluate' => $evaluate,
                ':recommendation' => $recommendation
            );

            try {
                $sql = "REPLACE INTO review_comment SET
                            evaluate = :evaluate,
                            recommendation = :recommendation
                        WHERE review = :review
                        AND user = :user
                        AND section = :section
                        ";
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * metodo para poner (o quitar) el punto a un criterio
         */
        public function score ($checker, $criteria, $score = null) {

            $values = array(
                ':review'   => $this->id,
                ':user'     => $checker,
                ':criteria' => $criteria,
                ':score'    => $score
            );

            try {
                $sql = "REPLACE INTO review_comment SET
                            evaluate = :evaluate,
                            recommendation = :recommendation
                        WHERE review = :review
                        AND user = :user
                        AND section = :section
                        ";
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }


    }
    
}