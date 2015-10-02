<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\User {

    use Goteo\Model;

    class Review extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $name,
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

            if (empty($this->user))
                $errors[] = 'No hay ningun usuario al que asignar';

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
                return false;
			}
		}

        /*
         * Dar por lista una revision
         */
		public function ready (&$errors = array()) {
			$values = array (
				':user'=>$this->user,
				':review'=>$this->id,
			);

            try {
                self::query("UPDATE user_review SET ready = 1 WHERE review = :review AND user = :user", $values);

                // recalcular puntuacion global de la revision
                Model\Review::recount($this->id, $errors);

				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido marcar la revision ' . $this->id . ' del usuario ' . $this->user . ' como lista. ' . $e->getMessage();
                return false;
			}
		}

        /*
         * Reabrir una revision
         */
		public function unready (&$errors = array()) {
			$values = array (
				':user'=>$this->user,
				':review'=>$this->id,
			);

            try {
                self::query("UPDATE user_review SET ready = 0 WHERE review = :review AND user = :user", $values);

                // recalcular puntuacion global de la revision
                Model\Review::recount($this->id, $errors);

				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido reabrir la revision ' . $this->id . ' del usuario ' . $this->user . '. ' . $e->getMessage();
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
                            user_review.ready as ready,
                            user.id as user_id,
                            user.name as user_name,
                            user.email as user_email,
                            user.avatar as user_avatar
                        FROM user_review
                        LEFT JOIN user
                        ON user.id=user_review.user
                        WHERE user_review.review = :id
                        ";
                $query = static::query($sql, array(':id'=>$review));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $share) {

                    // nombre y avatar
                    // datos del usuario. Eliminación de user::getMini
        
                    $user = new \Goteo\Model\User;
                    $user->name = $share['user_name'];
                    $user->avatar = \Goteo\Model\Image::get($share['user_avatar']);

                    $array[$share['id']] = (object) array(
                        'user'   => $share['id'],
                        'avatar' => $user->avatar,
                        'name'   => $user->name,
                        'ready'  => $share['ready']
                    );
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /*
         * Devuelve true o false si es legal que este usuario haga algo con esta revision
         */
        public static function is_legal ($user, $review) {
            $sql = "SELECT user, review FROM user_review WHERE user = :user AND review = :review";
            $values = array(
                ':user' => $user,
                ':review' => $review
            );
            $query = static::query($sql, $values);
            $legal = $query->fetchObject();
            if ($legal->user == $user && $legal->review == $review) {
                return true;
            } else {
                return false;
            }
        }

        /*
         * Graba un comentario para una sección
         */
         public function setComment ($section, $field, $text) {

             if (empty($this->user) || empty($this->id)) {
                 return false;
             }

             // primero comprobbar si ya hay registro,
            $sql = "SELECT COUNT(*) as cuantos FROM review_comment WHERE user = :user AND review = :review AND section = :section";
             $values = array(
                 ':user'    => $this->user,
                 ':review'  => $this->id,
                 ':section' => $section
             );

            $query = static::query($sql, $values);
            $exist = $query->fetchObject();

            if ($exist->cuantos == 1) {
                // si lo hay, update de este campo y texto
                 $sql = "UPDATE review_comment SET
                            `$field` = :text
                         WHERE review = :review
                         AND user = :user
                         AND section = :section
                         ";
            } else {
                // si no lo hay lo Insertamos con este campo y texto
                 $sql = "INSERT INTO review_comment SET
                            `$field` = :text,
                            review = :review,
                            user = :user,
                            section = :section
                         ";
            }

             $values[':text'] = $text;

             if (self::query($sql, $values)) {
                 return true;
             } else {
                 return false;
             }

         }

        /*
         * Graba la puntuacion para un criterio
         */
         public function setScore ($criteria, $score) {

             if (empty($this->user) || empty($this->id)) {
                 return false;
             }

             if ($score == true) {
                 $sql = "REPLACE INTO review_score SET
                            score = '1',
                            review = :review,
                            user = :user,
                            criteria = :criteria
                        ";
             } else {
                $sql = "DELETE FROM review_score
                            WHERE review = :review
                            AND user = :user
                            AND criteria = :criteria
                        ";
             }

             $values = array(
                 ':user'     => $this->user,
                 ':review'   => $this->id,
                 ':criteria' => $criteria

             );
             if (self::query($sql, $values)) {
                 return true;
             } else {
                 return false;
             }

         }

        /*
         * Metodo para contar la puntuacion dada por este revisor
         *
         * score es la puntuacion total
         * max es el maximo depuntuacio que podria haber obtenido
         *
         */
        public function recount (&$errors = array()) {
            try {
                $score = 0;
                $max   = 0;

                $sql = "SELECT
                            COUNT(criteria.id) as `max`,
                            COUNT(review_score.score) as score
                        FROM criteria
                        LEFT JOIN review_score
                            ON review_score.criteria = criteria.id
                            AND review_score.review = :review
                            AND review_score.user = :user
                        ";

                $query = static::query($sql, array(
                    ':review' => $this->id,
                    ':user'  => $this->user
                ));

                return $query->fetchObject();

            } catch(\PDOException $e) {
                $errors[] = "No se ha aplicado la puntuacion. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelve true o false si este usuario tiene asignada la revision de este proyecto
         */
        public static function is_assigned ($user, $project) {
            $sql = "
                SELECT project
                FROM review
                WHERE id IN (
                    SELECT review FROM user_review WHERE user = :user
                )
                AND project = :project";
            $values = array(
                ':user' => $user,
                ':project' => $project
            );
            $query = static::query($sql, $values);
            $legal = $query->fetchObject();
            if ($legal->project == $project) {
                return true;
            } else {
                return false;
            }
        }

	}
    
}