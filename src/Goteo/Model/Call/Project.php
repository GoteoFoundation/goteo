<?php

namespace Goteo\Model\Call {

    use \Goteo\Model,
        Goteo\Application\Lang,
        Goteo\Application\Config;


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

                $lang = Lang::current();

                $values = array(':call'=>$call);

                $sqlFilter = "";
                if (!empty($filters['category'])) {
                    $sqlFilter .= "LEFT JOIN project_category
                        ON project_category.project = call_project.project
                        AND project_category.category = :filter";
                    $values[':filter'] = $filters['category'];
                }

                $and = "WHERE";

                if($filters['all']) $filters['status'] = 'all';
                if($filters['published']) $filters['status'] = 'published';

                $statuses = array();
                $all = [Model\Project::STATUS_DRAFT, Model\Project::STATUS_REJECTED, Model\Project::STATUS_EDITING, Model\Project::STATUS_REVIEWING, Model\Project::STATUS_IN_CAMPAIGN, Model\Project::STATUS_FUNDED, Model\Project::STATUS_FULFILLED, Model\Project::STATUS_UNFUNDED];
                if($filters['status'] == 'published') {
                        $statuses = [Model\Project::STATUS_IN_CAMPAIGN, Model\Project::STATUS_FUNDED, Model\Project::STATUS_FULFILLED, Model\Project::STATUS_UNFUNDED];
                }
                elseif($filters['status'] == 'all') {
                        $statuses = $all;
                } elseif($filters['status']) {
                    $ss = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
                    foreach($ss as $s) {
                        if(in_array($s, $all)) {
                            $statuses[] = $s;
                        }
                    }
                }

                if (empty($statuses)) {
                    $sqlFilter .= " $and (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{32}') )";
                    $and = "AND";
                } else {
                    $sqlFilter .= " $and (project.status IN (" . implode(",", $statuses) . ") )";
                }


                // metemos los datos del proyecto en la instancia
                list($fields, $joins) = self::getLangsSQLJoins($lang, null, null, 'Goteo\Model\Project');


                $sql = "SELECT
                    project.id,
                    project.name,
                    $fields,
                    project.lang,
                    project.currency,
                    project.currency_rate,
                    project.status,
                    project.translate,
                    project.progress,
                    project.owner,
                    project.node,
                    project.amount,
                    project.mincost,
                    project.maxcost,
                    project.days,
                    project.num_investors,
                    project.popularity,
                    project.num_messengers,
                    project.num_posts,
                    project.created,
                    project.updated,
                    project.published,
                    project.success,
                    project.closed,
                    project.passed,
                    project.contract_name,
                    project.contract_nif,
                    project.phone,
                    project.contract_email,
                    project.address,
                    project.zipcode,
                    project.location,
                    project.country,
                    project.image,
                    project.video_usubs,
                    project.category,
                    project.media_usubs,
                    project.currently,
                    project.project_location,
                    project.scope,
                    project.resource,
                    project.comment,
                    project.contract_entity,
                    project.entity_office,
                    project.entity_name,
                    project.entity_cif,
                    project.post_address,
                    project.secondary_address,
                    project.post_zipcode,
                    project.post_location,
                    project.post_country,
                    project.amount_users,
                    project.amount_call,
                    project.maxproj,
                    project.analytics_id,
                    project.facebook_pixel,
                    project.social_commitment,
                    project.execution_plan,
                    project.execution_plan_url,
                    project.sustainability_model,
                    project.sustainability_model_url,
                    project.id REGEXP '[0-9a-f]{32}' as draft,
                    IFNULL(project.updated, project.created) as updated,
                            user.id as user_id,
                            user.name as user_name,
                            user.gender as user_gender,
                            project_conf.noinvest as noinvest,
                            project_conf.one_round as one_round,
                            project_conf.days_round1 as days_round1,
                            project_conf.days_round2 as days_round2
                        FROM project
                        INNER JOIN user
                            ON user.id = project.owner
                        LEFT JOIN project_conf
                            ON project_conf.project = project.id
                        $joins
                        INNER JOIN call_project
                            ON  call_project.project = project.id
                            AND call_project.call = :call
                        $sqlFilter
                        GROUP BY project.id
                        ORDER BY project.name ASC
                        ";

