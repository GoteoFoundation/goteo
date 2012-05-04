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
            $content,
            $pendiente; // para si esta pendiente de traduccion

        static public function get ($id, $node = \GOTEO_NODE, $lang = \LANG) {

            // buscamos la página para este nodo en este idioma
			$sql = "SELECT  page.id as id,
                            IFNULL(page_lang.name, IFNULL(page_node.name, page.name)) as name,
                            IFNULL(page_lang.description, IFNULL(page_node.description, page.description)) as description,
                            page.url as url,
                            IFNULL(page_node.lang, '$lang') as lang,
                            IFNULL(page_node.node, '$node') as node,
                            IFNULL(page_node.content, original.content) as content
                     FROM page
                     LEFT JOIN page_lang
                        ON  page_lang.id = page.id
                        AND page_lang.lang = :lang
                     LEFT JOIN page_node
                        ON  page_node.page = page.id
                        AND page_node.lang = :lang
                        AND page_node.node = :node
                     LEFT JOIN page_node as original
                        ON  original.page = page.id
                        AND original.node = :node
                        AND original.lang = 'es'
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
		public static function getAll($lang = \LANG, $node = \GOTEO_NODE) {
            $pages = array();

            try {

                $values = array(':lang' => $lang, ':node' => $node);

                if ($node != \GOTEO_NODE) {
                    $sqlFilter .= " WHERE page.id IN ('about', 'contact', 'press', 'service')";
                }

                $sql = "SELECT
                            page.id as id,
                            IFNULL(page_lang.name, IFNULL(page_node.name, page.name)) as name,
                            IFNULL(page_lang.description, IFNULL(page_node.description, page.description)) as description,
                            IF(page_node.content IS NULL, 1, 0) as pendiente,
                            page.url as url
                        FROM page
                        LEFT JOIN page_lang
                            ON  page_lang.id = page.id
                            AND page_lang.lang = :lang
                        LEFT JOIN page_node
                            ON  page_node.page = page.id
                            AND page_node.lang = :lang
                            AND page_node.node = :node
                        $sqlFilter
                        ORDER BY pendiente DESC, name ASC
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

		/*
		 *  Lista simple de páginas
		 */
		public static function getList($node = \GOTEO_NODE) {
            $pages = array();

            try {

                if ($node != \GOTEO_NODE) {
                    $sqlFilter = " WHERE page.id IN ('about', 'contact', 'press', 'service')";
                } else {
                    $sqlFilter = '';
                }

                $values = array(':lang' => 'es', ':node' => $node);

                $sql = "SELECT
                            page.id as id,
                            IFNULL(page_node.name, page.name) as name,
                            IFNULL(page_node.description, page.description) as description,
                            page.url as url
                        FROM page
                        LEFT JOIN page_node
                           ON  page_node.page = page.id
                           AND page_node.lang = :lang
                           AND page_node.node = :node
                        $sqlFilter
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

            if (empty($this->name)) {
                $errors[] = 'Registro sin nombre';
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
                    ':page' => $this->id,
                    ':lang' => $this->lang,
                    ':node' => $this->node,
                    ':name' => $this->name,
                    ':description' => $this->description,
                    ':contenido' => $this->content
                );

				$sql = "REPLACE INTO page_node
                            (page, node, lang, name, description, content)
                        VALUES
                            (:page, :node, :lang, :name, :description, :contenido)
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

        /**
         * PAra actualizar solamente el contenido
         * @param <type> $errors
         * @return <type>
         */
		public function update($id, $lang, $node, $name, $description, $content, &$errors = array()) {
  			try {
                $values = array(
                    ':page' => $id,
                    ':lang' => $lang,
                    ':node' => $node,
                    ':name' => $name,
                    ':description' => $description,
                    ':content' => $content
                );

				$sql = "REPLACE INTO page_node
                            (page, node, lang, name, description, content)
                        VALUES
                            (:page, :node, :lang, :name, :description, :content)
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