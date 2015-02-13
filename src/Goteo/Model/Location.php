<?php

/*
 * Este modelo es para la geo localizacion
 *
 * Usamos la libreria geoloc para todas las funcionalidades de webservices
 *
 */

namespace Goteo\Model {

    class Location extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $city,
            $region,
            $country,
            $country_code,  // codigo pais  ISO 3166-1 alpha-2
            $longitude,
            $latitude,
            $valid = 1;

        /**
         * Obtener datos de una localizacion (longitud, latitud, nombre completo(montado), poblacion, provincia, pais y si está validada )
         * @param   type mixed  $id     Identificador
         * @return  type object         Instancia de geolocalización
         */
        static public function get ($id) {
            try {
                $query = self::query("SELECT * FROM location WHERE id = ?", array($id));
                $item = $query->fetchObject(__CLASS__);
                $item->name = "{$item->city}, {$item->region}, {$item->country}";

                return empty($item->id) ? false : $item;

            } catch(\PDOException $e) {
                // throw new \Goteo\Core\Exception($e->getMessage());
                return false;
            }
        }

        /**
         * Saves the location. Does not changes the id if the place if the same
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function save (&$errors = array()) {
            if (!$this->validate())
                return false;

            $fields = array(
                'id',
                'city',
                'region',
                'country',
                'country_code',
                'longitude',
                'latitude',
                'valid'
                );

            $values = array();
            $insert = array();
            $update = array();

            foreach ($fields as $field) {
                if($field != 'id') $update[] = "location.$field = :$field ";
                $insert["location.$field"] = ":$field";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "INSERT INTO location (" . implode(',', array_keys($insert)) . ') VALUES (' . implode(',', array_values($insert)) . ')
                        ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id),' . implode(',', $update);
                // echo "$sql\n";die;
                self::query($sql, $values);
                // echo "[:".self::insertId()."\n";
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "LOCATION SAVING FAILED!!! " . $e->getMessage();
                return false;
            }
        }

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate (&$errors = array()) {
            if (empty($this->country_code))
                $errors[] = 'Country code missing';

            if (empty($this->country))
                $errors[] = 'Country missing';

            if (empty($this->longitude))
                $errors[] = 'Longitude missing';

            if (empty($this->latitude))
                $errors[] = 'Latitude missing';

            // por otra parte, no se puede crear si esta localidad-region-pais ya existe en la tabla
            // o si, estas coordenadas latitud-longitud ya existen en la tabla


            if (empty($errors))
                return true;
            else
                return false;
        }

        /*
         * Conteo de usuarios por geolocalización
         */
        public static function countBy ($type = 'user', $count = 'registered', $keyword = '') {

            switch ($count) {
                case 'located':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type'";
                    break;
                case 'unlocable':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.locable = 0 AND location_item.type = '$type'";
                    break;
                case 'not-country':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type' AND location.country_code NOT LIKE '{$keyword}'";
                    break;
                case 'country':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type' AND location.country_code LIKE '{$keyword}'";
                    break;
                case 'region':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type' AND location.region LIKE '{$keyword}'";
                    break;
                case 'geolocation':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.location = '{$keyword}' AND location_item.type = '$type'";
                    break;
            }

            if (!empty($sql)) {
                $query = self::query($sql);
                $num = $query->fetchColumn();

                return $num;
            } else {
                return false;
            }
        }


    }
}
