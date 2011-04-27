<?php
namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception;

	/*
	 * Clase para gestionar el contenido de las páginas institucionales
	 */
    class Page {

        public
            $id,
            $lang,
            $node,
            $name,
            $description,
            $url;

        static public function get ($id = null, $lang = 'es') {
			if ($id === null)
				return '';

            return array();

            // buscamos la página y la versión para este nodo en este idioma
            /*

			// buscamos el texto en la tabla
			$query = Model::query("SELECT text FROM text WHERE id = :id AND lang = :lang", array(':id' => $id, ':lang' => $lang));
			$exist = $query->fetchObject();
			if ($exist->text) {
//				return $_cache[$id][$lang] = $exist->text;
				return $exist->text;
			} else {
				// lo metemos en la tabla pero no en cache
				Model::query("REPLACE INTO text (id, lang, text) VALUES (:id, :lang, :text)", array(':id' => $id, ':lang' => $lang, ':text' => $id));
				Model::query("REPLACE INTO purpose (text, purpose) VALUES (:text, :purpose)", array(':text' => $id, ':purpose' => "Texto $id"));

				return $id;
			}
             * *
             */
		}

		/*
		 *  Metodo para la lista de páginas
		 */
		public static function getAll($lang = 'es', $filter = null) {
            $texts = array();

            $values = array(':lang'=>$lang);

            $sql = "SELECT id, text FROM text WHERE lang = :lang";
            if (!empty($filter)) {
                $sql .= " AND id LIKE :filter";
                $values[':filter'] = "%$filter%";
            }
            $sql .= " ORDER BY id ASC";

            try {
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $text) {
                    $text->purpose = self::getPurpose($text->id);
                    $texts[] = $text;
                }
                return $texts;
            } catch (\PDOException $e) {
                throw new Exception($e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

		/*
		 *  Esto se usara para la gestión de contenido
		 */
		public function save($data, &$errors = array()) {
            return false;

/*
			if (!is_array($data) ||
				empty($data['id']) ||
				empty($data['text']) ||
				empty($data['lang'])) {
					return false;
			}

			if (Model::query("UPDATE text SET text = :text WHERE id = :id AND lang = :lang", array(':text' => $data['text'], ':id' => $data['id'], ':lang' => $data['lang']))) {
				return true;
			}
			else {
				$errors[] = 'Error al insertar los datos <pre>' . print_r($data, 1) . '</pre>';
				return false;
			}
*/

		}


	}
}