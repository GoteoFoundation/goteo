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
		public static function get ($call, $filter =  null, $all = false) {
            $array = array ();
            try {

                $values = array(':call'=>$call);

                $sqlFilter = "";
                if (!empty($filter)) {
                    $sqlFilter .= "LEFT JOIN project_category
                        ON project_category.project = call_project.project
                        AND project_category.category = :filter";
                    $values[':filter'] = $filter;
                }

                if (!$all) {
                    $sqlFilter .= " WHERE (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{5,40}') )";
                }

                $sql = "SELECT
                            project.id as id,
                            project.name as name,
                            project.status as status,
                            project.owner as owner,
                            project.project_location as location,
                            project.subtitle as subtitle,
                            project.description as description
                        FROM project
                        INNER JOIN call_project
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

                    $item->user = Model\User::getMini($item->owner);
                    
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
         * Devuelve la convocatoria a la que está asignado (mini) 
         *
         * @param varchar50 $project proyecto
         * @return object $call convocatoria
         */
        public static function miniCalled ($project) {
            try {
                $sql = "SELECT
                            call_project.call as id
                        FROM call_project
                        WHERE  call_project.project = :project
                        LIMIT 1
                        ";

                $query = static::query($sql, array(':project'=>$project));
                $called = $query->fetchColumn();
                if (!empty ($called)) {
                    $call = Model\Call::getMini($called);

                    return $call;
                }

            } catch(\PDOException $e) {
                return null;
            }
		}

        /**
         * Devuelve la convocatoria de la que puede obtener riego
         *
         * @param varchar50 $project proyecto
         * @return Model\Call $call convocatoria
         */
        public static function called ($project) {

            // fallo directo: si no está en uno de los estados, si no está en primera ronda, si ha llegado al óptimo
            if (!in_array($project->status, array(1, 2, 3))
                || $project->round > 1
                || $project->invested >= $project->maxcost) {
                return false;
            }

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

                $query = static::query($sql, array(':project'=>$project->id));
                $called = $query->fetchColumn();
                if (!empty ($called)) {
                    $call = Model\Call::get($called);

                    // recalculo de maxproj si es modalidad porcentaje
                    if (empty($project->mincost)) {
                        $call->maxproj = false;
                    } elseif (!empty($call->maxproj) && $call->modemaxp == 'per') {
                        $call->maxproj = $project->mincost * $call->maxproj / 100;
                    }

                    // calcular el obtenido por este proyecto
                    $call->project_got = Model\Invest::invested($project->id, 'call', $call->id);

                    // calcular cuanto puede obtener por un aporte
                    $call->curr_maxdrop = 99999999; // limite actual base
                    // si establecido un máximo por aporte
                    if (!empty($call->maxdrop)) {
                        $call->curr_maxdrop = $call->maxdrop;
                    }
                    
                    // * si establecido un máximo por proyecto y lo que la diferencia es menos que el limite actual
                    if (!empty($call->maxproj)) {
                        $new_maxdrop = $call->maxproj - $call->project_got;
                        if ($new_maxdrop < $call->curr_maxdrop)
                            $call->curr_maxdrop = $new_maxdrop;
                    }

                    // * si lo que le falta para el óptimo es menos que el limite actual
                    $new_maxdrop = $project->maxcost - $project->invested;
                    if ($new_maxdrop < $call->curr_maxdrop)
                        $call->curr_maxdrop = $new_maxdrop;

                    // * si a la convocatoria le queda menos que el limite actual
                    if ($call->rest < $call->curr_maxdrop)
                        $call->curr_maxdrop = $call->rest;
                    

                    return $call;
                }

            } catch(\PDOException $e) {
                return null;
            }

            return false;
        }

        /*
         * Devuelve true o false si este proyecto está seleccionado en alguna de las convocatorias del usuario
         */
        public static function is_assigned ($user, $project) {
            $sql = "
                SELECT project
                FROM call_project
                WHERE `call` IN (
                    SELECT id FROM `call`WHERE owner = :user
                )
                AND project = :project";
            $values = array(
                ':user' => $user,
                ':project' => $project
            );
            $query = static::query($sql, $values);
            $legal = $query->fetchObject();
            if ($legal->project == $project) {
                return true;
            } else {
                return false;
            }
        }
    
    }
    
}