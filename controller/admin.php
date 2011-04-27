<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Text,
		Goteo\Library\Lang,
        Goteo\Library\Paypal,
        Goteo\Library\Page;

	class Admin extends \Goteo\Core\Controller {

        public function index () {
			// si tenemos usuario logueado
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            return new View('view/admin/index.html.php');
        }


        /*
         * Gestión de páginas institucionales
         */
		public function pages ($node = 'goteo', $lang = 'es') {
			// si tenemos usuario logueado
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

			$using = Lang::get($lang);

            // si estamos editando una página
            if (isset($_GET['page'])) {
                $id = $_GET['page'];

                // si llega post, vamos a guardar los cambios

                // sino, mostramos para editar
                $page = Page::get($id, $node, $lang);
                return new View(
                    'view/admin/pageEdit.html.php',
                    array(
                        'using' => $using,
                        'page' => $page
                    )
                 );
            }

            // si estamos en la lista de páginas
			$pages = Page::getAll();

            return new View(
                'view/admin/pages.html.php',
                array(
                    'using' => $using,
                    'pages' => $pages
                )
            );
		}

		public function texts ($lang = 'es') {
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
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            $projects = Model\Project::getList($node);

            $status = Model\Project::status();

            

            return new View(
                'view/admin/checking.html.php',
                array(
                    'projects'=>$projects,
                    'status'=>$status
                )
            );
        }

        /*
         *  administración de nodos y usuarios (segun le permita el ACL al usuario validado)
         */
        public function managing($node = 'goteo') {
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
         *  Revisión de aportes
         *
         * dummy para ejecutar cargos
         */
        public function accounting($node = 'goteo') {
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            // estados del proyecto
            $status = Model\Project::status();


            /// si piden unos detalles,
            if (isset($_GET['details'])) {
                $invest = Model\Invest::get($_GET['details']);
                $project = Model\Project::get($invest->project);
                $details = array();
                if (!empty($invest->preapproval)) {
                    $details['preapproval'] = Paypal::preapprovalDetails($invest->preapproval, $errors);
                }
                if (!empty($invest->payment)) {
                    $details['payment'] = Paypal::paymentDetails($invest->payment, $errors);
                }
                return new View(
                    'view/admin/investDetails.html.php',
                    array(
                        'invest'=>$invest,
                        'project'=>$project,
                        'details'=>$details,
                        'status'=>$status
                    )
                );
            }

            /*
             *  Lista de proyectos en campaña
             *  indicando cuanto han conseguido, cuantos dias y los cofinanciadores
             *  Para cada cofinanciador sus aportes
             *  enlace para ejecutar cargo
             */
            $projects = Model\Project::invested();

            foreach ($projects as &$proj) {

                // para cada uno sacar todos los datos de su aporte
                foreach ($proj->investors as $key=>&$investor) {

                    $invest = Model\Invest::get($investor['invest']);

                    $investStatus = '';
                    $investor['invest'] = $invest;
                    
                    //estado del aporte
                    if (empty($invest->preapproval)) {
                        //si no tiene preaproval, cancelar
                        $investStatus = 'Cancelado porque no ha hecho bien el preapproval.';
                        $invest->cancel();
                    } else {
                        if (empty($invest->payment)) {
                            //si tiene preaprval y no tiene pago, cargar
                            $investStatus = 'Preaproval listo, esperando a los 40/80 dias para ejecutar el cargo. ';
                            if (isset($_GET['execute'])) {
                                $errors = array();

                                if (Paypal::pay($invest, $errors))
                                    $investStatus .= 'Cargo ejecutado. ';
                                else
                                    $investStatus .= 'Fallo al ejecutar el cargo. ';

                                if (!empty($errors))
                                    $investStatus .= implode('<br />', $errors);
                            }
                        } else {
                            $investStatus = 'Transacción finalizada.';
                        }
                    }

                    $investor['status'] = $investStatus;
                }

            }

            return new View(
                'view/admin/accounting.html.php',
                array(
                    'projects'=>$projects,
                    'status'=>$status
                )
            );
        }


	}

}