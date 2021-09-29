<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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
                self::recount($this->id);

                return true;
            } catch(\PDOException $e) {
                $errors[] = $e->getMessage();
                return false;
            }
        }


        /**
         * saca una lista simple de los proycetos con revision o susceptibles de abrir revision
         */
        public static function getProjects($node = null) {
            $projects = array();

            $values = array();

            $sqlFilter = "";
            if (!empty($node) && $node != \GOTEO_NODE) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $node;
            }

            $sql = "SELECT
                        project.id as id,
                        project.name as name
                    FROM project
                    LEFT JOIN review
                        ON review.project = project.id
                    WHERE (project.status = 2 OR review.id IS NOT NULL)
                        $sqlFilter
                    ORDER BY project.name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {
                $projects[$proj->id] = $proj->name;
            }

            return $projects;
        }

        /**
         * Saca una lista completa de proyectos con revision o susceptibles de abrir revision
         *
         * @param string node id
         * @return array of project instances
         */
        public static function getList($filters = array(), $node = null) {
            $projects = array();

            $values = array();

            $sqlFilter = "";
            if (!empty($node) && $node != \GOTEO_NODE) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $node;
            }

            if (!empty($filters['status'])) {
                if ($filters['status'] == 'unstarted') {
                    $sqlFilter .= " AND review.id IS NULL";
                } else {
                    $status = $filters['status'] == 'open' ? '1' : '0';
                    $sqlFilter .= " AND review.status = :status";
                    $values[':status'] = $status;
                }
            }
            if (!empty($filters['project'])) {
                $sqlFilter .= " AND project.id = :project";
                $values[':project'] = $filters['project'];
            }

            if (!empty($filters['checker'])) {
                $sqlFilter .= " AND review.id IN (
                    SELECT review
                    FROM user_review
                    WHERE user = :checker
                    )";
                $values[':checker'] = $filters['checker'];
            }

            $sql = "SELECT
                        project.id as project,
                        project.name as name,
                        project.translate as translate,
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
                        $sqlFilter
                    ORDER BY project.progress DESC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {

                $checkers = array();

                $subquery = self::query("
                    SELECT
                        user_review.review as id,
                        user.id as user,
                        user.name as name,
                        user_review.ready as ready
                    FROM user
                    JOIN user_review ON user.id = user_review.user
                    WHERE user_review.review = ?
                ", array($proj->review));
                foreach ($subquery->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User\Review') as $checker) {

                    $cuenta = $checker->recount();
                    $checker->score = $cuenta->score;
                    $checker->max   = $cuenta->max;

                    $checkers[$checker->user] =  $checker;
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
            $reviews = array();

            $sql = "SELECT
                        review.id as id,
                        project.id as project,
                        project.name as name,
                        project.status as project_status,
                        user.name as owner_name,
                        user.id as owner,
                        user_review.ready as ready,
                        project.progress as progress,
                        review.score as score,
                        review.max as max,
                        review.to_checker as comment
                    FROM user_review
                    INNER JOIN review
                        ON review.id = user_review.review
                    INNER JOIN project
                        ON project.id = review.project
                    INNER JOIN user
                        ON user.id = project.owner
                    WHERE review.status = 1
                    AND user_review.user = ?
                    ORDER BY project.name ASC
                    ";

//            echo "$sql <br />";  die;
            $query = self::query($sql, array($user));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $review) {
                $reviews[] = $review;
            }
            return $reviews;
        }

        /**
         * Para obtener las revisiones del historial
         */
        public static function history($user) {
            $reviews = array();

            $sql = "SELECT
                        review.id as id,
                        project.id as project,
                        project.name as name,
                        user.name as owner_name,
                        user.id as owner,
                        user_review.ready as ready,
                        project.progress as progress,
                        review.score as score,
                        review.max as max
                    FROM user_review
                    INNER JOIN review
                        ON review.id = user_review.review
                    INNER JOIN project
                        ON project.id = review.project
                    INNER JOIN user
                        ON user.id = project.owner
                    WHERE user_review.ready = 1
                    AND user_review.user = ?
                    ORDER BY project.name ASC
                    ";

//            echo "$sql <br />";  die;
            $query = self::query($sql, array($user));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $review) {
                $reviews[$review->id] = $review;
            }
            return $reviews;
        }

        /*
         *  Para conseguir los datos de una revision
         *  Id, proyecto, nombre del proyecto
         */
        public static function getData ($id) {
                $query = static::query("
                    SELECT
                        review.id as id,
                        project.id as project,
                        project.name as name,
                        user.name as owner_name,
                        user.id as owner,
                        project.progress as progress,
                        review.score as score,
                        review.max as max,
                        review.to_checker as comment
                    FROM review
                    INNER JOIN project
                        ON project.id = review.project
                    INNER JOIN user
                        ON user.id = project.owner
                    WHERE review.id = :id
                    ", array(':id' => $id));

                $review = $query->fetchObject();


                $checkers = array();

                $subquery = self::query("
                    SELECT
                        user_review.review as id,
                        user.id as user,
                        user.name as name,
                        user_review.ready as ready
                    FROM user
                    JOIN user_review ON user.id = user_review.user
                    WHERE user_review.review = ?
                ", array($id));
                foreach ($subquery->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User\Review') as $checker) {

                    $review->ready = $checker->ready;

                    $cuenta = $checker->recount();
                    $checker->score = $cuenta->score;
                    $checker->max   = $cuenta->max;

                    $checkers[$checker->user] =  $checker;
                }
                unset($subquery);

                $review->checkers = $checkers;

                return $review;
        }


        /*
         * Monta el array de evaluación con los puntos de criterios y comentarios por seccion
         */
        public static function getEvaluation ($review, $user) {

            $evaluation = array();

            // primero los criterios
            $evaluation['criteria'] = array();
            $sql = "SELECT
                        criteria,
                        score
                    FROM review_score
                    WHERE user = :user
                    AND review = :review
                    ORDER BY criteria ASC
                    ";

//            echo "$sql <br />";  die;
            $query = self::query($sql, array('review'=>$review, 'user'=>$user));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $evaluation['criteria'][$item->criteria] = $item->score;
            }

            // ahora los comentarios por seccion
            $sql = "SELECT
                        section,
                        evaluation,
                        recommendation
                    FROM review_comment
                    WHERE user = :user
                    AND review = :review
                    ORDER BY section ASC
                    ";

//            echo "$sql <br />";  die;
            $query = self::query($sql, array('review'=>$review, 'user'=>$user));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $evaluation[$item->section]['evaluation'] = $item->evaluation;
                $evaluation[$item->section]['recommendation'] = $item->recommendation;
            }

            // puntuacion actual
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
                ':review' => $review,
                ':user'  => $user
            ));

            $current = $query->fetchObject();
            $evaluation['score'] = $current->score;
            $evaluation['max'] = $current->max;



            return $evaluation;
        }



        /*
         * Metodo para contar la puntuacion general de la revision
         * Hace la media de las revisiones de revisor que esten listas
         *
         * score es la media de las puntuaciones
         * max es el maximo depuntuacio que podria haber obtenido (numero de criterios)
         *
         */
        public static function recount ($id, &$errors = array()) {
            try {
                $score = 0;
                $max   = 0;

                $sql = "SELECT
                            COUNT(criteria.id) as `max`
                        FROM criteria
                        ";

                $query = static::query($sql);
                
                $cuenta = $query->fetchObject();

                $max = $cuenta->max;

                // listado de revisiones listas
                $sql = "SELECT
                            user_review.user as id
                        FROM user_review
                        WHERE user_review.review = ?
                        AND user_review.ready = 1
                        ";

                $query = static::query($sql, array($id));

                $checkers = $query->fetchAll(\PDO::FETCH_CLASS);

                foreach ($checkers as $checker) {
                    $sql = "SELECT
                                COUNT(review_score.score) as score
                            FROM review_score
                                WHERE review_score.review = :review
                                AND review_score.user = :user
                            ";

                    $query = static::query($sql, array(
                        ':review' => $id,
                        ':user'  => $checker->id
                    ));

                    $rev = $query->fetchObject();

                    $score += $rev->score;
                }

                $score = $score / count($checkers);

                $sql = "UPDATE review SET score = :score, max = :max WHERE id = :review";
                self::query($sql, array(':review' => $id, ':score' => $score, ':max' => $max));

                return true;
                
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

    }
    
}
