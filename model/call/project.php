<?php

namespace Goteo\Model\Call {

    use Goteo\Model;

    class Project extends \Goteo\Core\Model {

        public
            $id,
            $call;


        /**
         * Get the projects assigned to a call
         * @param varcahr(50) $id  Call identifier
         * @return array of categories identifiers
         */
		public static function get ($call, $filter =  null) {
            $array = array ();
            try {

                $values = array(':call'=>$call);

                if (!empty($filter)) {
                    $sqlFilter = "INNER JOIN project_category
                        ON project_category.project = call_project.project
                        AND project_category.category = :filter";
                    $values[':filter'] = $filter;
                } else {
                    $sqlFilter = "";
                }

                $sql = "SELECT
                            project.id as id,
                            project.name as name,
                            project.status as status,
                            project.project_location as location,
                            project.subtitle as subtitle,
                            project.description as description
                        FROM project
                        JOIN call_project
                            ON  call_project.project = project.id
                            AND call_project.call = :call
                        $sqlFilter
                        GROUP BY project.id
                        ORDER BY project.name ASC
                        ";
                
                $query = static::query($sql, $values);
                $items = $query->fetchAll(\PDO::FETCH_OBJ);

                foreach ($items as $item) {
                    // cuanto han recaudado
                    // de los usuarios
                    $item->amount_users = Model\Invest::invested($item->id, 'users');
                    // de la convocatoria
                    $item->amount_call = Model\Invest::invested($item->id, 'call', $call);

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
         * Los que corresponden a los criterios de la convocatoria (como en discover/call) pero tambien si estan en edicion
         * @FIXME no tiene en cuenta la localidad por la problematica de varias localidades y ámbito
         *
         * Que no tenga en cuenta los proyectos numeraco
         *
         * @param void
         * @return array
         */
		public static function getAvailable ($call) {
            $array = array ();
            $values = array(':call' => $call);
            
            try {
                $sql = "
                    SELECT
                        project.id as id,
                        project.name as name,
                        project.status as status,
                        project.project_location as location
                    FROM project
                    WHERE (status > 1  OR (status = 1 AND id NOT REGEXP '[0-9a-f]{5,40}') )
                    AND project.status < 4
                    AND project.id IN (
                                        SELECT distinct(project)
                                        FROM project_category
                                        WHERE category IN (
                                                SELECT distinct(category)
                                                FROM call_category
                                                WHERE `call` = :call
                                            )
                                    )
                    AND project.id IN (
                                        SELECT DISTINCT(project)
                                        FROM reward
                                        WHERE icon IN (
                                                SELECT distinct(icon)
                                                FROM call_icon
                                                WHERE `call` = :call
                                            )
                                    )
                    AND project.id NOT IN (SELECT project FROM call_project WHERE `call` = :call)
                    ORDER BY name ASC";

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

        /**
         * Devuelve la convocatoria de la que puede obtener riego
         *
         * @param varchar50 $project proyecto
         * @return Model\Call $call convocatoria
         */
        public static function called ($project, $mincost) {

            try {
                $sql = "SELECT
                            call_project.call as id
                        FROM call_project
                        INNER JOIN `call`
                            ON call_project.call = call.id
                        WHERE  call_project.project = :project
                        AND call.status > 3 AND call.status < 6
                        LIMIT 1
                        ";
//                die(str_replace(':project', "'$project'", $sql));

                $query = static::query($sql, array(':project'=>$project));
                $called = $query->fetchColumn();
                if (!empty ($called)) {
                    $call = Model\Call::get($called);

                    // recalculo de maxproj si es modalidad porcentaje
                    if (empty($mincost)) {
                        $call->maxproj = false;
                    } elseif (!empty($call->maxproj) && $call->modemaxp == 'per') {
                        $call->maxproj = $mincost * $call->maxproj / 100;
                    }

                    return $call;
                }

            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }

            return false;
        }

	}
    
}