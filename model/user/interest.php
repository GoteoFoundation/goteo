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
                    $array[$int[0]] = $int[0];
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
            $array = array ();
            try {
                $query = static::query("SELECT id, name FROM interest ORDER BY name ASC");
                $interests = $query->fetchAll();
                foreach ($interests as $int) {
                    $array[$int[0]] = $int[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ningun interes para guardar';
                //Text::get('validate-interest-noid');

            if (empty($this->user))
                $errors[] = 'No hay ningun usuario al que asignar';
                //Text::get('validate-interest-nouser');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $values = array(':user'=>$this->user, ':interest'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_interest (user, interest) VALUES(:user, :interest)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "El interés {$this->id} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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
                //Text::get('remove-interest-fail');
                return false;
			}
		}

        /*
         * Lista de usuarios que comparten intereses con el usuario
         */
        public static function share ($user) {
             $array = array ();
            try {
               $sql = "SELECT DISTINCT(user_interest.user) as id
                        FROM user_interest
                        INNER JOIN user_interest as mine
                            ON user_interest.interest = mine.interest
                            AND mine.user = :me
                        WHERE user_interest.user != :me
                        ";
                $query = static::query($sql, array('me'=>$user));
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar
                    $user = \Goteo\Model\User::get($share['id']);
                    // meritocracia
                    $support = (object) $user->support;
                    // proyectos publicados
                    $query = self::query('SELECT COUNT(id) FROM project WHERE owner = ? AND status = 3', array($share['id']));
                    $projects = $query->fetchColumn(0);

                    $array[] = (object) array(
                        'user' => $share['id'],
                        'avatar' => $user->avatar,
                        'name' => $user->name,
                        'projects' => $projects,
                        'invests' => $support->invests
                    );
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

	}
    
}