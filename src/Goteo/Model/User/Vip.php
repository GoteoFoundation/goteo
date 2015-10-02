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

	use Goteo\Model\Image;

    class Vip extends \Goteo\Core\Model {

        public
            $user,
            $image;


        /**
         * Get the interests for a user
         * @param varcahr(50) $id  user identifier
         */
	 	public static function get ($id) {

            try {
                $query = static::query("SELECT * FROM user_vip WHERE user = ?", array($id));
                if ($vip = $query->fetchObject(__CLASS__)) {

                    if (!empty($vip->image)) {
                        $vip->image = Image::get($vip->image);
                    }
                } else {
                    $vip = new Vip(array('user'=>$id, 'image'=>''));
                }

                return $vip;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {}

		/*
		 *  Guarda las webs del usuario
		 */
		public function save (&$errors = array()) {

            // Imagen
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);

                if ($image->save($errors)) {
                    $image = $image->id;
                } else {
                    Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $image = '';
                }
            } elseif ($this->image instanceof Image) {
                $image = $this->image->id;
            } else {
                $image = $this->image;
            }

            $values = array(':user'=>$this->user, ':image'=>$image);

			try {
	            $sql = "REPLACE INTO user_vip (user, image) VALUES(:user, :image)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La imagen no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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
				':user'=>$this->user
			);

            try {
                self::query("DELETE FROM user_vip WHERE user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar la imagen vip del usuario ' . $this->user . ' ' . $e->getMessage();
                return false;
			}
		}

	}

}
