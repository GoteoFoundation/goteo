<?php
namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Application\Lang,
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

        static public function get ($id, $node = \GOTEO_NODE, $lang = null) {
            if(empty($lang)) $lang = Lang::current();
            //idioma de soporte
            $default_lang=Model::default_lang($lang);

            // buscamos la página para este nodo en este idioma
			$sql = "SELECT  page.id as id,
                            IFNULL(page_node.name, IFNULL(default_lang.name, page.name)) as name,
                            IFNULL(page_node.description, IFNULL(default_lang.description, page.description)) as description,
                            page.url as url,
                            IFNULL(page_node.lang, '$lang') as lang,
                            IFNULL(page_node.node, '$node') as node,
                            IFNULL(page_node.content, IFNULL(default_lang.content, NULL)) as content
                     FROM page
                     LEFT JOIN page_node
                        ON  page_node.page = page.id
                        AND page_node.lang = :lang
                        AND page_node.node = :node
                     LEFT JOIN page_node as default_lang
                        ON  default_lang.page = page.id
                        AND default_lang.node = :node
                        AND default_lang.lang = :default_lang
                     WHERE page.id = :id
                ";

			$query = Model::query($sql, array(
                                            ':id' => $id,
                                            ':lang' => $lang,
                                            ':node' => $node,
                                            ':default_lang' =>$default_lang
                                        )
                                    );
			$page = $query->fetchObject(__CLASS__);

            if((empty($page->content))&&($node!=\GOTEO_NODE))
                $page=self::get($id, \GOTEO_NODE, $lang);

            return $page;
		}

		/*
		 *  Metodo para la lista de páginas
		 */
		public static function getAll($filters = array(), $lang = null, $node = \GOTEO_NODE) {
            if(empty($lang)) $lang = Lang::current();
            $pages = array();

            try {

                $values = array(':lang' => $lang, ':node' => $node);

                if ($node != \GOTEO_NODE) {
                    $sqlFilters .= " AND page.id IN ('about', 'contact', 'press', 'service')";
                }

                if (!empty($filters['text'])) {
                    $sqlFilters .= " AND ( page_node.name LIKE :text
                        OR  page_node.description LIKE :text
                        OR  page_node.content LIKE :text)";
                    $values[':text'] = "%{$filters['text']}%";
                }
                // pendientes de traducir
                if (!empty($filters['pending'])) {
                    $sqlFilters .= " HAVING pendiente = 1";
                }

                $sql = "SELECT
                            page.id as id,
                            IFNULL(page_node.name, IFNULL(original.name, page.name)) as name,
                            IFNULL(page_node.description, IFNULL(original.description, page.description)) as description,
                            IF(page_node.content IS NULL OR page_node.pending = 1, 1, 0) as pendiente,
                            page_node.content as content,
                            original.content as original_content,
                            original.name as original_name,
                            original.description as original_description,
                            page.url as url
                        FROM page
                        LEFT JOIN page_node
                            ON  page_node.page = page.id
                            AND page_node.lang = :lang
                            AND page_node.node = :node
                         LEFT JOIN page_node as original
                            ON  original.page = page.id
                            AND original.node = :node
                            AND original.lang = 'es'
                        WHERE page.url IS NOT NULL
                        $sqlFilters
                        ORDER BY pendiente DESC, name ASC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $page) {
                    $pages[] = $page;
                }
                return $pages;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
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
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, true) . "</pre>");
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
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, true) . "</pre>";
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}

		}

		/*
		 *  Esto se usara para la gestión de contenido
		 */
		public function add(&$errors = array()) {

  			try {
                $values = array(
                    ':id' => $this->id,
                    ':name' => $this->name,
                    ':url' => '/about/'.$this->id
                );

				$sql = "INSERT INTO page
                            (id, name, url)
                        VALUES
                            (:id, :name, :url)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, true) . "</pre>";
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}

		}

        /**
         * Para actualizar solamente el contenido
         * @param <type> $errors
         * @return <type>
         */
		public static function update($id, $lang, $node, $name, $description, $content, &$errors = array()) {
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
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, true) . "</pre>";
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}

		}

        /**
         * Para marcar todas las traducciones de una página como pendiente de traducir
         */
		public static function setPending($id, $node = \GOTEO_NODE, &$errors = array()) {
  			try {
                $values = array(
                    ':page' => $id,
                    ':node' => $node
                );

				$sql = "UPDATE page_node
				            SET pending = 1
				            WHERE page = :page
				            AND node = :node
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, true) . "</pre>";
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al marcar como pendiente la traducción de la pagina '.$id.' del nodo '.$node.'. ' . $e->getMessage();
                return false;
			}

		}


	}
}
