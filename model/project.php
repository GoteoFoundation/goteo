<?php

namespace Goteo\Model {

    use Goteo\Core\ACL,
        Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Model\User,
        Goteo\Model\Image,
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
            $contract_name, // Nombre y apellidos
            $contract_nif, // Guardar sin espacios ni puntos ni guiones
            $phone, // guardar sin espacios ni puntos
            $address,
            $zipcode,
            $location, // owner's location
            $country,

            // Edit project description
            $name,
            $image,
            $gallery = array(), // array de instancias image de project_image
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
            $scope,  // ambito de alcance

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

            $round = 0, // para ver si ya está en la fase de los 40 a los 80

            $errors = array(), // para los fallos en los datos
            $okeys  = array(), // para los campos que estan ok

            // para puntuacion
            $score = 0, //puntos
            $max = 0, // maximo de puntos

            $messages = array(), // mensajes de los usuarios hilos con hijos

            $finishable = false; // llega al progresso mínimo para enviar a revision



        /**
         * Inserta un proyecto con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */
        public function create ($node = \GOTEO_NODE, &$errors = array()) {

            $user = $_SESSION['user']->id;

            if (empty($user)) {
                return false;
            }
            
            // cojemos el número de proyecto de este usuario
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
                ':name' => "Mi proyecto $num",
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
                $errors[] = "ERROR al crear un nuevo proyecto<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>";
                \trace($this);
                die($errors[0]);
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

                if (isset($project->media)) {
                    $project->media = new Project\Media($project->media);
                }

                // owner
                $project->user = User::get($project->owner);

                // imagen
                $project->image = Image::get($project->image);

                // galeria
                $project->gallery = Image::getAll($project->id, 'project');

				// categorias
                $project->categories = Project\Category::get($id);

				// costes y los sumammos
				$project->costs = Project\Cost::getAll($id);

                $project->minmax();

				// retornos colectivos
				$project->social_rewards = Project\Reward::getAll($id, 'social');
				// retornos individuales
				$project->individual_rewards = Project\Reward::getAll($id, 'individual');

				// colaboraciones
				$project->supports = Project\Support::getAll($id);

                //-----------------------------------------------------------------
                // Diferentes verificaciones segun el estado del proyecto
                //-----------------------------------------------------------------
                //para proyectos en campaña o posterior
                if ($project->status > 2) {
                    // recompensas
                    foreach ($project->individual_rewards as &$reward) {
                        $reward->none = false;
                        // si controla unidades de esta recompensa, mirar si quedan
                        if ($reward->units > 0) {
                            $reward->taken = $reward->getTaken();
                            if ($reward->taken >= $reward->units) {
                                $reward->none = true;
                            }
                        }
                    }

                    $project->investors = Invest::investors($project->id);

                    $amount = Invest::invested($project->id);
                    if ($project->invested != $amount) {
                        self::query("UPDATE project SET amount = '{$amount}' WHERE id = ?", array($project->id));
                    }
                    $project->invested = $amount;

                    //mensajes
                    $project->messages = Message::getAll($project->id);

                    // tiempo de campaña
                    if ($project->status == 3) {
                        $days = $project->daysActive();
                        if ($days > 40) {
                            $days = 80 - $days;
                            $project->round = 2;
                        } else {
                            $days = 40 - $days;
                            $project->round = 1;
                        }

                        if ($days < 0) {
                            // no deberia estar en campaña sino financuiado o caducado
                            $days = 0;
                        }

                        if ($project->days != $days) {
                            self::query("UPDATE project SET days = '{$days}' WHERE id = ?", array($project->id));
                        }
                        $project->days = $days;
                    } else {
                        $project->days = 0;
                    }
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
         *  Cargamos los datos mínimos de un proyecto
         */
        public static function getMini($id) {

            try {
				// metemos los datos del proyecto en la instancia
				$query = self::query("SELECT id, name, owner, comment FROM project WHERE id = ?", array($id));
				$project = $query->fetchObject(); // stdClass para qno grabar accidentalmente y machacar todo

                // owner
                $project->user = User::getMini($project->owner);

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
                //Text::get('validate-project-noid');

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
            if (!empty($errors))
                return false;
            else
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

                // Image
                if (is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);
                    $image->save();
                    $this->gallery[] = $image;
                    $this->image = $image->id;

                    /**
                     * Guarda la relación NM en la tabla 'project_image'.
                     */
                    if(!empty($image->id)) {
                        self::query("REPLACE project_image (project, image) VALUES (:project, :image)", array(':project' => $this->id, ':image' => $image->id));
                    }
                }

                $fields = array(
                    'contract_name',
                    'contract_nif',
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
         * comprueba errores de datos y actualiza la puntuación
         */
        public function check() {
            //primero resetea los errores y los okeys
            $this->errors = self::blankErrors();
            $this->okeys  = self::blankErrors();

            $errors = &$this->errors;
            $okeys  = &$this->okeys ;

            // reseteamos la puntuación
            $this->setScore(0, 0, true);

            /***************** Revisión de campos del paso 1, PERFIL *****************/
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

            if(!empty($this->user->avatar)) {
                $okeys['userProfile']['avatar'] = 'ok';
                $score+=2;
            }

            if (!empty($this->user->about)) {
                $okeys['userProfile']['about'] = 'ok';
                ++$score;
                // otro +1 si tiene más de 1000 caracteres
                if (\strlen($this->user->about) > 1000) {
                    ++$score;
                }
                // además error si tiene más de 2000
                if (\strlen($this->user->about) > 2000) {
                    $errors['userProfile']['about'] = Text::get('validate-user-field-about');
                    unset($okeys['userProfile']['about']);
                }
            }

            if (!empty($this->user->keywords)) {
                $okeys['userProfile']['keywords'] = 'ok';
                ++$score;
            }

            if (!empty($this->user->contribution)) {
                $okeys['userProfile']['contribution'] = 'ok';
                ++$score;
            }

            if (!empty($this->user->interests)) {
                $okeys['userProfile']['interests'] = 'ok';
                ++$score;
            }

            if (!empty($this->user->webs)) {
                $okeys['userProfile']['webs'] = 'ok';
                ++$score;
                if (count($this->user->webs) > 2) ++$score;
            }

            if (!empty($this->user->facebook)) {
                $okeys['userProfile']['facebook'] = 'ok';
                ++$score;
                // if amigos > 1000 ++$score;
            }

            if (!empty($this->user->twitter)) {
                $okeys['userProfile']['twitter'] = 'ok';
                ++$score;
                // if followers > 1000 ++$score;
                // if listed > 100 ++$score;
            }

            if (!empty($this->user->linkedin)) {
                $okeys['userProfile']['linkedin'] = 'ok';
                // if contacts > 250 $score+=2;
            }

            //puntos
            $this->setScore($score, 19);
            /***************** FIN Revisión del paso 1, PERFIL *****************/

            /***************** Revisión de campos del paso 2,DATOS PERSONALES *****************/
            $score = 0;
            // obligatorios: todos
            if (empty($this->contract_name)) {
                $errors['userPersonal']['contract_name'] = Text::get('mandatory-project-field-contract-name');
            } else {
                 $okeys['userPersonal']['contract_name'] = 'ok';
                 ++$score;
            }

            if (empty($this->contract_nif)) {
                $errors['userPersonal']['contract_nif'] = Text::get('mandatory-project-field-contract-nif');
            } elseif (!Check::nif($this->contract_nif)) {
                $errors['userPersonal']['contract_nif'] = Text::get('validate-project-value-contract-nif');
            } else {
                 $okeys['userPersonal']['contract_nif'] = 'ok';
                 ++$score;
            }

            if (empty($this->phone)) {
                $errors['userPersonal']['phone'] = Text::get('mandatory-project-field-phone');
            } elseif (!Check::phone($this->phone)) {
                $errors['userPersonal']['phone'] = Text::get('validate-project-value-phone');
            } else {
                 $okeys['userPersonal']['phone'] = 'ok';
                 ++$score;
            }

            if (empty($this->address)) {
                $errors['userPersonal']['address'] = Text::get('mandatory-project-field-address');
            } else {
                 $okeys['userPersonal']['address'] = 'ok';
                 ++$score;
            }

            if (empty($this->zipcode)) {
                $errors['userPersonal']['zipcode'] = Text::get('mandatory-project-field-zipcode');
            } else {
                 $okeys['userPersonal']['zipcode'] = 'ok';
                 ++$score;
            }

            if (empty($this->location)) {
                $errors['userPersonal']['location'] = Text::get('mandatory-project-field-residence');
            } else {
                 $okeys['userPersonal']['location'] = 'ok';
            }

            if (empty($this->country)) {
                $errors['userPersonal']['country'] = Text::get('mandatory-project-field-country');
            } else {
                 $okeys['userPersonal']['country'] = 'ok';
                 ++$score;
            }

            $this->setScore($score, 6);
            /***************** FIN Revisión del paso 2, DATOS PERSONALES *****************/

            /***************** Revisión de campos del paso 3, DESCRIPCION *****************/
            $score = 0;
            // obligatorios: nombre, imagen, descripcion, about, motivation, categorias, video, localización
            if (empty($this->name)) {
                $errors['overview']['name'] = Text::get('mandatory-project-field-name');
            } else {
                 $okeys['overview']['name'] = 'ok';
                 ++$score;
            }

            if (empty($this->gallery)) {
                $errors['overview']['image'] = Text::get('mandatory-project-field-image');
            } else {
                 $okeys['overview']['image'] = 'ok';
                 ++$score;
                 if (count($this->gallery) >= 2) ++$score;
            }

            if (empty($this->description)) {
                $errors['overview']['description'] = Text::get('mandatory-project-field-description');
            } elseif (!Check::words($this->description, 150)) {
                 $errors['overview']['description'] = Text::get('validate-project-field-description');
            } else {
                 $okeys['overview']['description'] = 'ok';
                 ++$score;
            }

            if (empty($this->about)) {
                $errors['overview']['about'] = Text::get('mandatory-project-field-about');
             } else {
                 $okeys['overview']['about'] = 'ok';
                 ++$score;
                 /*
                 if (\strlen($this->about) > 2000) {
                     $errors['overview']['about'] = Text::get('validate-project-field-about');
                 }
                  * 
                  */
            }

            if (empty($this->motivation)) {
                $errors['overview']['motivation'] = Text::get('mandatory-project-field-motivation');
            } else {
                 $okeys['overview']['motivation'] = 'ok';
                 ++$score;
                 if (\strlen($this->motivation) > 2000) {
                     $errors['overview']['motivation'] = Text::get('validate-project-field-motivation');
                 }
            }

            if (!empty($this->goal))  {
                 $okeys['overview']['goal'] = 'ok';
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

            if (!empty($this->keywords)) {
                 $okeys['overview']['keywords'] = 'ok';
                 ++$score;
            }

            if (empty($this->media)) {
                $errors['overview']['media'] = Text::get('mandatory-project-field-media');
            } else {
                 $okeys['overview']['media'] = 'ok';
                 $score+=3;
            }

            if (!empty($this->currently)) {
                 $okeys['overview']['currently'] = 'ok';
                 ++$score;
                 if ($this->currently == 2 || $this->currently == 3) ++$score;
            }

            if (empty($this->project_location)) {
                $errors['overview']['project_location'] = Text::get('mandatory-project-field-location');
            } else {
                 $okeys['overview']['project_location'] = 'ok';
                 ++$score;
            }

            if (!empty($this->scope)) {
                 $okeys['overview']['scope'] = 'ok';
            }

            $this->setScore($score, 18);
            /***************** FIN Revisión del paso 3, DESCRIPCION *****************/

            /***************** Revisión de campos del paso 4, COSTES *****************/
            $score = 0; $scoreName = $scoreDesc = $scoreAmount = $scoreDate = 0;
            if (empty($this->costs)) {
                $errors['costs']['costs'] = Text::get('mandatory-project-costs');
            } else {
                if (count($this->costs) >= 2) {
                    ++$score;
                }
            }

            foreach($this->costs as $cost) {
                if (empty($cost->cost)) {
                    $errors['costs']['cost-'.$cost->id.'-cost'] = Text::get('mandatory-cost-field-name');
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-cost'] = 'ok';
                     $scoreName = 1;
                }

                if (empty($cost->type)) {
                    $errors['costs']['cost-'.$cost->id.'-type'] = Text::get('mandatory-cost-field-type');
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-type'] = 'ok';
                }

                if (!empty($cost->description)) {
                     $okeys['costs']['cost-'.$cost->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if (empty($cost->amount)) {
                    $errors['costs']['cost-'.$cost->id.'-amount'] = Text::get('mandatory-cost-field-amount');
                } else {
                     $okeys['costs']['cost-'.$cost->id.'-amount'] = 'ok';
                     $scoreAmount = 1;
                }

                if ($cost->type == 'task' && !empty($cost->from) && !empty($cost->until)) {
                    $scoreDate = 1;
                }

                if (isset($cost->required)) {
                     $okeys['costs']['cost-'.$cost->id.'-required'] = 'ok';
                }
            }

            $score = $score + $scoreName + $scoreDesc + $scoreAmount + $scoreDate;

            $costdif = $this->maxcost - $this->mincost;
            $maxdif = $this->mincost * 0.40;
            $scoredif = $this->mincost * 0.35;
            if ($costdif > $maxdif ) {
                $errors['costs']['total-costs'] = Text::get('validate-project-total-costs');
            }
            if ($costdif <= $scoredif ) {
                ++$score;
            }

            if (!empty($this->resource)) {
                 $okeys['costs']['resource'] = 'ok';
                 ++$score;
            }

            $this->setScore($score, 7);
            /***************** FIN Revisión del paso 4, COSTES *****************/

            /***************** Revisión de campos del paso 5, RETORNOS *****************/
            $score = 0; $scoreName = $scoreDesc = $scoreAmount = $scoreLicense = 0;
            if (empty($this->social_rewards)) {
                $errors['rewards']['social_rewards'] = Text::get('validate-project-social_rewards');
            } else {
                 if (count($this->social_rewards) >= 2) {
                     ++$score;
                 }
            }

            if (!empty($this->individual_rewards) && count($this->individual_rewards) >= 3) {
                 ++$score;
            }

            foreach ($this->social_rewards as $social) {
                if (empty($social->reward)) {
                    $errors['rewards']['social_reward-'.$social->id.'reward'] = Text::get('mandatory-social_reward-field-name');
                } else {
                     $okeys['rewards']['social_reward-'.$social->id.'reward'] = 'ok';
                     $scoreName = 4;
                }

                if (empty($social->description)) {
                    $errors['rewards']['social_rewards-'.$social->id.'-description'] = Text::get('mandatory-social_reward-field-description');
                } else {
                     $okeys['rewards']['social_rewards-'.$social->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if (empty($social->icon)) {
                    $errors['rewards']['social_rewards-'.$social->id.'-icon'] = Text::get('mandatory-social_reward-field-description');
                } else {
                     $okeys['rewards']['social_rewards-'.$social->id.'-icon'] = 'ok';
                }

                if (!empty($social->license)) {
                    $scoreLicense = 1;
                    /*
                     * Si elige de las mas abiertas
                    if ($license_group == 1) {
                        ++$score;
                    }
                     *
                     */
                }
            }
            
            $score = $score + $scoreName + $scoreDesc + $scoreLicense;
            $scoreName = $scoreDesc = 0;

            foreach ($this->individual_rewards as $individual) {
                if (!empty($individual->reward)) {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-reward'] = 'ok';
                     $scoreName = 1;
                }

                if (!empty($individual->description)) {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-description'] = 'ok';
                     $scoreDesc = 1;
                }

                if (!empty($individual->amount)) {
                     $okeys['rewards']['individual_reward-'.$individual->id.'-amount'] = 'ok';
                     $scoreAmount = 1;
                }
            }

            $score = $score + $scoreName + $scoreDesc + $scoreAmount;
            $this->setScore($score, 12);
            /***************** FIN Revisión del paso 5, RETORNOS *****************/

            /***************** Revisión de campos del paso 6, COLABORACIONES *****************/
            $scorename = $scoreDesc = 0;
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
            $this->setScore($score, 2);
            /***************** FIN Revisión del paso 6, COLABORACIONES *****************/

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
                $progress > 60 &&
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
         */
        public function ready(&$errors = array()) {
			try {
				$this->rebase();

                $sql = "UPDATE project SET status = :status, updated = :updated WHERE id = :id";
                self::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$this->id));
                
                return true;
                
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para revisión. ' . $e->getMessage();
                //Text::get('send-project-review-fail');
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
                //Text::get('send-project-reedit-fail');
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

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar el proyecto. ' . $e->getMessage();
                //Text::get('send-project-publish-fail');
                return false;
            }
        }

        /*
         * Cambio a estado canecelado
         */
        public function cancel(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>0, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                //Text::get('send-projecct-close-fail');
                return false;
            }
        }

        /*
         * Cambio a estado caducado
         */
        public function fail(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status, closed = :closed WHERE id = :id";
				self::query($sql, array(':status'=>6, ':closed'=>date('Y-m-d'), ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                //Text::get('send-projecct-close-fail');
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
                //Text::get('send-project-success-fail');
                return false;
            }
        }

        /*
         * Cambio a estado Retorno cumplido
         */
        public function satisfied(&$errors = array()) {
			try {
				$sql = "UPDATE project SET status = :status WHERE id = :id";
				self::query($sql, array(':status'=>5, ':id'=>$this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar el retorno por cunplido para el proyecto. ' . $e->getMessage();
                //Text::get('send-project-fulfill-fail');
                return false;
            }
        }

        /*
         * Si no se pueden borrar todos los registros, estado cero para que lo borre el cron
         */
        public function delete(&$errors = array()) {

            if ($this->status != 1) {
                return false;
            }

            self::query("START TRANSACTION");
            try {
                //borrar todos los registros
                self::query("DELETE FROM project_category WHERE project = ?", array($this->id));
                self::query("DELETE FROM cost WHERE project = ?", array($this->id));
                self::query("DELETE FROM reward WHERE project = ?", array($this->id));
                self::query("DELETE FROM support WHERE project = ?", array($this->id));
                self::query("DELETE FROM image WHERE id IN (SELECT image FROM project_image WHERE project = ?)", array($this->id));
                self::query("DELETE FROM project_image WHERE project = ?", array($this->id));
                self::query("DELETE FROM message WHERE project = ?", array($this->id));
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
                    
                    // actualizar las tablas relacionadas en una transacción
                    $fail = false;
                    if (self::query("START TRANSACTION")) {
                        try {
                            self::query("UPDATE project_category SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE cost SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE reward SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE support SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project_image SET project = :newid WHERE project = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            self::query("UPDATE project SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
                            // borro los permisos, el dashboard los creará de nuevo
                            self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));

                            // si todo va bien, commit y cambio el id de la instancia
                            self::query("COMMIT");
                            $this->id = $newid;
                            return true;

                        } catch (\PDOException $e) {
                            self::query("ROLLBACK");
                            return false;
                        }
                    } else {
                        throw new Goteo\Core\Exception('Fallo al iniciar transaccion rebase. ' . \trace($e));
                    }
                }

                return true;
            } catch (\PDOException $e) {
                throw new Goteo\Core\Exception('Fallo rebase id temporal. ' . \trace($e));
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



        public function daysActive() {
            // días desde el published
            $sql = "
                SELECT DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%j') as days
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
            $projects = array();

            $sql = "SELECT * FROM project WHERE status > 0 AND owner = ? ORDER BY name ASC";
            $query = self::query($sql, array($owner));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $projects[] = self::get($proj->id);
            }
            
            return $projects;
        }

        /*
         * Lista de proyectos publicados
         */
        public static function published($type = 'all', $limit = null)
        {
            // segun el tipo (ver controller/discover.php)
            switch ($type) {
                case 'popular':
                    // de los que estan en campaña,
                    // los que tienen más usuarios (unicos) cofinanciadores y mensajeros
                    $sql = "SELECT COUNT(DISTINCT(user.id)) as people, project.id as id
                            FROM project
                            LEFT JOIN invest
                                ON invest.project = project.id
                                AND invest.status <> 2
                            LEFT JOIN message
                                ON message.project = project.id
                            LEFT JOIN user 
                                ON user.id = invest.user OR user.id = message.user
                            WHERE project.status= 3 
                            AND (project.id = invest.project
                                OR project.id = message.project)
                            GROUP BY project.id
                            ORDER BY people DESC";
                    break;
                case 'outdate':
                    // los que les quedan 15 dias o menos
                    $sql = "SELECT  id
                            FROM    project
                            WHERE   days <= 15
                            AND     days > 0
                            AND     status = 3
                            ORDER BY days ASC";
                    break;
                case 'recent':
                    // los que llevan menos tiempo desde el published, hasta 15 dias
                    $sql = "SELECT 
                                project.id as id,
                                DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%e') as day
                            FROM project
                            WHERE project.status = 3
                            HAVING day <= 15 AND day IS NOT NULL
                            ORDER BY day DESC";
                    break;
                case 'success':
                    // los que estan 'financiado' o 'retorno cumplido'
                    $sql = "SELECT id FROM project WHERE status = 4 OR status = 5 ORDER BY name ASC";
                    break;
                case 'available':
                    // ni edicion ni revision ni cancelados, estan disponibles para verse publicamente
                    $sql = "SELECT id FROM project WHERE status > 2 AND status < 6 ORDER BY name ASC";
                    break;
                default: 
                    // todos los que estan 'en campaña'
                    $sql = "SELECT id FROM project WHERE status = 3 ORDER BY name ASC";
            }

            // Limite
            if (!empty($limit) && \is_numeric($limit)) {
                $sql .= " LIMIT $limit";
            }

            $projects = array();
            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = self::get($proj['id']);
            }
            return $projects;
        }

        /*
         * Lista de proyectos cofinanciados
         */
        public static function invested($user = null)
        {
            // si recibimos un usuario, sacamos los que haya invertido ese usuario
            if (!empty($user)) {
                $he = " WHERE user='$user'";
            } else {
                $he = '';
            }

            $projects = array();
            $query = self::query("SELECT *
                                  FROM  project
                                  WHERE project.status = 3 OR project.status = 4
                                  AND project.id IN (SELECT DISTINCT(project) FROM invest$he)
                                  ORDER BY name ASC");
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $projects[] = self::get($proj->id);
            }
            return $projects;
        }

        /**
         * Saca una lista completa de proyectos
         *
         * @param string node id
         * @return array of project instances
         */
        public static function getList($filters = array(), $node = \GOTEO_NODE) {
            $projects = array();

            $sqlFilter = "";
            if (!empty($filters['status'])) {
                $sqlFilter .= " AND status = " . $filters['status'];
            }
            if (!empty($filters['category'])) {
                $sqlFilter .= " AND id IN (
                    SELECT project
                    FROM project_category
                    WHERE category = {$filters['category']}
                    )";
            }

            $sql = "SELECT 
                        id
                    FROM project
                    WHERE status > 0
                        AND node = ?
                        $sqlFilter
                    ORDER BY progress DESC
                    ";

            $query = self::query($sql, array($node));
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = self::get($proj['id']);
            }
            return $projects;
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
//                0=>Text::get('form-project_status-cancelled'),
                1=>Text::get('form-project_status-edit'),
                2=>Text::get('form-project_status-review'),
                3=>Text::get('form-project_status-campaing'),
                4=>Text::get('form-project_status-success'),
                5=>Text::get('form-project_status-fulfilled'),
                6=>Text::get('form-project_status-expired'));
        }

        /*
         * Siguiente etapa en la vida del proyeto
         */
        public static function waitfor () {
            return array(
                0=>Text::get('form-project_waitfor-cancel'),
                1=>Text::get('form-project_waitfor-edit'),
                2=>Text::get('form-project_waitfor-review'),
                3=>Text::get('form-project_waitfor-campaing'),
                4=>Text::get('form-project_waitfor-success'),
                5=>Text::get('form-project_waitfor-fulfilled'),
                6=>Text::get('form-project_waitfor-expired'));
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