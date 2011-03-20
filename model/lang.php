<?php

namespace Goteo\Model {

	/*
	 * Clase para sacar textos estáticos de la tabla text
	 *  (por ahora utilizar gettext no nos compensa, quizás más adelante)
	 *
	 */
    class Lang extends \Goteo\Core\Model {
		
		static public function get ($id = null) {
			if ($id === null)
				return false;

			$query = self::query("SELECT * FROM lang WHERE id = :id", array(':id' => $id));
			return $query->fetchObject();
		}

		/*
		 *  Esto se usara para la gestión de idiomas
		 */
		public function save($data, &$errors = array()) {
			if (!is_array($data) ||
				empty($data['id']) ||
				empty($data['name']) ||
				empty($data['active'])) {
					return false;
			}

			if (self::query("REPLACE INTO lang (id, name, active) VALUES (:id, :name, :active)", array(':id' => $data['id'], ':name' => $data['name'], ':active' => $data['active']))) {
				return true;
			}
			else {
				$errors[] = 'Error al insertar los datos <pre>' . print_r($data, 1) . '</pre>';
				return false;
			}
		}

	}
	
}