<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Text,
		Goteo\Library\Lang;

	class Admin extends \Goteo\Core\Controller {

        public function index () {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
			// si tenemos usuario logueado
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            return new View('view/admin/index.html.php');
        }


		public function texts ($lang = 'es') {
		    if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }

			// si tenemos usuario logueado
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            // comprobamos el filtro
            $filters = Text::filters();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $filters)) {
                $filter = $_GET['filter'];
            } else {
                $filter = null;
            }

			$using = Lang::get($lang);
			$texts = Text::getAll($lang, $filter);

            return new View(
                'view/admin/texts.html.php',
                array(
                    'using' => $using,
                    'texts' => $texts,
                    'filters' => $filters
                    )
                );
		}

		public function translate ($id = null, $lang = 'es') {
		    if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }

			// si tenemos usuario logueado
			$using = Lang::get($lang);

            $text = new \stdClass();
            $text->id = $id;
			$text->text = Text::get($id, 'es');
			$text->translation = Text::get($id, $lang);
			$text->purpose = Text::getPurpose($id);

            $viewData = array(
                'using' => $using,
                'text' => $text
            );

            if (isset($_GET['filter']))
                $filter = "?filter=" . $_GET['filter'];
            else
                $filter = '';

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				$errors = array();

				$data = array(
					'id' => $id,
					'text' => $_POST['newtext'],
					'lang' => $lang
				);

				if (Text::save($data, $errors)) {
                    throw new Redirection("/admin/texts/".$filter);
				}
				else {
                    $viewData['errors'] = $errors;
				}
			}

            return new View(
                'view/admin/texts.html.php',
                $viewData
                );
		}

        /*
         *  Revisión de proyectos, aqui llega con un nodo y si no es el suyo a la calle (o al suyo)
         */
        public function checking($node = 'goteo') {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            $projects = Model\Project::getAll($node);

            return new View(
                'view/admin/checking.html.php',
                array(
                    'projects'=>$projects
                )
            );
        }

        /*
         *  administración de nodos y usuarios (segun le permita el ACL al usuario validado)
         */
        public function managing($node = 'goteo') {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            $users = Model\User::getAll();

            return new View(
                'view/admin/managing.html.php',
                array(
                    'users'=>$users
                )
            );
        }

        /*
         *  Revisión de proyectos, aqui llega con un nodo y si no es el suyo a la calle (o al suyo)
         */
        public function accounting($node = 'goteo') {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            $content = 'Administración de las transacciones para cobrar las aportaciones';
//            include 'view/admin/accounting.html.php';
            return new View('view/admin/accounting.html.php', array('content'=>$content));
        }


	}

}