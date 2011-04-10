<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Library\Text;

    class Project extends \Goteo\Core\Model {
        
        public        
            $id = null,
            $owner, // User who created it
            $node, // Node this project belongs to
            $status,
            $progress, // puntuation %
            $amount, // Current donated amount

            // Register contract data
            $contract_name,
            $contract_surname,
            $contract_nif, // Guardar sin espacios ni puntos ni guiones
            $contract_email,
            $phone, // guardar sin espacios ni puntos
            $address,
            $zipcode,
            $location, // owner's location
            $country,

            // Edit project description
            $name,
            $image,
            $description,
             $motivation,
             $about,
             $goal,
             $related,
            $categories = array(),
            $media,
            $keywords, // por ahora se guarda en texto tal cual
            $currently, // Current development status of the project
            $project_location, // project execution location
                
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
            $days = 0, //para 40 desde la publicación o para 80 si no está caducado
            $investors = array(), // usuarios que han invertido


            // para guardar los errores en el proyecto
            $errors = array(
                'userProfile'  => array(),
                'userPersonal' => array(),
                'overview'     => array(),
                'costs'        => array(),
                'rewards'      => array(),
                'supports'     => array()
            );

        /**
         * Inserta un proyecto con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create ($user, $node = 'goteo') {

            // cojemos el número de proyecto de este usuario
            $query = self::query("SELECT COUNT(id) as num FROM project WHERE owner = ?", array($user));
            if ($now = $query->fetchObject())
                $num = $now->num + 1;
            else
                $num = 1;

            $values = array(
                ':id'   => md5($user.'-'.$num),
                ':name' => "Mi proyecto $num",
                ':status'   => 1,
                ':progress' => 0,
                ':owner' => $user,
                ':node' => $node,
                ':amount' => 0,
                ':created'  => date('Y-m-d')
                );

            $sql = "REPLACE INTO project (id, name, status, progress, owner, node, amount, created)
                 VALUES (:id, :name, :status, :progress, :owner, :node, :amount, :created)";
            try {
				self::query($sql, $values);

                $this->id = $values[':id'];
                $this->owner = $user;
                $this->node = $node;
                $this->status = 1;
                $this->progress = 0;

                // cargar los datos legales del usuario

                return $this->id;
            } catch (\PDOException $e) {
                throw new Goteo\Core\Exception("ERROR al crear un nuevo proyecto<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
        }
        
        /*
         *  Cargamos los datos del proyecto
         */
        public static function get($id) {
            try {
				// metemos los datos del proyecto en la instancia
				$query = self::query("SELECT * FROM project WHERE id = ?", array($id));
				$project = $query->fetchObject(__CLASS__);

				// categorias
                $project->categories = Project\Category::get($id);

				// costes y los sumammos
				$project->costs = Project\Cost::getAll($id);

				foreach ($project->costs as $item) {
					if ($item->required == 1) {
						$project->mincost += $item->amount;
						$project->maxcost += $item->amount;
					}
					else {
						$project->maxcost += $item->amount;
					}
				}

				// retornos colectivos
				$project->social_rewards = Project\Reward::getAll($id, 'social');
				// retornos individuales
				$project->individual_rewards = Project\Reward::getAll($id, 'individual');

				// colaboraciones
				$project->supports = Project\Support::getAll($id);

                //checkeamos los campos y actualizamos el progreso
                $project->check();
                $project->evaluate();

                if ($project->status > 2) {
                    // cálculo de evolución
                    $project->invested = Invest::invested($project->id);
                    $project->days = self::daysLeft($project->id);
                    $project->investors = Invest::investors($project->id);
                }

				return $project;

			} catch(\PDOException $e) {
				throw \Goteo\Core\Exception($e->getMessage());
			}
		}

        public function validate(&$errors = array()) { return true; }

        /**
         * actualiza en un proyecto pares de campo=>valor
         * @param array $data
         * @param array $errors
         */
        public function save (&$errors = array()) {
            if(!$this->validate($errors)) return false;

  			try {
                // nif y telefono sin guiones, espacios ni puntos
                $this->contract_nif = str_replace(array('_', '.', ' ', '-', ','), '', $this->contract_nif);
                $this->phone = str_replace(array('_', '.', ' ', '-', ','), '', $this->phone);

                $fields = array(
                    'contract_name',
                    'contract_surname',
                    'contract_nif',
                    'contract_email',
                    'phone',
                    'address',
                    'zipcode',
                    'location',
                    'country',
                    'name',
                    'image',
                    'description',
                    'motivation',
                    'about',
                    'goal',
                    'related',
                    'keywords',
                    'media',
                    'currently',
                    'project_location',
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

				$set .= ", updated = :updated";
				$values[':updated'] = date('Y-m-d');
				$values[':id'] = $this->id;

				$sql = "UPDATE project SET " . $set . " WHERE id = :id";
				$res = self::query($sql, $values);

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el proyecto.' . $e->getMessage();
                return false;
			}

        }

        // metodo para calcular el % de progreso
        public function evaluate ()
        {
            $score = 0; // campos sin error dan puntos
            $max = 0; // el máximo que se puede conseguir

            /***************** Revisión de campos del paso 1, PERFIL *****************/
            $max += 8;
            $errors = $this->errors['userProfile'];
            if (empty($errors['name'])) $score++;
            if (empty($errors['avatar'])) $score++;
            if (empty($errors['about'])) $score++;
            if (empty($errors['interests'])) $score++;
            if (empty($errors['keywords'])) $score++;
            if (empty($errors['contribution'])) $score++;
            if (empty($errors['blog'])) $score++;
            if (empty($errors['facebook'])) $score++;
            /***************** FIN Revisión del paso 1, PERFIL *****************/

            /***************** Revisión de campos del paso 2,DATOS PERSONALES *****************/
            $max += 9;
            $errors = $this->errors['userPersonal'];
            if (empty($errors['contract_name'])) $score++;
            if (empty($errors['contract_surname'])) $score++;
            if (empty($errors['contract_nif'])) $score++;
            if (empty($errors['contract_email'])) $score++;
            if (empty($errors['phone'])) $score++;
            if (empty($errors['address'])) $score++;
            if (empty($errors['zipcode'])) $score++;
            if (empty($errors['location'])) $score++;
            if (empty($errors['country'])) $score++;
            /***************** FIN Revisión del paso 2, DATOS PERSONALES *****************/

            /***************** Revisión de campos del paso 3, DESCRIPCION *****************/
            $max += 12;
            $errors = $this->errors['overview'];
            if (empty($errors['name'])) $score++;
            if (empty($errors['image'])) $score++;
            if (empty($errors['description'])) $score++;
            if (empty($errors['motivation'])) $score++;
            if (empty($errors['about'])) $score++;
            if (empty($errors['goal'])) $score++;
            if (empty($errors['related'])) $score++;
            if (empty($errors['categories'])) $score++;
            if (empty($errors['media'])) $score++;
            if (empty($errors['keywords'])) $score++;
            if (empty($errors['currently'])) $score++;
            if (empty($errors['project_location'])) $score++;
            /***************** FIN Revisión del paso 3, DESCRIPCION *****************/

            /***************** Revisión de campos del paso 4, COSTES *****************/
            $max += 3;
            $errors = $this->errors['costs'];
            if (empty($errors['ncost'])) $score++;
            if (empty($errors['total-costs'])) $score++;
            if (empty($errors['resource'])) $score++;
            foreach($this->costs as $cost) {
                if (empty($errors['cost'.$cost->id])
                   && empty($errors['cost-description'.$cost->id])
                   && empty($errors['cost-dates'.$cost->id])) $score++;
                $max++;
            }
            /***************** FIN Revisión del paso 4, COSTES *****************/

            /***************** Revisión de campos del paso 5, RETORNOS *****************/
            $max += 3;
            $errors = $this->errors['rewards'];
            if (empty($errors['nsocial_reward'])) $score++;
            if (empty($errors['nindividual_reward'])) $score++;
            foreach ($this->social_rewards as $social) {
                if (empty($errors['social_reward'.$social->id])
                   && empty($errors['social_rewards-description'.$social->id])
                   && empty($errors['social_reward-license'.$social->id])) $score++;
                $max++;
            }
            foreach ($this->individual_rewards as $individual) {
                if (empty($errors['individual_reward'.$individual->id])
                   && empty($errors['individual_reward-description'.$individual->id])
                   && empty($errors['individual_reward-amount'.$individual->id])) $score++;
                $max++;
            }
            /***************** FIN Revisión del paso 5, RETORNOS *****************/

            /***************** Revisión de campos del paso 6, COLABORACIONES *****************/
            $errors = $this->errors['supports'];
            foreach ($this->supports as $support) {
                if (empty($errors['support'.$support->id])
                   &&  empty($errors['support-description'.$support->id])) $score++;
                $max++;
            }
            /***************** FIN Revisión del paso 6, COLABORACIONES *****************/

            // Cálculo del % de progreso
            $progress = 100 * $score / $max;
            $progress = round($progress, 0);
            if ($progress > 100) $progress = 100;

            // actualizar el progreso
            $sql = "UPDATE project SET progress = :progress WHERE id = :id";
            if (self::query($sql, array(':progress'=>$progress, ':id'=>$this->id))) {
                $this->progress = $progress;
            }
        }


        /*
         *  Para validar los campos del proyecto
         * cualquier campo incorrecto lo guarda en badfields y en badmessages
         * luego se consulta para pintar el checkbox lateral o los errores
         * También cuenta la puntuación del 1 al 100 para el proyecto y lo guarda
         *
         * Hay que ver el perfil del usuario, tener un perfil decente también da puntos, no?
         *
         */
        public function check ($step = null)
        {
            $errors = &$this->errors;
            /***************** Revisión de campos del paso 1, PERFIL *****************/
            if ($step == 'userProfile' || $step == null) {
                // el check del modelo usuario
                $user = User::get($this->owner);
                $user->check($errors['userProfile']);
            }
            /***************** FIN Revisión del paso 1, PERFIL *****************/

            /***************** Revisión de campos del paso 2,DATOS PERSONALES *****************/
            if ($step == 'userPersonal' || $step == null) {
                if (empty($this->contract_name))
                    $errors['userPersonal']['contract_name'] = Text::get('mandatory project field contract name');

                if (empty($this->contract_surname))
                    $errors['userPersonal']['contract_surname'] = Text::get('mandatory project field contract surname');

                if (empty($this->contract_nif))
                    $errors['userPersonal']['contract_nif'] = Text::get('mandatory project field contract nif');
                elseif (!Check::Nif($this->contract_nif))
                    $errors['userPersonal']['contract_nif'] = Text::get('validate project value contract nif');

                if (empty($this->contract_email))
                    $errors['userPersonal']['contract_email'] = Text::get('mandatory project field contract email');
                elseif (!Check::Mail($this->contract_email))
                    $errors['userPersonal']['contract_email'] = Text::get('validate project value contract email');

                if (empty($this->phone))
                    $errors['userPersonal']['phone'] = Text::get('mandatory project field phone');
                elseif (!Check::Phone($this->phone))
                    $errors['userPersonal']['phone'] = Text::get('validate project value phone');

                if (empty($this->address))
                    $errors['userPersonal']['address'] = Text::get('mandatory project field address');

                if (empty($this->zipcode))
                    $errors['userPersonal']['zipcode'] = Text::get('mandatory project field zipcode');

                if (empty($this->location))
                    $errors['userPersonal']['location'] = Text::get('mandatory project field residence');

                if (empty($this->country))
                    $errors['userPersonal']['country'] = Text::get('mandatory project field country');
            }
            /***************** FIN Revisión del paso 2, DATOS PERSONALES *****************/

            /***************** Revisión de campos del paso 3, DESCRIPCION *****************/
            if ($step == 'overview' || $step == null) {
                if (empty($this->name))
                    $errors['overview']['name'] = Text::get('mandatory project field name');

                if (empty($this->image))
                    $errors['overview']['image'] = Text::get('mandatory project field image');

                if (empty($this->description))
                    $errors['overview']['description'] = Text::get('mandatory project field description');
                elseif (!Check::Words($this->description, 150))
                    $errors['overview']['description'] = Text::get('validate project value description');

                if (empty($this->motivation))
                    $errors['overview']['motivation'] = Text::get('mandatory project field motivation');

                 if (empty($this->about))
                    $errors['overview']['about'] = Text::get('mandatory project field about');

                if (empty($this->goal))
                    $errors['overview']['goal'] = Text::get('mandatory project field goal');

                if (empty($this->related))
                    $errors['overview']['related'] = Text::get('mandatory project field related');

                if (empty($this->categories))
                    $errors['overview']['categories'] = Text::get('mandatory project field category');

                if (empty($this->media))
                    $errors['overview']['media'] = Text::get('mandatory project field media');

                $keywords = explode(',', $this->keywords);
                if ($keywords < 5)
                    $errors['overview']['keywords'] = Text::get('validate project value keywords');

                if (empty($this->currently))
                    $errors['overview']['currently'] = Text::get('validate project field currently');

                if (empty($this->project_location))
                    $errors['overview']['project_location'] = Text::get('mandatory project field location');
            }
            /***************** FIN Revisión del paso 3, DESCRIPCION *****************/

            /***************** Revisión de campos del paso 4, COSTES *****************/
            if ($step == 'costs' || $step == null) {
                if (count($this->costs) < 2)
                    $errors['costs']['ncost'] = Text::get('mandatory project costs');
                elseif (count($this->costs) < 5)
                    $errors['costs']['ncost'] = Text::get('validate project field costs');

                foreach($this->costs as $cost) {
                    if (empty($cost->cost))
                        $errors['costs']['cost'.$cost->id] = Text::get('mandatory cost field name');

                    if (empty($cost->description))
                        $errors['costs']['cost-description'.$cost->id] = Text::get('mandatory cost field description');

                    if (empty($cost->from) || empty($cost->until))
                        $errors['costs']['cost-dates'.$cost->id] = Text::get('validate cost field dates');
                }

                $costdif = $this->maxcost - $this->mincost;
                $maxdif = $this->mincost * 0.40;
                if ($costdif > $maxdif )
                    $errors['costs']['total-costs'] = Text::get('validate project total costs');

                if (empty($this->resource))
                    $errors['costs']['resource'] = Text::get('mandatory project field resource');
            }
            /***************** FIN Revisión del paso 4, COSTES *****************/

            /***************** Revisión de campos del paso 5, RETORNOS *****************/
            if ($step == 'rewards' || $step == null) {
                if (count($this->social_rewards) < 5)
                    $errors['rewards']['nsocial_reward'] = Text::get('validate project social_rewards');

                if (count($this->individual_rewards) < 5)
                    $errors['rewards']['nindividual_reward'] = Text::get('validate project individual_rewards');

                foreach ($this->social_rewards as $social) {
                    if (empty($social->reward))
                        $errors['rewards']['social_reward'.$social->id] = Text::get('mandatory social_reward field name');

                    if (empty($social->description))
                        $errors['rewards']['social_rewards-description'.$social->id] = Text::get('mandatory social_reward field description');

                    if (empty($social->license))
                        $errors['rewards']['social_reward-license'.$social->id] = Text::get('validate social_reward license');
                }

                foreach ($this->individual_rewards as $individual) {
                    if (empty($individual->reward))
                        $errors['rewards']['individual_reward'.$individual->id] = Text::get('mandatory individual_reward field name');

                    if (empty($individual->description))
                        $errors['rewards']['individual_reward-description'.$individual->id] = Text::get('mandatory individual_reward field description');

                    if (empty($individual->reward))
                        $errors['rewards']['individual_reward-amount'.$individual->id] = Text::get('mandatory individual_reward field amount');
                }
            }
            /***************** FIN Revisión del paso 5, RETORNOS *****************/


            /***************** Revisión de campos del paso 6, COLABORACIONES *****************/
            if ($step == 'supports' || $step == null) {
                foreach ($this->supports as $support) {
                    if (empty($support->support))
                        $errors['supports']['support'.$support->id] = Text::get('mandatory support field name');

                    if (empty($support->description))
                        $errors['supports']['support-description'.$support->id] = Text::get('mandatory support field description');
                }
            }
            /***************** FIN Revisión del paso 6, COLABORACIONES *****************/
        }

        /*
         * Listo para revisión
         */
        public function ready(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, updated = :updated WHERE id = :id";
				self::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$this->id));
				$this->rebase();
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para revisión. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelto al estado de edición
         */
        public function enable(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, updated = :updated WHERE id = :id";
				self::query($sql, array(':status'=>1, ':updated'=>date('Y-m-d'), ':id'=>$this->id));
				$this->rebase();
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para edición. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado de publicación
         */
        public function publish(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, published = :published WHERE id = :id";
				self::query($sql, array(':status'=>3, ':published'=>date('Y-m-d'), ':id'=>$this->id));
				$this->rebase();
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Para cambiar el id temporal a idealiza
         * solo si es md5
         */
        public function rebase() {
            try {
                if (preg_match('/^[A-Fa-f0-9]{32}$/',$this->id)) {
                    // idealizar el nombre
                    $newid = self::checkId(self::idealiza($this->name));
                    if ($newid == false) return false;
                    // actualizar las tablas relacionadas
                    self::query("UPDATE project_category SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                    self::query("UPDATE cost SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                    self::query("UPDATE reward SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                    self::query("UPDATE support SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                    // actualizar el registro
                    self::query("UPDATE project SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
                    $this->id = $newid;
                }

                return true;
            } catch (\PDOException $e) {
                throw new Goteo\Core\Exception('Fallo rebase id temporal. ' . $e->getMessage());
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
                throw new Goteo\Core\Exception('Fallo al verificar id única para el proyecto. ' . $e->getMessage());
            }
        }

        public static function daysLeft($id) {
            // días desde el published
            // si han pasado más de 40 días y no ha conseguido el mínimo, estado caducado
            // si ha alcanzado el mínimo, días desde el success
        }

        /*
         * Lista de proyectos de un usuario
         */
        public static function ofmine($owner)
        {
            $filters = array('owner'=>$owner);
            $projects = self::getAll($filters, 'name ASC');
            return $projects;
        }

        /*
         * Lista de proyectos publicados
         */
        public static function published()
        {
            $filters = array('status'=>3);
            $projects = self::getAll($filters, 'name ASC');
            return $projects;
        }

        /**
         * Saca una lista de proyectos
         *
         * @param array $filters
         * @param string $order
         * @return array or false
         */
        public static function getAll($filters = array(), $order = '') {
            $vals = array();
            $filter = "";
            foreach ($filters as $field=>$value) {
                $filter .= $filter == "" ? " WHERE" : " AND";
                if (strtolower(substr($value, 0, 2)) == 'is') {
                    $filter .= " $field " . $value;
                }
                elseif (substr($value, 0, 1) == '!') {
                    $filter .= " $field != :$field";
                    $vals[":$field"] = substr($value, 1);
                }
                else {
                    $filter .= " $field = :$field";
                    $vals[":$field"] = $value;
                }

            }

            if (!empty ($order)) {
                $order = " ORDER BY $order";
            }

			try {
                $sql = "SELECT * FROM project" . $filter . $order;
				$query = self::query($sql, $vals);
                return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
            } catch (\PDOException $e) {
				throw new Goteo\Core\Exception($e->getMessage());
            }
        }

        /*
         * Estados de desarrollo del propyecto
         */
        public static function currentStatus () {
            return array(
                1=>'Inicial',
                2=>'Medio',
                3=>'Avanzado',
                4=>'Finalizado');
        }

        /*
         * Estados de publicación de un proyecto
         */
        public static function status () {
            return array(
                1=>'Editándose',
                2=>'Pendiente valoración',
                3=>'En campaña',
                4=>'Financiado',
                5=>'Caducado',
                6=>'Retorno cumplido');
        }


        public static function daysLfet($project) {
            return 30;
        }

    }

}