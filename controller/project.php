<?php

namespace Goteo\Controller {
    
    use Goteo\Core\Error,
		Goteo\Library\Text,
        Goteo\Model;
    
    class Project extends \Goteo\Core\Controller {

		public function index ($id = null) {
            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

                if (!$project->id) {
                    throw new Error(404);
                }
                else {
					include 'view/project/public.html.php';
				}

            }
		}

        public function manage ($id = null) {
            Model\User::restrict();

            if (!$id) {
				header('Location: /');
				die;
            } else {
				$_SESSION['current_project'] = $id;
				header('Location: /project/edit');
				die;
            }
        }

		/*
		 * Paso cero de nuevo proyecto
		 * @TODO : de nuevo el usuario no deberia llegar por la url sino por la session
		 * pero aun no tenemos la validación de usuario...
		 */
        public function create () {
            Model\User::restrict();

			$user = $_SESSION['user'];
			$user = unserialize(serialize($user));

            if (!$user) {
				header('Location: /');
				die;
            } else {
                $project = new Model\Project();

                if ($project->create($user->id)) {
					$_SESSION['current_project'] = $project->id;
					header('Location: /project/user/');
					die;
				}
				else {
					echo 'ERROR al crear el proyecto';
					die;
				}

            }

        }



		/*
		 * Paso 1 - PERFIL
		 */
        public function user () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];
			$user = $_SESSION['user'];
			$user = unserialize(serialize($user));

            if (!$id || !$userid) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				$user = Model\User::get($user->id);
