<?php

namespace Goteo\Controller {
    
    use Goteo\Core\Error,
        Goteo\Model\Project as Prj;
    
    class Project extends \Goteo\Core\Controller {
        
        public function index ($id = null) {
            
            if ($id === null) {
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

		/*
		 * Paso cero de nuevo proyecto
		 * @TODO : de nuevo el usuario no deberia llegar por la url sino por la session
		 * pero aun no tenemos la validación de usuario...
		 */
        public function crear ($user = null) {

            if ($user === null) {
				header('Location: /');
				die;
            } else {
                $project = new Prj();

                if ($project->create($user)) {
					header('Location: /project/user');
					die;
				}
				else {
					header('Location: /');
					die;
				}

            }

        }



		/*
		 * Paso 1 - PERFIL
		 */
        public function user () {

            include 'view/project/user.html.php';

        }

		/*
		 * Paso 2 - DATOS PERSONALES
		 */
        public function register () {
            
            include 'view/project/register.html.php'; 
            
        }
        
		/*
		 * Paso 3 - DESCRIPCIÓN
		 */
        public function edit () {

            include 'view/project/edit.html.php';

        }

		/*
		 * Paso 4 - COSTES
		 */
        public function tasks () {

            include 'view/project/tasks.html.php';

        }

		/*
		 * Paso 5 - RETORNO
		 */
        public function rewards () {

            include 'view/project/rewards.html.php';

        }

		/*
		 * Paso 6 - COLABORACIONES
		 */
        public function supports () {

            include 'view/project/supports.html.php';

        }

		/*
		 * Paso 7 - PREVIEW
		 */
        public function overview () {

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

			echo 'FIN';

        }

        
    }
    
}
