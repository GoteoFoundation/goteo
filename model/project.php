<?php

namespace Goteo\Model {

    use Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Model\User,
        Goteo\Model\Message;

    class Project extends \Goteo\Core\Model {

        public
            $id = null,
            $owner, // User who created it
            $node, // Node this project belongs to
            $status,
            $progress, // puntuation %
            $amount, // Current donated amount

            $user, // owner's user information

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

            //operaciones habilitadas para el owner
            $finishable = false, // si puede mandarlo a revisar
            $enableable = false, // si puede recuperar estando caducado

            $errors = array(), // para los fallos en los datos

            $messages = array(); // mensajes de los usuarios hilos con hijos



        /**
         * Inserta un proyecto con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create ($user, $node = 'goteo', &$errors = array()) {

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
                $this->name = $values[':name'];
                $this->owner = $user;
                $this->node = $node;
                $this->status = 1;
                $this->progress = 0;

                // cargar los datos legales del usuario

                return $this->id;
            } catch (\PDOException $e) {
                $errors[] = "ERROR al crear un nuevo proyecto<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>";
                return false;
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

                // owner
                $project->user = User::get($project->owner);

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

                //-----------------------------------------------------------------
                // Diferentes verificaciones segun el estado del proyecto
                //-----------------------------------------------------------------
                //para proyectos en edición/revision
                if ($project->status < 3) {
                    //checkeamos los campos y actualizamos el progreso
                    $project->evaluate();
                    // si el progreso llega al mínimo, marcamos el finishable
                    if ($project->progress > 60)
                        $project->finishable = true;
                } else {
                    //para resto de estados
                    $project->investors = Invest::investors($project->id);

                    ////@FIXME!! estos procesos de calculo de inversión y dias lo hará el cron
                    //--------------------------------------------------------------------------------------------
                    $amount = Invest::invested($project->id);
                    if ($project->invested != $amount) {
                        self::query("UPDATE project SET amount = '{$amount}' WHERE id = ?", array($project->id));
                        $project->invested = $amount; // por ahora
                    }

                    //mensajes
                    $project->messages = Message::getAll($project->id);

                    // tiempo de campaña
                    if ($project->status == 3) {
                        $days = $project->daysActive();
                        if ($days > 40)
                            $days = 80 - $days;
                        else
                            $days = 40 - $days;

                        if ($days < 0)
                            $days = 0;

                        if ($project->days != $days) {
                            self::query("UPDATE project SET days = '{$days}' WHERE id = ?", array($project->id));
                        }
                        $project->days = $days; // por ahora
                    }
                    //--------------------------------------------------------------------------------------------
                    //@FIXME!! OJO, esto es trabajo del cron
                    /***
                    // si ha llegado a los 40 días
                    if ($days >= 40) {
                        // si no ha alcanzado el mínimo, pasa a estado caducado
                        if ($project->invested < $project->mincost) {
  //                          $project->fail();
                        } else {
                            // si ha alcanzado el mínimo tiene hasta 80 días para conseguir el óptimo
                            if ($days >= 80) {
                                // ha llegadio a los 80 dias habiendo alcanzado el mínimo
                                // (si no fuera así estaria en estado caducado y no se verificaría en este punto
//                                $project->succeed();
                            } else {
                                // ha conseguido el mínimo y sigue publicado hasta que consiga el óptimo
                                $project->days = 80 - $days;
                            }
                        }
                    } else {
                        $project->days = 40 - $days;
                    }

                    // si se ha conseguido el optimo, pasa a estado financiado
                    if ($project->invested >= $project->maxcost) {
//                        $project->succeed();
                    }
                    ****/
                    //--------------------------------------------------------------------------------------------

                }
                //-----------------------------------------------------------------
                // Fin de verificaciones
                //-----------------------------------------------------------------

