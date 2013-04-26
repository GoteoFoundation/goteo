<?php

namespace Goteo\Model\User {

	use Goteo\Model\Location as Geolocation;

    class Location extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $lon,
            $lat;


        /**
         * Recupera la geolocalización de este 
         * @param varcahr(50) $id  user identifier
         * @return instance of Goteo\Model\Location
         */
	 	public static function get ($id) {
            
            try {
                $query = static::query("SELECT id FROM location_item WHERE type = 'user' AND item = ?", array($id));
                if ($loc = $query->fetchColumn()) {
                    return Geolocation::get($loc);
                } else {
                    return false;
                }
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {}

		/*
		 *  Guarda la asignación del usuario a la localización
		 */
		public function save (&$errors = array()) {

            // Imagen
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);
                if ($image->save()) {
                    $image = $image->id;
                } else {
                    Message::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $image = '';
                }
            } elseif ($this->image instanceof Image) {
                $image = $this->image->id;
            } else {
                $image = $this->image;
            }

            $values = array(':item'=>$this->user, ':location'=>$this->location, ':type'=>'user');

			try {
	            $sql = "REPLACE INTO location_item (location, item, type) VALUES(:location, :item, :type)";
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