/*
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					if ($user->save($_POST, $errors)) {
						header('Location: /project/register');
						die;
					}
				}
*/

			$guideText = Text::get('guide project user information');
            include 'view/project/user.html.php';

			}

        }

		/*
		 * Paso 2 - DATOS PERSONALES
		 */
        public function register () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				if (isset($_POST['submit'])) {

					// campos que guarda este paso
					$fields = array(
						'contract_name',
						'contract_surname',
						'contract_nif',
						'contract_email',
						'phone',
						'address',
						'zipcode',
						'location',
						'country'
						);

					foreach ($fields as $field) {
						$project->$field = $_POST[$filed];
					}

					$errors = array();
					$project->save($errors);
				}

				$guideText = Text::get('guide project contract information');
				include 'view/project/register.html.php';
			}
        }
        
		/*
		 * Paso 3 - DESCRIPCIÓN
		 */
        public function edit () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				if (isset($_POST['submit'])) {

					$errors = array();

					// campos que guarda este paso
					$fields = array(
						'name',
						'image',
						'description',
						'motivation',
						'about',
						'goal',
						'related',
						'category',
						'media',
						'currently',
						'project_location'
						);

					foreach ($fields as $field) {
						$project->$field = $_POST[$field];
					}

					$project->save($errors);
					
					//tratar imagen

					//tratar keywords
					if (!empty($_POST['keywords'])) {
						$keys = explode(',', $_POST['keywords']);
						foreach ($keys as $key) {
							$keyword = new Model\Project\Keyword();

							$keyword->id = '';
							$keyword->project = $id;
							$keyword->keyword = $key;

							$keyword->save($errors);
							
							$project->keywords[] = $keyword;
						}
					}

					//remove keywords
					foreach ($_POST as $key=>$value) {
						if (substr($key, 0, strlen('remove-keyword')) == 'remove-keyword' && !empty($value)) {
						echo "$key: $value<br />";
							$keyword = Model\Project\Keyword::get($value);

							if (!empty($keyword)) {
								echo 'eliminada clave ' . $value;
								$keyword->remove($errors);
								unset($keyword);
							}
						}
					}

				}

				$currently = Model\Project::currentStatus();
				$category  = Model\Project::categories();

				$guideText = Text::get('guide project description');
				include 'view/project/edit.html.php';

			}

        }

		/*
		 * Paso 4 - COSTES
		 */
        public function costs () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				if (isset($_POST['submit'])) {

					$errors = array();

					$this->resource = $_POST['resource'];
					$project->save($errors);

					//tratar costes existentes
					foreach ($project->costs as $cost) {
						// primero mirar si lo estan quitando
						if ($_POST['remove-cost' . $cost->id] == 1) {
							$cost->remove($errors);
							continue;
						}

						if (!empty($_POST['cost' . $cost->id])) {
						
							$cost->cost = $_POST['cost' . $cost->id];
							$cost->description = $_POST['cost-description' . $cost->id];
							$cost->amount = $_POST['cost-amount' . $cost->id];
							$cost->type = $_POST['cost-type' . $cost->id];
							$cost->required = $_POST['cost-required' . $cost->id];
							$cost->from = $_POST['cost-from' . $cost->id];
							$cost->until = $_POST['cost-until' . $cost->id];
						
							$cost->save($errors);
						}
					}

					//tratar nuevo coste
					if (!empty($_POST['ncost'])) {

						$cost = new Model\Project\Cost();

						$cost->id = '';
						$cost->project = $project->id;
						$cost->cost = $_POST['ncost'];
						$cost->description = $_POST['ncost-description'];
						$cost->amount = $_POST['ncost-amount'];
						$cost->type = $_POST['ncost-type'];
						$cost->required = $_POST['ncost-required'];
						$cost->from = $_POST['ncost-from'];
						$cost->until = $_POST['ncost-until'];

						$cost->save($errors);

						$project->costs[] = $cost;
					}
				}

			}

			$guideText = Text::get('guide project costs');
            include 'view/project/costs.html.php';

        }

		/*
		 * Paso 5 - RETORNO
		 */
        public function rewards () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				if (isset($_POST['submit'])) {

					$errors = array();

					//tratar retornos sociales
					foreach ($project->social_rewards as $reward) {
						// primero mirar si lo estan quitando
						if ($_POST['remove-social_reward' . $reward->id] == 1) {
							$reward->remove($errors);
							continue;
						}

						if (!empty ($_POST['social_reward' . $reward->id])) {
							$reward->reward = $_POST['social_reward' . $reward->id];
							$reward->description = $_POST['social_reward-description' . $reward->id];
							$reward->icon = $_POST['social_reward-icon' . $reward->id];
							$reward->license = $_POST['social_reward-license' . $reward->id];
						}
						$reward->save($errors);
					}

					// retornos individuales
					foreach ($project->individual_rewards as $reward) {
						// primero mirar si lo estan quitando
						if ($_POST['remove-individual_reward' . $reward->id] == 1) {
							$reward->remove($errors);
							continue;
						}

						if (!empty ($_POST['individual_reward' . $reward->id])) {
							$reward->reward = $_POST['individual_reward' . $reward->id];
							$reward->description = $_POST['individual_reward-description' . $reward->id];
							$reward->icon = $_POST['individual_reward-icon' . $reward->id];
							$reward->amount = $_POST['individual_reward-amount' . $reward->id];
							$reward->units = $_POST['individual_reward-units' . $reward->id];
						}
						$reward->save($errors);
					}



					// tratar nuevos retornos
					if (!empty($_POST['nsocial_reward'])) {
						$reward = new Model\Project\Reward();

						$reward->id = '';
						$reward->project = $project->id;
						$reward->reward = $_POST['nsocial_reward'];
						$reward->description = $_POST['nsocial_reward-description'];
						$reward->type = 'social';
						$reward->icon = $_POST['nsocial_reward-icon'];
						$reward->license = $_POST['nsocial_reward-license'];

						$reward->save($errors);

						$project->social_rewards[] = $reward;
					}

					if (!empty($_POST['nindividual_reward'])) {
						$reward = new Model\Project\Reward();

						$reward->id = '';
						$reward->project = $project->id;
						$reward->reward = $_POST['nindividual_reward'];
						$reward->description = $_POST['nindividual_reward-description'];
						$reward->type = 'individual';
						$reward->icon = $_POST['nindividual_reward-icon'];
						$reward->amount = $_POST['nindividual_reward-amount'];
						$reward->units = $_POST['nindividual_reward-units'];

						$reward->save($errors);

						$project->individual_rewards[] = $reward;
					}

				}

				$guideText = Text::get('guide project rewards');
				include 'view/project/rewards.html.php';

			}

        }

		/*
		 * Paso 6 - COLABORACIONES
		 */
        public function supports () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				if (isset($_POST['submit'])) {
					$errors = array();

					// tratar colaboraciones existentes
					foreach ($project->supports as $support) {
						// primero mirar si lo estan quitando
						if ($_POST['remove-support' . $support->id] == 1) {
							$support->remove($errors);
							continue;
						}

						if (!empty ($_POST['support' . $support->id])) {
							$support->support = $_POST['support' . $support->id];
							$support->description = $_POST['support-description' . $support->id];
							$support->type = $_POST['support-type' . $support->id];
						}
						$support->save($errors);
					}

					// tratar nueva colaboracion
					if (!empty($_POST['nsupport'])) {
						$support = new Model\Project\Support();

						$support->id = '';
						$support->project = $project->id;
						$support->support = $_POST['nsupport'];
						$support->description = $_POST['nsupport-description'];
						$support->type = $_POST['nsupport-type'];

						$project->supports[] = $support;
					}
					
				}

				$guideText = Text::get('guide project support');
				include 'view/project/supports.html.php';

			}

        }

		/*
		 * Paso 7 - PREVIEW
		 */
        public function overview () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				$finish = false;
				$errors = array();
				$project->validate($errors);
				if (empty($errors)) {
					$success[] = Text::get('guide project success noerrors');
					if ($project->progress > 80 && $project->status == 1) {
						$success[] = Text::get('guide project success minprogress');
						$success[] = Text::get('guide project success okfinish');
						$finish = true;
					}
				}
				
				$guideText = Text::get('guide project overview');
				include 'view/project/overview.html.php';

			}
			
        }

		/*
		 * Paso 8 - Listo para revision
		 *
		 * Pasa el proyecto a estado "Pendiente de revisión"
		 * Cambia el id temporal apor el idealiza del nombre del proyecto
		 *		(ojo que no se repita)
		 *		(ojo en las tablas relacionadas)
		 */
        public function close () {
            Model\User::restrict();

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = Model\Project::get($id);

				if ($project->ready()) {
					unset($_SESSION['current_project']);
					header('Location: /dashboard');
					die;
				}
				else {
					header('Location: /project/overview');
					die;
				}

			}
        }

    }
    
}