				return $project;

			} catch(\PDOException $e) {
				throw \Goteo\Core\Exception($e->getMessage());
			}
		}

        /*
         *  Para validar los campos del proyecto que son NOT NULL en la tabla
         */
        public function validate(&$errors = array()) {

            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'El proyecto no tiene id';

            if (empty($this->name))
                $errors[] = 'El proyecto no tiene nombre';
            
            if (empty($this->status))
                $this->status = 1;
            
            if (empty($this->progress))
                $this->progress = 0;
            
            if (empty($this->owner))
                $errors[] = 'El proyecto no tiene usuario creador';
            
            if (empty($this->node))
                $this->node = 'goteo';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }


        // check solo comprueba errores de datos que no evita que grabe
        public function check() {
            $errors = &$this->errors;
            //los siguientes permiten guardar
            /***************** Revisión de campos del paso 1, PERFIL *****************/
            $user = User::get($this->owner);
            $user->validate($errors['userProfile']);
            /***************** FIN Revisión del paso 1, PERFIL *****************/

            /***************** Revisión de campos del paso 2,DATOS PERSONALES *****************/
            if (empty($this->contract_name))
                $errors['userPersonal']['contract_name'] = Text::get('mandatory-project-field-contract-name');

            if (empty($this->contract_surname))
                $errors['userPersonal']['contract_surname'] = Text::get('mandatory-project-field-contract-surname');

            if (empty($this->contract_nif))
                $errors['userPersonal']['contract_nif'] = Text::get('mandatory-project-field-contract-nif');
            elseif (!Check::Nif($this->contract_nif))
                $errors['userPersonal']['contract_nif'] = Text::get('validate-project-value-contract-nif');

            if (empty($this->contract_email))
                $errors['userPersonal']['contract_email'] = Text::get('mandatory-project-field-contract-email');
            elseif (!Check::Mail($this->contract_email))
                $errors['userPersonal']['contract_email'] = Text::get('validate-project-value-contract-email');

            if (empty($this->phone))
                $errors['userPersonal']['phone'] = Text::get('mandatory-project-field-phone');
            elseif (!Check::Phone($this->phone))
                $errors['userPersonal']['phone'] = Text::get('validate-project-value-phone');

            if (empty($this->address))
                $errors['userPersonal']['address'] = Text::get('mandatory-project-field-address');

            if (empty($this->zipcode))
                $errors['userPersonal']['zipcode'] = Text::get('mandatory-project-field-zipcode');

            if (empty($this->location))
                $errors['userPersonal']['location'] = Text::get('mandatory-project-field-residence');

            if (empty($this->country))
                $errors['userPersonal']['country'] = Text::get('mandatory-project-field-country');
            /***************** FIN Revisión del paso 2, DATOS PERSONALES *****************/

            /***************** Revisión de campos del paso 3, DESCRIPCION *****************/
            if (empty($this->name))
                $errors['overview']['name'] = Text::get('mandatory-project-field-name');

            if (empty($this->image))
                $errors['overview']['image'] = Text::get('mandatory-project-field-image');

            if (empty($this->description))
                $errors['overview']['description'] = Text::get('mandatory-project-field-description');
            elseif (!Check::Words($this->description, 150))
                $errors['overview']['description'] = Text::get('validate-project-value-description');

            if (empty($this->motivation))
                $errors['overview']['motivation'] = Text::get('mandatory-project-field-motivation');

//             if (empty($this->about))
//                $errors['overview']['about'] = Text::get('mandatory-project-field-about');

            if (empty($this->goal))
                $errors['overview']['goal'] = Text::get('mandatory-project-field-goal');

            if (empty($this->related))
                $errors['overview']['related'] = Text::get('mandatory-project-field-related');

            if (empty($this->categories))
                $errors['overview']['categories'] = Text::get('mandatory-project-field-category');

            if (empty($this->media))
                $errors['overview']['media'] = Text::get('mandatory-project-field-media');

            $keywords = explode(',', $this->keywords);
            if ($keywords < 5)
                $errors['overview']['keywords'] = Text::get('validate-project-value-keywords');

            if (empty($this->currently))
                $errors['overview']['currently'] = Text::get('validate-project-field-currently');

            if (empty($this->project_location))
                $errors['overview']['project_location'] = Text::get('mandatory-project-field-location');
            /***************** FIN Revisión del paso 3, DESCRIPCION *****************/

            /***************** Revisión de campos del paso 4, COSTES *****************/
            if (count($this->costs) < 2)
                $errors['costs']['costs'] = Text::get('mandatory-project-costs');
            elseif (count($this->costs) < 5)
                $errors['costs']['costs'] = Text::get('validate-project-field-costs');

            foreach($this->costs as $cost) {
                if (empty($cost->cost))
                    $errors['costs']['cost-'.$cost->id.'-cost'] = Text::get('mandatory-cost-field-name');

                if (empty($cost->description))
                    $errors['costs']['cost-'.$cost->id.'-description'] = Text::get('mandatory-cost-field-description');

                if (empty($cost->from) || empty($cost->until))
                    $errors['costs']['cost-'.$cost->id.'-dates'] = Text::get('validate-cost-field-dates');
            }

            $costdif = $this->maxcost - $this->mincost;
            $maxdif = $this->mincost * 0.40;
            if ($costdif > $maxdif )
                $errors['costs']['total-costs'] = Text::get('validate-project-total-costs');

            if (empty($this->resource))
                $errors['costs']['resource'] = Text::get('mandatory-project-field-resource');
            /***************** FIN Revisión del paso 4, COSTES *****************/

            /***************** Revisión de campos del paso 5, RETORNOS *****************/
            if (count($this->social_rewards) < 5)
                $errors['rewards']['social_rewards'] = Text::get('validate-project-social_rewards');

            if (count($this->individual_rewards) < 5)
                $errors['rewards']['individual_rewards'] = Text::get('validate-project-individual_rewards');

            foreach ($this->social_rewards as $social) {
                if (empty($social->reward))
                    $errors['rewards']['social_reward-'.$social->id.'reward'] = Text::get('mandatory-social_reward-field-name');

                if (empty($social->description))
                    $errors['rewards']['social_rewards-'.$social->id.'-description'] = Text::get('mandatory-social_reward-field-description');

                if (empty($social->license))
                    $errors['rewards']['social_reward-'.$social->id.'-license'] = Text::get('validate-social_reward-license');
            }

            foreach ($this->individual_rewards as $individual) {
                if (empty($individual->reward))
                    $errors['rewards']['individual_reward-'.$individual->id.'-reward'] = Text::get('mandatory-individual_reward-field-name');

                if (empty($individual->description))
                    $errors['rewards']['individual_reward-'.$individual->id.'-description'] = Text::get('mandatory-individual_reward-field-description');

                if (empty($individual->reward))
                    $errors['rewards']['individual_reward-'.$individual->id.'-amount'] = Text::get('mandatory-individual_reward-field-amount');
            }
            /***************** FIN Revisión del paso 5, RETORNOS *****************/


            /***************** Revisión de campos del paso 6, COLABORACIONES *****************/
            foreach ($this->supports as $support) {
                if (empty($support->support))
                    $errors['supports']['support-'.$support->id.'-support'] = Text::get('mandatory-support-field-name');

                if (empty($support->description))
                    $errors['supports']['support-'.$support->id.'-description'] = Text::get('mandatory-support-field-description');
            }
            /***************** FIN Revisión del paso 6, COLABORACIONES *****************/

            return true;
        }

        /**
         * actualiza en la tabla los datos del proyecto
         * @param array $project->errors para guardar los errores de datos del formulario, los errores de proceso se guardan en $project->errors['process']
         */
        public function save (&$errors = array()) {
            if(!$this->validate($errors)) { return false; }

  			try {
                // fail para pasar por todo antes de devolver false
                $fail = false;

                // nif y telefono sin guiones, espacios ni puntos
                $this->contract_nif = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->contract_nif);
                $this->phone = str_replace(array('_', '.', ' ', '-', ',', ')', '('), '', $this->phone);

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
				if (!self::query($sql, $values)) {
                    $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                    $fail = true;
                }

//                echo "$sql<br />";
                // y aquí todas las tablas relacionadas
                // cada una con sus save, sus new y sus remove
                // quitar las que tiene y no vienen
                // añadir las que vienen y no tiene

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
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
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
                    $this->costs = Project\Cost::getAll($this->id);

                //retornos colectivos
				$tiene = Project\Reward::getAll($this->id, 'social');
                $viene = $this->social_rewards;
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
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
                $quita = array_diff_assoc($tiene, $viene);
                $guarda = array_diff_assoc($viene, $tiene);
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
                return false;
			}

        }

        // metodo para calcular el % de progreso
        public function evaluate ()
        {
            //primero resetea los errores
            $this->errors = self::blankErrors();
            // y los checkea de nuevo
            $this->check();

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

            // Para que no lleguen al 100 le añadimos 20 al maximo
            $max += 20;

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
         * cualquier campo incorrecto lo guarda en badfields y en badmessages
         * luego se consulta para pintar el checkbox lateral o los errores
         * También cuenta la puntuación del 1 al 100 para el proyecto y lo guarda
         */

        /*
         * Listo para revisión
         */
        public function ready(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, updated = :updated WHERE id = :id";
				self::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$this->id));
				$this->rebase(); // solo cuando termina la edición
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
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado caducado
         */
        public function fail(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>5, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Financiado
         */
        public function succeed(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, success = :success WHERE id = :id";
				self::query($sql, array(':status'=>4, ':success'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar por financiado el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Retorno cumplido
         */
        public function satisfied(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>6, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar el retorno por cunplido para el proyecto. ' . $e->getMessage();
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

        public function daysActive() {
            // días desde el published
            $sql = "
                SELECT DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%e') as days
                FROM project
                WHERE id = ?";
            $query = self::query($sql, array($this->id));
            $past = $query->fetchObject();

            return $past->days - 1;
        }

        /*
         * Lista de proyectos de un usuario
         */
        public static function ofmine($owner)
        {
            $sql = "SELECT * FROM project WHERE owner = ? ORDER BY name ASC";
            $query = self::query($sql, array($owner));
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        /*
         * Lista de proyectos publicados
         */
        public static function published()
        {
            $projects = array();
            $query = self::query("SELECT id FROM project WHERE status = 3 ORDER BY name ASC");
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = self::get($proj['id']);
            }
            return $projects;
        }

        /*
         * Lista de proyectos cofinanciados
         */
        public static function invested()
        {
            $projects = array();
            $query = self::query("SELECT *
                                  FROM  project
                                  WHERE project.status = 3 OR project.status = 4
                                  AND project.id IN (SELECT DISTINCT(project) FROM invest)
                                  ORDER BY name ASC");
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $projects[] = $proj;
            }
            return $projects;
        }

        /**
         * Saca una lista completa de proyectos para la revisión
         *
         * @param string node id
         * @return array of project instances
         */
        public static function getList($node = 'goteo') {
            $projects = array();
            $query = self::query("SELECT id FROM project WHERE node = ? ORDER BY progress DESC", array($node));
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = self::get($proj['id']);
            }
            return $projects;
        }



        /*
         *  getAll obsoleta
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
         *
         */

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


        public static function blankErrors() {
            // para guardar los fallos en los datos
            $errors = array(
                'userProfile'  => array(),  // Errores en el paso 1
                'userPersonal' => array(),  // Errores en el paso 2
                'overview'     => array(),  // Errores en el paso 3
                'costs'        => array(),  // Errores en el paso 4
                'rewards'      => array(),  // Errores en el paso 5
                'supports'     => array()   // Errores en el paso 6
            );

            return $errors;
        }
    }

}