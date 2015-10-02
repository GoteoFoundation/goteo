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

    class Apikey extends \Goteo\Core\Model {

        public
            $key,
            $user_id;


        /**
         * Recupera la clave api de este usario 
         * @param varcahr(50) $id  user identifier
         * @return string  $key
         */
	 	public static function get ($id) {
            
            try {
                $query = static::query("SELECT `key` FROM user_api WHERE user_id = ?", array($id));
                $key = $query->fetchColumn();
                return (!empty($key)) ? $key : null;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            if (empty($this->key)) return false;
            if (empty($this->user_id)) return false;
        }

		/*
		 *  Guarda la clave del usuario
		 */
		public function save (&$errors = array()) {

            $values = array(':user'=>$this->user_id, ':key'=>$this->key);

			try {
	            $sql = "REPLACE INTO user_api (user_id, `key`) VALUES(:user, :key)";
				if (self::query($sql, $values)) {
                    return true;
                } else {
                    return false;
                }
			} catch(\PDOException $e) {
				$errors[] = "No se ha podido crear registro user_api. Por favor, revise los datos." . $e->getMessage();
				return false;
			}

		}

	}

}