                // echo \sqldbg($sql, $values);
                $query = static::query($sql, $values);

                foreach ($query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Project') as $proj) {
                    $project=\Goteo\Model\Project::getWidget($proj);
                    // cuanto han recaudado
                    // de los usuarios
                    if (!isset($project->amount_users)) {
                        $project->amount_users = Model\Invest::invested($proj->id, 'users', $call);
                    }

                    if (!isset($project->amount_call)) {
                        $project->amount_call = Model\Invest::invested($proj->id, 'call', $call);
                    }
                    $projects[] = $project;
                }

                return $projects;

            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get the projects assigned to a call
         * @param varcahr(50) $id  Call identifier
         * @return array of categories identifiers
         */
        public static function getMini ($call, $filters = array()) {
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
                if($filters['all']) $filters['status'] = 'all';
                if($filters['published']) $filters['status'] = 'published';

                $statuses = array();
                $all = [Model\Project::STATUS_DRAFT, Model\Project::STATUS_REJECTED, Model\Project::STATUS_EDITING, Model\Project::STATUS_REVIEWING, Model\Project::STATUS_IN_CAMPAIGN, Model\Project::STATUS_FUNDED, Model\Project::STATUS_FULFILLED, Model\Project::STATUS_UNFUNDED];
                if($filters['status'] == 'published') {
                        $statuses = [Model\Project::STATUS_IN_CAMPAIGN, Model\Project::STATUS_FUNDED, Model\Project::STATUS_FULFILLED, Model\Project::STATUS_UNFUNDED];
                }
                elseif($filters['status'] == 'all') {
                        $statuses = $all;
                } elseif($filters['status']) {
                    $ss = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
                    foreach($ss as $s) {
                        if(in_array($s, $all)) {
                            $statuses[] = $s;
                        }
                    }
                }

                if (empty($statuses)) {
                    $sqlFilter .= " $and (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{32}') )";
                    $and = "AND";
                } else {
                    $sqlFilter .= " $and (project.status IN (" . implode(",", $statuses) . ") )";
                }

                $sql = "SELECT
                            project.id as id,
                            project.name as name,
                            project.status as status,
                            project.amount as amount,
                            project.amount_users as amount_users,
                            project.amount_call as amount_call
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
                    WHERE (status > 1  OR (status = 1 AND id NOT REGEXP '[0-9a-f]{32}') )
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
				$errors[] = "La proyecto {$this->name} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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
                   self::numProjects($this->call);

                    return true;
                } else {
                    $errors[] = 'No se ha podido quitar el proyecto ' . $this->id . ' de la convocatoria ' . $this->call . ' ';
                    return false;
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
        public static function calledMini ($project) {
            try {

                $sql = "
                  SELECT
                    `call`.*,
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
                    $user = new Model\User;
                    $user->name = $call->user_name;
                    $user->email = $call->user_email;
                    $user->lang = $call->user_lang;
                    $user->node = $call->user_node;
                    $user->avatar = Model\Image::get($call->user_avatar);

                    $call->user = $user;

                    // riego comprometido
                    if (!isset($call->used)) {
                        $call->used = $call->getUsed();
                    }

                    // riego restante
                    if (!isset($call->rest)) {
                        $call->rest = $call->getRest($call->used);
                    }

                    // proyectos asignados
                    if (!isset($call->applied)) {
                        $call->applied = $call->getApplied();
                    }


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
         *  le añade el valor de si genera riego o no (dropeable)
         *  y de cual es el límite para este proyecto: original (rawmaxproj) y en este momento (maxproj)
         *
         * @param varchar50 $project proyecto
         * @param object $thisCall instancia de convocatoria
         * @return Model\Call $call convocatoria
         *
         *    Valores del proyecto necesarios
         *
         * · $project->round (si tenemos la ronda en la que está el proyecto no puede definir configuración por ronda)
         * · $project->mincost (importe mínimo del proyecto)
         * · $project->maxcost (importe máximo del proyecto)
         * · $project->amount_call (riego ya conseguido )
         * · $project->invested (total conseguido por el proyecto, para ver si ha alcanzado mínimo/optimo )
         *
         *
         *   Valores de convocatoria necesarios
         *
         * · $call->maxproj
         * · $call->modemaxp
         *
         *
         *   Valores de convocatoria calculados
         *
         * · $call->conf ( configuración de límite para esta ronda )
         * · $call->rawmaxproj ( maximo original )
         * · $call->maxproj ( maximo en este momento )
         * · $call->dropable  ( si puede conseguir riego )
         *
         *
         */
        public static function setDropable ($project, $thisCall = null) {

            try {
                if (!$thisCall instanceof Model\Call) {
                    $sql = "SELECT
                                call.*
                            FROM `call`
                            INNER JOIN call_project
                                ON call.id = call_project.call

                            WHERE  call_project.project = :project
                            LIMIT 1
                            ";

                    $query = static::query($sql, array(':project'=>$project->id));
                    $call = $query->fetchObject('\Goteo\Model\Call');
                } else {
                    $call = $thisCall;
                }

                // configuración para esta ronda
                $call->conf = ($project->round > 0) ? $call->getConf('limit'.$project->round) : 'none';

                $call->unique_user_drop=$call->getConf('unique_user_drop');

                // calcular el obtenido por este proyecto, si no lo tenemos
                if (!isset($project->amount_call))
                    $project->amount_call = Model\Invest::invested($project->id, 'call', $call->id);

                // máximo inicial para el proyecto (según configuración económica /admin/calls/dropconf )
                if (empty($call->maxproj)) {
                    // si no hay valor de máximo, el máximo es específico para este proyecto (la mitad del óptimo)
                    $call->rawmaxproj = $call->maxproj = floor($project->maxcost / 2);

                } elseif ($call->modemaxp == 'per') {
                    // si es modalidad porcentaje, el máximo es específico para este proyecto  (porcentaje sobre mínimo)
                    $call->rawmaxproj = $call->maxproj = $project->mincost * $call->maxproj / 100;

                } else {
                    // si la modalidad es importe, el máximo es genérico
                    $call->rawmaxproj = $call->maxproj;

                }


                // Casos en los que no se riega :
                // por defecto (sin configuración) no riega en segunda ronda
                if (!isset($call->conf) && $project->round == 2) {
                    $call->dropable = false;
                    $call->maxproj = 0;
                    return $call;
                }

                // si está configurado que no se riega en esta ronda
                if (isset($call->conf) && $call->conf == 'none') {
                    $call->dropable = false;
                    $call->maxproj = 0;
                    return $call;
                }

                // si está limitado a cubrir costes, no puede regarse más
                if ($call->conf == 'minimum' && $project->invested >= $project->maxcost) {
                    $call->dropable = false;
                    $call->maxproj = 0;
                    return $call;
                }

                // if unlimited and the project has reached the optimum not match
                if ($call->conf == 'unlimited' && $project->invested >= $project->maxcost) {
                    $call->dropable = false;
                    $call->maxproj = 0;
                    return $call;
                }

                // si el proyecto no está en campaña ni de coña puede obtener riego
                if ($project->status != 3) {
                    $call->dropable = false;
                    $call->maxproj = 0;
                    return $call;
                }

                // el resto de casos se riegan
                $call->dropable = true;


                // si no tiene configuracion, comportamiento por defecto
                if (!isset($call->conf)) {
                    // lo que ya ha conseguido más la mitad de lo que le faltaría para llegar al óptimo (la otra mitad la pone el usuario)
                    $call->maxproj = min($call->maxproj, ($project->amount_call + floor(($project->maxcost - $project->invested) / 2)));

                } elseif($call->conf == 'normal') {
                    // si la config para esta ronda la config. es el máximo original
                    $call->maxproj = $call->rawmaxproj; // realmente no es necesario ya que $call->rawmaxproj = $call->maxproj

                } elseif ($call->conf == 'minimum') {
                    // si tiene configuración de que en esta ronda el mínimo es más prioritario que el límite
                    // lo que ya ha conseguido más la mitad de lo que le faltaría para llegar al mínimo (la otra mitad la pone el usuario)
                    $call->maxproj = min($call->maxproj, ($project->amount_call + floor(($project->mincost - $project->invested) / 2)));

                } elseif ($call->conf == 'unlimited') {
                    // si tiene configurado ilimitado, el límite por proyecto podría aumentar
                    // lo que ya ha conseguido más la mitad de lo que le faltaría para llegar al óptimo (la otra mitad la pone el usuario)
                    $call->maxproj = $project->amount_call + floor(($project->maxcost - $project->invested) / 2);

                }

                // y que no sea negativo
                if ($call->maxproj < 0) $call->maxproj = 0;

                return $call;

            } catch(\PDOException $e) {

                // @FIXME aviso de excepción en el calculo de si un proyecto puede conseguir riego y cuanto
                return null;
            }

        }



        /*
         * Método para calcular cuanto riego puede generar el aporte
         *
         * la cantidad puede estar definida ( /controller/invest ) o no ( página aportar de proyecto )
         *
         * Necesita $call->conf $call->dropable y el $call->maxproj definido por Call\Project::setDropable en Project::get
         *
         *
         * @param type $called
         * @param type $amount ( lo que aporta el usuario )
         */
        public static function getMaxdrop ($project, $amount = 0) {

            $call = $project->called;
            // el proyecto ya no se puede regar si:
            // si el proyecto no está en una convocatoria o esa convocatoria ya no riega
            if (!isset($call) || !$call instanceof Model\Call || !$call->dropable)
                return 0;

            // si le falta alguno de los valores requeridos
            if (!isset($call->conf) || !isset($call->dropable) || !isset($call->maxproj)) {
                $call = static::setDropable($project, $call);
            }

            // si la configuración de límite no permite riego, no puede generar nada
            if (isset($call->conf) && $call->conf == 'none')
                return 0;

            // si está limitado a cubrir costes y el proyecto ha superado el mínimo
            if ($call->conf == 'minimum' && $project->invested >= $project->mincost) {
                return 0;
            }


            // si establecido un máximo por aporte
            $maxdrop = (!empty($call->maxdrop)) ? $call->maxdrop : 99999999;

            // si no tiene configuración, como máximo podrá llegar hasta el óptimo (contando con lo que ponga el usuario)
            if(!isset($call->conf)) {
                $maxdrop = min($maxdrop, ($project->maxcost - $project->invested - $amount));

                // y que no sea mayor al límite por proyecto si tiene límite por proyecto, ese es
                if (isset($call->maxproj)) {
                    $maxdrop = min($maxdrop, $call->maxproj);
                }

            } elseif($call->conf == 'normal') {
                // si la config para esta ronda la config. es el máximo definido

            } elseif ($call->conf == 'minimum') {
                // si la configuración es de cubrir costes, segun si sabemos la cantidad que aporta o no
                if(!empty($amount)) {
                    // lo que le falta para llegar al mínimo menos lo que ponga el usuario
                    $maxdrop = min($maxdrop, ($project->mincost - $project->invested - $amount));
                } else {
                    // hasta la mitad de lo que le falta para llegar al mínimo
                    $maxdrop = min($maxdrop, (floor(($project->mincost - $project->invested) /2)));
                }

            } elseif($call->conf == 'unlimited') {
                // si la configuración es de ilimitado, segun si sabemos la cantidad que aporta o no
                if(!empty($amount)) {
                    // lo que le falta para llegar al óptimo menos lo que ponga el usuario
                    $maxdrop = min($maxdrop, ($project->maxcost - $project->invested - $amount));
                } else {
                    // hasta la mitad de lo que le falta para llegar al óptimo
                    $maxdrop = min($maxdrop, (floor(($project->maxcost - $project->invested) /2)));
                }

            }


            // que no supere el máximo por proyecto
            $maxdrop = min($maxdrop, $call->maxproj);

            // If fullunlimite the limit is only the max invest set by conf
            if($call->conf == 'fullunlimited')
                $maxdrop=$call->maxdrop;

            //if unlimited, not take into account the original max
            elseif($call->conf != 'unlimited')
                // y no supere lo que puede llegar a conseguir de la convocatoria (máximo original menos lo ya conseguido)
                $maxdrop = min($maxdrop, ($call->rawmaxproj - $project->amount_call));

            // que no supere lo que le queda por repartir a la convocatoria
            $maxdrop = min($maxdrop, $call->rest);

            // y no queremos que riege negativo
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
                echo \sqldbg($sql, $values);
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

            return (int) $got->projects;
        }

        /*
         * Numero de proyectos publicados en una convocatoria
         */
        public static function numRunningProjects ($call) {

            $debug = false;

            $values = array(':call' => $call);

            $sql = "SELECT  COUNT(*) as projects, call.running_projects as num
                FROM    `call`
                INNER JOIN call_project
                    ON call_project.call = call.id
                INNER JOIN project
                    ON call_project.project = project.id
                    AND project.status = 3
                WHERE   call.id = :call
                ";

            if ($debug) {
                echo \sqldbg($sql, $values);
                die;
            }

            $query = static::query($sql, $values);
            if($got = $query->fetchObject()) {
                // si ha cambiado, actualiza el numero de inversores en proyecto
                if ($got->projects != $got->num) {
                    $values['num'] = (int) $got->projects;
                    static::query("UPDATE `call` SET running_projects = :num  WHERE id = :call", $values);
                }
            }

            return (int) $got->messengers;
        }

        /*
         * Numero de proyectos publicados en una convocatoria
         */
        public static function numSuccessProjects ($call) {

            $debug = false;

            $values = array(':call' => $call);

            $sql = "SELECT  COUNT(*) as projects, call.success_projects as num
                FROM    `call`
                INNER JOIN call_project
                    ON call_project.call = call.id
                INNER JOIN project
                    ON call_project.project = project.id
                    AND (project.amount >= project.mincost)
                    AND (project.amount>0)
                WHERE   call.id = :call
                ";

            if ($debug) {
                echo \sqldbg($sql, $values);
                die;
            }

            $query = static::query($sql, $values);
            if($got = $query->fetchObject()) {
                // si ha cambiado, actualiza el numero de inversores en proyecto
                if ($got->projects != $got->num) {
                    $values['num'] = (int) $got->projects;
                    static::query("UPDATE `call` SET success_projects = :num  WHERE id = :call", $values);
                }
            }

            return (int) $got->projects;
        }

        /*
         * Añade un proyecto aplicado
         */
        public static function addOneApplied ($call, $applied = null) {

            $debug = false;

            if (isset($applied) && !empty($applied)) {
                $applied++;
            } else {
                $sql = "SELECT
                            COUNT(project.id) as cuantos,
                            `call`.id as id,
                            `call`.applied as num
                        FROM `call`
                        INNER JOIN call_project
                            ON  call_project.call = call.id
                        INNER JOIN project
                            ON project.id = call_project.project
                            AND (
                                  project.status > 1
                                  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{32}')
                              )
                        WHERE call.id = :call
                        ";

                $query = static::query($sql, array(':call'=>$call));
                $applied = $query->fetchColumn(0);
            }

            $sql = "UPDATE `call` SET applied = :num  WHERE id = :call";
            $values = array(':call' => $call, ':num' => $applied);

            static::query($sql , $values);

            if ($debug) {
                echo \sqldbg($sql , $values);
                die;
            }

            return true;
        }

        /* Project gender stats */

        public static function genderStats($projects)
        {
            $tot_male = 0;
            $tot_female = 0;
            $tot_gender= 0;

            if(is_array($projects)) {
                foreach($projects as $project)
                {
                    if($project->user->gender=="M")
                    {
                        $tot_male++;
                        $tot_gender++;
                    }
                    elseif($project->user->gender=="F")
                    {
                        $tot_female++;
                        $tot_gender++;
                    }
                }
            }

            if($tot_gender)
            {
                $percent_male=round(($tot_male/$tot_gender)*100);
                $percent_female=round(($tot_female/$tot_gender)*100);
            }

            else
            {
                $percent_male=0;
                $percent_female=0;
            }

            return ['percent_male' => $percent_male, 'percent_female' => $percent_female ];

        }


    }

}
