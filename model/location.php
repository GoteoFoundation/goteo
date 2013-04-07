<?php

/*
 * Este modelo es para la geo localizacion
 */

namespace Goteo\Model {
    
    class Location extends \Goteo\Core\Model {
    
        public
            $id,
            $name,
            $location,
            $region,
            $country = 'España',
            $lon,
            $lat,
            $valid = 1;

        public static $type = array(
            'user' => 'Usuario',
            'project' => 'Proyecto',
            'node' => 'Nodo',
            'call' => 'Convocatoria',
        );

        /**
         * Obtener datos de una localizacion (longitud, latitud, nombre completo(montado), poblacion, provincia, pais y si está validada )
         * @param   type mixed  $id     Identificador
         * @return  type object         Instancia de geolocalización
         */
        static public function get ($id) {
            try {
                $query = static::query("SELECT *, CONCAT(location, region, country) as name FROM location WHERE id = ?", array($id));
                $item = $query->fetchObject(__CLASS__);

                return $item;
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /**
         * Lista de tareas
         *
         * @param  bool $visible    true|false
         * @return mixed            Array de objetos de tareas
         */
        public static function getAll ($filters = array()) {

            $values = array();

            $list = array();

            $sqlFilter = "";
            $and = " WHERE";
            if (isset($filters['valid']) && $filters['valid'] != 'all') {
                if ($filters['valid']) {
                    $sqlFilter .= "$and valid = 1";
                    $and = " AND";
                } else {
                    $sqlFilter .= "$and valid = 0 OR valid IS NULL";
                    $and = " AND";
                }
            }
            if (!empty($filters['location'])) {
                $sqlFilter .= "$and MD5(location) = :location";
                $values[':location'] = $filters['location'];
                $and = " AND";
            }
            if (!empty($filters['region'])) {
                $sqlFilter .= "$and MD5(region) = :region";
                $values[':region'] = $filters['region'];
                $and = " AND";
            }
            if (!empty($filters['country'])) {
                $sqlFilter .= "$and MD5(country) = :country";
                $values[':country'] = $filters['country'];
                $and = " AND";
            }

            if (!empty($filters['name'])) {
                $sqlFilter .= "$and (location LIKE :name OR region LIKE :name OR country LIKE :name)";
                $values[':name'] = "%".$filters['name']."%";
                $and = " AND";
            }
            /*
            if (isset($filters['used']) && $filters['used'] != 'all') {
                if ($filters['used']) {
                    $sqlFilter .= "$and id IN (SELECT DISTINCT(location) FROM user_location, project_location, node_location, call_location)";
                    $and = " AND";
                } else {
                    $sqlFilter .= "$and id NOT IN (SELECT DISTINCT(location) FROM user_location, project_location, node_location, call_location)";
                    $and = " AND";
                }
            }
             */

            $sql = "SELECT *, CONCAT(location, region, country) as name
                    FROM location
                    $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[] = $item;
            }
            return $list;
        }

        /**
         * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function save (&$errors = array()) {
             if (!$this->validate())
                return false;

            $fields = array(
                'id',
                'location',
                'region',
                'country',
                'lon',
                'lat',
                'valid'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO location SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate (&$errors = array()) {
            if (empty($this->country))
                $errors[] = 'Falta pais';

            if (empty($this->lon))
                $errors[] = 'Falta longitud';

            if (empty($this->lat))
                $errors[] = 'Falta latitud';

            if (empty($errors))
                return true;
            else
                return false;
        }


        /*
         * Lista simple de una columna filtrada por otra
         */
        public static function getList ($type = 'country', $filter = array('type'=>'', 'value'=>'')) {

            $list = array();
            $values = array();

            if (!empty($filter['type']) && !empty($filter['value'])) {
                $sqlFilter = " AND MD5({$filter['type']}) LIKE :fVal";
                $values[':fVal'] = $filter['value'];
            }

            if ($filter['type'] == 'name' && !empty($filter['value'])) {
                $sqlFilter = " AND {$filter['type']} LIKE :fVal";
                $values[':fVal'] = "%".$filter['value']."%";
            }

            $sql = static::query("
                SELECT
                    DISTINCT({$type}) as name
                FROM  location
                WHERE {$type} IS NOT NULL
                AND {$type} != ''
                $sqlFilter
                ORDER BY name ASC
                ", $values);

            foreach ($sql->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[md5($item->name)] = $item->name;
            }

            return $list;
        }



        /*
         * Lista de elementos a checkear
         * segun el tipo:
         *      . los que no estén asignados a una geoloc
         */
        public static function getCheck ($type = 'user', $limit) {

            $list = array();
            $values = array();

            if (!empty($filter['type']) && !empty($filter['value'])) {
                $sqlFilter = " WHERE {$filter['type']} LIKE :fVal";
                $values[':fVal'] = $filter['value'];
            }

            $sql = static::query("
                SELECT
                    DISTINCT({$type}) as name
                FROM  location
                $sqlFilter
                ORDER BY name ASC
                ", $values);

            foreach ($sql->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $SubM = 'Goteo\Model' . \chr(92) . \ucfirst($type);
                $item->$type = $SubM::get($item->item);

                $list[] = $item;
            }

            return $list;
        }


        /*
         * Lista de elementos encontrados por localización
         */
        public static function getSearch ($type = 'user', $filters = array()) {

            $list = array();
            $values = array(':type'=>$type);

            $sqlFilter = "";
            $and = " WHERE";
            if (!empty($filters['location'])) {
                $sqlFilter .= "$and location.location LIKE :location";
                $values[':location'] = $filters['location'];
                $and = " AND";
            }
            if (!empty($filters['region'])) {
                $sqlFilter .= "$and location.region LIKE :region";
                $values[':region'] = $filters['region'];
                $and = " AND";
            }
            if (!empty($filters['country'])) {
                $sqlFilter .= "$and location.country LIKE :country";
                $values[':country'] = $filters['country'];
                $and = " AND";
            }

            if (!empty($filters['name'])) {
                $sqlFilter .= "$and (location.location LIKE :name OR location.region LIKE :name OR location.country LIKE :name)";
                $values[':name'] = "%".$filters['name']."%";
                $and = " AND";
            }

            // solo las de cierto elemento
            if (!empty($filters['item'])) {
                $sqlFilter .= "$and location_item.item = :item";
                $values[':item'] = $filters['item'];
                $and = " AND";
            }

            $sql = "SELECT 
                        *,
                        CONCAT(location, region, country) as name
                    FROM location
                    INNER JOIN location_item
                        ON location_item.location = location.id
                        AND location_item.type = :type
                    $sqlFilter
                    )
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $SubM = 'Goteo\Model' . \chr(92) . \ucfirst($type);
                $item->$type = $SubM::get($item->item);
                
                $list[] = $item;
            }

            return $list;
        }



    }
}