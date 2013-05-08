<?php

namespace Goteo\Model\User {

    class Location extends \Goteo\Core\Model {

        public
            $location,
            $user;


        /**
         * Recupera la geolocalización de este 
         * @param varcahr(50) $id  user identifier
         * @return int (id geolocation)
         */
	 	public static function get ($id) {
            
            try {
                $query = static::query("SELECT id FROM location_item WHERE type = 'user' AND item = ?", array($id));
                $loc = $query->fetchColumn();
                return (!empty($loc)) ? $loc : null;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            if (empty($this->location)) return false;
            if (empty($this->user)) return false;
        }

		/*
		 *  Guarda la asignación del usuario a la localización
		 */
		public function save (&$errors = array()) {

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
		public static function remove ($user, &$errors = array()) {
            $values = array(':item'=>$user, ':type'=>'user');

            try {
                self::query("DELETE FROM location_item WHERE type = :type AND user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar la geolocalización del usuario ' . $user . '.<br />' . $e->getMessage();
                return false;
			}
		}

		/*
		 *  Guarda datos de geologin
		 */
		public static function loginRec ($data, &$errors = array()) {
			try {
	            $sql = "REPLACE INTO geologin (user, ip, lon, lat, msg) VALUES(:user, :ip, :lon, :lat, :msg)";
				self::query($sql, $data);
				return true;
			} catch(\PDOException $e) {
                $errors[] = "Fallo SQL ".$e->getMessage();
				return false;
			}

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

	}

}