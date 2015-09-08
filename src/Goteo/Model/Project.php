<?php

namespace Goteo\Model {

    use Goteo\Core\ACL,
        Goteo\Application\Exception,
        Goteo\Application\Config,
        Goteo\Application\Session,
        Goteo\Application\Lang,
        Goteo\Model\Mail,
        Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Library\Feed,
        Goteo\Library\Currency
        ;

    class Project extends \Goteo\Core\Model {

        // PROJECT STATUS IDs
        const STATUS_DRAFT       = -1; // TODO: is this really necessary?
        const STATUS_REJECTED    = 0;
        const STATUS_EDITING     = 1; // en negociación
        const STATUS_REVIEWING   = 2; //
        const STATUS_IN_CAMPAIGN = 3;
        const STATUS_FUNDED      = 4;
        const STATUS_FULFILLED   = 5; // 'Caso de exito'
        const STATUS_UNFUNDED    = 6; // proyecto fallido

        public
            $id = null,
            $draft, // indica si el id es un md5 [0-9a-f]{32}
            $dontsave = false,
            $owner, // User who created it
            $node, // Node this project belongs to
            $nodeData, // Node data
            $status,   // Project status
            $progress, // puntuation %
            $amount, // Current donated amount

            $user, // owner's user information

            // Register contract data
            $contract_name, // Nombre y apellidos del responsable del proyecto
            $contract_nif, // Guardar sin espacios ni puntos ni guiones
            $contract_email, // cuenta paypal
            $phone, // guardar sin espacios ni puntos

            // Para marcar física o jurídica
            $contract_entity = false, // false = física (persona)  true = jurídica (entidad)

            // Para persona física
            $contract_birthdate,

            // Para entidad jurídica
            $entity_office, // cargo del responsable dentro de la entidad
            $entity_name,  // denomincion social de la entidad
            $entity_cif,  // CIF de la entidad

            // Campos de Domicilio: Igual para persona o entidad
            $address,
            $zipcode,
            $location, // owner's location
            $country,

            // Domicilio postal
            $secondary_address = false, // si es diferente al domicilio fiscal
            $post_address = null,
            $post_zipcode = null,
            $post_location = null,
            $post_country = null,


            // Edit project description
            $name,
            $subtitle,
            $lang = 'es',
            $currency = 'EUR',
            $currency_rate = 1,
            $image,
            $gallery = array(), // array de instancias image de project_image
            $secGallery = array(), // array de instancias image de project_image (secundarias)
            $all_galleries = array(), // array de instancias image de project_image (secundarias)
            $description,
             $motivation,
              $video,   // video de motivacion
               $video_usubs,   // universal subtitles para el video de motivacion
             $about,
             $goal,
             $related,
             $spread, //campo para que expliquen la difusión prevista del proyecto
             $reward, // nueva sección, solo editable por admines y traductores
            $categories = array(),
            $media, // video principal
             $media_usubs, // universal subtitles para el video principal
            $keywords, // por ahora se guarda en texto tal cual
            $currently, // Current development status of the project
            $project_location, // project execution location
            $scope,  // ambito de alcance

            $translate,  // si se puede traducir (bool)

            // costs
            $costs = array(),  // project\cost instances with type
            $schedule, // picture of the costs schedule
            $resource, // other current resources

            // Rewards
            $social_rewards = array(), // instances of project\reward for the public (collective type)
            $individual_rewards = array(), // instances of project\reward for investors  (individual type)

            // Collaborations
            $supports = array(), // instances of project\support

            // Comment
            $comment, // Comentario para los admin introducido por el usuario

            //Operative purpose properties
            $mincost = 0,
            $maxcost = 0,

            //Obtenido, Días, Cofinanciadores
            $invested = 0, //cantidad de inversión
            $days = 0, //para PRIMERA_RONDA días desde la publicación o para SEGUNDA_RONDA días si no está caducado
            $investors = array(), // aportes individuales a este proyecto
            $num_investors = 0, // numero de usuarios que han aportado

            $round = 0, // para ver si ya está en la segunda fase
            $passed = null, // para ver si hemos hecho los eventos de paso a segunda ronda
            $willpass = null, // fecha final de primera ronda

            $errors = array(), // para los fallos en los datos
            $okeys  = array(), // para los campos que estan ok

            // para puntuacion
            $score = 0, //puntos
            $max = 0, // maximo de puntos

            $messages = array(), // mensajes de los usuarios hilos con hijos

            $finishable = false, // llega al progresso mínimo para enviar a revision

            $tagmark = null,  // banderolo a mostrar


            $noinvest = 0,
            $watch = 0,
            $days_round1 = 40,
            $days_round2 = 40,
            $one_round = 0,
            $help_cost = 0,
            $help_license= 0,
            $called = null // si está en una convocatoria


        ;


        /**
         * Sobrecarga de métodos 'getter'.
         *
         * @param type string $name
         * @return type mixed
         */
        public function __get ($name) {
            if($name == "call") {
	            return Call\Project::calledMini($this->id);
	        }
            if($name == "allowpp") {
                return Project\Account::getAllowpp($this->id);
            }
            if($name == "budget") {
                $cost = new stdClass;
                $cost->mincost = $this->mincost;
                $cost->maxcost = $this->maxcost;
                //calcular si esta vacio
                if(empty($cost->mincost)) {
                    $cost = self::calcCosts($this->id);
                }
	            return $cost;
	        }
            return $this->$name;
        }


        /**
         * Returns an array of project languages
         * @param  [type] $project_id [description]
         * @return [type]             [description]
         */
        public function getLangs() {
            $sql = 'SELECT lang FROM project WHERE id = :id
                    UNION
                SELECT lang FROM project_lang WHERE id = :id
                ORDER BY lang ASC';

            $query = static::query($sql, array(':id' => $this->id));
            $langs = array();
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $lang) {
                $langs[$lang->lang] = Lang::getName($lang->lang);
            }
            return $langs;
        }

        /**
         * Check if the project is can be seen by the user id
         * @param  Goteo\Model\User $user  the user to check (if empty checks )
         * @return boolean          true if success, false otherwise
         */
        public function userCanView(User $user = null) {

            // already published:
            if($this->status >= self::STATUS_IN_CAMPAIGN) return true;
            if(empty($user)) return false;
            // owns the project
            if($this->owner === $user->id) return true;
            // is admin in the project node
            if($user->hasRoleInNode($this->node, ['admin', 'superadmin', 'root'])) return true;
            // is reviewer
            if($user->hasRoleInNode($this->node, ['checker']) && User\Review::is_assigned($user->id, $this->id)) return true;
            // is caller
            if($user->hasRoleInNode($this->node, ['caller']) && Call\Project::is_assigned($user->id, $this->id)) return true;

            return false;
        }

        /**
         * Check if the project is editable by the user id
         * @param  Goteo\Model\User $user  the user to check
         * @return boolean          true if success, false otherwise
         */
        public function userCanEdit(User $user = null) {
            if(empty($user)) return false;
            // owns the project
            if($this->owner === $user->id) return true;
            // is superadmin in the project node
            if($user->hasRoleInNode($this->node, ['superadmin', 'root'])) return true;
            // is a consultant
            if($user->hasRoleInNode($this->node, ['consultant', 'admin']) && array_key_exists($user->id, $this->getConsultants())) return true;
            // is reviewer ... ¡no! ?? TODO: check this...
            // if($user->hasRoleInNode($this->node, ['checker']) && User\Review::is_assigned($user->id, $this->id)) return true;

            return false;
        }

        /**
         * Check if the project is removable by the user id
         * @param  Goteo\Model\User $user  the user to check
         * @return boolean          true if success, false otherwise
         */
        public function userCanDelete(User $user = null) {
            if(empty($user)) return false;
            if(!in_array($this->status, array(self::STATUS_DRAFT, self::STATUS_REJECTED, self::STATUS_EDITING))) return false;
            // owns the project
            if($this->owner === $user->id) return true;
            // is superadmin in the project node
            if($user->hasRoleInNode($this->node, ['superadmin', 'root'])) return true;

            return false;
        }

        /**
         * Check if the project can be published and other sensitive actions
         * @param  Goteo\Model\User $user  the user to check
         * @return boolean          true if success, false otherwise
         */
        public function userCanModerate(User $user = null) {
            if(empty($user)) return false;

            // is superadmin in the project node
            if($user->hasRoleInNode($this->node, ['superadmin', 'root'])) return true;
            // is a consultant
            if($user->hasRoleInNode($this->node, ['consultant', 'admin']) && array_key_exists($user->id, $this->getConsultants())) return true;

            return false;
        }

        /**
         * Check if the user has the rol "manager" in the project
         * @param  Goteo\Model\User $user  the user to check
         * @return boolean          true if success, false otherwise
         */
        public function userCanManage(User $user = null) {
            if(empty($user)) return false;

            // is manager or superadmin in the project node
            if($user->hasRoleInNode($this->node, ['manager', 'superadmin', 'root'])) return true;

            return false;
        }

        /**
         * Check if the project is administrable by the user id
         * Meaning touchgin sensitive data such as bank account, etc
         * @param  Goteo\Model\User $user  the user to check
         * @return boolean          true if success, false otherwise
         */
        public function userCanAdmin(User $user = null, $include_admins = false) {
            if(empty($user)) return false;

            $roles = ['superadmin', 'root'];
            if($include_admins) $roles[] = 'admin';
            // is superadmin in the project node
            if($user->hasRoleInNode($this->node, $roles)) return true;

            return false;
        }

