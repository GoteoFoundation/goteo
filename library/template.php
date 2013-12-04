<?php
namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception;

	/*
	 * Clase para gestionar las plantillas de los emails automáticos
	 */
    class Template {

        public
            $id,
            $lang,
            $name,
            $purpose,
            $title,
            $text;

        static public function get ($id, $lang = \LANG) {

            //@PIÑON que saque el texto en inglés cuando sea en francés y la plantilla no esté traducida
            if ($lang == 'fr') {
                $traducidas = array();
                $qaux = Model::query("SELECT id  FROM template_lang WHERE lang = 'fr'");
                foreach ($qaux->fetchAll(\PDO::FETCH_OBJ) as $trans) {
                    $traducidas[] = $trans->id;
                }
                if (!in_array($id, $traducidas)) $lang = 'en';
            }
            
            // buscamos la página para este nodo en este idioma
			$sql = "SELECT  template.id as id,
                            template.name as name,
                            template.group as `group`,
                            template.purpose as purpose,
                            IFNULL(template_lang.title, template.title) as title,
                            IFNULL(template_lang.text, template.text) as text
                     FROM template
                     LEFT JOIN template_lang
                        ON  template_lang.id = template.id
                        AND template_lang.lang = :lang
                     WHERE template.id = :id
                ";

			$query = Model::query($sql, array(
                                            ':id' => $id,
                                            ':lang' => $lang
                                        )
                                    );
			$template = $query->fetchObject(__CLASS__);
            return $template;
		}

		/*
		 *  Metodo para la lista de páginas
		 */
		public static function getAll($filters = array()) {
            $templates = array();

            try {

                $values = array(':lang' => \LANG);
                $sqlFilter = '';
                $and = "WHERE";
                if (!empty($filters['group'])) {
                    $sqlFilter .= " $and template.`group` = :group";
                    $and = "AND";
                    $values[':group'] = "{$filters['group']}";
                }
                if (!empty($filters['name'])) {
                    $sqlFilter .= " $and (template.`name` LIKE :name OR template.`purpose` LIKE :name OR template.`title` LIKE :name)";
                    $and = "AND";
                    $values[':name'] = "%{$filters['name']}%";
                }
                
                $sql = "SELECT
                            template.id as id,
                            template.name as name,
                            template.purpose as purpose,
                            IFNULL(template_lang.title, template.title) as title,
                            IFNULL(template_lang.text, template.text) as text
                        FROM template
                        LEFT JOIN template_lang
                            ON  template_lang.id = template.id
                            AND template_lang.lang = :lang
                        $sqlFilter
                        ORDER BY name ASC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $template) {
                    $templates[] = $template;
                }
                return $templates;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

		/*
		 *  Lista de plantillas para filtro
		 */
		public static function getAllMini() {
            $templates = array();

            try {
                $sql = "SELECT
                            template.id as id,
                            template.name as name
                        FROM template
                        ORDER BY name ASC
                        ";

                $query = Model::query($sql);
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $template) {
                    $templates[$template->id] = $template->name;
                }
                return $templates;
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

            if (empty($this->title)) {
                $errors[] = 'Registro sin titulo';
                $allok = false;
            }

            if (empty($this->text)) {
                $errors[] = 'Registro sin contenido';
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
                    ':template' => $this->id,
                    ':name' => $this->name,
                    ':group' => $this->group,
                    ':purpose' => $this->purpose,
                    ':title' => $this->title,
                    ':text' => $this->text
                );

				$sql = "REPLACE INTO template
                            (id, name, purpose, title, text, `group`)
                        VALUES
                            (:template, :name, :purpose, :title, :text, :group)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la palntilla. ' . $e->getMessage();
                return false;
			}

		}

        /*
         * Grupos de textos
         */
        static public function groups()
        {
            $groups = array(
                'general' => 'Propósito general',
                'massive' => 'Masivos',
                'access'  => 'Registro y acceso usuario',
                'project' => 'Actividad proyecto',
                'tips'    => 'Auto-tips difusión',
                'invest'  => 'Proceso aporte',
                'contact' => 'Comunicación',
                'advice'  => 'Avisos al autor'
            );

            \asort($groups);

            return $groups;
        }

	}
}