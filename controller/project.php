<?php
// @FIXME !!!! 
// estamos arrastrando el id del proyecto por toda la url porque todavia no hemos hecho todo el tema de la session
// ...............................................................................................................


namespace Goteo\Controller {
    
    use Goteo\Core\Error,
        Goteo\Model\Project as Prj;
    
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

            if (!$id) {
				header('Location: /');
				die;
            } else {
                $project = new Prj($id);
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

			echo "Proyecto $id listo para revisión<br />";
			unset($_SESSION['current_project']);

        }

        
    }
    
}