        /**
         * Inserta un proyecto con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create ($node = null, &$errors = array()) {
            if(empty($node)) $node = Config::get('node');
            $user = $this->owner;

            if (empty($user)) {
                $errors[] = 'No user owner assigned';
                return false;
            }

            // si aplicando a convocatoria, asignar el proyecto al nodo del convocador
            if (isset($_SESSION['oncreate_applyto'])) {
                $call = $_SESSION['oncreate_applyto'];
                $callData = Call::getMini($call);
                 if (!empty($callData->user->node)) {
                     $node = $callData->user->node;
                     // también movemos al impulsor a ese nodo
                    self::query("UPDATE user SET node = :node WHERE id = :id", array(':node'=>$node, ':id'=>$user));
                 }
            }

            // cogemos el número de proyecto de este usuario
            $query = self::query("SELECT COUNT(id) as num FROM project WHERE owner = ?", array($user));
            if ($now = $query->fetchObject())
                $num = $now->num + 1;
            else
                $num = 1;

            // datos del usuario que van por defecto: name->contract_name,  location->location
            $userProfile = User::get($user);
            // datos del userpersonal por defecto a los cammpos del paso 2
            $userPersonal = User::getPersonal($user);

            $values = array(
                ':id'   => md5($user.'-'.$num),
                ':name' => "El nuevo proyecto de {$userProfile->name}",
                ':lang' => !empty($_SESSION['lang']) ? $_SESSION['lang'] : 'es',
                ':currency' => 'EUR',
                ':currency_rate' => 1,
                ':status'   => 1,
                ':progress' => 0,
                ':owner' => $user,
                ':node' => $node,
                ':amount' => 0,
                ':days' => 0,
                ':created'  => date('Y-m-d'),
                ':contract_name' => ($userPersonal->contract_name) ?
                                    $userPersonal->contract_name :
                                    $userProfile->name,
                ':contract_nif' => $userPersonal->contract_nif,
                ':phone' => $userPersonal->phone,
                ':address' => $userPersonal->address,
                ':zipcode' => $userPersonal->zipcode,
                ':location' => ($userPersonal->location) ?
                                $userPersonal->location :
                                $userProfile->location,
                ':country' => ($userPersonal->country) ?
                                $userPersonal->country :
                                Check::country(),
                ':project_location' => ($userPersonal->location) ?
                                $userPersonal->location :
                                $userProfile->location,
                );

            $campos = array();
            foreach (\array_keys($values) as $campo) {
                $campos[] = \str_replace(':', '', $campo);
            }

            $sql = "REPLACE INTO project (" . implode(',', $campos) . ")
                 VALUES (" . implode(',', \array_keys($values)) . ")";
            try {
				self::query($sql, $values);

                foreach ($campos as $campo) {
                    $this->$campo = $values[":$campo"];
                }

                return $this->id;

            } catch (\PDOException $e) {
                $errors[] = "ERROR al crear un nuevo proyecto<br />$sql<br /><pre>" . print_r($values, true) . "</pre>";
                \trace($this);
                // die($errors[0]);
                return false;
            }
        }

        /*
         *  Cargamos los datos del proyecto
         *  TODO: better exception throwing (namespaced)
        *   TODO: Project::get deberia retornar false por coherencia con los otros modelos
         */
        public static function get($id, $lang = null) {

            try {
				// metemos los datos del proyecto en la instancia
                $sql = "SELECT project.*,
                                project.id REGEXP '[0-9a-f]{32}' as draft,
                                node.name as node_name,
                                node.url as node_url,
                                node.label as node_label,
                                node.active as node_active,
                                project_conf.*,
                                user.name as user_name,
                                user.email as user_email,
                                user.avatar as user_avatar,
                                IFNULL(user_lang.about, user.about) as user_about,
                                user.location as user_location,
                                user.id as user_id,
                                user.twitter as user_twitter,
                                user.linkedin as user_linkedin,
                                user.identica as user_identica,
                                user.google as user_google,
                                user.facebook as user_facebook
                FROM project
				LEFT JOIN project_conf
				    ON project_conf.project = project.id
				LEFT JOIN node
				    ON node.id = project.node
                INNER JOIN user
                    ON user.id=project.owner
                LEFT JOIN user_lang
                    ON  user_lang.id = user.id
                    AND user_lang.lang = :lang
				WHERE project.id = :id
				";

                $values = array(':id'=>$id,':lang'=>$lang);
				$query = self::query($sql, $values);
				$project = $query->fetchObject(__CLASS__);

                if (!$project instanceof \Goteo\Model\Project) {
                    throw new Exception\ModelNotFoundException(Text::html('fatal-error-project'));
                }

                // si nos estan pidiendo el idioma original no traducimos nada, damos lo que sacamos de
                if(!empty($lang) && $lang!=$project->lang)
                {
                    //Obtenemos el idioma de soporte segun si está traducido  a ese idioma o no
                    $trans_lang=self::default_lang_by_id($id, 'project_lang', $lang);

                    $sql = "
                        SELECT
                            IFNULL(project_lang.description, project.description) as description,
                            IFNULL(project_lang.motivation, project.motivation) as motivation,
                            IFNULL(project_lang.video, project.video) as video,
                            IFNULL(project_lang.about, project.about) as about,
                            IFNULL(project_lang.goal, project.goal) as goal,
                            IFNULL(project_lang.related, project.related) as related,
                            IFNULL(project_lang.reward, project.reward) as reward,
                            IFNULL(project_lang.keywords, project.keywords) as keywords,
                            IFNULL(project_lang.media, project.media) as media,
                            IFNULL(project_lang.subtitle, project.subtitle) as subtitle
                        FROM project
                        LEFT JOIN project_lang
                            ON  project_lang.id = project.id
                            AND project_lang.lang = :lang
                        WHERE project.id = :id
                        ";
                    // no veo que haga falta cambiar el idioma a la instancia del proyecto
                    //, IFNULL(project_lang.lang, project.lang) as lang
                    $query = self::query($sql, array(':id'=>$id, ':lang'=>$trans_lang));

                    foreach ($query->fetch(\PDO::FETCH_ASSOC) as $field=>$value) {
                        $project->$field = $value;
                    }
                }

                // datos del nodo
                $project->nodeData = new Node;
                $project->nodeData->id = $project->node;
                $project->nodeData->name = $project->node_name;
                $project->nodeData->url = $project->node_url;
                $project->nodeData->active = $project->node_active;

                // label
                $project->nodeData->label = Image::get($project->node_label);

                 //para diferenciar el único nodo de los canales
                if($project->node=='barcelona')
                    $project->nodeData->type='node';

                if (isset($project->media)) {
                    $project->media = new Project\Media($project->media);
                }
                if (isset($project->video)) {
                    $project->video = new Project\Media($project->video);
                }

                // owner

                $project->user = new User;
                $project->user->id = $project->user_id;
                $project->user->name = $project->user_name;
                $project->user->email = $project->user_email;
                $project->user->lang = $project->user_lang;
                $project->user->about = $project->user_about;
                $project->user->location = $project->user_location;

                $project->user->avatar = Image::get($project->user_avatar);

                $project->user->webs = User\Web::get($project->user_id);

                //
                $project->user->twitter = $project->user_twitter;
                $project->user->facebook = $project->user_facebook;
                $project->user->linkedin = $project->user_linkedin;
                $project->user->identica = $project->user_identica;
                $project->user->google = $project->user_google;

                $project->user->facebook = $project->user_facebook;

                //all galleries
                $project->all_galleries = Project\Image::getGalleries($project->id);
                //Main gallery
                $project->gallery = $project->all_galleries[''];
                $project->secGallery = $project->all_galleries;

                // image from main gallery
                if (empty($project->image)) {
                    $project->image = Project\Image::setImage($project->id, $project->gallery);
                }
                else {
                    $project->image = Image::get($project->image);
                }

                // categorias
                $project->categories = Project\Category::get($id);


                // @FIXME #42 : para contenidos adicionales (cost, reward, support) se está suponiendo erroneamente que el contenido original es español
                // no se está teniendo en cuenta el idioma original del proyecto
                // @TODO :
                //        o pasamos el idioma original a estos getAll y modificamos el código
                //        o modificamos registro _lang para idioma original  al modificarse estos contenidos (no arregla casos ya existentes)

                // si se está solicitando el mismo idioma del proyecto, queremos que estos getAll nos den el contenido original
                // para eso hacemos $lang = null ya que luego ya no se usa mas esta variable
                if ($lang == $project->lang) {
                    $lang = null;
                }

                // costes y los sumammos
				$project->costs = Project\Cost::getAll($id, $lang);
                $project->minmax();

				// retornos colectivos
				$project->social_rewards = Project\Reward::getAll($id, 'social', $lang);
				// retornos individuales
				$project->individual_rewards = Project\Reward::getAll($id, 'individual', $lang);

				// colaboraciones
				$project->supports = Project\Support::getAll($id, $lang);

                // Fin contenidos adicionales


                // extra conf
                if (empty($project->days_round1)) $project->days_round1 = 40;
                if (empty($project->days_round2)) $project->days_round2 = 40;

                $project->days_total = ($project->one_round) ? $project->days_round1 : ( $project->days_round1 + $project->days_round2 );

                //-----------------------------------------------------------------
                // Diferentes verificaciones segun el estado del proyecto
                //-----------------------------------------------------------------
                $project->investors = Invest::investors($id);

                if($project->status >= 3 && empty($project->amount)) {
                    $project->amount = Invest::invested($id);
                }
                $project->invested = $project->amount; // compatibilidad, ->invested no debe usarse


                // campos calculados para los números del menu

                //consultamos y actualizamos el numero de inversores
                if($project->status >= 3 && $project->amount > 0 && !isset($project->num_investors)) {
                    $project->num_investors = Invest::numInvestors($id);
                }

                //mensajes y mensajeros
                // solo cargamos mensajes en la vista mensajes
                if ($project->status >= 3 && !isset($project->num_messengers)) {
                    $project->num_messengers = Message::numMessengers($id);
                }

                // novedades
                // solo cargamos blog en la vista novedades
                if ($project->status >= 3 && !isset($project->num_posts)) {
                    $project->num_posts =  Blog\Post::numPosts($id);
                }


                // calculos de días y banderolos
                $project->setDays();
                $project->setTagmark();

                // fecha final primera ronda (fecha campaña + PRIMERA_RONDA)
                if (!empty($project->published)) {
                    $ptime = strtotime($project->published);
                    $project->willpass = date('Y-m-d', \mktime(0, 0, 0, date('m', $ptime), date('d', $ptime)+$project->days_round1, date('Y', $ptime)));
                    $project->willfinish = date('Y-m-d', \mktime(0, 0, 0, date('m', $ptime), date('d', $ptime)+$project->days_total, date('Y', $ptime)));
                }

                // podría estar asignado a alguna convocatoria
                $call = Call\Project::calledMini($project->id);
                if ( $call instanceof Call ) {

                    // cuanto han recaudado
                    // de los usuarios
                    if (!isset($project->amount_users)) {
                        $project->amount_users = Invest::invested($project->id, 'users', $call->id);
                    }
                    // de la convocatoria
                    if (!isset($project->amount_call)) {
                        $project->amount_call = Invest::invested($project->id, 'call', $call->id);
                    }

                    $call = Call\Project::setDropable($project, $call);
                    $project->called = $call;

                } else {

                    $project->called = null;

                }

                // recomendaciones de padrinos
                $project->patrons = Patron::getRecos($project->id);


                //-----------------------------------------------------------------
                // Fin de verificaciones
                //-----------------------------------------------------------------

				return $project;

			} catch(\PDOException $e) {
				throw new Exception\ModelException($e->getMessage());
			}
		}

