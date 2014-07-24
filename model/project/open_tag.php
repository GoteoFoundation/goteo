<?php

namespace Goteo\Model\Project {

    class Open_tag extends \Goteo\Core\Model {

        public
            $id,
            $project;


        /**
         * Get the open_tags for a project
         * @param varcahr(50) $id  Project identifier
         * @return array of categories identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT open_tag FROM project_open_tag WHERE project = ?", array($id));
                $open_tags = $query->fetchAll();
                foreach ($open_tags as $cat) {
                    $array[$cat[0]] = $cat[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all open_tags available
         *
         * @param void
         * @return array
         */
		public static function getAll () {
            $array = array ();
            try {
                $sql = "
                    SELECT
                        open_tag.id as id,
                        IFNULL(open_tag_lang.name, open_tag.name) as name
                    FROM    open_tag
                    LEFT JOIN open_tag_lang
                        ON  open_tag_lang.id = open_tag.id
                        AND open_tag_lang.lang = :lang
                    ORDER BY name ASC
                    ";

                $query = static::query($sql, array(':lang'=>\LANG));
                $open_tags = $query->fetchAll();
                foreach ($open_tags as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all open_tags for this project by name
         *
         * @param void
         * @return array
         */
		public static function getNames ($project = null, $limit = null) {

            $lang = \LANG;
            
            $array = array ();
            
            try {
                $sqlFilter = "";
                if (!empty($project)) {
                    $sqlFilter = " WHERE open_tag.id IN (SELECT open_tag FROM project_open_tag WHERE project = '$project')";
                }

                if(self::default_lang($lang)=='es')
                {          
                    // Español como alternativa

                    $sql = "SELECT 
                                open_tag.id,
                                IFNULL(open_tag_lang.name, open_tag.name) as name,
                                open_tag.post as post
                            FROM open_tag
                            LEFT JOIN open_tag_lang
                                ON  open_tag_lang.id = open_tag.id
                                AND open_tag_lang.lang = :lang
                            $sqlFilter
                            ORDER BY `order` ASC
                            ";
                }
                else
                {
                    // Inglés como alternativa

                    $sql = "SELECT 
                                open_tag.id,
                                IFNULL(open_tag_lang.name, IFNULL(eng.name,open_tag.name)) as name,
                                open_tag.post as post
                            FROM open_tag
                            LEFT JOIN open_tag_lang
                                ON  open_tag_lang.id = open_tag.id
                                AND open_tag_lang.lang = :lang
                            LEFT JOIN open_tag_lang as eng
                                ON  eng.id = open_tag.id
                                AND eng.lang = 'en'
                            $sqlFilter
                            ORDER BY `order` ASC
                            ";
                }
                if (!empty($limit)) {
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>\LANG));
                $open_tags = $query->fetchAll();
                foreach ($open_tags as $cat) {
                    $array[$cat[0]] = $cat[1];
                    $array[post] = $cat[2];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ninguna categoria para guardar';
                //Text::get('validate-open_tag-empty');

            if (empty($this->project))
                $errors[] = 'No hay ningun proyecto al que asignar';
                //Text::get('validate-open_tag-noproject');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO project_open_tag (project, open_tag) VALUES(:project, :open_tag)";
                $values = array(':project'=>$this->project, ':open_tag'=>$this->id);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La categoria {$open_tag} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':open_tag'=>$this->id,
			);

			try {
                self::query("DELETE FROM project_open_tag WHERE open_tag = :open_tag AND project = :project", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' del proyecto ' . $this->project . ' ' . $e->getMessage();
                //Text::get('remove-open_tag-fail');
                return false;
			}
		}

	}
    
}