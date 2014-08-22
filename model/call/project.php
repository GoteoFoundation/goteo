<?php

namespace Goteo\Model\Call {

    use Goteo\Model;

    class Project extends \Goteo\Core\Model {

        public
            $id,
            $call;

        // limites por ronda:
        // $call->conf->limit1 para primera
        // $call->conf->limit2 para segunda
        //  'normal' = limite normal por proyecto definido (cantidad o % sobre mínimo)
        //  'unlimited' = sin límite (óptimo como límite técnico. Ver, Model\Project::called cuando establece `dropable`)
        //  'minimum' = límite y luego minimo (mínimo más restrictivo que límite)
        //  'none' = no hay riego


        /**
         * Get the projects assigned to a call
         * @param varcahr(50) $id  Call identifier
         * @return array of categories identifiers
         */
		public static function get ($call, $filters = array()) {
            $array = array ();
            try {

                $values = array(':call'=>$call);

                $sqlFilter = "";
                if (!empty($filters['category'])) {
                    $sqlFilter .= "LEFT JOIN project_category
                        ON project_category.project = call_project.project
                        AND project_category.category = :filter";
                    $values[':filter'] = $filters['category'];
                }

                $and = "WHERE";
                if (!isset($filters['all'])) {
                    $sqlFilter .= " $and (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{5,40}') )";
                    $and = "AND";
                    $sql_draft ="project.id REGEXP '[0-9a-f]{5,40}' as draft,";
                }
                if (isset($filters['published'])) {
                    $sqlFilter .= " $and project.status >= 3";
                    $and = "AND";
                }


                $sql = "SELECT
                            project.id as id,
                            project.name as name,
                            project.status as status,
                            project.owner as owner,
                            project.amount as amount,
                            project.mincost as mincost,
                            project.maxcost as maxcost,
                            project.project_location as location,
                            project.subtitle as subtitle,
                            project.description as description,
                            $sql_draft
                            IF(project.passed IS NULL, 1, 2) as round
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
                    if (empty($item->amount_users)) {
                        $item->amount_users = Model\Invest::invested($item->id, 'users', $call);
                    }
                    // de la convocatoria
                    if (empty($item->amount_call)) {
                        $item->amount_call = Model\Invest::invested($item->id, 'call', $call);
                    }

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
                    $errors[] = "$sql <pre>".print_r($values, true)."</pre>";
                }
			} catch(\PDOException $e) {
				$errors[] = "La proyecto {$project} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}

		}

		/**
		 * Quitar un proyecto de la convocatoria
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

                    // actualizar numero de proyectos

                    return true;
                } else {
                    $errors[] = "$sql <pre>".print_r($values, true)."</pre>";
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

                $sql = "
                  SELECT
                    call.id as id,
                    call.name as name,
                    call.owner as owner,
                    call.lang as lang,
                    user.name as user_name,
                    user.email as user_email,
                    user.avatar as user_avatar,
                    user.lang as user_lang,
                    user.node as user_node
                  FROM `call`
                  INNER JOIN call_project
                    ON call.id = call_project.call
                  INNER JOIN user
                    ON user.id = call.owner
                  WHERE call_project.project = :project
                  LIMIT 1
                  ";
                // metemos los datos del convocatoria en la instancia
                $query = self::query($sql, array(':project'=>$project));
                if ($call = $query->fetchObject('\Goteo\Model\Call')) {

                    // owner
                    $user = new User;
                    $user->name = $call->user_name;
                    $user->email = $call->user_email;
                    $user->lang = $call->user_lang;
                    $user->node = $call->user_node;
                    $user->avatar = Image::get($call->user_avatar);

                    $call->user = $user;

                    return $call;

                } else {
                    return null;
                }

            } catch(\PDOException $e) {
                return null;
            }
		}

        /**
         * Devuelve la convocatoria de la que puede obtener riego
         *
         * @param varchar50 $project proyecto
         * @param object $thisCall instancia de convocatoria
         * @param int $thisGot riego conseguido
         * @return Model\Call $call convocatoria
         */
        public static function called ($project, $thisCall = null, $thisGot = null) {

            try {
                if ($thisCall instanceof Model\Call) {
                    $called = $thisCall->id;
                } else {
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
                }

                if (!empty ($called)) {
                    $call = (!isset($thisCall) || !$thisCall instanceof Model\Call) ? Model\Call::get($called) : $thisCall;

                    // configuración para esta ronda
                    $call->conf = ($project->round > 0) ? $call->getConf('limit'.$project->round) : 'none';

                    // calcular el obtenido por este proyecto, si no lo tenemos
                    $call->project_got = (!isset($thisGot)) ? Model\Invest::invested($project->id, 'call', $call->id) : $thisGot;

                    // limite por proyecto
                    if (empty($call->maxproj)) {
                        $call->maxproj = floor($project->maxcost / 2);
                    } elseif ($call->modemaxp == 'per') {
                        // recalculo de maxproj si es modalidad porcentaje
                        $call->rawmaxproj = $call->maxproj = $project->mincost * $call->maxproj / 100;
                    } else {
                        // limite bruto
                        $call->rawmaxproj = $call->maxproj;
                    }

                    // si no tiene configuracion
                    if (!isset($call->conf)) {
                        // lo que ya ha conseguido más la mitad de lo que le faltaría para llegar al óptimo (la otra mitad la pone el usuario)
                        $call->maxproj = min($call->maxproj, ($call->project_got + floor(($project->maxcost - $project->invested) / 2)));
                    }
                    // si la config para esta ronda la config. es el límite normal
                    elseif($call->conf == 'normal') {
                        $call->maxproj = $call->rawmaxproj;
                    }
                    // si tiene configuración de que en esta ronda el mínimo es más prioritario que el límite
                    elseif ($call->conf == 'minimum') {
                        // lo que ya ha conseguido más la mitad de lo que le faltaría para llegar al mínimo (la otra mitad la pone el usuario)
                        $call->maxproj = min($call->maxproj, ($call->project_got + floor(($project->mincost - $project->invested) / 2)));
                    }
                    // si tiene configurado ilimitado, el límite por proyecto SUBE!!!
                    elseif ($call->conf == 'unlimited') {
                        // lo que ya ha conseguido más la mitad de lo que le faltaría para llegar al óptimo (la otra mitad la pone el usuario)
                        $call->maxproj = $call->project_got + floor(($project->maxcost - $project->invested) / 2);
                    }

                    // y que no sea negativo
                    if ($call->maxproj < 0) $call->maxproj = 0;

                    // es regable a menos que la configuración no lo permita para esta ronda
                    // y siempre que no haya superado el óptimo
                    $call->dropable = true;
                    if (isset($call->conf) && $call->conf == 'none') {
                        $call->dropable = false;
                        $call->maxproj = 0;
                    }

                    // por defecto no permite en segunda ronda
                    if (!isset($call->conf) && $project->round == 2) {
                        $call->dropable = false;
                        $call->maxproj = 0;
                    }

                    // si está limitado a cubrir costes, no puede regarse más
                    if ($call->conf == 'minimum' && $project->invested >= $project->maxcost) {
                        $call->dropable = false;
                        $call->maxproj = 0;
                    }

                    // si no está en campaña ni de coña puede obtener riego
                    if ($project->status != 3) {
                        $call->dropable = false;
                        $call->maxproj = 0;
                    }

                    return $call;
                }

            } catch(\PDOException $e) {
                return null;
            }

            return null;
        }

