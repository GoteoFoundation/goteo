<?php

namespace Goteo\Model\User {

    use Goteo\Model\Location;

    class UserLocation extends \Goteo\Core\Model {
        protected $Table = 'location_item';
        public
            $location,
            $locations = array(), //array of addresses
            $method, // latitude,longitude obtaining method
                     // ip      = auto detection from ip,
                     // browser = user automatic provided,
                     // manual    = user manually provided
            $locable = true, //if is or not locable
            $info, //Some stored info
            $user;

        /**
         * Recupera la geolocalización de este
         * @param varcahr(50) $id  user identifier
         * @return int (id geolocation)
         */
	 	public static function get ($id) {

            $query = static::query("SELECT * FROM location_item WHERE type = 'user' AND item = ?", array($id));
            if($ob = $query->fetchObject()) {
                if(!($loc = Location::get($ob->location))) {
                    //location non exists
                    $loc = new Location();
                }
                $loc = new UserLocation(array(
                    'location' => (int) $ob->location,
                    'locations' => array($loc),
                    'user' => $id,
                    'method' => $ob->method,
                    'info' => $ob->info,
                    'locable' => (bool) $ob->locable
                ));
            }
            return $loc ? $loc : false;
		}

		public function validate(&$errors = array()) {
            if (empty($this->location)) {
                $errors[] = 'Location ID missing!';
                return false;
            }
            if (empty($this->user)) {
                $errors[] = 'User ID missing!';
                return false;
            }
            $methods = array('ip', 'browser', 'manual');
            if (!in_array($this->method, $methods)) {
                $errors[] = 'Method (' . $this->method . ') error! must be one of: ' . implode(', ', $methods);
                return false;
            }
            return true;
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

            $values = array(':item'     => $this->user,
                            ':location' => $this->location,
                            ':method'   => $this->method,
                            ':locable'  => $this->locable,
                            ':info'     => $this->info,
                            ':type'     => 'user'
                            );

            try {
                $sql = "REPLACE INTO location_item (location, item, type, method, locable, info) VALUES (:location, :item, :type, :method, :locable, :info)";
                self::query($sql, $values);
			} catch(\PDOException $e) {
				$errors[] = "No se ha podido asignar. Por favor, revise los datos." . $e->getMessage();
				return false;
			}
            return true;
		}

		/**
		 * Desasignar el usuario de su localización
		 *
		 * @param varchar(50) $user id de un usuario
		 * @param array $errors
		 * @return boolean
		 */
		public function delete (&$errors = array()) {
            $user = $this->user;
            $values = array(':item'=>$user, ':type'=>'user');

            try {
                self::query("DELETE FROM location_item WHERE type = :type AND item = :item", $values);
            } catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar la geolocalización del usuario ' . $user . '.<br />' . $e->getMessage();
                return false;
            }
			return true;
		}

        /**
         * Adds a location to the corresponding location/location_item tables according to the user
         * @param [type] $data    [description]
         * @param array  &$errors [description]
         * @return instance of Model\User\UserLocation if successfull, false otherwise
         */
        public static function addUserLocation($data, &$errors = array()) {
            try {
                $location = new Location($data);
                if($location->save($errors)) {
                    //check if exists location
                    $user_loc = new UserLocation(array(
                        'location' => $location->id,
                        'user' => $data['user'],
                        'method' => $data['method'],
                        'locable' => !self::isUnlocable($data['user'])
                    ));
                    if($user_loc->save($errors)) {
                        $user_loc->locations[] = $location;
                        return $user_loc;
                    }
                    if(empty($errors)) $errors[] = 'unknow error';
                }
            } catch(\PDOException $e) {
                $errors[] = "Fallo SQL ".$e->getMessage();
                return false;
            }
            return false;
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
                if(self::query("INSERT INTO location_item ($prop, type, item) VALUES (:value, 'user', :user)
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
                $query = self::query("SELECT locable FROM location_item WHERE type = 'user' AND item = ?", array($user));
                return !(bool) $query->fetchColumn();
            } catch(\PDOException $e) {
                return true;
            }
		}

	}

}
