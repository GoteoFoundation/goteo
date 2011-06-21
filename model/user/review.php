<?php

namespace Goteo\Model\User {

    class Review extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $ready;


        /**
         * Get the reviews for a user
         * @param varcahr(50) $id  user identifier
         * @return array of reviews identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT review FROM user_review WHERE user = ?", array($id));
                $reviews = $query->fetchAll();
                foreach ($reviews as $int) {
                    $array[$int[0]] = $int[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay una revision para asignar';
                //Text::get('validate-review-noid');

            if (empty($this->user))
                $errors[] = 'No hay ningun usuario al que asignar';
                //Text::get('validate-review-nouser');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $values = array(':user'=>$this->user, ':review'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_review (user, review) VALUES(:user, :review)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La revisión {$this->id} no se ha asignado correctamente. Por favor, revise el metodo User\Review->save." . $e->getMessage();
				return false;
			}

		}

		/**
		 * Quitarle una revision al usuario
		 *
		 * @param varchar(50) $user id del usuario
		 * @param INT(12) $id  identificador de la tabla review
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':user'=>$this->user,
				':review'=>$this->id,
			);

            try {
                self::query("DELETE FROM user_review WHERE review = :review AND user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido desasignar la revision ' . $this->id . ' del usuario ' . $this->user . ' ' . $e->getMessage();
                //Text::get('remove-review-fail');
                return false;
			}
		}

        /*
         * Dar por lista una revision
         */
		public function ready () {
			$values = array (
				':user'=>$this->user,
				':review'=>$this->id,
			);

            try {
                self::query("UPDATE user_review SET ready = 1 WHERE review = :review AND user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido marcar la revision ' . $this->id . ' del usuario ' . $this->user . ' como lista. ' . $e->getMessage();
                //Text::get('remove-review-fail');
                return false;
			}
		}

        /*
         * Lista de usuarios que tienen asignada cierta revision
         */
        public static function checkers ($review) {
             $array = array ();
            try {
               $sql = "SELECT 
                            DISTINCT(user_review.user) as id,
                            user_review.ready as ready
                        FROM user_review
                        WHERE user_review.review = :id
                        ";
                $query = static::query($sql, array(':id'=>$user));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $share) {

                    // nombre i avatar
                    $user = \Goteo\Model\User::getMini($share['id']);

                    $array[] = (object) array(
                        'user'   => $share['id'],
                        'avatar' => $user->avatar,
                        'name'   => $user->name,
                        'ready'  => $user->ready
                    );
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

	}
    
}