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
            $url,
            $content;

        static public function get ($id, $lang = \LANG, $node = \GOTEO_NODE) {

            // buscamos la página para este nodo en este idioma
			$sql = "SELECT  page.id as id,
                            IFNULL(page_lang.name, page.name) as name,
                            IFNULL(page_lang.description, page.description) as description,
                            page.url as url,
                            IFNULL(page_node.lang, '$lang') as lang,
                            IFNULL(page_node.node, '$node') as node,
                            IFNULL(page_node.content, '') as content
                     FROM page
                     LEFT JOIN page_lang
                        ON  page_lang.id = page.id
                        AND page_lang.lang = :lang
                     LEFT JOIN page_node
                        ON  page_node.page = page.id
                        AND page_node.lang = :lang
                        AND page_node.node = :node
                     WHERE page.id = :id
                ";

			$query = Model::query($sql, array(
                                            ':id' => $id,
                                            ':lang' => $lang,
                                            ':node' => $node
                                        )
                                    );
			$page = $query->fetchObject(__CLASS__);
            return $page;
		}

		/*
		 *  Metodo para la lista de páginas
		 */
		public static function getAll() {
            $pages = array();

            try {

                $values = array(':lang' => \LANG);

                $sql = "SELECT
                            page.id as id,
                            IFNULL(page_lang.name, page.name) as name,
                            IFNULL(page_lang.description, page.description) as description,
                            page.url as url
                        FROM page
                        LEFT JOIN page_lang
                            ON  page_lang.id = page.id
                            AND page_lang.lang = :lang
                        ORDER BY name ASC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $page) {
                    $pages[] = $page;
                }
                return $pages;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

        public function validate(&$errors = array()) {

            $allok = true;

            if (empty($this->id)) {
                $errors[] = 'Registro sin id';
                $allok = false;
            }

            if (empty($this->lang)) {
                $errors[] = 'Registro sin lang';
                $allok = false;
            }

            if (empty($this->node)) {
                $errors[] = 'Registro sin node';
                $allok = false;
            }

            return $allok;
        }

		/*
		 *  Esto se usara para la gestión de contenido
		 */
		public function save(&$errors = array()) {
            if(!$this->validate($errors)) { return false; }

  			try {
                $values = array(
                    ':id' => $this->id,
                    ':name' => $this->name,
                    ':description' => $this->description
                );

				$sql = "UPDATE page
                            SET name = :name,
                                description = :description
                            WHERE id = :id
                        ";
				Model::query($sql, $values);





                $values = array(
                    ':page' => $this->id,
                    ':lang' => $this->lang,
                    ':node' => $this->node,
                    ':contenido' => $this->content
                );

				$sql = "REPLACE INTO page_node
                            (page, node, lang, content)
                        VALUES
                            (:page, :node, :lang, :contenido)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}

		}


	}
}