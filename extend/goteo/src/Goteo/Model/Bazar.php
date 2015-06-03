<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model\Project,
        \Goteo\Model\Image,
        \Goteo\Library\Check;

    class Bazar extends \Goteo\Core\Model {

        public
            $id,
            $reward,
            $project,
            $title,
            $description,
			$amount,
			$image,
            $order,
            $active;


        /*
         *  Devuelve datos de un elemento
         */
        public static function get ($id) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'bazar_lang', \LANG);

                $query = static::query("
                    SELECT
                        bazar.id as id,
                        bazar.reward as reward,
                        bazar.project as project,
                        IFNULL(bazar_lang.title, bazar.title) as title,
                        IFNULL(bazar_lang.description, bazar.description) as description,
						bazar.description as description,
						bazar.amount as amount,
						bazar.image as image,
                        bazar.order as `order`,
                        bazar.active as `active`
                    FROM    bazar
                    LEFT JOIN bazar_lang
                        ON bazar_lang.id = bazar.id
                        AND bazar_lang.lang = :lang
                    WHERE bazar.id = :id
                    ", array(':id'=>$id, ':lang'=>$lang));

                if($promo = $query->fetchObject(__CLASS__)) {

                    if (!empty($promo->image))
                        $promo->image = Image::get($promo->image);

                    $promo->project = Project::getMini($promo->project);
                }
                return $promo;
        }

        /*
         * Lista de elementos del bazar (para mostrar publicamente)
         * Solo proyectos en campaña (y recompensas que queden unidades)
         */
        public static function getAll () {

            $promos = array();

            if(self::default_lang(\LANG)=='es') {
                $different_select=" IFNULL(bazar_lang.title, bazar.title) as title,
                                    IFNULL(bazar_lang.description, bazar.description) as description";
                }
                else {
                    $different_select=" IFNULL(bazar_lang.title, IFNULL(eng.title, bazar.title)) as title,
                                        IFNULL(bazar_lang.description, IFNULL(eng.description, bazar.description)) as description";
                    $eng_join=" LEFT JOIN bazar_lang as eng
                                    ON  eng.id = bazar.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                        bazar.id as id,
                        bazar.reward as reward,
                        bazar.project as project,
                        bazar.image as image,
                        $different_select,
                        bazar.amount as amount,
                        bazar.image as image,
                        bazar.order as `order`,
                        bazar.active as `active`
                    FROM    bazar
                    LEFT JOIN bazar_lang
                        ON bazar_lang.id = bazar.id
                        AND bazar_lang.lang = :lang
                    $eng_join
                    INNER JOIN project
                        ON project.id = bazar.project
                        AND project.status = 3
                    WHERE bazar.active = 1
                    ORDER BY `order` ASC, title ASC";

            $query = static::query($sql, array(':lang'=>\LANG));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {

                if (!empty($promo->image))
                    $promo->img = Image::get($promo->image);

                $promo->project = Project::getMini($promo->project);

                $promos[] = $promo;
            }

            return $promos;
        }

        /*
         * Lista de elementos del bazar (para gestión)
         */
        public static function getList () {

            // estados
            $status = Project::status();

            $promos = array();

            $query = static::query("
                SELECT
                    bazar.id as id,
                    bazar.reward as reward,
                    bazar.project as project,
                    project.status as status,
                    bazar.title as title,
                    bazar.description as description,
                    bazar.amount as amount,
                    bazar.image as image,
                    bazar.order as `order`,
                    bazar.active as `active`
                FROM    bazar
                LEFT JOIN project
                    ON project.id = bazar.project
                ORDER BY `order` ASC, title ASC
                ");

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->status = $status[$promo->status];

                if (!empty($promo->image))
                    $promo->image = Image::get($promo->image);

                $promos[] = $promo;
            }

            return $promos;
        }

        /*
         * Lista de elementos disponibles para bazar
         */
        public static function available ($current = null) {

            $list = array();

            $sqlCurr = "";
            $values = array();
            if (!empty($current)) {
                $sqlCurr = " WHERE reward.id != :curr";
                $values[':curr'] = $current;
            }

            $query = static::query("
                SELECT
                    reward.id as reward,
                    project.id as project,
                    reward.reward as name,
                    project.name as projname,
                    reward.amount as amount,
                    icon.name as icon
                FROM    reward
                INNER JOIN project
                    ON project.id = reward.project
                    AND project.status = 3
                LEFT JOIN icon
                    ON icon.id = reward.icon
                WHERE reward.id NOT IN (SELECT reward FROM bazar {$sqlCurr} )
                AND reward.type = 'individual'
                ORDER BY project ASC, amount ASC
                ", $values);

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $item->name = Text::recorta($item->name, 50);
                $item->projname = Text::recorta($item->projname, 50);
                $list[$item->reward] = $item;
            }

            return $list;
        }

        // ya no validamos esto
        public function validate (&$errors = array()) {
            if ($this->active && (empty($this->reward) || empty($this->project) || empty($this->amount) ))
                $errors[] = 'Se quiere publicar y no tiene recompensa/proyecto/importe. Seleccionar item o no publicar';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            // Imagen para el regalo
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);

                if ($image->save($errors)) {
                    $this->image = $image->id;
                } else {
                    \Goteo\Application\Message::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->image = '';
                }
            }

            $fields = array(
                'id',
                'reward',
                'project',
                'title',
                'description',
				'amount',
				'image',
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
                $sql = "REPLACE INTO bazar SET " . $set;
//                echo $sql;
//                echo \trace($values);
//                die;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /* Para activar/desactivar un elemento
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE bazar SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /**
         * Static compatible version of parent delete()
         * @param  [type] $id [description]
         * @return [type]     [description]
         */
        public function delete($id = null) {
            if(empty($id)) return parent::delete();

            if(!($ob = Bazar::get($id))) return false;
            return $ob->delete();

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {

            return Check::reorder($id, 'up', 'bazar', 'id', 'order');
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {

            return Check::reorder($id, 'down', 'bazar', 'id', 'order');
        }

        /*
         *
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM bazar ');
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }

}
