<?php

namespace Goteo\Model\User {

    use Goteo\Model\Location;

    class UserLocation extends \Goteo\Core\Model {
        protected $Table = 'user_location';
        public
            $method, // latitude,longitude obtaining method
                     // ip      = auto detection from ip,
                     // browser = user automatic provided,
                     // manual    = user manually provided
            $locable = true, //if is or not locable
            $city,
            $region,
            $country,
            $country_code,  // codigo pais  ISO 3166-1 alpha-2
            $longitude,
            $latitude,
            $info, //Some stored info
            $user;

        /**
         * Recupera la geolocalización de este
         * @param varcahr(50) $user  user identifier
         * @return UserLocation instance
         */
	 	public static function get ($user) {

            $query = static::query("SELECT * FROM user_location WHERE user = ?", array($user));
            if($ob = $query->fetchObject()) {
                $loc = new UserLocation(array(
                    'user' => $user,
                    'method' => $ob->method,
                    'info' => $ob->info,
                    'locable' => (bool) $ob->locable
                ));
            }
            return $loc ? $loc : false;
		}

		public function validate(&$errors = array()) {
            if (empty($this->user)) {
                $errors[] = 'User ID missing!';
            }
            $methods = array('ip', 'browser', 'manual');
            if (!in_array($this->method, $methods)) {
                $errors[] = 'Method (' . $this->method . ') error! must be one of: ' . implode(', ', $methods);
            }
            if (empty($this->country_code)) {
                $errors[] = 'Country code missing';
            }
            if (empty($this->country)) {
                $errors[] = 'Country missing';
            }
            if (empty($this->longitude)) {
                $errors[] = 'Longitude missing';
            }
            if (empty($this->latitude)) {
                $errors[] = 'Latitude missing';
            }
            if (empty($errors)) {
                return true;
            }
            else {
                return false;
            }
        }

		/*
		 *  Guarda la asignación del usuario a la localización
		 */
		public function save (&$errors = array()) {
            if (!$this->validate($errors)) {
                return false;
            }

            // remove from unlocable if method is not IP
            if($this->method !== 'ip') $this->locable = true;

            $values = array(':user'         => $this->user,
                            ':method'       => $this->method,
                            ':locable'      => $this->locable,
                            ':info'         => $this->info,
                            ':city'         => $this->city,
                            ':region'       => $this->region,
                            ':country'      => $this->country,
                            ':country_code' => $this->country_code,
                            ':longitude'    => $this->longitude,
                            ':latitude'     => $this->latitude
                            );

            try {
                $sql = "REPLACE INTO user_location (user, method, locable, info, city, region, country, country_code, longitude, latitude) VALUES (:user, :method, :locable, :info, :city, :region, :country, :country_code, :longitude, :latitude)";
                self::query($sql, $values);
            } catch(\PDOException $e) {
                $errors[] = "Error updating location for user. " . $e->getMessage();
                return false;
			}
            return true;
		}

		/**
		 * Desasignar el usuario de su localización
		 *
		 * @param array $errors
		 * @return boolean
		 */
		public function delete (&$errors = array()) {
            try {
                self::query("DELETE FROM user_location WHERE user = ?", array($this->user));
            } catch(\PDOException $e) {
                $errors[] = 'Error removing location for user ' . $this->user . '. ' . $e->getMessage();
                return false;
            }
			return true;
		}

        /**
         * Sets a property
         * @param [type] $user    [description]
         * @param [type] $prop    [description]
         * @param [type] $value   [description]
         * @param [type] &$errors [description]
         */
        public static function setProperty($user, $prop, $value, &$errors) {
            try {
                if(self::query("INSERT INTO user_location ($prop, type, item) VALUES (:value, 'user', :user)
                                ON DUPLICATE KEY UPDATE $prop = :value", array(':value' => $value, ':user' => $user)));
                    return true;
            } catch(\PDOException $e) {
                $errors[] = 'Error modifying [' . $prop . '] with val [' . $value . '] ' . $e->getMessage();
            }
            return false;

        }


        /**
         * Borrar de unlocable
         *
         * @param varchar(50) $user id de un usuario
         * @param array $errors
         * @return boolean
         */
        public static function setLocable ($user, &$errors = array()) {
            return self::setProperty($user, 'locable', 1, $errors);
        }

        /**
         * Añadir a unlocable
         *
         * @param varchar(50) $user id de un usuario
         * @param array $errors
         * @return boolean
         */
        public static function setUnlocable ($user, &$errors = array()) {
            return self::setProperty($user, 'locable', 0, $errors);
		}


        /**
         * Si está como ilocalizable
         * @param varcahr(50) $id  user identifier
         * @return int (have an unlocable register)
         */
	 	public static function isUnlocable ($user) {

            try {
                $query = self::query("SELECT locable FROM user_location WHERE user = ?", array($user));
                return !(bool) $query->fetchColumn();
            } catch(\PDOException $e) {
                return true;
            }
		}

	}

}
