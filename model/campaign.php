<?php

namespace Goteo\Model {
    
    class Campaign extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description;

        /*
         *  Devuelve datos de una campaña
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        description
                    FROM    campaign
                    WHERE id = :id
                    ", array(':id' => $id));
                $campaign = $query->fetchObject(__CLASS__);

                return $campaign;
        }

        /*
         * Lista de campañas
         */
        public static function getAll () {

            $campaigns = array();

            $sql = "
                SELECT
                    campaign.id as id,
                    campaign.name as name
                FROM    campaign";

            $sql .= " ORDER BY name ASC";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $campaign) {
                $campaigns[$campaign->id] = $campaign->name;
            }

            return $campaigns;
        }

        /*
         * Lista de campañas activas o disponibles o algo así
         */
        public static function getList () {

            $campaigns = array();

            $sql = "
                SELECT
                    campaign.id,
                    campaign.name,
                    (   SELECT
                        COUNT(invest.id)
                        FROM invest
                        WHERE invest.campaign = campaign.id
                        AND (invest.status = 0 OR invest.status = 1)
                    ) as used
                FROM    campaign
                ORDER BY campaign.name ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $campaign) {
                $campaigns[$campaign->id] = $campaign;
            }

            return $campaigns;
        }

        public function validate (&$errors = array()) {
            if (empty($this->name))
                $errors[] = 'Falta nombre';
                //Text::get('mandatory-campaign-name');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name',
                'description'
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
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un campaigno
         */
        public static function delete ($id) {

            $sql = "DELETE FROM campaign WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

    }
    
}