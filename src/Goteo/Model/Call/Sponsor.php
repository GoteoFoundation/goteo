<?php

namespace Goteo\Model\Call {

    use Goteo\Library\Check,
        Goteo\Model\Image;


    class Sponsor extends \Goteo\Core\Model {

        //table for this model is not sponsor but call_sponsor
        protected $Table = 'call_sponsor';

        public
            $id,
            $call,
            $name,
            $url,
            $image,
            $order,
            $amount,
            $main = 1;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $sql = static::query("
                    SELECT
                        id,
                        `call`,
                        name,
                        url,
                        image,
                        amount,
                        main,
                        `order`
                    FROM    call_sponsor
                    WHERE id = :id
                    ", array(':id' => $id));
                $sponsor = $sql->fetchObject(__CLASS__);

                if (!empty($sponsor->image)) {
                    $sponsor->image = Image::get($sponsor->image);
                }

                return $sponsor;
        }

        /*
         * Lista de patrocinadores
         */
        public static function getAll ($call) {

            $list = array();

            $sql = static::query("
                SELECT
                    id,
                    `call`,
                    name,
                    url,
                    image,
                    amount,
                    main,
                    `order`
                FROM    call_sponsor
                WHERE `call` = :call
                ORDER BY `order` ASC, name ASC
                ", array(':call'=>$call));

            foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {
                $list[] = $sponsor;
            }

            return $list;
        }

        /*
         * Lista de patrocinadores
         */
        public static function getList ($call=null, $type=null, $filters=[]) {

            $list = array();

            
            if($call)
            {
                $where='WHERE `call` = :call';
            }
            else
            {
                // If no call list of differents sponsors with some call published
                $where='WHERE `call` IN (SELECT `id` FROM `call` WHERE status IN (3, 4, 5))';
                $group='GROUP BY name';
            }

            if (!empty($filters['landing_match'])&&!$call) {
                 $where= "WHERE landing_match = 1";
                 $order_landing='order_landing_match ASC, ';
            }

            if($type=="main")
                $type_filter=" AND `main`=1";
            elseif($type=="collaborator")
                $type_filter=" AND `main`=0";
            else
                $type_filter="";

            $sql = static::query("
                SELECT
                    id,
                    `call`,
                    name,
                    url,
                    image,
                    amount,
                    main,
                    `order`
                FROM    call_sponsor
                $where
                $type_filter
                $group
                ORDER BY 
                $order_landing
                `order` ASC, name ASC
                ", array(':call'=>$call));

            foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {
                // imagen
                if (!empty($sponsor->image)) {
                    $sponsor->image = Image::get($sponsor->image);
                }

                $list[] = $sponsor;
            }

            return $list;
        }

        public function validate (&$errors = array()) {
            if (empty($this->call))
                $errors[] = 'Falta convocatoria';
/*
            if (empty($this->name))
                $errors[] = 'Falta nombre';

            if (empty($this->url))
                $errors[] = 'Falta enlace';
*/
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
            } elseif ($this->image instanceof Image) {
                $this->image = $this->image->id;
            }

            $fields = array(
                'id',
                'call',
                'name',
                'url',
                'image',
                'order',
                'amount',
                'main'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO call_sponsor SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = $e->getMessage();
                return false;
            }
        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id, $call) {
            $extra = array (
                    'call' => $call
                );
            return Check::reorder($id, 'up', 'call_sponsor', 'id', 'order', $extra);
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id, $call) {
            $extra = array (
                    'call' => $call
                );
            return Check::reorder($id, 'down', 'call_sponsor', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($call) {
            $sql = self::query('SELECT MAX(`order`) FROM call_sponsor WHERE `call` = :call', array(':call' => $call));
            $order = $sql->fetchColumn(0);
            return ++$order;

        }

    }

}
