<?php

/*
 * Este modelo es para la geo localizacion
 *
 * Usamos la libreria geoloc para todas las funcionalidades de webservices
 *
 */

namespace Goteo\Model {

    use Goteo\Library\Geoloc;

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

        public static $items = array(
            'user' => 'Usuario',
            'project' => 'Proyecto',
            'node' => 'Nodo',
            'call' => 'Convocatoria',
        );

        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == "uses") { // numero de usuarios asignados a la localización
	            return self::countBy('located', $this->id);
	        }
            return $this->$name;
        }

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
         * Lista de Geolocalizaciones (tabla maestra `location`)
         *
         * @param  mixed $filters array de filtros
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
            if (!empty($filters['city'])) {
                $sqlFilter .= "$and MD5(city) = :city";
                $values[':city'] = $filters['city'];
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
            if (!empty($filters['country_code'])) {
                $sqlFilter .= "$and MD5(country_code) = :country_code";
                $values[':country_code'] = $filters['country_code'];
                $and = " AND";
            }

            if (!empty($filters['name'])) {
                $sqlFilter .= "$and (city LIKE :name OR region LIKE :name OR country LIKE :name)";
                $values[':name'] = "%".$filters['name']."%";
                $and = " AND";
            }

            $sql = "SELECT *
                    FROM location
                    $sqlFilter
                    ORDER BY country, region, city
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $item->name = "{$item->city}, {$item->region}, {$item->country}, {$item->country_code}";
                $list[] = $item;
            }
            return $list;
        }

        /**
         * Lista simple de Geolocalizaciones, solo nombre y silo validateas
         * y ordenada
         *
         * @param  mixed $filters array de filtros
         * @return mixed            Array de objetos de tareas
         */
        public static function getAllMini () {

            $list = array();

            $sql = "SELECT id, city, region, country, country_code
                    FROM location
                    WHERE valid = 1
                    ";

            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[$item->id] = "{$item->location}, {$item->region}, {$item->country}, {$item->country_code}";
            }

            asort($list);

            return $list;
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

            $sql = "
                SELECT
                    DISTINCT({$type}) as name
                FROM  location
                WHERE {$type} IS NOT NULL
                AND {$type} != ''
                $sqlFilter
                ORDER BY name ASC
                ";
            $query = static::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[md5($item->name)] = $item->name;
            }

            return $list;
        }

        /**
         * Metodo para sacar las que hay en proyectos
         * @return array strings
         *
         * Cerca de la obsolitud
         *
         */
		public static function getProjLocs () {

            $results = array();

            $sql = "SELECT distinct(project_location) as location
                    FROM project
                    WHERE status > 2
                    ORDER BY location ASC";

            try {
                $query = self::query($sql);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                    $results[md5($item->location)] = $item->location;
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la lista de localizaciones');
            }
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
            if (!empty($filters['city'])) {
                $sqlFilter .= "$and location.city LIKE :city";
                $values[':city'] = $filters['city'];
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
            if (!empty($filters['country_code'])) {
                $sqlFilter .= "$and location.country_code LIKE :country_code";
                $values[':country_code'] = $filters['country_code'];
                $and = " AND";
            }

            if (!empty($filters['name'])) {
                $sqlFilter .= "$and (location.city LIKE :name OR location.region LIKE :name OR location.country LIKE :name)";
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
                        CONCAT(city, region, country) as name
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
                $SubM = 'Goteo\Model\\' . \ucfirst($type) .'\\' . \ucfirst($type) . 'Location';
                $item->$type = $SubM::get($item->item);

                $list[] = $item;
            }

            return $list;
        }

        /*
         * Conteo de usuarios por geolocalización
         */
        public static function countBy ($type = 'registered', $keyword = '') {

            switch ($type) {
                case 'registered':
                    $sql = "SELECT COUNT(id) FROM user";
                    break;
                case 'node':
                    $sql = "SELECT COUNT(id) FROM user WHERE node = '{$keyword}'";
                    break;
                case 'no-location':
                    $sql = "SELECT COUNT(id) FROM user WHERE TRIM(location) = '' OR location IS NULL";
                    break;
                case 'located':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.type = 'user'";
                    break;
                case 'unlocated':
                    $sql = "SELECT COUNT(id) FROM user WHERE id NOT IN (SELECT item FROM location_item WHERE location_item.type = 'user')";
                    break;
                case 'unlocable':
                    $sql = "SELECT COUNT(user) FROM unlocable";
                    break;
                case 'not-country':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.location IN (SELECT location.id FROM location WHERE location.country NOT LIKE '{$keyword}')";
                    break;
                case 'country':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.location IN (SELECT location.id FROM location WHERE location.country LIKE '{$keyword}')";
                    break;
                case 'region':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.location IN (SELECT location.id FROM location WHERE location.region LIKE '{$keyword}')";
                    break;
                case 'geolocation':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.location = '{$keyword}'";
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
