<?php

namespace Goteo\Model {
    
    class Campaign extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $call,
            $name,
            $title,
            $description,
            $order,
            $active;

        /*
         *  Devuelve datos de un destacada
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        campaign.id as id,
                        campaign.node as node,
                        campaign.call as call,
                        call.name as name,
                        campaign.order as `order`,
                        campaign.active as `active`
                    FROM    campaign
                    INNER JOIN call
                        ON call.id = campaign.call
                    WHERE campaign.id = :id
                    ", array(':id'=>$id, ':lang'=>\LANG));
                $campaign = $query->fetchObject(__CLASS__);

                return $campaign;
        }

        /*
         * Lista de campañas destacadas
         */
        public static function getAll ($activeonly = false, $node = \GOTEO_NODE) {

            // estados
            $status = call::status();

            $promos = array();

            $sqlFilter = ($activeonly) ? " AND campaign.active = 1" : '';

            $query = static::query("
                SELECT
                    campaign.id as id,
                    campaign.call as call,
                    call.name as name,
                    call.status as status,
                    campaign.order as `order`,
                    campaign.active as `active`
                FROM    campaign
                INNER JOIN call
                    ON call.id = campaign.call
                WHERE campaign.node = :node
                $sqlFilter
                ORDER BY `order` ASC, name ASC
                ", array(':node' => $node, ':lang'=>\LANG));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->description =Text::recorta($promo->description, 100, false);
                $promo->status = $status[$promo->status];
                $promos[] = $promo;
            }

            return $promos;
        }

        /*
         * Lista de campañas disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND call != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    call.id as id,
                    call.name as name,
                    call.status as status
                FROM    call
                WHERE status = 3
                AND call.id NOT IN (SELECT call FROM campaign WHERE campaign.node = :node{$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }


        public function validate (&$errors = array()) {
            if (empty($this->node))
                $errors[] = 'Falta nodo';

            if ($this->active && empty($this->call))
                $errors[] = 'Se muestra y no tiene campaña';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'node',
                'call',
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
                $sql = "REPLACE INTO campaign SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto destacada
         */
        public static function delete ($id) {

            $sql = "DELETE FROM campaign WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un destacada
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE campaign SET active = :active WHERE id = :id";
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
            return Check::reorder($id, 'up', 'campaign', 'id', 'order', $extra);
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'campaign', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM campaign WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }

}