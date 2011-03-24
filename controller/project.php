<?php

namespace Goteo\Controller {
    
    use Goteo\Core\Error,
        Goteo\Model\Project as Prj,
		Goteo\Model\User as Usr; // <-- solo para el primer paso
    
    class Project extends \Goteo\Core\Controller {

		public function index ($id = null) {
            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

                if (!$project->id) {
                    throw new Error(404);
                }
                else {
					include 'view/project/public.html.php';
				}

            }
		}

        public function manage ($id = null) {
            
            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				if ($project->status > 1) {
					header('Location: /project/' . $id);
					die;
				}
				else {
					$_SESSION['current_project'] = $id;
					header('Location: /project/edit');
					die;
				}
            }
        }

		/*
		 * Paso cero de nuevo proyecto
		 * @TODO : de nuevo el usuario no deberia llegar por la url sino por la session
		 * pero aun no tenemos la validación de usuario...
		 */
        public function create () {

			$user = $_SESSION['user'];

            if (!$user) {
				header('Location: /');
				die;
            } else {
                $project = new Prj();

                if ($project->create($user)) {
					$_SESSION['current_project'] = $project->id;
					header('Location: /project/user/');
					die;
				}
				else {
					echo 'ERROR al crear el proyecto';
//					header('Location: /ERROR');
//					die;
				}

            }

        }



		/*
		 * Paso 1 - PERFIL
		 */
        public function user () {

			$id = $_SESSION['current_project'];
			$userid = $_SESSION['user'];

            if (!$id || !$userid) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				$user = new Usr($userid);

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					if ($user->save($_POST, $errors)) {
						header('Location: /project/register');
						die;
					}
				}

			}
            include 'view/project/user.html.php';

        }

		/*
		 * Paso 2 - DATOS PERSONALES
		 */
        public function register () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$errors = array();
					if ($project->save($_POST, $errors)) {
						header('Location: /project/edit');
						die;
					}
				} else {
					/*
					// en este punto, si no tiene los campos contract, los cargamos de su último proyecto
					if (empty($project->contract_name) && 
						empty($project->contract_surname) &&
						empty($project->contract_nif) &&
						empty($project->contract_email)) {
							$project->lastContract();
					}
					 *  No estoy seguro de que esto sea tan bueno, si estan revisando y lo ven rellenado pensaran que esta ok
					 * y si aparece error pensara que no funciona...
					 */
				}

			}
            include 'view/project/register.html.php'; 
            
        }
        
		/*
		 * Paso 3 - DESCRIPCIÓN
		 */
        public function edit () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				$currents = Prj::currentStatus();

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$errors = array();
					//tratar imagen
					
					//tratar keywords
					if (!empty($_POST['keywords'])) {
						$keys = explode(',', $_POST['keywords']);
						foreach ($keys as $key) {
							$project->newKeyword($key, $errors);
						}
					}


					/*if ($project->save($_POST, $errors)) {
						header('Location: /project/costs');
						die;
					}*/
				}

			}
            include 'view/project/edit.html.php';

        }

		/*
		 * Paso 4 - COSTES
		 */
        public function costs () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$errors = array();

//					echo '<pre>' . print_r($_POST, 1) . '</pre>';

					//tratar costes existentes
					foreach ($project->costs as $cost) {
						$data = array(
							'id'=>$cost->id,
							'cost'=>$_POST['cost' . $cost->id],
							'amount'=>$_POST['cost-amount' . $cost->id],
							'type'=>$_POST['cost-type' . $cost->id],
							'required'=>$_POST['cost-required' . $cost->id],
							'from'=>$_POST['cost-from' . $cost->id],
							'until'=>$_POST['cost-until' . $cost->id]
						);
						$cost->save($data, $errors);
					}

					//tratar nuevo coste
					if (!empty($_POST['ncost'])) {
						$newCost = array(
							'cost'=>$_POST['ncost'],
							'amount'=>$_POST['ncost-amount'],
							'type'=>$_POST['ncost-type'],
							'required'=>$_POST['ncost-required'],
							'from'=>$_POST['ncost-from'],
							'until'=>$_POST['ncost-until']
						);
						$project->newCost($newCost, $errors);
					}

					// otros recursos
					$project->save(array('resource'=>$_POST['resource']), $errors);

					/*if (empty($errors)) {
						header('Location: /project/rewards');
						die;
					}*/
				}

			}
            include 'view/project/costs.html.php';

        }

		/*
		 * Paso 5 - RETORNO
		 */
        public function rewards () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {

					$errors = array();
					// tratar retornos existentes
					foreach ($project->social_rewards as $reward) {
						$data = array(
							'reward'=>$_POST['social_reward' . $reward->id],
							'icon'=>$_POST['social_reward-icon' . $reward->id],
							'license'=>$_POST['social_reward-license' . $reward->id]
						);

						$reward->save($data, $errors);
					}
					
					foreach ($project->individual_rewards as $reward) {
						$data = array(
							'reward'=>$_POST['individual_reward' . $reward->id],
							'icon'=>$_POST['individual_reward-icon' . $reward->id],
							'amount'=>$_POST['individual_reward-amount' . $reward->id],
							'units'=>$_POST['individual_reward-units' . $reward->id]
						);

						$reward->save($data, $errors);
					}


					// tratar nuevos retornos
					if (!empty($_POST['nsocial_reward'])) {
						$newSocialReward = array(
							'reward'=>$_POST['nsocial_reward'],
							'type'=>'social',
							'icon'=>$_POST['nsocial_reward-icon'],
							'license'=>$_POST['nsocial_reward-license']
						);
						$project->newSocialReward($newSocialReward, $errors);
					}

					if (!empty($_POST['nindividual_reward'])) {
						$newIndividualReward = array(
							'reward'=>$_POST['nindividual_reward'],
							'type'=>'individual',
							'icon'=>$_POST['nindividual_reward-icon'],
							'amount'=>$_POST['nindividual_reward-amount'],
							'units'=>$_POST['nindividual_reward-units']
						);
						$project->newIndividualReward($newIndividualReward, $errors);
					}

					/*if (empty($errors)) {
						header('Location: /project/supports');
						die;
					}*/
				}

			}
            include 'view/project/rewards.html.php';

        }

		/*
		 * Paso 6 - COLABORACIONES
		 */
        public function supports () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$errors = array();
					// tratar colaboraciones existentes
					foreach ($project->supports as $support) {
						$data = array(
							'support'=>$_POST['support' . $support->id],
							'type'=>$_POST['support-type' . $support->id],
							'description'=>$_POST['support-description' . $support->id]
						);
						$support->save($data, $errors);
					}

					// tratar nueva colaboracion
					if (!empty($_POST['nsupport'])) {
						$newSupport = array(
								'support'=>$_POST['nsupport'],
								'type'=>$_POST['nsupport-type'],
								'description'=>$_POST['nsupport-description']
						);
						$project->newSupport($newSupport, $errors);
					}

					/*if (empty($errors)) {
						header('Location: /project/overview');
						die;
					}*/
				}

			}
            include 'view/project/supports.html.php';

        }

		/*
		 * Paso 7 - PREVIEW
		 */
        public function overview () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				$finish = false;
				if (!empty($project->badmessages)) {
					$errors = $project->badmessages;
				}
				else {
					$success[] = 'Todos los campos del formulario son correctos';
					if ($project->progress > 60 && $project->status == 1) {
						$success .= 'Pulse el botón finalizar para que revisemos su proyecto. No podrá modificar los datos del proyecto una vez finalizado.';
						$finish = true;
					}
				}
			}
			
            include 'view/project/overview.html.php';

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

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

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
