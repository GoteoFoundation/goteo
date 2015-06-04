<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Model\Image;


    class Sponsor extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $name,
            $url,
            $image,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $sql = static::query("
                    SELECT
                        id,
                        node,
                        name,
                        url,
                        image,
                        `order`
                    FROM    sponsor
                    WHERE id = :id
                    ", array(':id' => $id));
                $sponsor = $sql->fetchObject(__CLASS__);

                return $sponsor;
        }

        /*
         * Lista de patrocinadores (para panel admin)
         */
        public static function getAll ($node = \GOTEO_NODE) {

            $list = array();

            $sql = static::query("
                SELECT
                    id,
                    node,
                    name,
                    url,
                    image,
                    `order`
                FROM    sponsor
                WHERE node = :node
                ORDER BY `order` ASC, name ASC
                ", array(':node'=>$node));

            foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {
                $list[] = $sponsor;
            }

            return $list;
        }

        /*
         * Lista de patrocinadores
         */
        public static function getList ($node = \GOTEO_NODE) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    name,
                    url,
                    image
                FROM    sponsor
                WHERE node = :node
                ORDER BY `order` ASC, name ASC
                ";

            // echo $sql;

            $query = static::query($sql, array(':node'=>$node));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {

               // echo \trace($sponsor);

                // imagen
                $sponsor->image = Image::get($sponsor->image);

                $list[] = $sponsor;
            }

            return $list;
        }

        public function validate (&$errors = array()) {
            if (empty($this->name))
                $errors[] = 'Falta nombre';

            if (empty($this->url))
                $errors[] = 'Falta url';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            // Primero la imagenImagen
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);

                if ($image->save($errors)) {
                    $this->image = $image->id;
                } else {
                    \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->image = '';
                }
            }

            $fields = array(
                'id',
                'node',
                'name',
                'url',
                'image',
                'order'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO sponsor SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                Check::reorder($this->id, 'up', 'sponsor');

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
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

            if(!($ob = Sponsor::get($id))) return false;
            return $ob->delete();

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'sponsor', 'id', 'order', $extra);
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'sponsor', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($node = \GOTEO_NODE) {
            $sql = self::query('SELECT MAX(`order`) FROM sponsor WHERE node = :node', array(':node' => $node));
            $order = $sql->fetchColumn(0);
            return ++$order;

        }

    }

}
