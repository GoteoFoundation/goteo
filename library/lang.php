<?php

namespace Goteo\Library {

	use Goteo\Core\Model;
	/*
	 * Clase para sacar textos estáticos de la tabla text
	 *  (por ahora utilizar gettext no nos compensa, quizás más adelante)
	 *
	 */
    class Lang {
		
		static public function get ($id = \GOTEO_DEFAULT_LANG) {
			$query = Model::query("SELECT * FROM lang WHERE id = :id", array(':id' => $id));
			return $query->fetchObject();
		}

        /*
         * Devuelve los idiomas
         */
		public static function getAll ($activeOnly = false) {
            $array = array();
			$query = Model::query("SELECT id, name FROM lang ORDER BY id ASC");
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $lang) {
                $array[$lang->id] = $lang;
            }
            return $array;
		}


		/*
		 *  Esto se usara para la gestión de idiomas
         * aunque quizas no haya gestión de idiomas
		 */
		public function save($data, &$errors = array()) {
			if (!is_array($data) ||
				empty($data['id']) ||
				empty($data['name']) ||
				empty($data['active'])) {
					return false;
			}

			if (Model::query("REPLACE INTO lang (id, name, active) VALUES (:id, :name, :active)", array(':id' => $data['id'], ':name' => $data['name'], ':active' => $data['active']))) {
				return true;
			}
			else {
				$errors[] = 'Error al insertar los datos ' . \trace($data);
				return false;
			}
		}

	}
	
}