        /*
         * Método para calcular cuanto puede generar este aporte concreto
         *
         * @param type $called
         * @param type $amount
         */
        public static function currMaxdrop ($project, $amount = 0) {

            $call = $project->called;

            // si está limitado a cubrir costes, no puede regarse más
            if ($call->conf == 'minimum' && $project->invested >= $project->maxcost) {
                return 0;
            }

            // si el proyecto no está en una convocatoria o ya no se puede regar
            if (!isset($call) || !$call instanceof Model\Call || !$call->dropable)
                return 0;

             if (isset($call->conf) && $call->conf == 'none')
                return 0;

            // si establecido un máximo por aporte
            $maxdrop = (!empty($call->maxdrop)) ? $call->maxdrop : 99999999;

            // si no tiene configuración el óptimo es límite técnico
            if(!isset($call->conf)) {
                $maxdrop = min($maxdrop, ($project->maxcost - $project->invested - $amount));
                if (isset($call->maxproj)) {
                    // y que no sea mayor al límite por proyecto si tiene límite por proyecto, ese es
                    $maxdrop = min($maxdrop, $call->maxproj);
                }
            }
            // si la config para esta ronda la config. es el límite normal
            elseif($call->conf == 'normal') {
                if (isset($call->maxproj)) {
                    // y que no sea mayor al límite por proyecto si tiene límite por proyecto, ese es
                    $maxdrop = min($maxdrop, $call->maxproj);
                }
            }
            // una vez aplicado el límite normal, aplicamos el limite sobre mínimo
            elseif ($call->conf == 'minimum') {
                $maxdrop = min($maxdrop, ($project->mincost - $project->invested - $amount));
                if (isset($call->maxproj)) {
                    // y que no sea mayor al límite por proyecto si tiene límite por proyecto, ese es
                    $maxdrop = min($maxdrop, $call->maxproj);
                }
            }
            // si la configuración de de ilimitado cámbia completamente, el límite SUBE!!
            elseif($call->conf == 'unlimited') {
                if(!empty($amount)) {
                    $maxdrop = min($maxdrop, ($project->maxcost - $project->invested - $amount));
                } else {
                    $maxdrop = min($maxdrop, (floor(($project->maxcost - $project->invested) /2)));
                }
                // independiente del límite por proyecto
            }


            // y no supere lo que le queda por conseguir
            $maxdrop = min($maxdrop, ($call->maxproj - $call->project_got));

            // siempre mientras le quede riego
            $maxdrop = min($maxdrop, $call->rest);

            // y no queremos que riege negativo, sacamos el menor de todos los límites o cero
            if ($maxdrop < 0) $maxdrop = 0;

            return $maxdrop;
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

        /*
         * Numero de proyectos publicados en una convocatoria
         */
        public static function numProjects ($call) {

            $debug = false;

            $values = array(':call' => $call);

            $sql = "SELECT  COUNT(*) as projects, call.num_projects as num
                FROM    `call`
                INNER JOIN call_project
                    ON call_project.call = call.id
                INNER JOIN project
                    ON call_project.project = project.id
                WHERE   call.id = :call
                ";

            if ($debug) {
                echo \trace($values);
                echo $sql;
                die;
            }

            $query = static::query($sql, $values);
            if($got = $query->fetchObject()) {
                // si ha cambiado, actualiza el numero de inversores en proyecto
                if ($got->projects != $got->num) {
                    $values['num'] = (int) $got->projects;
                    static::query("UPDATE `call` SET num_projects = :num  WHERE id = :call", $values);
                }
            }

            return (int) $got->messengers;
        }

    }

}
