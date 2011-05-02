<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Text,
		Goteo\Library\Lang,
        Goteo\Library\Paypal,
        Goteo\Library\Page,
        Goteo\Library\Worth;

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

            $errors = array();
            
            // si estamos editando una página
            if (isset($_GET['page'])) {
                $id = $_GET['page'];

                $page = Page::get($id, $node, $lang);

                // si llega post, vamos a guardar los cambios
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $page->content = $_POST['content'];
                    if ($page->save($errors))
                        throw new Redirection("/admin/pages");
                }


                // sino, mostramos para editar
                return new View(
                    'view/admin/pageEdit.html.php',
                    array(
                        'using' => $using,
                        'page' => $page,
                        'errors'=>$errors
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
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            $errors = array();

            // poner un proyecto en campaña
            if (isset($_GET['publish'])) {
                $project = Model\Project::get($_GET['publish']);
                $project->publish($errors);
            }

            // dar un proyecto por fallido / cerrado  manualmente
            if (isset($_GET['cancel'])) {
                $project = Model\Project::get($_GET['cancel']);
                $project->fail($errors);
            }

            // si no está en edición, recuperarlo
            if (isset($_GET['enable'])) {
                $project = Model\Project::get($_GET['enable']);
                $project->enable($errors);
            }

            // dar un proyecto por financiado manualmente
            if (isset($_GET['complete'])) {
                $project = Model\Project::get($_GET['complete']);
                $project->succeed($errors);
            }


            $projects = Model\Project::getList($node);
            $status = Model\Project::status();

            return new View(
                'view/admin/checking.html.php',
                array(
                    'projects' => $projects,
                    'status' => $status,
                    'errors' => $errors
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
            // estados de aporte
            $investStatus = Model\Invest::status();
            // niveles meritocraticos
            $worthcracy = Worth::getAll();


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
//die('<pre>' . print_r($projects, 1) . '</pre>');
            foreach ($projects as &$project) {

                $project->invests = Model\Invest::getAll($project->id);

                // para cada uno sacar todos sus aportes
                foreach ($project->invests as $key=>&$invest) {

                    if ($invest->status == 2)
                        continue;
                    
                    $invest->paypalStatus = '';
                    
                    //estado del aporte
                    if (empty($invest->preapproval)) {
                        //si no tiene preaproval, cancelar
                        $invest->paypalStatus = 'Cancelado porque no ha hecho bien el preapproval.';
                        $invest->cancel();
                    } else {
                        if (empty($invest->payment)) {
                            //si tiene preaprval y no tiene pago, cargar
                            $invest->paypalStatus = 'Preaproval listo, esperando a los 40/80 dias para ejecutar el cargo. ';
                            if (isset($_GET['execute'])) {
                                $errors = array();

                                if (Paypal::pay($invest, $errors))
                                    $invest->paypalStatus .= 'Cargo ejecutado. ';
                                else
                                    $invest->paypalStatus .= 'Fallo al ejecutar el cargo. ';

                                if (!empty($errors))
                                    $invest->paypalStatus .= implode('<br />', $errors);
                            }
                        } else {
                            $invest->paypalStatus = 'Transacción finalizada.';
                        }
                    }

                }

            }

            return new View(
                'view/admin/accounting.html.php',
                array(
                    'projects' => $projects,
                    'status' => $status,
                    'investStatus' => $investStatus,
                    'worthcracy' => $worthcracy
                )
            );
        }


	}

}