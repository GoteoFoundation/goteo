<?php

namespace Goteo\Model\Call {

    class Project extends \Goteo\Core\Model {

        public
            $id,
            $call;


        /**
         * Get the projects assigned to a call
         * @param varcahr(50) $id  Call identifier
         * @return array of categories identifiers
         */
        //@TODO añadir cuanto dinero llevan de esta convocatoria
		public static function get ($call) {
            $array = array ();
            try {
                $sql = "SELECT
                            project.id,
                            project.name as name,
                            project.status as status
                        FROM project
                        JOIN call_project
                            ON  call_project.project = project.id
                            AND call_project.call = :call
                        ORDER BY project.name ASC
                        ";
                $query = static::query($sql, array(':call'=>$call));
                $items = $query->fetchAll(\PDO::FETCH_OBJ);
                foreach ($items as $item) {
                    $array[$item->id] = $item;
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all projects available
         *
         * @param void
         * @return array
         */
		public static function getAll ($call = null) {
            $array = array ();
            $values = array();
            try {
                $sql = "
                    SELECT
                        project.id as id,
                        project.name as name,
                        project.status as status
                    FROM project
                    WHERE project.status > 0";

                if (!empty($call)) {
                    $sql .= " AND project.id NOT IN (SELECT project FROM call_project WHERE `call` = :call)";
                    $values[':call'] = $call;
                }

                $sql .= " ORDER BY name ASC
                    ";

                $query = static::query($sql, $values);
                $items = $query->fetchAll(\PDO::FETCH_OBJ);
                foreach ($items as $item) {
                    $array[$item->id] = $item;
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
				if (self::query($sql, $values)) {
    				return true;
                } else {
                    $errors[] = "$sql <pre>".print_r($values, 1)."</pre>";
                }
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
                $sql = "DELETE FROM call_project WHERE project = :project AND `call` = :call";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "$sql <pre>".print_r($values, 1)."</pre>";
                }
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el proyecto ' . $this->id . ' de la convocatoria ' . $this->call . ' ' . $e->getMessage();
                //Text::get('remove-project-fail');
                return false;
			}
		}

	}
    
}