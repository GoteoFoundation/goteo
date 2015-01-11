<?php

namespace Goteo\Model\User {

    use Goteo\Model\Location;

    class UserLocation extends \Goteo\Core\Model {
        protected $Table = 'location_item';
        public
            $location,
            $locations = array(),
            $user;

        /**
         * Recupera la geolocalización de este
         * @param varcahr(50) $id  user identifier
         * @return int (id geolocation)
         */
	 	public static function get ($id) {

            $query = static::query("SELECT location FROM location_item WHERE type = 'user' AND item = ?", array($id));
            if($loc = $query->fetchColumn()) {
                if($loc = Location::get($loc)) {
                    $loc = new UserLocation(array(
                        'location' => $loc->id,
                        'locations' => array($loc),
                        'user' => $id
                    ));
                    // $this->user = //create user
                    // $this->user = //create location
                }
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
            return true;
        }

		/*
		 *  Guarda la asignación del usuario a la localización
		 */
		public function save (&$errors = array()) {
            if (!$this->validate())
                return false;

            $values = array(':item'=>$this->user, ':location'=>$this->location, ':type'=>'user');

			try {
	            $sql = "REPLACE INTO location_item (location, item, type) VALUES(:location, :item, :type)";
				if (self::query($sql, $values)) {
                    // lo quitamos de unlocable
                    self::locable($this->user, $errors);
                    return true;
                } else {
                    return false;
                }
			} catch(\PDOException $e) {
				$errors[] = "No se ha podido asignar. Por favor, revise los datos." . $e->getMessage();
				return false;
			}

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
         * @return instance of Model\Location if successfull, false otherwise
         */
        public static function addUserLocation($data, &$errors = array()) {
            try {
                $location = new Location($data);
                if($location->save($errors)) {
                    $user_loc = new UserLocation(array(
                        'location' => $location->id,
                        'user' => $data['user']
                    ));
                    if($user_loc->save($errors)) {
                        return $location;
                    }
                }
            } catch(\PDOException $e) {
                $errors[] = "Fallo SQL ".$e->getMessage();
                return false;
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
		public static function locable ($user, &$errors = array()) {
            try {
                self::query("DELETE FROM unlocable WHERE user = :user", array(':user'=>$user));
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar al usuario ' . $user . ' de los ilocalizables.<br />' . $e->getMessage();
                return false;
			}
		}

		/**
		 * Añadir a unlocable
		 *
		 * @param varchar(50) $user id de un usuario
		 * @param array $errors
		 * @return boolean
		 */
		public static function unlocable ($user, &$errors = array()) {
            try {
                self::query("REPLACE INTO unlocable (user) VALUES (:user)", array(':user'=>$user));
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido añadir al usuario ' . $user . ' a los ilocalizables.<br />' . $e->getMessage();
                return false;
			}
		}


        /**
         * Si está como ilocalizable
         * @param varcahr(50) $id  user identifier
         * @return int (have an unlocable register)
         */
	 	public static function is_unlocable ($user) {

            try {
                $query = static::query("SELECT user FROM unlocable WHERE user = ?", array($user));
                $gl = $query->fetchColumn();
                return ($gl == $user) ? true : false;
            } catch(\PDOException $e) {
                return false;
            }
		}

	}

}
