<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model\Project,
        \Goteo\Model\Image,
        \Goteo\Library\Check;

    class Promote extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $name,
            $title,
            $description,
            $order,
            $active;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'promote_lang', \LANG);

                $query = static::query("
                    SELECT
                        promote.id as id,
                        promote.node as node,
                        promote.project as project,
                        project.name as name,
                        IFNULL(promote_lang.title, promote.title) as title,
                        IFNULL(promote_lang.description, promote.description) as description,
                        promote.order as `order`,
                        promote.active as `active`
                    FROM    promote
                    LEFT JOIN promote_lang
                        ON promote_lang.id = promote.id
                        AND promote_lang.lang = :lang
                    INNER JOIN project
                        ON project.id = promote.project
                    WHERE promote.id = :id
                    ", array(':id'=>$id, ':lang'=>$lang));
                $promote = $query->fetchObject(__CLASS__);

                return $promote;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($activeonly = false, $node = \GOTEO_NODE, $lang = \LANG) {

            $promos = array();

            $sqlFilter = ($activeonly) ? " AND promote.active = 1" : '';

            if(self::default_lang($lang)=='es') {
                $different_select=" IFNULL(promote_lang.title, promote.title) as title,
                                    IFNULL(promote_lang.description, promote.description) as promo_text";
                }
            else {
                    $different_select=" IFNULL(promote_lang.title, IFNULL(eng.title, promote.title)) as title,
                                        IFNULL(promote_lang.description, IFNULL(eng.description, promote.description)) as promo_text";
                    $eng_join=" LEFT JOIN promote_lang as eng
                                    ON  eng.id = promote.id
                                    AND eng.lang = 'en'";
                }

            // sacamos tambien todos los dfatos que da el project::getMedium
            $query = static::query("
                SELECT
                    promote.id as id,
                    promote.project as project,
                    project.name as name,
                    project.status as status,
                    $different_select,
                    project.published as published,
                    project.created as created,
                    project.updated as updated,
                    project.mincost as mincost,
                    project.maxcost as maxcost,
                    project.amount as amount,
                    project.description as description,
                    project.num_investors as num_investors,
                    project.days as days,
                    user.id as user_id,
                    user.name as user_name,
                    project.image as image,
                    promote.order as `order`,
                    promote.active as `active`
                FROM    promote
                LEFT JOIN promote_lang
                    ON promote_lang.id = promote.id
                    AND promote_lang.lang = :lang
                $eng_join
                INNER JOIN project
                    ON project.id = promote.project
                INNER JOIN user
                    ON user.id = project.owner
                WHERE promote.node = :node
                $sqlFilter
                ORDER BY `order` ASC, title ASC
                ", array(':node' => $node, ':lang'=>$lang));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->promo_text = Text::recorta($promo->promo_text, 100, false);
                //variables usadas en view/project/widget/project.html.php




                // aquí usará getWidget para sacar todo esto
                // $promo->projectData = Project::getWidget($promo);


                // el getWidget hará todo esto:
                $Widget = new Project();
                $Widget->id = $promo->project;
                $Widget->status = $promo->status;
                $Widget->name = $promo->name;
                $Widget->description = $promo->description;
                $Widget->published = $promo->published;

                // configuración de campaña
                $Widget->noinvest = $promo->noinvest;
                $Widget->watch = $promo->watch;
                $Widget->days_round1 = (!empty($promo->days_round1)) ? $promo->days_round1 : 40;
                $Widget->days_round2 = (!empty($promo->days_round2)) ? $promo->days_round2 : 40;
                $Widget->one_round = $promo->one_round;
                $Widget->days_total = ($Widget->days_round1 + $Widget->days_round2);


                // imagen
                if (!empty($promo->image)) {
                    $Widget->image = Image::get($promo->image);
                } else {
                    $first = Project\Image::setFirst($promo->project);
                    $Widget->image = Image::get($first);
                }

                $Widget->amount = $promo->amount;
                $Widget->invested = $promo->amount;

                //de momento... habria que mejorarlo
                $Widget->categories = Project\Category::getNames($promo->project, 2);
                $Widget->social_rewards = Project\Reward::getAll($promo->project, 'social', $lang);

                if(!empty($promo->num_investors)) {
                    $Widget->num_investors = $promo->num_investors;
                } else {
                    $Widget->num_investors = Invest::numInvestors($promo->project);
                }

                //mensajes y mensajeros
                // solo cargamos mensajes en la vista mensajes
                if (!empty($promo->num_messengers)) {
                    $Widget->num_messengers = $promo->num_messengers;
                } else {
                    $Widget->num_messengers = Message::numMessengers($promo->project);
                }

                // novedades
                // solo cargamos blog en la vista novedades
                if (!empty($promo->num_posts)) {
                    $Widget->num_posts = $promo->num_posts;
                } else {
                    $Widget->num_posts =  Post::numPosts($promo->project);
                }

                if(!empty($promo->mincost) && !empty($promo->maxcost)) {
                    $Widget->mincost = $promo->mincost;
                    $Widget->maxcost = $promo->maxcost;
                } else {
                    $calc = Project::calcCosts($promo->project);
                    $Widget->mincost = $calc->mincost;
                    $Widget->maxcost = $calc->maxcost;
                }
                $Widget->user = new User;
                $Widget->user->id = $promo->user_id;
                $Widget->user->name = $promo->user_name;

                //calcular dias sin consultar sql
                $Widget->days = $promo->days;
                $Widget->round = 0;

                $project_conf = Project\Conf::get($Widget->id);
                $Widget->days_round1 = $project_conf->days_round1;
                $Widget->days_round2 = $project_conf->days_round2;
                $Widget->days_total = $project_conf->days_round1 + $project_conf->days_round2;
                $Widget->one_round = $project_conf->one_round;

                $Widget->setDays(); // esto hace una consulta para el número de días que lleva
                $Widget->setTagmark(); // esto no hace consulta


                // hasta aquí, ya tenemos el widget

                $promo->projectData = $Widget;

                $promos[] = $promo;
            }
                // print_r($promos);die;

            return $promos;
        }

        /*
         * Lista de proyectos disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND project != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status = 3
                AND project.id NOT IN (SELECT project FROM promote WHERE promote.node = :node{$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        // ya no validamos esto
        public function validate (&$errors = array()) {
            if (empty($this->node))
                $errors[] = 'Falta nodo';

            if ($this->active && empty($this->project))
                $errors[] = 'Se muestra y no tiene proyecto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
//            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'node',
                'project',
                'title',
                'description',
                'order',
                'active'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO promote SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto destacado
         */
        public static function delete ($id) {

            $sql = "DELETE FROM promote WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un destacado
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE promote SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'promote', 'id', 'order', $extra);
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'promote', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM promote WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }

}
