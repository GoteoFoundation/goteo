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

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$errors = array();
					if ($project->save($_POST, $errors)) {
						header('Location: /project/tasks');
						die;
					}
				}

			}
            include 'view/project/edit.html.php';

        }

		/*
		 * Paso 4 - COSTES
		 */
        public function tasks () {

			$id = $_SESSION['current_project'];

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$errors = array();
					if ($project->save($_POST, $errors)) {
						header('Location: /project/rewards');
						die;
					}
				}

			}
            include 'view/project/tasks.html.php';

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
					if ($project->save($_POST, $errors)) {
						header('Location: /project/supports');
						die;
					}
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
					if ($project->save($_POST, $errors)) {
						header('Location: /project/overview');
						die;
					}
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
				$errors = array();
				$success = '';
				$finish = false;
				$project->validate(__FUNCTION__, $errors, $success, $finish);
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
				
				$sql = "UPDATE project SET status = :status, updated = :updated WHERE id = :id";
				if (Prj::query($sql, array(':status'=>2, ':updated'=>date('Y-m-d'), ':id'=>$id))) {
					if ($project->rebase()) {
						unset($_SESSION['current_project']);
						header('Location: /dashboard');
						die;
					} else {
						echo 'Error catastrofico al remontar el proyecto!!';
						die;
					}
				}
				else {
					header('Location: /project/overview');
					die;
				}

			}
        }

    }
    
}
