<?php

namespace Goteo\Model\User {

    class Interest extends \Goteo\Core\Model {

        public
            $id,
            $user;


        /**
         * Get the interests for a user
         * @param varcahr(50) $id  user identifier
         * @return array of interests identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT interest FROM user_interest WHERE user = ?", array($id));
                $interests = $query->fetchAll();
                foreach ($interests as $int) {
                    $array[] = $int[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all categories available
         *
         * @param void
         * @return array
         */
		public static function getAll () {
            return array(
                1=>'Educación',
                2=>'Economía solidaria',
                3=>'Empresa abierta',
                4=>'Formación técnica',
                5=>'Desarrollo',
                6=>'Software',
                7=>'Hardware');
		}

		public function validate(&$errors = array()) {}

		/*
		 *  save... al ser un solo campo quiza no lo usemos
		 */
		public function save (&$errors = array()) {

            $values = array(':user'=>$this->user, ':interest'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_interest (user, interest) VALUES(:user, :interest)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "El interés {$interest} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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