        /*
         *  Cargamos los datos mínimos de un proyecto: id, name, owner, comment, lang, status, user
         */
        public static function getMini($id) {

            try {
				// metemos los datos del proyecto en la instancia
				$query = self::query("SELECT
                                        project.id as id,
                                        project.name as name,
                                        project.owner as owner,
                                        project.comment as comment,
                                        project.lang as lang,
                                        project.status as status,
                                        project.node as node,
                                        project.image as image,
                                        user.id as user_id,
                                        user.name as user_name,
                                        user.avatar as user_avatar,
                                        user.email as user_email,
                                        IFNULL(user.lang, 'es') as user_lang,
                                        user.node as user_node
                                      FROM project
                                      LEFT JOIN user
                                      ON user.id=project.owner
                                      WHERE project.id = ?", array($id));
				$project = $query->fetchObject(__CLASS__);

                if (!$project instanceof \Goteo\Model\Project) {
                    throw new Exception\ModelNotFoundException(Text::html('fatal-error-project'));
                }

                // primero, que no lo grabe
                $project->dontsave = true;

                // owner
                $project->user=new User;
                $project->user->id=$project->user_id;
                $project->user->name=$project->user_name;
                $project->user->email=$project->user_email;
                $project->user->lang=$project->user_lang;
                $project->user->node=$project->user_node;

                $project->user->avatar = Image::get($project->user_avatar);
                $project->image = Image::get($project->image);

                // convocado
                $call = Call\Project::calledMini($project->id);

                if ( $call instanceof Call ) {

                    // cuanto han recaudado
                    // de los usuarios
                    if (!isset($project->amount_users)) {
                        $project->amount_users = Invest::invested($project->id, 'users', $call->id);
                    }
                    // de la convocatoria
                    if (!isset($project->amount_call)) {
                        $project->amount_call = Invest::invested($project->id, 'call', $call->id);
                    }

                    $project->called = $call;

                } else {

                    $project->called = null;

                }

                return $project;

			} catch(\PDOException $e) {
				throw new Exception\ModelException($e->getMessage());
			}
		}

        /*
         *  Cargamos los datos suficientes para pintar un widget de proyecto
         */
        public static function getMedium($id, $lang = null) {
            if(empty($lang)) $lang = Lang::current();
            try {

                $sql ="
                SELECT
                    project.id as id,
                    project.id as project,
                    project.name as name,
                    project.description as description,
                    project.status as status,
                    project.created as created,
                    project.updated as updated,
                    project.published as published,
                    project.success as success,
                    project.closed as closed,
                    project.passed as passed,
                    project.mincost as mincost,
                    project.maxcost as maxcost,
                    project.amount as amount,
                    project.image as image,
                    project.num_investors as num_investors,
                    project.num_messengers as num_messengers,
                    project.num_posts as num_posts,
                    project.days as days,
                    project.owner as owner,
                    user.id as user_id,
                    user.name as user_name,
                    user.email as user_email,
                    user.avatar as user_avatar,
                    user.lang as user_lang,
                    project_conf.noinvest as noinvest,
                    project_conf.one_round as one_round,
                    project_conf.days_round1 as days_round1,
                    project_conf.days_round2 as days_round2
                FROM  project
                INNER JOIN user
                    ON user.id = project.owner
                LEFT JOIN project_conf
                    ON project_conf.project = project.id
                WHERE project.id = :id";

				// metemos los datos del proyecto en la instancia
                $values = array(':id'=>$id);
				$query = self::query($sql, $values);
				$project = $query->fetchObject(__CLASS__);

                if (!$project instanceof \Goteo\Model\Project) {
                    throw new Exception\ModelNotFoundException(Text::html('fatal-error-project'));
                }

                $project->user=new User;
                $project->user->id=$project->user_id;
                $project->user->name=$project->user_name;
                $project->user->email=$project->user_email;
                $project->user->lang=$project->user_lang;
                $project->user->node=$project->user_node;

                $project->user->avatar = Image::get($project->user_avatar);
                $project->image = Image::get($project->image);

                // si recibimos lang y no es el idioma original del proyecto, ponemos la traducción y mantenemos para el resto de contenido
                if(!empty($lang) && $lang!=$project->lang) {

                    //Obtenemos el idioma de soporte
                    $lang=self::default_lang_by_id($id, 'project_lang', $lang);

                    $sql = "
                        SELECT
                            IFNULL(project_lang.description, project.description) as description,
                            IFNULL(project_lang.subtitle, project.subtitle) as subtitle
                        FROM project
                        LEFT JOIN project_lang
                            ON  project_lang.id = project.id
                            AND project_lang.lang = :lang
                        WHERE project.id = :id
                        ";
                    $query = self::query($sql, array(':id'=>$id, ':lang'=>$lang));
                    foreach ($query->fetch(\PDO::FETCH_ASSOC) as $field=>$value) {
                        $project->$field = $value;
                    }
                }


                // aquí usará getWidget para sacar todo esto
                $project = self::getWidget($project);

                // podría estar asignado a alguna convocatoria
                $call = Call\Project::calledMini($project->id);

                if ( $call instanceof Call ) {
                    $call = Call\Project::setDropable($project, $call);
                    $project->called = $call;
                } else {
                    $project->called = null;
                }

                // Y añadir el dontsave
                $project->dontsave = true;

                // datos del nodo
                // no se usa en el widget
                // if (!empty($project->node)) $project->nodeData = Node::getMini($project->node);

                return $project;

            } catch(\PDOException $e) {
				throw new Exception\ModelException($e->getMessage());
            }
        }

        /*
         *  Datos extra para un widget de proyectos
         */
        public static function getWidget(Project $project, $lang = null) {
            if(empty($lang)) $lang = Lang::current();
            $Widget = new Project();
            $Widget->id = (!empty($project->project)) ? $project->project : $project->id;
            $Widget->status = $project->status;
            $Widget->name = $project->name;
            $Widget->owner = $project->owner;
            $Widget->description = $project->description;
            $Widget->published = $project->published;
            $Widget->created = $project->created;
            $Widget->updated = $project->updated;
            $Widget->success = $project->success;
            $Widget->closed = $project->closed;

            // configuración de campaña
            // $project_conf = Project\Conf::get($Widget->id);  lo sacamos desde la consulta
            // no necesario: $Widget->watch = $project->watch;
            $Widget->noinvest = $project->noinvest;
            $Widget->days_round1 = (!empty($project->days_round1)) ? $project->days_round1 : 40;
            $Widget->days_round2 = (!empty($project->days_round2)) ? $project->days_round2 : 40;
            $Widget->one_round = $project->one_round;
            $Widget->days_total = ($project->one_round) ? $Widget->days_round1 : ($Widget->days_round1 + $Widget->days_round2);

            // image from main gallery
            $Widget->image = Image::get($project->image);

            $Widget->amount = $project->amount;
            $Widget->invested = $project->amount; // compatibilidad, ->invested no debe usarse
            $Widget->num_investors = $project->num_investors;

            // @TODO : hay que hacer campos calculados conn traducción para esto
            $Widget->cat_names = Project\Category::getNames($Widget->id, 2, $lang);
            $Widget->rewards = Project\Reward::getWidget($Widget->id);

            if(!empty($project->mincost) && !empty($project->maxcost)) {
                $Widget->mincost = $project->mincost;
                $Widget->maxcost = $project->maxcost;
            } else {
                $calc = Project::calcCosts($project->project);
                $Widget->mincost = $calc->mincost;
                $Widget->maxcost = $calc->maxcost;
            }
            $Widget->user = new User;
            $Widget->user->id = $project->user_id;
            $Widget->user->name = $project->user_name;
            $Widget->user->email = $project->user_email;
            $Widget->user->lang = $project->user_lang;

            // calcular dias sin consultar sql
            $Widget->days = $project->days;

            $Widget->setDays(); // esto hace una consulta para el número de días que le faltaan segun configuración
            $Widget->setTagmark(); // esto no hace consulta

            return $Widget;

        }

        /*
         * Listado simple de todos los proyectos de cierto nodo
         * @return: strings array
         */
        public static function getAll($node = null) {
            if(empty($node)) $node = Config::get('node');

            $list = array();

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name
                FROM    project
                WHERE project.node = :node
                ORDER BY project.name ASC
                ", array(':node' => $node));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /**
         * Get consultants for this project
         * @return array array of user id that are consultants
         */
        public function getConsultants() {
            if($this->consultants && is_array($this->consultants)) return $this->consultants;
            $this->consultants = self::getConsultantsForProject($this);
            return $this->consultants;
        }
        /*
         * Static version
         * Array asociativo de los asesores de un proyecto
         *  (o todos los que asesoran alguno, si no hay filtro)
         * @return: strings array
         */
        public static function getConsultantsForProject (Project $project) {

            $list = array();

            $sqlFilter = '';
            $values = array();
            if ($project) {
                $sqlFilter .= ' WHERE user_project.project = :project';
                $values[':project'] = $project->id;
            }

            $query = static::query("
                SELECT
                    DISTINCT(user_project.user) as consultant,
                    user.name as name
                FROM user_project
                INNER JOIN user
                    ON user.id = user_project.user
                $sqlFilter
                ORDER BY user.name ASC
                ", $values);

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->consultant] = $item->name;
            }
            // add default node consultant if empty
            if(empty($list) && $project) {
                $sql = 'SELECT
                        default_consultant AS consultant,
                        user.name as name
                    FROM node
                    INNER JOIN user
                        ON user.id = node.default_consultant
                    WHERE node.id = :node';
                $query = static::query($sql, [':node' => $project->node]);
                if ($item = $query->fetchObject()) {
                    $list[$item->consultant] = $item->name;
                }
            }

            // TODO: add default consultant from settings
            return $list;
        }


        /*
         * Asignar a un usuario como asesor de un proyecto
         * @return: boolean
         */
        public function assignConsultant ($user, &$errors = array()) {

            $values = array(':user'=>$user, ':project'=>$this->id);

            try {
                $sql = "REPLACE INTO user_project (`user`, `project`) VALUES(:user, :project)";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = 'No se ha creado el registro `user_project`';
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = 'No se ha podido asignar al usuario {$user} como asesor del proyecto {$this->id}.' . $e->getMessage();
                return false;
            }

        }

        /*
         * Quitarle a un usuario el asesoramiento de un proyecto
         * @return: boolean
         */
        public function unassignConsultant ($user, &$errors = array()) {
            $values = array (
                ':user'=>$user,
                ':project'=>$this->id,
            );

            try {
                if (self::query("DELETE FROM user_project WHERE `project` = :project AND `user` = :user", $values)) {
                    return true;
                } else {
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar al usuario {$user} del asesoramiento del proyecto {$this->id}. ' . $e->getMessage();
                return false;
            }
        }

        /*
         *  Para obtener el ratio de conversión original
         *  Este método se llama en save()
         *
         *  Solo tiene sentido si han seleccionado una divisa diferente a la de por defecto
         *
         */
        public function setCurrency() {

            if ($this->currency == Currency::DEFAULT_CURRENCY) {

                $this->currency_rate = 1;

            } elseif (empty($this->currency_rate) || $this->currency_rate == 1) {

                // solo grabamos ratio la primera vez
                $this->currency_rate = Currency::rate($this->currency);

            }

        }

        /*
         *  Para calcular la ronda de un proyecto y los dias restantes de campaña
         *  Este método se llama al instanciar un proyecto con get() o getMedium(), modificando sus atributos $round y $days
         */
        public function setDays() {

            if ($this->status == 3) { // En campaña
                // Tiempo de campaña (días desde la fecha de publicación del proyecto)
                $days = $this->days_active = date_interval($this->published);

                if ($days < $this->days_round1) { // En primera ronda
                    $this->round = 1;
                    $daysleft = $this->days_round1 - $days;
                } elseif ( !$this->one_round && $days >= $this->days_round1 && $days <= $this->days_total ) { // En segunda ronda
                    $this->round = 2;
                    $daysleft = $this->days_total - $days;
                } elseif ($days >= $this->days_total) { // Ha finalizado la campaña
                    $this->round = ($this->one_round) ? 1 : 2;
                    $daysleft = 0;
                } else {
                    $this->round = 0;
                    $daysleft = 0;
                }

                // no deberia estar en campaña sino financiado o caducado
                if ($daysleft < 0) $daysleft = 0;

            } else { // $this->status != 3
                $this->round = 0;
                $daysleft = 0;
            }

            if ($this->days != $daysleft) {
                self::query("UPDATE project SET days = '{$daysleft}' WHERE id = ?", array($this->id));
                $this->days = $daysleft;
            }
        }

         /*
         * Array asociativo de las agrupaciones (open_tags) de un proyecto
         *  (o todos los que asesoran alguno, si no hay filtro)
         * @return: strings array
         */
        public static function getOpen_Tags ($project = null) {

            $list = array();

            $sqlFilter = "";
            if (!empty($project)) {
                $sqlFilter .= " WHERE project_open_tag.project = '{$project}'";
            }


            $query = static::query("
                SELECT
                    DISTINCT(project_open_tag.open_tag) as open_tag,
                    open_tag.name as name
                FROM project_open_tag
                INNER JOIN open_tag
                    ON open_tag.id = project_open_tag.open_tag
                $sqlFilter
                ORDER BY open_tag.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->open_tag] = $item->name;
            }

            return $list;
        }


        /*
         * Asignar una agrupación a un proyecto
         * @return: boolean
         */
        public function assignOpen_tag ($open_tag, &$errors = array()) {

            $values = array(':open_tag'=>$open_tag, ':project'=>$this->id);

            try {
                $sql = "REPLACE INTO project_open_tag (`project`, `open_tag`) VALUES(:project, :open_tag)";
                if (self::query($sql, $values)) {

                    return true;
                } else {
                    $errors[] = 'No se ha creado el registro `project_open_tag`';
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = 'No se ha podido asignar la agrupacion {$open_tag} al proyecto {$this->id}.' . $e->getMessage();
                return false;
            }

        }

        /*
         * Quitar un tipo de agrupación a un proyecto
         * @return: boolean
         */
        public function unassignOpen_tag ($open_tag, &$errors = array()) {
            $values = array (
                ':open_tag'=>$open_tag,
                ':project'=>$this->id,
            );

            try {
                if (self::query("DELETE FROM project_open_tag WHERE `project` = :project AND `open_tag` = :open_tag", $values)) {
                    return true;
                } else {
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar la agrupación {$open_tag} al proyecto {$this->id}. ' . $e->getMessage();
                return false;
            }
        }

        /*
         *  Para ver que tagmark le toca
         */
        public function setTagmark() {
            // a ver que banderolo le toca
            // "financiado" al final de los SEGUNDA_RONDA dias
            if ($this->status == 4) :
                $this->tagmark = 'gotit';
            // "en marcha" cuando llega al optimo en primera o segunda ronda
            elseif ($this->status == 3 && $this->amount >= $this->maxcost) :
                $this->tagmark = 'onrun';
            // "en marcha" y "aun puedes" cuando está en la segunda ronda
            elseif ($this->status == 3 && $this->round == 2) :
                $this->tagmark = 'onrun-keepiton';
            // Obtiene el mínimo durante la primera ronda, "aun puedes seguir aportando"
            elseif ($this->status == 3 && $this->round == 1 && $this->amount >= $this->mincost ) :
                $this->tagmark = 'keepiton';
            // tag de exitoso cuando es retorno cumplido
            elseif ($this->status == 5) :
                $this->tagmark = 'success';
            // tag de caducado
            elseif ($this->status == 6) :
                $this->tagmark = 'fail';
            endif;
        }

        /*
         *  Para validar los campos del proyecto que son NOT NULL en la tabla
         * @return: boolean
         */
        public function validate(&$errors = array()) {

            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'El proyecto no tiene id';
                //Text::get('validate-project-noid');

            if (empty($this->lang))
                $this->lang = 'es';

            if (empty($this->currency))
                $this->lang = 'EUR';

            if (empty($this->status))
                $this->status = 1;

            if (empty($this->progress))
                $this->progress = 0;

            if (empty($this->owner))
                $errors[] = 'El proyecto no tiene usuario creador';
                //Text::get('validate-project-noowner');

            if (empty($this->node))
                $this->node = 'goteo';

            //cualquiera de estos errores hace fallar la validación
            return empty($errors);
        }

        /**
         * actualiza en la tabla los datos del proyecto
         * @param array $project->errors para guardar los errores de datos del formulario, los errores de proceso se guardan en $project->errors['process']
         */
        public function save (&$errors = array()) {
            if ($this->dontsave) { return false; }

            if(!$this->validate($errors)) { return false; }

  			try {
                // fail para pasar por todo antes de devolver false
                $fail = false;

                // los nif sin guiones, espacios ni puntos
                $this->contract_nif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->contract_nif);
                $this->entity_cif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->entity_cif);

                // Nueva imagen desde post, será un array de tipo _FILES[]
                if (is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);

                    if ($image->save($errors)) {
                        $this->images[] = $image;

                        /**
                         * Guarda la relación NM en la tabla 'project_image'.
                         */
                        if(!empty($image->id)) {
                            self::query("REPLACE project_image (project, image) VALUES (:project, :image)", array(':project' => $this->id, ':image' => $image->id));
                        }

                        // recalculamos las galerias e imagen

                        // getGallery en Project\Image  procesa todas las secciones
                        $galleries = Project\Image::getGalleries($this->id);
                        Project\Image::setImage($this->id, $galleries['']);

                    }
                    else {
                        // print_r($errors);
                        // Si hay errores al colgar una imagen, mostrar error correspondiente
                        $fail = true;
                    }
                }

                // lang, currency, currency_rate
                $this->setCurrency();


                $fields = array(
                    'contract_name',
                    'contract_nif',
                    'contract_email',
                    'contract_entity',
                    'contract_birthdate',
                    'entity_office',
                    'entity_name',
                    'entity_cif',
                    'phone',
                    'address',
                    'zipcode',
                    'location',
                    'country',
                    'secondary_address',
                    'post_address',
                    'post_zipcode',
                    'post_location',
                    'post_country',
                    'name',
                    'subtitle',
                    'lang',
                    'currency',
                    'currency_rate',
                    'description',
                    'motivation',
                    'video',
                    'video_usubs',
                    'about',
                    'goal',
                    'related',
                    'spread',
                    'reward',
                    'keywords',
                    'media',
                    'media_usubs',
                    'currently',
                    'project_location',
                    'scope',
                    'resource',
                    'comment'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$field;
                }

                // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
//				$set .= ", updated = :updated";
//				$values[':updated'] = date('Y-m-d');
				$values[':id'] = $this->id;

				$sql = "UPDATE project SET " . $set . " WHERE id = :id";
				if (!self::query($sql, $values)) {
                    $errors[] = $sql . '<pre>' . print_r($values, true) . '</pre>';
                    $fail = true;
                }

//                echo "$sql<br />";
                // y aquí todas las tablas relacionadas
                // cada una con sus save, sus new y sus remove
                // quitar las que tiene y no vienen
                // añadir las que vienen y no tiene

                // project_conf, solo si ha marcado one round
                // if ($this->one_round) {
                    $conf = Project\Conf::get($this->id);
                    $conf->one_round = $this->one_round;

                    //almacenamos si ha pedido ayuda marcando los checkbox help
                    $conf->help_cost = $this->help_cost;
                    $conf->help_license = $this->help_license;
                    $conf->save();
                // }

                //categorias
                $tiene = Project\Category::get($this->id);
                $viene = $this->categories;
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    $category = new Project\Category(
                        array(
                            'id'=>$item,
                            'project'=>$this->id)
                    );
                    if (!$category->remove($errors))
                        $fail = true;
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                // recuperamos las que le quedan si ha cambiado alguna
                if (!empty($quita) || !empty($guarda))
                    $this->categories = Project\Category::get($this->id);

                //costes
                $tiene = Project\Cost::getAll($this->id);
                $viene = $this->costs;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    $item->project_date = $this->created; // fecha de creación del proyecto para verificar fechas de la tarea
                    if (!$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        $viene[$key]->project_date = $this->created; // fecha de creación del proyecto para verificar fechas de la tarea
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
                    $this->costs = Project\Cost::getAll($this->id);

                // recalculo de minmax
                $this->minmax();

                //retornos colectivos
				$tiene = Project\Reward::getAll($this->id, 'social');
                $viene = $this->social_rewards;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
    				$this->social_rewards = Project\Reward::getAll($this->id, 'social');

                //recompenssas individuales
				$tiene = Project\Reward::getAll($this->id, 'individual');
                $viene = $this->individual_rewards;
                $quita = array_diff_key($tiene, $viene);
                $guarda = array_diff_key($viene, $tiene);
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
    				$this->individual_rewards = Project\Reward::getAll($this->id, 'individual');

				// colaboraciones
				$tiene = Project\Support::getAll($this->id);
                $viene = $this->supports;
                $quita = array_diff_key($tiene, $viene); // quitar los que tiene y no viene
                $guarda = array_diff_key($viene, $tiene); // añadir los que viene y no tiene
                foreach ($quita as $key=>$item) {
                    if (!$item->remove($errors)) {
                        $fail = true;
                    } else {
                        unset($tiene[$key]);
                    }
                }
                foreach ($guarda as $key=>$item) {
                    if (!$item->save($errors))
                        $fail = true;
                }
                /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo*/
                foreach ($tiene as $key => $row) {
                    // a ver la diferencia con el que viene
                    if ($row != $viene[$key]) {
                        if (!$viene[$key]->save($errors))
                            $fail = true;
                    }
                }

                if (!empty($quita) || !empty($guarda))
    				$this->supports = Project\Support::getAll($this->id);

                //listo
                return !$fail;

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el proyecto.' . $e->getMessage();
                //Text::get('save-project-fail');
                return false;
			}
        }

        /*
         * @return: boolean
         */
        public function saveLang (&$errors = array()) {

  			try {
                $fields = array(
                    'id'=>'id',
                    'lang'=>'lang_lang',
                    'subtitle'=>'subtitle_lang',
                    'description'=>'description_lang',
                    'motivation'=>'motivation_lang',
                    'video'=>'video_lang',
                    'about'=>'about_lang',
                    'goal'=>'goal_lang',
                    'related'=>'related_lang',
                    'reward'=>'reward_lang',
                    'keywords'=>'keywords_lang',
                    'media'=>'media_lang'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field=>$ffield) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    if (empty($this->$ffield)) {
                        $this->$ffield = null;
                    }
                    $values[":$field"] = $this->$ffield;
                }

				$sql = "REPLACE INTO project_lang SET " . $set;
				if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = $sql . '<pre>' . print_r($values, true) . '</pre>';
                    return false;
                }
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el proyecto.' . $e->getMessage();
                //Text::get('save-project-fail');
                return false;
			}

        }

        /*
         * comprueba errores de datos y actualiza la puntuación
         *
         * @param steps array : pasos del formulario
         *
         */
        public function check($steps = null) {

            $errors = &$this->errors;
            $okeys  = &$this->okeys ;

            // reseteamos la puntuación
            $this->setScore(0, 0, true);


            if (isset($steps) && isset($steps['userProfile'])) {
                /***************** Revisión de campos del paso 1, PERFIL *****************/
                $maxScore = 12;
                $score = 0;
                // obligatorios: nombre, email, ciudad
                if (empty($this->user->name)) {
                    $errors['userProfile']['name'] = Text::get('validate-user-field-name');
                } else {
                    $okeys['userProfile']['name'] = 'ok';
                    ++$score;
                }

                // se supone que tiene email porque sino no puede tener usuario, no?
                if (!empty($this->user->email)) {
                    ++$score;
                }

                if (empty($this->user->location)) {
                    $errors['userProfile']['location'] = Text::get('validate-user-field-location');
                } else {
                    $okeys['userProfile']['location'] = 'ok';
                    ++$score;
                }

                if(!empty($this->user->avatar) && $this->user->avatar->id != 1) {
                    $okeys['userProfile']['avatar'] = empty($errors['userProfile']['avatar']) ? 'ok' : null;
                    $score+=2;
                }

                if (!empty($this->user->about)) {
                    $okeys['userProfile']['about'] = 'ok';
                    ++$score;
                    // otro +1 si tiene más de 1000 caracteres (pero menos de 2000)
                    if (\strlen($this->user->about) > 1000 && \strlen($this->user->about) < 2000) {
                        ++$score;
                    }
                } else {
                    $errors['userProfile']['about'] = Text::get('validate-user-field-about');
                }

                if (!empty($this->user->interests)) {
                    $okeys['userProfile']['interests'] = 'ok';
                    ++$score;
                }

                if (empty($this->user->webs)) {
                    $errors['userProfile']['webs'] = Text::get('validate-project-userProfile-web');
                } else {
                    $okeys['userProfile']['webs'] = 'ok';
                    ++$score;
                    if (count($this->user->webs) > 2) ++$score;

                    $anyerror = false;
                    foreach ($this->user->webs as $web) {
                        if (trim(str_replace('http://','',$web->url)) == '') {
                            $anyerror = !$anyerror ?: true;
                            $errors['userProfile']['web-'.$web->id.'-url'] = Text::get('validate-user-field-web');
                        } else {
                            $okeys['userProfile']['web-'.$web->id.'-url'] = 'ok';
                        }
                    }

                    if ($anyerror) {
                        unset($okeys['userProfile']['webs']);
                        $errors['userProfile']['webs'] = Text::get('validate-project-userProfile-any_error');
                    }
                }

                if (!empty($this->user->facebook)) {
                    $okeys['userProfile']['facebook'] = 'ok';
                    ++$score;
                }

                if (!empty($this->user->twitter)) {
                    $okeys['userProfile']['twitter'] = 'ok';
                    ++$score;
                }

                if (!empty($this->user->linkedin)) {
                    $okeys['userProfile']['linkedin'] = 'ok';
                }

                //puntos
                $this->setScore($score, $maxScore);
                /***************** FIN Revisión del paso 1, PERFIL *****************/
            }

            if (isset($steps) && isset($steps['userPersonal'])) {
                /***************** Revisión de campos del paso 2,DATOS PERSONALES *****************/
                $maxScore = 6;
                $score = 0;
                // obligatorios: todos
                if (empty($this->contract_name)) {
                    $errors['userPersonal']['contract_name'] = Text::get('mandatory-project-field-contract_name');
                } else {
                     $okeys['userPersonal']['contract_name'] = 'ok';
                     ++$score;
                }

                if (empty($this->contract_nif)) {
                    $errors['userPersonal']['contract_nif'] = Text::get('mandatory-project-field-contract_nif');
                } elseif ( !Check::nif($this->contract_nif) ) {
                    $errors['userPersonal']['contract_nif'] = Text::get('validate-project-value-contract_nif');
                } else {
                    $okeys['userPersonal']['contract_nif'] = 'ok';
                    ++$score;
                }

                if (empty($this->contract_birthdate)) {
                    $errors['userPersonal']['contract_birthdate'] = Text::get('mandatory-project-field-contract_birthdate');
                } else {
                     $okeys['userPersonal']['contract_birthdate'] = 'ok';
                }

                if (empty($this->phone)) {
                    $errors['userPersonal']['phone'] = Text::get('mandatory-project-field-phone');
                } elseif (!Check::phone($this->phone)) {
                    $errors['userPersonal']['phone'] = Text::get('validate-project-value-phone');
                } else {
                     $okeys['userPersonal']['phone'] = 'ok';
                     ++$score;
                }


                $this->setScore($score, $maxScore);
                /***************** FIN Revisión del paso 2, DATOS PERSONALES *****************/
            }

            if (isset($steps) && isset($steps['overview'])) {
                /***************** Revisión de campos del paso 3, DESCRIPCION *****************/
                $maxScore = 13;
                $score = 0;
                // obligatorios: nombre, subtitulo, imagen, descripcion, about, motivation, categorias, video, localización
                if (empty($this->name)) {
                    $errors['overview']['name'] = Text::get('mandatory-project-field-name');
                } else {
                     $okeys['overview']['name'] = 'ok';
                     ++$score;
                }

                if (!empty($this->subtitle)) {
                     $okeys['overview']['subtitle'] = 'ok';
                }

                if (empty($this->description)) {
                    $errors['overview']['description'] = Text::get('mandatory-project-field-description');
                } elseif (!Check::words($this->description, 80)) {
                     $errors['overview']['description'] = Text::get('validate-project-field-description');
                } else {
                     $okeys['overview']['description'] = 'ok';
                     ++$score;
                }

                if (!empty($this->related)) {
                     $okeys['overview']['related'] = 'ok';
                     ++$score;
                }

                if (empty($this->categories)) {
                    $errors['overview']['categories'] = Text::get('mandatory-project-field-category');
                } else {
                     $okeys['overview']['categories'] = 'ok';
                     ++$score;
                }

                if (empty($this->media)) {
                    // solo error si no está aplicando a una convocatoria
                    if (!isset($this->called)) {
                        $errors['overview']['media'] = Text::get('mandatory-project-field-media');
                    }
                } else {
                     $okeys['overview']['media'] = 'ok';
                     $score+=3;
                }

                if (empty($this->project_location)) {
                    $errors['overview']['project_location'] = Text::get('mandatory-project-field-location');
                } else {
                     $okeys['overview']['project_location'] = 'ok';
                     ++$score;
                }

                if (!$this->draft)
                {
                    if (empty($this->about)) {
                        $errors['overview']['about'] = Text::get('mandatory-project-field-about');
                     } else {
                        $okeys['overview']['about'] = 'ok';
                        ++$score;
                    }

                     if (empty($this->motivation)) {
                    $errors['overview']['motivation'] = Text::get('mandatory-project-field-motivation');
                    } else {
                        $okeys['overview']['motivation'] = 'ok';
                        ++$score;
                    }

                    // paso 3b: imágenes
                    if (empty($this->gallery) && empty($errors['images']['image'])) {
                        $errors['images']['image'] .= Text::get('mandatory-project-field-image');
                    } else {
                        $okeys['images']['image'] = (empty($errors['images']['image'])) ? 'ok' : null;
                        ++$score;
                        if (count($this->gallery) >= 2) ++$score;
                    }

                    if (!empty($this->goal))  {
                        $okeys['overview']['goal'] = 'ok';
                        ++$score;
                    }

                } else {
                    // este paso, para los draft, tiene menos puntuación máxima
                    $maxScore = 8;
                }

                $this->setScore($score, $maxScore);
                /***************** FIN Revisión del paso 3, DESCRIPCION *****************/
            }

            if (isset($steps) && isset($steps['costs']) && (!$this->help_cost)) {
                /***************** Revisión de campos del paso 4, COSTES *****************/
                $maxScore = 4;
                $score = 0; $scoreName = $scoreDesc = $scoreAmount = 0;

                if (count($this->costs) < 2) {
                    $errors['costs']['costs'] = Text::get('mandatory-project-costs');
                } else {
                     $okeys['costs']['costs'] = 'ok';
                    ++$score;
                }

                $anyerror = false;
                foreach($this->costs as $cost) {
                    if (empty($cost->cost)) {
                        $errors['costs']['cost-'.$cost->id.'-cost'] = Text::get('mandatory-cost-field-name');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['costs']['cost-'.$cost->id.'-cost'] = 'ok';
                         $scoreName = 1;
                    }

                    if (empty($cost->type)) {
                        $errors['costs']['cost-'.$cost->id.'-type'] = Text::get('mandatory-cost-field-type');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['costs']['cost-'.$cost->id.'-type'] = 'ok';
                    }

                    if (empty($cost->description)) {
                        $errors['costs']['cost-'.$cost->id.'-description'] = Text::get('mandatory-cost-field-description');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['costs']['cost-'.$cost->id.'-description'] = 'ok';
                         $scoreDesc = 1;
                    }

                    if (empty($cost->amount)) {
                        $errors['costs']['cost-'.$cost->id.'-amount'] = Text::get('mandatory-cost-field-amount');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['costs']['cost-'.$cost->id.'-amount'] = 'ok';
                         $scoreAmount = 1;
                    }

                    //  Si no hay fechas no debe haber error por fechas
                    /*
                    if ($cost->type == 'task' && (empty($cost->from) || empty($cost->until))) {
                        $errors['costs']['cost-'.$cost->id.'-dates'] = Text::get('mandatory-cost-field-task_dates');
                        $anyerror = !$anyerror ?: true;
                    } elseif ($cost->type == 'task') {
                        $okeys['costs']['cost-'.$cost->id.'-dates'] = 'ok';
                    }
                    */
                }

                if ($anyerror) {
                    unset($okeys['costs']['costs']);
                    $errors['costs']['costs'] = Text::get('validate-project-costs-any_error');
                }

                $score = $score + $scoreName + $scoreDesc + $scoreAmount;

                // Mantenemos error si no hay costes
                if ($this->mincost == 0) {
                    $errors['costs']['total-costs'] = Text::get('mandatory-project-total-costs');
                } else {
                    $okeys['costs']['total-costs'] = 'ok';
                }

                $this->setScore($score, $maxScore);
                /***************** FIN Revisión del paso 4, COSTES *****************/
            }

            if (isset($steps) && isset($steps['rewards'])) {
                /***************** Revisión de campos del paso 5, RETORNOS *****************/

                //Si ha marcado checkbox de ayuda en licencias maxScore pasa a la mitad
                $maxScore = ($this->help_license)? 4 : 8;
                $score = 0; $scoreName = $scoreDesc = $scoreAmount = $scoreLicense = 0;
                //Si ha solicitado ayuda marcando el checkbox no lo tenemos en cuenta

                if (empty($this->social_rewards)&&(!$this->help_license)) {
                    $errors['rewards']['social_rewards'] = Text::get('validate-project-social_rewards');
                } else {
                     $okeys['rewards']['social_rewards'] = 'ok';
                     if (count($this->social_rewards) >= 2) {
                         ++$score;
                     }
                }

                if (empty($this->individual_rewards)) {
                    $errors['rewards']['individual_rewards'] = Text::get('validate-project-individual_rewards');
                } else {
                    $okeys['rewards']['individual_rewards'] = 'ok';
                    if (count($this->individual_rewards) >= 3) {
                        ++$score;
                    }
                    else {
                        $errors['rewards']['individual_rewards'] = Text::get('validate-project-individual_rewards');

                    }
                }

                //Si ha pedido ayuda con licencias nos saltamos la parte de retornos.
                if(!$this->help_license)
                {

                    $anyerror = false;
                    foreach ($this->social_rewards as $social) {
                        if (empty($social->reward)) {
                            $errors['rewards']['social_reward-'.$social->id.'reward'] = Text::get('mandatory-social_reward-field-name');
                            $anyerror = !$anyerror ?: true;
                        } else {
                             $okeys['rewards']['social_reward-'.$social->id.'reward'] = 'ok';
                             $scoreName = 1;
                        }

                        if (empty($social->description)) {
                            $errors['rewards']['social_reward-'.$social->id.'-description'] = Text::get('mandatory-social_reward-field-description');
                            $anyerror = !$anyerror ?: true;
                        } else {
                             $okeys['rewards']['social_reward-'.$social->id.'-description'] = 'ok';
                             $scoreDesc = 1;
                        }

                        if (empty($social->icon)) {
                            $errors['rewards']['social_reward-'.$social->id.'-icon'] = Text::get('mandatory-social_reward-field-icon');
                            $anyerror = !$anyerror ?: true;
                        } else {
                             $okeys['rewards']['social_reward-'.$social->id.'-icon'] = 'ok';
                        }

                        if (!empty($social->license)) {
                            $scoreLicense = 1;
                        }
                    }

                    if ($anyerror) {
                        unset($okeys['rewards']['social_rewards']);
                        $errors['rewards']['social_rewards'] = Text::get('validate-project-social_rewards-any_error');
                    }

                    $score = $score + $scoreName + $scoreDesc + $scoreLicense;
                    $scoreName = $scoreDesc = $scoreAmount = 0;

                }

                $anyerror = false;
                foreach ($this->individual_rewards as $individual) {
                    if (empty($individual->reward)) {
                        $errors['rewards']['individual_reward-'.$individual->id.'-reward'] = Text::get('mandatory-individual_reward-field-name');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['rewards']['individual_reward-'.$individual->id.'-reward'] = 'ok';
                         $scoreName = 1;
                    }

                    if (empty($individual->description)) {
                        $errors['rewards']['individual_reward-'.$individual->id.'-description'] = Text::get('mandatory-individual_reward-field-description');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['rewards']['individual_reward-'.$individual->id.'-description'] = 'ok';
                         $scoreDesc = 1;
                    }

                    if (empty($individual->amount)) {
                        $errors['rewards']['individual_reward-'.$individual->id.'-amount'] = Text::get('mandatory-individual_reward-field-amount');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['rewards']['individual_reward-'.$individual->id.'-amount'] = 'ok';
                         $scoreAmount = 1;
                    }

                    if (empty($individual->icon)) {
                        $errors['rewards']['individual_reward-'.$individual->id.'-icon'] = Text::get('mandatory-individual_reward-field-icon');
                        $anyerror = !$anyerror ?: true;
                    } else {
                         $okeys['rewards']['individual_reward-'.$individual->id.'-icon'] = 'ok';
                    }
                }

                if ($anyerror) {
                    unset($okeys['rewards']['individual_rewards']);
                    $errors['rewards']['individual_rewards'] = Text::get('validate-project-individual_rewards-any_error');
                }

                $score = $score + $scoreName + $scoreDesc + $scoreAmount;
                $this->setScore($score, $maxScore);
                /***************** FIN Revisión del paso 5, RETORNOS *****************/
            }

            if (isset($steps) && isset($steps['suports'])) {
                /***************** Revisión de campos del paso 6, COLABORACIONES *****************/
                $maxScore = 2;
                $scoreName = $scoreDesc = 0;
                foreach ($this->supports as $support) {
                    if (!empty($support->support)) {
                         $okeys['supports']['support-'.$support->id.'-support'] = 'ok';
                         $scoreName = 1;
                    }

                    if (!empty($support->description)) {
                         $okeys['supports']['support-'.$support->id.'-description'] = 'ok';
                         $scoreDesc = 1;
                    }
                }
                $score = $scoreName + $scoreDesc;
                $this->setScore($score, $maxScore);
                /***************** FIN Revisión del paso 6, COLABORACIONES *****************/
            }

            //-------------- Calculo progreso ---------------------//
            $this->setProgress();
            //-------------- Fin calculo progreso ---------------------//

            return true;
        }

        /*
         * reset de puntuación
         */
        public function setScore($score, $max, $reset = false) {
            if ($reset == true) {
                $this->score = $score;
                $this->max = $max;
            } else {
                $this->score += $score;
                $this->max += $max;
            }
        }

        /*
         * actualizar progreso segun score y max
         */
        public function setProgress () {
            // Cálculo del % de progreso
            $progress = 100 * $this->score / $this->max;
            $progress = round($progress, 0);

            if ($progress > 100) $progress = 100;
            if ($progress < 0)   $progress = 0;

            if ($this->status == 1 &&
                $progress >= 80 &&
                \array_empty($this->errors)
                ) {
                $this->finishable = true;
            }
            $this->progress = $progress;
            // actualizar el registro
            self::query("UPDATE project SET progress = :progress WHERE id = :id",
                array(':progress'=>$this->progress, ':id'=>$this->id));
        }


        /*
         * Listo para revisión
         * @return: boolean
         */
        public function ready(&$errors = array()) {
			try {
				$this->rebase();

                $sql = "UPDATE project SET status = :status, updated = :updated WHERE id = :id";
                self::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$this->id));

                // si está en una convocatoria hay que actualizar el numero de proyectos aplicados
                if (isset($this->called)) {
                    Call\Project::addOneApplied($this->called->id, $this->called->applied);
                }

                return true;

            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para revisión. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelto al estado de edición
         * @return: boolean
         */
        public function enable(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>1, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para edición. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado de publicación
         * @return: boolean
         */
        public function publish(&$errors = array()) {
			try {
				$sql = "UPDATE project SET passed = NULL, status = :status, published = :published WHERE id = :id";
				self::query($sql, array(':status'=>3, ':published'=>date('Y-m-d'), ':id'=>$this->id));

                /*
                 * Estos mensajes se automantinen en el paso del superform y en dashboard
                 *
                 *
                // borramos mensajes anteriores que sean de colaboraciones
                self::query("DELETE FROM message WHERE id IN (SELECT thread FROM support WHERE project = ?)", array($this->id));

                // creamos los hilos de colaboración en los mensajes
                foreach ($this->supports as $id => $support) {
                    $msg = new Message(array(
                        'user'    => $this->owner,
                        'project' => $this->id,
                        'date'    => date('Y-m-d'),
                        'message' => "{$support->support}: {$support->description}",
                        'blocked' => true
                        ));
                    if ($msg->save()) {
                        // asignado a la colaboracion como thread inicial
                        $sql = "UPDATE support SET thread = :message WHERE id = :support";
                        self::query($sql, array(':message'=>$msg->id, ':support'=>$support->id));
                    }
                    unset($msg);
                }
                */

                // actualizar numero de proyectos publicados del usuario
                User::updateOwned($this->owner);

                // si está en una convocatoria hay que actualizar el numero de proyectos en marcha
                if (isset($this->called)) {
                    Call\Project::numRunningProjects($this->called->id);
                }


                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado canecelado
         * @return: boolean
         */
        public function cancel(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>0, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado caducado
         * @return: boolean
         */
        public function fail(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>6, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Financiado
         * @return: boolean
         */
        public function succeed(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, success = :success WHERE id = :id";
				self::query($sql, array(':status'=>4, ':success'=>date('Y-m-d'), ':id'=>$this->id));

                // si está en una convocatoria hay que actualizar el numero de proyectos en marcha
                if (isset($this->called)) {
                    Call\Project::numSuccessProjects($this->called->id);
                }

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar por financiado el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Marcamos la fecha del paso a segunda ronda
         * @return: boolean
         */
        public function passDate(&$errors = array()) {
			try {
				$sql = "UPDATE project SET passed = :passed WHERE id = :id";
				self::query($sql, array(':passed'=>date('Y-m-d'), ':id'=>$this->id));

                // si está en una convocatoria hay que actualizar el numero de proyectos en marcha
                if (isset($this->called)) {
                    Call\Project::numSuccessProjects($this->called->id);
                }

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo SQL al marcar fecha de paso de ronda. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Retorno cumplido
         * @return: boolean
         */
        public function satisfied(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>5, ':id'=>$this->id));

                // si está en una convocatoria hay que actualizar el numero de proyectos en marcha
                if (isset($this->called)) {
                    Call\Project::numSuccessProjects($this->called->id);
                }

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar el retorno por cunplido para el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelve a estado financiado (por retorno pendiente) pero no modifica fecha
         * @return: boolean
         */
        public function rollback(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>4, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar el retorno pendiente para el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Si no se pueden borrar todos los registros, estado cero para que lo borre el cron
         * @return: boolean
         */
        public function remove(&$errors = array()) {

            if ($this->status > 1) {
                $errors[] = "El proyecto no esta descartado ni en edicion";
                return false;
            }

            self::query("START TRANSACTION");
            try {
                //borrar todos los registros
                self::query("DELETE FROM project_category WHERE project = ?", array($this->id)); // categorias
                self::query("DELETE FROM cost WHERE project = ?", array($this->id)); // necesidades
                self::query("DELETE FROM reward WHERE project = ?", array($this->id)); // recompensas y retornos
                self::query("DELETE FROM support WHERE project = ?", array($this->id)); // colaboraciones
                self::query("DELETE FROM message WHERE project = ?", array($this->id)); // mensajes
                self::query("DELETE FROM project_account WHERE project = ?", array($this->id)); // cuentas
                self::query("DELETE FROM review WHERE project = ?", array($this->id)); // revisión
                self::query("DELETE FROM project_lang WHERE id = ?", array($this->id)); // traducción
                self::query("DELETE FROM project WHERE id = ?", array($this->id));
                // y los permisos
                self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));
                // si todo va bien, commit y cambio el id de la instancia
                self::query("COMMIT");
                return true;
            } catch (\PDOException $e) {
                self::query("ROLLBACK");
				$sql = "UPDATE project SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>0, ':id'=>$this->id));
                $errors[] = "Fallo en la transaccion, el proyecto ha quedado como descartado";
                return false;
            }
        }

        /**
         * Creates a new project for a user and node/channel
         */
        static public function createNewProject(User $user = null, $node_id = null) {

            if(empty($user)) $user = Session::getUser();
            if(empty($node_id)) $node_id = Config::get('current_node');

            $project = new self(array('owner' => $user->id));

            $errors = array();
            if ($project->create($node_id, $errors)) {

                //TODO: as events
                //
                // Evento Feed
                $log = new Feed();
                $log->setTarget($user->id, 'user');
                $log->populate('usuario crea nuevo proyecto', 'admin/projects',
                    \vsprintf('%s ha creado un nuevo proyecto, %s', array(
                        Feed::item('user', $user->name, $user->id),
                        Feed::item('project', $project->name, $project->id))
                    ));
                $log->doAdmin('project');
                unset($log);

                // Si hay que asignarlo a un proyecto
                if ($call = Session::get('oncreate_applyto')) {

                    $registry = new Call\Project;
                    $registry->id = $project->id;
                    $registry->call = $call;
                    if ($registry->save($errors)) {

                        $callData = Call::getMini($call);
                        // email al autor

                        //  idioma de preferencia del usuario
                        $prefer = User::getPreferences($user->id);
                        $comlang = !empty($prefer->comlang) ? $prefer->comlang : $user->lang;

                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(39, $comlang);

                        // Sustituimos los datos
                        $subject = str_replace('%CALLNAME%', $callData->name, $template->title);

                        // En el contenido:
                        $search  = array('%USERNAME%', '%CALLNAME%', '%CALLERNAME%', '%CALLURL%');
                        $replace = array($user->name, $callData->name, $callData->user->name, SITE_URL.'/call/'.$call);
                        $content = \str_replace($search, $replace, $template->text);


                        $mailHandler = new Mail();

                        $mailHandler->to = $user->email;
                        $mailHandler->toName = $user->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send($errors)) {
                            Application\Message::info(Text::get('assign-call-success', $callData->name));
                        } else {
                            Application\Message::error(Text::get('project-review-confirm_mail-fail'));
                            \mail(Config::getMail('fail'), 'Fallo al enviar mail al crear proyecto asignando a convocatoria', 'Teniamos que enviar email a ' . $user->email . ' con esta instancia <pre>'.print_r($mailHandler, true).'</pre> y ha dado estos errores: <pre>' . print_r($errors, true) . '</pre>');
                        }

                        unset($mailHandler);

                        // Evento feed
                        $log = new Feed();
                        $log->setTarget($call, 'call');
                        $log->populate('nuevo proyecto asignado a convocatoria ' . $call, 'admin/calls/'.$call.'/projects',
                            \vsprintf('Nuevo proyecto %s aplicado a la convocatoria %s', array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('call', $call, $call))
                            ));
                        $log->doAdmin('project');
                        unset($log);
                    } else {
                        \mail(Config::getMail('fail'), 'Fallo al asignar a convocatoria al crear proyecto', 'Teniamos que asignar el nuevo proyecto ' . $project->id . ' a la convocatoria ' . $call . ' con esta instancia <pre>'.print_r($register, true).'</pre> y ha dado estos errores: <pre>' . print_r($errors, true) . '</pre>');
                    }
                }

                return $project;
            }

            throw new Exception\ModelException('Error creating project: ' . implode("\n", $errors));
        }

        /*
         * Para cambiar el id temporal a idealiza
         * solo si es md5
         * @return: boolean
         */
        public function rebase($newid = null, &$errors = array()) {
            try {
                if (preg_match('/^[A-Fa-f0-9]{32}$/',$this->id)) {
                    // idealizar el nombre
                    $newid = self::checkId(self::idealiza($this->name));
                    if ($newid == false) return false; //TODO: ???? Esto no pasa nunca, checkId lanza una excepcion...

                    // actualizar las tablas relacionadas en una transacción
                    $fail = false;
                    if (self::query("START TRANSACTION")) {
                        try {
                            // Project_conf, project_image se actualiza solo (foreing key CASCADE)
                            self::query("UPDATE project_category SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE cost SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE reward SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE support SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE message SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project_account SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE invest SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE review SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project_lang SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE blog SET owner = :newid WHERE owner = :id AND type='project'", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            // borro los permisos, el dashboard los creará de nuevo
                            self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));

                            // si todo va bien, commit y cambio el id de la instancia
                            self::query("COMMIT");
                            $this->id = $newid;
                            return true;

                        } catch (\PDOException $e) {
                            $errors[] = $e->getMessage();
                            self::query("ROLLBACK");
                            return false;
                        }
                    } else {
                        throw new Exception\ModelException('Fallo al iniciar transaccion rebase. ');
                    }
                } elseif (!empty ($newid)) {
                  // echo "Cambiando id proyecto: de {$this->id} a {$newid}<br /><br />";

                    if (self::query("START TRANSACTION")) {
                        try {

                           // echo 'en transaccion <br />';
                            // @FIXME : estos 4 primeros se pueden hacer en una sola sentencia con un STR_REPLACE
                            // acls
                            $acls = self::query("SELECT * FROM acl WHERE url like :id", array(':id'=>"%{$this->id}%"));
                            foreach ($acls->fetchAll(\PDO::FETCH_OBJ) as $rule) {
                                $url = str_replace($this->id, $newid, $rule->url);
                                self::query("UPDATE `acl` SET `url` = :url WHERE id = :id", array(':url'=>$url, ':id'=>$rule->id));

                            }
                           // echo 'acls listos <br />';

                            // mails
                            $mails = self::query("SELECT * FROM mail WHERE content like :id", array(':id'=>"%{$this->id}%"));
                            foreach ($mails->fetchAll(\PDO::FETCH_OBJ) as $mail) {
                                $content = str_replace($this->id, $newid, $mail->content);
                                self::query("UPDATE `mail` SET `content` = :content WHERE id = :id;", array(':content'=>$content, ':id'=>$mail->id));

                            }
                           // echo 'mails listos <br />';

                            // feed
                            $feeds = self::query("SELECT * FROM feed WHERE url like :id", array(':id'=>"%{$this->id}%"));
                            foreach ($feeds->fetchAll(\PDO::FETCH_OBJ) as $feed) {
                                $title = str_replace($this->id, $newid, $feed->title);
                                $html = str_replace($this->id, $newid, $feed->html);
                               self::query("UPDATE `feed` SET `title` = :title, `html` = :html  WHERE id = :id", array(':title'=>$title, ':html'=>$html, ':id'=>$feed->id));

                            }

                            // feed
                            $feeds2 = self::query("SELECT * FROM feed WHERE target_type = 'project' AND target_id = :id", array(':id'=>$this->id));
                            foreach ($feeds2->fetchAll(\PDO::FETCH_OBJ) as $feed2) {
                                self::query("UPDATE `feed` SET `target_id` = '{$newid}'  WHERE id = '{$feed2->id}';");

                            }

                            // traductores
                            $sql = "UPDATE `user_translate` SET `item` = '{$newid}' WHERE `user_translate`.`type` = 'project' AND `user_translate`.`item` = :id;";
                            self::query($sql, array(':id'=>$this->id));

                            self::query("UPDATE cost SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE message SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project_category SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project_lang SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE reward SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE support SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project_account SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE invest SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE patron SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE invest SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));


                            // si todo va bien, commit y cambio el id de la instancia
                            self::query("COMMIT");
                            $this->id = $newid;
                            return true;

                        } catch (\PDOException $e) {
                            self::query("ROLLBACK");
                            $errors[] = $e->getMessage();
                            return false;
                        }
                    } else {
                        throw new Exception\ModelException('Fallo al iniciar transaccion rebase. ');
                    }
                }

                return true;
            } catch (\PDOException $e) {
                throw new Exception\ModelException('Fallo rebase id temporal. ' . $e->getMessage());
            }

        }

        /*
         *  Para verificar id única
         */
        public static function checkId($id, $num = 1) {
            try
            {
                $query = self::query("SELECT id FROM project WHERE id = :id", array(':id'=>$id));
                $exist = $query->fetchObject();
                // si  ya existe, cambiar las últimas letras por un número
                if (!empty($exist->id)) {
                    $sufix = (string) $num;
                    if ((strlen($id)+strlen($sufix)) > 49)
                        $id = substr($id, 0, (strlen($id) - strlen($sufix))) . $sufix;
                    else
                        $id = $id . $sufix;
                    $num++;
                    $id = self::checkId($id, $num);
                }
                return $id;
            }
            catch (\PDOException $e) {
                throw new Exception\ModelException('Fallo al verificar id única para el proyecto. ' . $e->getMessage());
            }
        }


        /*
         *  Para actualizar el minimo/optimo de costes
         */
        public function minmax() {
            $this->mincost = 0;
            $this->maxcost = 0;

            foreach ($this->costs as $item) {
                if ($item->required == 1) {
                    $this->mincost += $item->amount;
                    $this->maxcost += $item->amount;
                }
                else {
                    $this->maxcost += $item->amount;
                }
            }
        }

        /*
         * Lista de proyectos de un usuario
         * @return: array of Project
         */
        public static function ofmine($owner, $published = false)
        {
            $lang = Lang::current();
            $projects = array();
            $values = array();
            $values[':lang'] = $lang;
            $values[':owner'] = $owner;

            if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(project_lang.description, project.description) as description";
            }
            else {
                $different_select=" IFNULL(project_lang.description, IFNULL(eng.description, project.description)) as description";
                $eng_join=" LEFT JOIN project_lang as eng
                                ON  eng.id = project.id
                                AND eng.lang = 'en'";
            }

            if ($published) {
                $sqlFilter = " AND project.status > 2";
            }

            $sql ="
                SELECT
                    project.id as project,
                    $different_select,
                    project.status as status,
                    project.published as published,
                    project.created as created,
                    project.updated as updated,
                    project.success as success,
                    project.closed as closed,
                    project.mincost as mincost,
                    project.maxcost as maxcost,
                    project.amount as amount,
                    project.image as image,
                    project.num_investors as num_investors,
                    project.num_messengers as num_messengers,
                    project.num_posts as num_posts,
                    project.days as days,
                    project.name as name,
                    project.owner as owner,
                    user.id as user_id,
                    user.name as user_name,
                    project_conf.noinvest as noinvest,
                    project_conf.one_round as one_round,
                    project_conf.days_round1 as days_round1,
                    project_conf.days_round2 as days_round2
                FROM  project
                INNER JOIN user
                    ON user.id = project.owner
                LEFT JOIN project_conf
                    ON project_conf.project = project.id
                LEFT JOIN project_lang
                    ON  project_lang.id = project.id
                    AND project_lang.lang = :lang
                $eng_join
                WHERE project.owner = :owner
                $sqlFilter
                ORDER BY  project.status ASC, project.created DESC
                ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $projects[] = self::getWidget($proj);
            }

            return $projects;
        }

        /*
         * Lista de proyectos que tienen las categorias preferidas de un usuario
         * @return: array of Project
         */
        public static function favouriteCategories($user, $published = false)
        {
            $lang = Lang::current();
            $projects = array();
            $values = array();
            $values[':lang'] = $lang;
            $values[':user'] = $user;

            if(self::default_lang($lang) === Lang::current()) {
                $different_select=" IFNULL(project_lang.description, project.description) as description";
            }
            else {
                $different_select=" IFNULL(project_lang.description, IFNULL(eng.description, project.description)) as description";
                $eng_join=" LEFT JOIN project_lang as eng
                                ON  eng.id = project.id
                                AND eng.lang = 'en'";
            }


            $sqlFilter = " AND project.status = 3";


            $sql ="
                SELECT
                    project.id as project,
                    $different_select,
                    project.status as status,
                    project.published as published,
                    project.created as created,
                    project.updated as updated,
                    project.success as success,
                    project.closed as closed,
                    project.mincost as mincost,
                    project.maxcost as maxcost,
                    project.amount as amount,
                    project.image as image,
                    project.num_investors as num_investors,
                    project.num_messengers as num_messengers,
                    project.num_posts as num_posts,
                    project.days as days,
                    project.name as name,
                    project.owner as owner,
                    user.id as user_id,
                    user.name as user_name,
                    project_conf.noinvest as noinvest,
                    project_conf.one_round as one_round,
                    project_conf.days_round1 as days_round1,
                    project_conf.days_round2 as days_round2
                FROM  project
                INNER JOIN user
                    ON user.id = project.owner
                LEFT JOIN project_conf
                    ON project_conf.project = project.id
                LEFT JOIN project_lang
                    ON  project_lang.id = project.id
                    AND project_lang.lang = :lang
                $eng_join
                WHERE project.id IN (
                    SELECT project
                    FROM project_category
                    WHERE category IN (
                        SELECT interest
                            FROM user_interest
                        WHERE user = :user
                    ))
                $sqlFilter
                ORDER BY  project.status ASC, project.created DESC
                ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $projects[] = self::getWidget($proj);
            }

            return $projects;
        }


        /**
         * Lista de proyectos publicados
         * @param $type string
         * @param $node string
         * @param $count returns a integer with the number of elements instead the list
         * @return: array of Project
         */
        public static function published($filter = array(), $node = null, $offset = 0, $limit = 10, $count = false)
        {
            //compatibility with simple string filter
            if(!is_array($filter)) {
                $filter = array('type' => (string) $filter);
            }
            $lang = Lang::current();

            $values = array();
            $where = array();
            $join = '';

            if (!empty($filter['status']))
                $status=$filter['status'];
            else
                // todos los que estan 'en campaña', en cualquier nodo
                $status = array(self::STATUS_IN_CAMPAIGN);

            $order = 'name ASC';

            if($node) {
                $where[] = 'project.node = :node';
                $values[':node'] = $node;
            }

            // segun el tipo
            if ($filter['type'] === 'popular') {
                $popularity = (int)$filter['popularity'];
                if(empty($popularity)) $popularity = 20;
                // de los que estan en campaña,
                // los que tienen más usuarios entre cofinanciadores y mensajeros
                $where[] = 'project.popularity >' . $popularity;
                $order = 'status=3 DESC, popularity DESC';
            }
            elseif($filter['type'] === 'outdate') {
                // los que les quedan 15 dias o menos
                $where[] = 'days <= 15 AND days > 0';
                $order = 'days ASC';
            }
            elseif($filter['type'] === 'recent') {
                $where[] = 'project.passed IS NULL';
                $order = 'published DESC';
            }
            elseif($filter['type'] === 'success') {
                // los que han conseguido el mínimo
                $where[] = 'project.amount >= mincost';
                $order = 'published DESC';
                $status[] = self::STATUS_FUNDED;
                $status[] = self::STATUS_FULFILLED;
            }
            elseif($filter['type'] === 'almost-fulfilled') {
                // para gestión de retornos
                $order = 'name ASC';
                $status = array(self::STATUS_FUNDED, self::STATUS_FULFILLED);
            }
            elseif($filter['type'] === 'fulfilled') {
                // retorno cumplido
                $status = array(self::STATUS_FULFILLED);
                $order = 'name ASC';
            }
            elseif($filter['type'] === 'available') {
                // ni edicion ni revision ni cancelados, estan disponibles para verse publicamente
                $status[] = self::STATUS_FUNDED;
                $status[] = self::STATUS_FULFILLED;
                $order = 'name ASC';
            }
            elseif($filter['type'] === 'archive') {
                // caducados sin financiacion
                $status = array(self::STATUS_UNFUNDED);
                $order = 'closed DESC';
            }
            elseif($filter['type'] === 'promoted') {
                // en "promote"
                $status[] = self::STATUS_FUNDED;
                $status[] = self::STATUS_FULFILLED;
                $join = 'INNER JOIN promote ON promote.project = project.id';
                $where[] = 'promote.active = 1';
                if($node) {
                    $where[] = 'promote.node = :node';
                }
                $order = 'promote.order ASC, name ASC';
            }
            elseif($filter['type'] === 'random') {
                $order = 'RAND()';
            }

            // filter by category?
            if(array_key_exists('category', $filter)) {
                if(!is_array($filter['category'])) $filter['category'] = array($filter['category']);
                $where[] = ' project.id IN (SELECT project FROM project_category WHERE project_category.category IN (' . implode(',', $filter['category']) . '))';
            }

            // Build the query
            $where = 'project.status IN ('. implode(', ', $status) .') ' . ($where ? ' AND ' . implode(' AND ', $where) : '');

            // Return total count for pagination
            if($count) {
                $sql = "SELECT COUNT(project.id) FROM project $join WHERE $where";
                return (int) self::query($sql, $values)->fetchColumn();
            }

            if(self::default_lang($lang) === Config::get('lang')) {
                $lang_select = ' IFNULL(project_lang.description, project.description) AS description';
            }
            else {
                $lang_select = ' IFNULL(project_lang.description, IFNULL(eng.description, project.description)) AS description';
                $lang_join = " LEFT JOIN project_lang AS eng
                                ON  eng.id = project.id
                                AND eng.lang = 'en'";
            }

            $offset = (int) $offset;
            $limit = (int) $limit;
            if($limit)
                $limit_sql='LIMIT '.$offset.','.$limit;

            $sql ="
                SELECT
                    project.id AS project,
                    project.name AS name,
                    $lang_select,
                    project.status AS status,
                    project.published AS published,
                    project.created AS created,
                    project.updated AS updated,
                    project.success AS success,
                    project.closed AS closed,
                    project.mincost AS mincost,
                    project.maxcost AS maxcost,
                    project.amount AS amount,
                    project.image AS image,
                    project.num_investors AS num_investors,
                    project.num_messengers AS num_messengers,
                    project.num_posts AS num_posts,
                    project.days AS days,
                    project.popularity AS popularity,
                    user.id AS user_id,
                    user.name AS user_name,
                    project_conf.noinvest AS noinvest,
                    project_conf.one_round AS one_round,
                    project_conf.days_round1 AS days_round1,
                    project_conf.days_round2 AS days_round2
                FROM  project
                INNER JOIN user
                    ON user.id = project.owner
                $join
                LEFT JOIN project_conf
                    ON project_conf.project = project.id
                LEFT JOIN project_lang
                            ON  project_lang.id = project.id
                            AND project_lang.lang = :lang
                $lang_join
                WHERE
                $where
                ORDER BY $order
                $limit_sql
                ";

            $values[':lang'] = $lang;

            // if($filter['type'] == 'recent') {sqldbg($sql, $values);die;}
            $projects = array();
            $query = self::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $projects[] = self::getWidget($proj);
            }
            return $projects;
        }

        //
        /**
         * Lista de proyectos en campaña y/o financiados
         *   para crear aporte manual
         *   para gestión de contratos
         *
         * @param bool $campaignonly  solo saca proyectos en proceso de campaña  (parece que esto no se usa...)
         * @param bool $mini  devuelve array asociativo id => nombre, para cuando no se necesita toda la instancia
         * @return array de instancias de proyecto // array asociativo (si recibe mini = true)
         */
        public static function active($campaignonly = false, $mini = false)
        {
            $projects = array();

            if ($campaignonly) {
                $sqlFilter = " WHERE project.status = 3";
            } else {
                $sqlFilter = " WHERE project.status = 3 OR project.status = 4";
            }

            $sql = "SELECT id, name FROM  project {$sqlFilter} ORDER BY name ASC";

            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                if ($mini) {
                    $projects[$proj->id] = $proj->name;
                } else {
                    $projects[] = self::get($proj->id);
                }
            }
            return $projects;
        }

        /**
         * Lista de proyectos para ser revisados por el cron/daily
         * en campaña
         *  o financiados hace más de dos meses y con retornos/recompensas pendientes
         *
         * solo carga datos necesarios para cron/daily
         *
         * @return array de instancias parciales de proyecto (getMedium)
         */
        public static function review()
        {
            $projects = array();

            // en cron Daily solo se miran proyectos:
            // en campaña (hasta el día siguiente a final de primera ronda)
            //, 2 meses post-financiado (80 + 60 = 140 días)
            //, 8 meses post financiado  (80 + 240 = 320 días)
            $sql = "SELECT
                    id, status,
                    DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%j') as dias
                FROM  project
                WHERE status IN ('3', '4')
                HAVING ( status = 3 AND dias BETWEEN 0 AND 42 ) OR (status = 4 AND ( dias BETWEEN 138 AND 142 OR dias BETWEEN 318 AND 322 ) )
                ORDER BY dias ASC";


            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {

                // FIXME  (depende de days_total, complicado tenerlo en cuenta en la consulta sql )
                /*
                if ($proj->status == 4 &&
                    (
                        ( $proj->dias < 138  &&  $proj->dias > 142 )
                    ||
                        ( $proj->dias < 318 &&  $proj->dias > 322 )
                    )
                )
                    continue;
                */


                $the_proj = self::getMedium($proj->id); // ya coge la configuración de rondas

                // porcentaje conseguido
                $the_proj->percent = floor(($the_proj->amount / $the_proj->mincost) * 100);

                // en days mantenemos el número de días de campaña
                $the_proj->days = (int) $proj->dias - 1;

                // número de recomendaciones de padrinos
                $the_proj->patrons = Patron::numRecos($proj->id);


                $projects[] = $the_proj;
            }
            return $projects;
        }

        /*
         * Lista de proyectos en campaña (para ser revisados por el cron/execute)
         *
         * Escogemos los proyectos que están a 5 días de terminar primera ronda o 3 días de terminar segunda.
         * En cron/execute necesitamos estos proyectos para feed y mail automático.
         * @return: array of Project (full instance (get))
         */
        public static function getActive($debug = false)
        {
            $projects = array();

            $sql = "
                SELECT
                    project.id as id,
                    project.published,
                    project.passed,
                    project.success,
                    project_conf.days_round1,
                    project_conf.days_round2,
                    project_conf.one_round,
                    DATEDIFF( DATE_ADD( published, INTERVAL IFNULL(days_round1, 40) DAY ), now() ) as rest_primera,
                    DATEDIFF( DATE_ADD( published, INTERVAL IFNULL(days_round1, 40) + IFNULL(days_round2, 40) DAY ), now() ) as rest_total
                FROM  project
                LEFT JOIN project_conf ON project_conf.project = project.id
                WHERE project.status = 3
                AND (
                      DATEDIFF( DATE_ADD( published, INTERVAL IFNULL(days_round1, 40) DAY ), now() ) BETWEEN -3 AND 5
                    OR
                      DATEDIFF( DATE_ADD( published, INTERVAL IFNULL(days_round1, 40) + IFNULL(days_round2, 40) DAY ), now() ) BETWEEN -3 AND 3
                )
                ORDER BY name ASC
            ";

            if ($debug) echo $sql.'<hr />';

            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {
                $projects[] = self::get($proj->id);
            }
            return $projects;
        }

        /**
         * Obtiene los proyectos que llevan $months meses con status=4 (proyecto financiado) y
         *
         * @param int $months
         * @return $projects
         */
        public static function getFunded($months = 10) {
            $success_date = date('Y-m-d', strtotime("-$months month"));

            $filter = ['status' => self::STATUS_FUNDED, 'success' => $success_date];
            $total = self::getList($filter, null, 0, 0, true);
            $projects = self::getList($filter, null, 0, $total);

            return $projects;
        }


        /**
         * Busca proyectos en estado revisión (2) que tengan fecha de publicación ese día.
         *
         * @param type $date
         * @return $projects
         */
        public static function getPublishToday() {
            $filter = ['status' => self::STATUS_REVIEWING, 'published' => date('Y-m-d')];
            $total = self::getList($filter, null, 0, 0, true);
            $projects = self::getList($filter, null, 0, $total);

            return $projects;
        }

        /**
         * Saca una lista completa de proyectos
         *
         * @param array filters
         * @param string node id
         * @param int limit items per page or 0 for unlimited
         * @param int page
         * @param int pages
         * @return array of project instances
         */
        public static function getList($filters = array(), $node = null, $offset = 0, $limit = 10, $count = false) {

            $projects = array();

            $values = array();
            $owners = array();

            $sqlOrder = '';

            // los filtros

            // pre-filtro de nombre|email de usuario
            if (!empty($filters['name'])) {
                $query = self::query("SELECT id FROM user WHERE (name LIKE :user OR email LIKE :user)",
                    array(':user' => "%{$filters['name']}%"));
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $names) {
                    $owners[] = $names->id;
                }
            }

            $sqlFilter = "";
            $sqlConsultantFilter = "";

            if ((!empty($filters['consultant'])) && ($filters['consultant'] != -1)) {
                $sqlFilter .= " AND user_project.user = :consultant";
                $values[':consultant'] = $filters['consultant'];
                $sqlConsultantFilter = " INNER JOIN user_project ON user_project.project = project.id";
            }
            if (!empty($filters['multistatus'])) {
                $sqlFilter .= " AND project.status IN ({$filters['multistatus']})";
            }
            if ($filters['status'] > -1) {
                $sqlFilter .= " AND project.status = :status";
                $values[':status'] = $filters['status'];
            } elseif ($filters['status'] == -2) {
                // en negociacion
                $sqlFilter .= " AND (project.status = 1  AND project.id NOT REGEXP '[0-9a-f]{32}')";
            } elseif($filters['status'] == -3) {
                //all projects...
            }
            else {
                // default valid projects
                $sqlFilter .= " AND (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{32}') )";
            }
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND project.owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND project.owner IN ('".implode("','", $owners)."')";
//                $values[':user'] = "%{$filters['name']}%";
            }
            if (!empty($filters['proj_name'])) {
                $sqlFilter .= " AND project.name LIKE :name";
                $values[':name'] = "%{$filters['proj_name']}%";
            }
            if (!empty($filters['proj_id'])) {
                $sqlFilter .= " AND project.id = :proj_id";
                $values[':proj_id'] = $filters['proj_id'];
            }
            if (!empty($filters['published'])) {
                $sqlFilter .= " AND project.published = :published";
                $values[':published'] = $filters['published'];
            }
            if (!empty($filters['category'])) {
                $sqlFilter .= " AND project.id IN (
                    SELECT project
                    FROM project_category
                    WHERE category = :category
                    )";
                $values[':category'] = $filters['category'];
            }
            if (!empty($filters['called'])) {

                switch ($filters['called']) {

                    //en cualquier convocatoria
                    case 'all':
                        $sqlFilter .= " AND project.id IN (
                        SELECT project
                        FROM call_project)";
                        break;
                    //en ninguna convocatoria
                    case 'none':
                        $sqlFilter .= " AND project.id NOT IN (
                        SELECT project
                        FROM call_project)";
                        break;
                    //filtro en esta convocatoria
                    default:
                        $sqlFilter .= " AND project.id IN (
                        SELECT project
                        FROM call_project
                        WHERE `call` = :called
                        )";
                        $values[':called'] = $filters['called'];
                        break;

                }

            }
            if (!empty($filters['node'])) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $filters['node'];
            } elseif (!empty($node) && !Config::isMasterNode($node)) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $node;
            }
            if (!empty($filters['success'])) {
                $sqlFilter .= " AND success = :success";
                $values[':success'] = $filters['success'];
            }

            //el Order
            if ($filters['order'] === 'updated') {
                $sqlOrder = " ORDER BY project.updated DESC";
            }
            elseif($filters['order']) {
                $sqlOrder = " ORDER BY {$filters['order']}";
            }
            else {
                $sqlOrder = " ORDER BY project.name ASC";
            }

            $where = "project.id != ''
                      $sqlFilter
                      $sqlOrder";

            if($count) {
                // Return count
                $sql = "SELECT COUNT(id)
                    FROM project
                    $sqlConsultantFilter
                    WHERE $where";
                return (int) self::query($sql, $values)->fetchColumn();
            }

            $offset = (int) $offset;
            $limit = (int) $limit;
            // la select
            //@Javier: esto es de admin pero meter los campos en la select y no usar getMedium ni getWidget.
            // Si la lista de proyectos necesita campos calculados lo añadimos aqui  (ver view/admin/projects/list.html.php)
            // como los consultores
            $sql = "SELECT
                        project.id,
                        project.id REGEXP '[0-9a-f]{32}' as draft,
                        project.name as name,
                        project.status as status,
                        project.published as published,
                        project.created as created,
                        IFNULL(project.updated, project.created) as updated,
                        project.success as success,
                        project.closed as closed,
                        project.node as node,
                        project.mincost as mincost,
                        project.maxcost as maxcost,
                        project.amount as amount,
                        project.image as image,
                        project.num_investors as num_investors,
                        project.num_messengers as num_messengers,
                        project.num_posts as num_posts,
                        project.days as days,
                        project.owner as owner,
                        project.translate as translate,
                        project.progress as progress,
                        user.email as user_email,
                        user.name as user_name,
                        user.lang as user_lang,
                        user.id as user_id,
                        project_conf.*
                    FROM project
                    LEFT JOIN project_conf
                    ON project_conf.project=project.id
                    LEFT JOIN user
                    ON user.id=project.owner

                    $sqlConsultantFilter
                    WHERE $where
                    LIMIT $offset, $limit
                    ";


            // echo \sqldbg($sql, $values);


            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Project') as $proj) {
                //$the_proj = self::getMedium($proj['id']);

                $proj->user = new User;
                $proj->user->id = $proj->user_id;
                $proj->user->name = $proj->user_name;
                $proj->user->email = $proj->user_email;
                $proj->user->lang = $proj->user_lang;

                // convocado
                $call = Call\Project::calledMini($proj->id);

                if ( $call instanceof Call ) {

                    // cuanto han recaudado
                    // de los usuarios
                    if (!isset($proj->amount_users)) {
                        $proj->amount_users = Invest::invested($proj->id, 'users', $call->id);
                    }
                    // de la convocatoria
                    if (!isset($proj->amount_call)) {
                        $proj->amount_call = Invest::invested($proj->id, 'call', $call->id);
                    }

                    $proj->called = $call;

                } else {

                    $proj->called = null;

                }


                    // extra conf
                $proj->days_total = ($proj->one_round) ? $proj->days_round1 : ( $proj->days_round1 + $proj->days_round2 );

                $proj->setDays();

                //calculo de maxcost, min_cost sólo si hace falta
                if(!isset($proj->mincost)) {
                    $costs = self::calcCosts($proj->id);
                    $proj->mincost = $costs->mincost;
                    $proj->maxcost = $costs->maxcost;
                }

                //cálculo de mensajeros
                if (!isset($proj->num_messengers)) {
                    $proj->num_messengers = Message::numMessengers($proj->id);
                }

                //cálculo de número de cofinanciadores
                if(!isset($proj->num_investors)) {
                    $proj->num_investors = Invest::numInvestors($proj->id);
               }


                $projects[] = $proj;
            }

            return $projects;
        }

        /**
         * Saca una lista de proyectos, solo datos simples
         *
         * @param string node id
         * @return array of items , not instances of this class.
         *
         * TODO: remove this method
         */
        public static function getMiniList($filters = array(), $node = null) {

            $projects = array();

            $values = array();


            // los filtros
            $sqlFilter = "";
            $sqlOrder = '';

            if (!empty($filters['multistatus'])) {
                $sqlFilter .= " AND project.status IN ({$filters['multistatus']})";
            }
            if ($filters['status'] > -1) {
                $sqlFilter .= " AND project.status = :status";
                $values[':status'] = $filters['status'];
            } elseif ($filters['status'] == -2) {
                $sqlFilter .= " AND (project.status = 1  AND project.id NOT REGEXP '[0-9a-f]{32}')";
            } else {
                $sqlFilter .= " AND (project.status > 1  OR (project.status = 1 AND project.id NOT REGEXP '[0-9a-f]{32}') )";
            }
            if (!empty($filters['proj_name'])) {
                $sqlFilter .= " AND project.name LIKE :name";
                $values[':name'] = "%{$filters['proj_name']}%";
            }
            if (!empty($filters['proj_id'])) {
                $sqlFilter .= " AND project.id = :proj_id";
                $values[':proj_id'] = $filters['proj_id'];
            }
            if (!empty($filters['node'])) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $filters['node'];
            } elseif (!empty($node) && !Config::isMasterNode($node)) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $node;
            }

            //el Order
            if (!empty($filters['order'])) {
                switch ($filters['order']) {
                    case 'success':
                        $sqlOrder .= " ORDER BY project.success ASC";
                    break;
                    case 'name':
                        $sqlOrder .= " ORDER BY project.name ASC";
                    break;
                    default:
                        $sqlOrder .= " ORDER BY {$filters['order']}";
                    break;
                }
            }

            // la select
            $sql = "SELECT
                        project.id,
                        project.name,
                        project.status,
                        project.published,
                        project.success,
                        project.owner,
                        project.node
                    FROM project
                    WHERE project.id != ''
                        $sqlFilter
                        $sqlOrder
                    LIMIT 999
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {
                $projects[] = $proj;
            }
            return $projects;
        }

        /**
         * Saca una lista de proyectos disponibles para traducir
         *
         * @param array filters
         * @param string node id
         * @return array of project instances
         */
        public static function getTranslates($filters = array(), $node = null) {
            if(empty($node)) $node = Config::get('node');
            $projects = array();

            $values = array(':node' => $node);

            $sqlFilter = "";
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['translator'])) {
                $sqlFilter .= " AND id IN (
                    SELECT item
                    FROM user_translate
                    WHERE user = :translator
                    AND type = 'project'
                    )";
                $values[':translator'] = $filters['translator'];
            }

            $sql = "SELECT
                        id
                    FROM project
                    WHERE translate = 1
                    AND node = :node
                        $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = self::getMini($proj['id']);
            }
            return $projects;
        }

        /**
         * Metodo para direcciones de proyectos
         * @return array strings
         *
         * Cerca de la obsolitud
         *
         */
        public static function getProjLocs () {

            $results = array();

            $sql = "SELECT distinct(project_location) as location
                    FROM project
                    WHERE status > 2
                    ORDER BY location ASC";

            try {
                $query = self::query($sql);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                    $results[md5($item->location)] = $item->location;
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception\ModelException('Fallo la lista de localizaciones');
            }
        }
        /**
         *  Saca las vias de contacto para un proyecto
         * @return: Project
         */
        public static function getContact($id) {

            $sql = "
                SELECT
                    project.name as project_name,
                    project.success as success_date,
                    user.name as owner_name,
                    project.contract_name as contract_name,
                    user.email as owner_email,
                    project.contract_email as contract_email,
                    project.phone as phone,
                    user.twitter as twitter,
                    user.facebook as facebook,
                    user.google as google,
                    user.identica as identica,
                    user.linkedin as linkedin
                FROM project
                INNER JOIN user
                    ON user.id = project.owner
                WHERE project.id = :id
            ";

            $query = self::query($sql, array(':id' => $id));
            $contact = $query->fetchObject();
            return $contact;
        }

        /**
         *  Metodo para obtener cofinanciadores agregados por usuario
         *  y sin convocadores
         * @return: array of arrays
         */
        public function agregateInvestors () {
            $investors = array();

            foreach($this->investors as $investor) {

                if (!empty($investor->campaign)) continue;

                $investors[$investor->user] = (object) array(
                    'user' => $investor->user,
                    'name' => $investor->name,
                    'avatar' => $investor->avatar,
                    'projects' => $investor->projects,
                    'worth' => $investor->worth,
                    'amount' => $investors[$investor->user]->amount + $investor->amount,
                    'date' => !empty($investors[$investor->user]->date) ?$investors[$investor->user]->date : $investor->date
                );
            }

            return $investors;
        }

        /*
        * Método para calcular el mínimo y óptimo de un proyecto
        * Actualiza en project el mincost y maxcost
        */
        public static function calcCosts($id) {
            $cost_query = self::query("SELECT
                        mincost AS oldmincost,
                        maxcost AS oldmaxcost,
                        (SELECT  SUM(amount)
                        FROM    cost
                        WHERE   project = project.id
                        AND     required = 1
                        ) as `mincost`,
                        (SELECT  SUM(amount)
                        FROM    cost
                        WHERE   project = project.id
                        ) as `maxcost`
                FROM project
                WHERE id =?", array($id));
            if($costs = $cost_query->fetchObject()) {
                if($costs->mincost != $costs->oldmincost || $costs->maxcost != $costs->oldmaxcost) {
                    self::query("UPDATE
                        project SET
                        mincost = :mincost,
                        maxcost = :maxcost
                     WHERE id = :id", array(':id' => $id, ':mincost' => $costs->mincost, ':maxcost' => $costs->maxcost));
                }
            }
            return $costs;
        }


        /*
         * Para saber si ha conseguido el mínimo
         * @return: boolean
         */
        public static function isSuccessful($id) {
            $sql = "SELECT
                            id,
                            (SELECT  SUM(amount)
                            FROM    cost
                            WHERE   project = project.id
                            AND     required = 1
                            ) as `mincost`,
                            (SELECT  SUM(amount)
                            FROM    invest
                            WHERE   project = project.id
                            AND     invest.status IN ('0', '1', '3', '4')
                            ) as `getamount`
                    FROM project
                    WHERE project.id = ?
                    HAVING getamount >= mincost
                    LIMIT 1
                    ";

            $query = self::query($sql, array($id));
            return ($query->fetchColumn() == $id);
        }

        /*
         * Para saber si un usuario es el impulsor
         * @return: boolean
         */
        public static function isMine($id, $user) {
            $sql = "SELECT id, owner FROM project WHERE id = :id AND owner = :owner";
            $values = array(
                ':id' => $id,
                ':owner' => $user
            );
            $query = static::query($sql, $values);
            $mine = $query->fetchObject();
            if ($mine->owner == $user && $mine->id == $id) {
                return true;
            } else {
                return false;
            }
        }

        /*
         * Para saber si un proyecto tiene traducción en cierto idioma
         * @return: boolean
         */
        public static function isTranslated($id, $lang) {
            $sql = "SELECT id FROM project_lang WHERE id = :id AND lang = :lang";
            $values = array(
                ':id' => $id,
                ':lang' => $lang
            );
            $query = static::query($sql, $values);
            $its = $query->fetchObject();
            if ($its->id == $id) {
                return true;
            } else {
                return false;
            }
        }

        /*
         * Estados de desarrollo del propyecto
         */
        public static function currentStatus () {
            return array(
                1=>Text::get('overview-field-options-currently_inicial'),
                2=>Text::get('overview-field-options-currently_medio'),
                3=>Text::get('overview-field-options-currently_avanzado'),
                4=>Text::get('overview-field-options-currently_finalizado'));
        }

        /*
         * Ámbito de alcance de un proyecto
         */
        public static function scope () {
            return array(
                1=>Text::get('overview-field-options-scope_local'),
                2=>Text::get('overview-field-options-scope_regional'),
                3=>Text::get('overview-field-options-scope_nacional'),
                4=>Text::get('overview-field-options-scope_global'));
        }

        /*
         * Estados de publicación de un proyecto
         */
        public static function status () {
            return array(
                self::STATUS_REJECTED => Text::get('form-project_status-cancelled'),
                self::STATUS_EDITING => Text::get('form-project_status-edit'),
                self::STATUS_REVIEWING => Text::get('form-project_status-review'),
                self::STATUS_IN_CAMPAIGN => Text::get('form-project_status-campaing'),
                self::STATUS_FUNDED => Text::get('form-project_status-success'),
                self::STATUS_FULFILLED => Text::get('form-project_status-fulfilled'),
                self::STATUS_UNFUNDED => Text::get('form-project_status-expired')
            );
        }

        /*
         * Estados de proceso de campaña
         */
        public static function procStatus () {
            return array(
                'first' => 'En primera ronda',
                'second' => 'En segunda ronda',
                'completed' => 'Campaña completada'
                );
        }

        /*
         * Siguiente etapa en la vida del proyeto
         */
        public static function waitfor () {
            return array(
                self::STATUS_REJECTED => Text::get('form-project_waitfor-cancel'),
                self::STATUS_EDITING => Text::get('form-project_waitfor-edit'),
                self::STATUS_REVIEWING => Text::get('form-project_waitfor-review'),
                self::STATUS_IN_CAMPAIGN => Text::get('form-project_waitfor-campaing'),
                self::STATUS_FUNDED => Text::get('form-project_waitfor-success'),
                self::STATUS_FULFILLED => Text::get('form-project_waitfor-fulfilled'),
                self::STATUS_UNFUNDED => Text::get('form-project_waitfor-expired')
            );
        }

        /*
         * @return: empty errors structure
         */
        public static function blankErrors() {
            // para guardar los fallos en los datos
            $errors = array(
                'userProfile'  => array(),  // Errores en el paso 1
                'userPersonal' => array(),  // Errores en el paso 2
                'overview'     => array(),  // Errores en el paso 3
                'images'       => array(),  // Errores en el paso 3b
                'costs'        => array(),  // Errores en el paso 4
                'rewards'      => array(),  // Errores en el paso 5
                'supports'     => array()   // Errores en el paso 6
            );

            return $errors;
        }

    }

}
