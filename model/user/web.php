<?php

namespace Goteo\Model\User {

    class Web extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $url;


        /**
         * Get the interests for a user
         * @param varcahr(50) $id  user identifier
         * @return array of interests identifiers
         */
	 	public static function get ($id) {
            try {
                $query = static::query("SELECT id, url FROM user_web WHERE user = ?", array($id));
                $webs = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

                return $webs;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {}

		/*
		 *  Guarda las webs del usuario
		 */
		public function save (&$errors = array()) {

            $values = array(':user'=>$this->user, ':id'=>$this->id, ':url'=>$this->url);

			try {
	            $sql = "REPLACE INTO user_web (id, user, url) VALUES(:id, :user, :url)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La web {$this->url} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
				return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $user id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':user'=>$this->user,
				':interest'=>$this->id,
			);

            try {
                self::query("DELETE FROM user_interest WHERE interest = :interest AND user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar el interes ' . $this->id . ' del usuario ' . $this->user . ' ' . $e->getMessage();
                return false;
			}
		}

	}
    
}