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
				echo $e->getMessage();
                return false;
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
				if ($res = self::query($sql, $values))  {
					return true;
				}
				else {
					echo "$sql<br /><pre>" . print_r($values, 1) . "</pre>";
				}
			} catch(\PDOException $e) {
				$errors[] = $e->getMessage();
				$errors[] = "El interés {$interest} no se ha asignado correctamente. Por favor, revise los datos.";
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

			if (self::query("DELETE FROM user_interest WHERE interest = :interest AND user = :user", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar el interes ' . $this->id . ' del usuario ' . $this->user;
				return false;
			}
		}

	}
    
}