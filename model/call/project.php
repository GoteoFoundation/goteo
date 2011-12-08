<?php

namespace Goteo\Model\Call {

    class Project extends \Goteo\Core\Model {

        public
            $id,
            $call;


        /**
         * Get the categories for a call
         * @param varcahr(50) $id  Call identifier
         * @return array of categories identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT project FROM call_project WHERE `call` = :call", array(':call'=>$id));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all categories available
         *
         * @param void
         * @return array
         */
		public static function getAll () {
            $array = array ();
            try {
                $sql = "
                    SELECT
                        project.id as id,
                        IFNULL(project_lang.name, project.name) as name
                    FROM    project
                    LEFT JOIN project_lang
                        ON  project_lang.id = project.id
                        AND project_lang.lang = :lang
                    ORDER BY name ASC
                    ";

                $query = static::query($sql, array(':lang'=>\LANG));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all categories for this call by name
         *
         * @param void
         * @return array
         */
		public static function getNames ($call = null, $limit = null) {
            $array = array ();
            try {
                $sqlFilter = "";
                if (!empty($call)) {
                    $sqlFilter = " WHERE project.id IN (SELECT project FROM call_project WHERE `call` = '$call')";
                }

                $sql = "SELECT 
                            project.id,
                            IFNULL(project_lang.name, project.name) as name
                        FROM project
                        LEFT JOIN project_lang
                            ON  project_lang.id = project.id
                            AND project_lang.lang = :lang
                        $sqlFilter
                        ORDER BY `order` ASC
                        ";
                if (!empty($limit)) {
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>\LANG));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ninguna proyecto para guardar';
                //Text::get('validate-project-empty');

            if (empty($this->call))
                $errors[] = 'No hay ningun proyecto al que asignar';
                //Text::get('validate-project-nocall');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO call_project (`call`, project) VALUES(:call, :project)";
                $values = array(':call'=>$this->call, ':project'=>$this->id);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La proyecto {$project} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $call id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':call'=>$this->call,
				':project'=>$this->id,
			);

			try {
                self::query("DELETE FROM call_project WHERE project = :project AND `call` = :call", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el proyecto ' . $this->id . ' de la convocatoria ' . $this->call . ' ' . $e->getMessage();
                //Text::get('remove-project-fail');
                return false;
			}
		}

	}
    
}