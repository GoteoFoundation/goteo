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
            $sql = "SELECT
                        id, name,
                        IFNULL(short, name) as short
                    FROM lang WHERE id = :id
                    ";
			$query = Model::query($sql, array(':id' => $id));
			return $query->fetchObject();
		}

        /*
         * Devuelve los idiomas
         */
		public static function getAll ($activeOnly = false) {
            $array = array();

            $sql = "SELECT
                        id, name,
                        IFNULL(short, name) as short
                    FROM lang
                    ";
            if ($activeOnly) {
                $sql .= "WHERE active = 1
                    ";
            }
            $sql .= "ORDER BY id ASC";

			$query = Model::query($sql);
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

		static public function is_active ($id) {
			$query = Model::query("SELECT id FROM lang WHERE id = :id AND active = 1", array(':id' => $id));
            if ($query->fetchObject()->id == $id) {
                return true;
            } else {
                return false;
            }
		}

        /*
         * Establece el idioma de visualización de la web
         */
		static public function set () {
            //echo 'Session: ' . $_SESSION['lang'] . '<br />';
            //echo 'Get: ' . $_GET['lang'] . '<br />';

            // si lo estan cambiando, ponemos el que llega
            if (isset($_GET['lang'])) {
/*                // si está activo, sino default
 *
 *  Aunque no esté activo!!
 *
                if (Lang::is_active($_GET['lang'])) {
 *
 */
                    $_SESSION['lang'] = $_GET['lang'];
   /*             } else {
                    $_SESSION['lang'] = \GOTEO_DEFAULT_LANG;
                }
    * 
    */
            } elseif (empty($_SESSION['lang'])) {
                // si no hay uno de session ponemos el default
                $_SESSION['lang'] = \GOTEO_DEFAULT_LANG;
            }
            // establecemos la constante
            define('LANG', $_SESSION['lang']);

            //echo 'New Session: ' . $_SESSION['lang'] . '<br />';
            //echo 'Const: ' . LANG . '<br />';
		}

		static public function locale () {
            $sql = "SELECT locale FROM lang WHERE id = :id";
			$query = Model::query($sql, array(':id' => \LANG));
			return $query->fetchColumn();
        }

	}
	
}