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
		public function pages () {
			// si tenemos usuario logueado
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            // nodo del usuario
            $node = 'goteo';

            // idioma que estamos gestionando
            $lang = GOTEO_DEFAULT_LANG;

			$using = Lang::get($lang);

            $errors = array();
            
            // si estamos editando una página
            if (isset($_GET['page'])) {
                $id = $_GET['page'];

                $page = Page::get($id);

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

		public function texts () {
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

			$using = Lang::get();
			$texts = Text::getAll($filter);

            return new View(
                'view/admin/texts.html.php',
                array(
                    'using' => $using,
                    'texts' => $texts,
                    'filters' => $filters
                    )
                );
		}

		public function translate ($id) {

            $lang = \GOTEO_DEFAULT_LANG;

            // no cache para textos
            define('GOTEO_ADMIN_NOCACHE', true);

			// si tenemos usuario logueado
			$using = Lang::get();

            $text = new \stdClass();
            $text->id = $id;
			$text->text = Text::get($id);
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
        public function checking() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            // nodo del usuario
            $node = 'goteo';

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

            // marcar todos los retornos cunmplidos
            if (isset($_GET['fulfill'])) {
                $project = Model\Project::get($_GET['fulfill']);
                $project->satisfied($errors);
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
         * proyectos destacados
         */
        public function promote() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            // nodo del usuario
            $node = 'goteo';

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $promo = new Model\Promote(array(
                    'node' => $node,
                    'project' => $_POST['project'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order']
                ));

				if ($promo->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Proyecto destacado correctamente';
                            break;
                        case 'edit':
                            $success = 'Destacado editado correctamente';
                            break;
                    }
				}
				else {
                    switch ($_POST['action']) {
                        case 'add':
                            // proyectos publicados para promocionar
                            $projects = Model\Project::published();

                            return new View(
                                'view/admin/promoEdit.html.php',
                                array(
                                    'action' => 'add',
                                    'promo' => $promo,
                                    'projects' => $projects,
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/promoEdit.html.php',
                                array(
                                    'action' => 'edit',
                                    'promo' => $promo,
                                    'errors' => $errors
                                )
                            );
                            break;
                    }
				}
			}


            if (isset($_GET['up'])) {
                Model\Promote::up($_GET['up']);
            }

            if (isset($_GET['down'])) {
                Model\Promote::down($_GET['down']);
            }

            if (isset($_GET['add'])) {
                // proyectos publicados para promocionar
                $projects = Model\Promote::available($node);

                // siguiente orden
                $next = Model\Promote::next($node);

                return new View(
                    'view/admin/promoEdit.html.php',
                    array(
                        'action' => 'add',
                        'promo' => (object) array('order' => $next),
                        'projects' => $projects
                    )
                );
            }

            if (isset($_GET['edit'])) {
                $promo = Model\Promote::get($_GET['edit']);

                return new View(
                    'view/admin/promoEdit.html.php',
                    array(
                        'action' => 'edit',
                        'promo' => $promo
                    )
                );
            }

            if (isset($_GET['remove'])) {
                Model\Promote::delete($_GET['remove']);
            }


            $promoted = Model\Promote::getAll($node);

            return new View(
                'view/admin/promote.html.php',
                array(
                    'promoted' => $promoted,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * preguntas frecuentes
         */
        public function faq() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            // nodo del usuario
            $node = 'goteo';

            // secciones
            $sections = Model\Faq::sections();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'node';
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $faq = new Model\Faq(array(
                    'id' => $_POST['id'],
                    'node' => $node,
                    'section' => $_POST['section'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order']
                ));

				if ($faq->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Pregunta añadida correctamente';
                            break;
                        case 'edit':
                            $success = 'Pregunta editado correctamente';
                            break;
                    }
				}
				else {
                    return new View(
                        'view/admin/faqEdit.html.php',
                        array(
                            'action' => $_POST['action'],
                            'faq' => $faq,
                            'sections' => $sections,
                            'errors' => $errors
                        )
                    );
				}
			}


            if (isset($_GET['up'])) {
                Model\Faq::up($_GET['up']);
            }

            if (isset($_GET['down'])) {
                Model\Faq::down($_GET['down']);
            }

            if (isset($_GET['add'])) {

                $next = Model\Faq::next($section, $node);

                return new View(
                    'view/admin/faqEdit.html.php',
                    array(
                        'action' => 'add',
                        'faq' => (object) array('section' => $section, 'order' => $next),
                        'sections' => $sections
                    )
                );
            }

            if (isset($_GET['edit'])) {
                $faq = Model\Faq::get($_GET['edit']);

                return new View(
                    'view/admin/faqEdit.html.php',
                    array(
                        'action' => 'edit',
                        'faq' => $faq,
                        'sections' => $sections
                    )
                );
            }

            if (isset($_GET['remove'])) {
                Model\Faq::delete($_GET['remove']);
            }


            $faqs = Model\Faq::getAll($filter, $node);

            return new View(
                'view/admin/faq.html.php',
                array(
                    'faqs' => $faqs,
                    'sections' => $sections,
                    'filter' => $filter,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * Tipos de Retorno/Recompensa (iconos)
         */
        public function icons() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            // grupos
            $groups = Model\Icon::groups();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $groups)) {
                $filter = $_GET['filter'];
            } else {
                $filter = '';
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $icon = new Model\Icon(array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'group' => $_POST['group']
                ));

				if ($icon->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Nuevo tipo añadido correctamente';
                            break;
                        case 'edit':
                            $success = 'Tipo editado correctamente';
                            break;
                    }
				}
				else {
                    return new View(
                        'view/admin/iconEdit.html.php',
                        array(
                            'action' => $_POST['action'],
                            'icon' => $icon,
                            'groups' => $groups,
                            'errors' => $errors
                        )
                    );
				}
			}

/*
            if (isset($_GET['add'])) {

                return new View(
                    'view/admin/iconEdit.html.php',
                    array(
                        'action' => 'add',
                        'icon' => (object) array('group' => ''),
                        'groups' => $groups
                    )
                );
            }
 * 
 */

            if (isset($_GET['edit'])) {
                $icon = Model\Icon::get($_GET['edit']);

                return new View(
                    'view/admin/iconEdit.html.php',
                    array(
                        'action' => 'edit',
                        'icon' => $icon,
                        'groups' => $groups
                    )
                );
            }

            /*
            if (isset($_GET['remove'])) {
                Model\Icon::delete($_GET['remove']);
            }
             *
             */


            $icons = Model\Icon::getAll($filter);

            return new View(
                'view/admin/icon.html.php',
                array(
                    'icons' => $icons,
                    'groups' => $groups,
                    'filter' => $filter,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * Licencias
         */
        public function licenses() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            // agrupaciones de mas a menos abertas
            $groups = Model\License::groups();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $groups)) {
                $filter = $_GET['filter'];
            } else {
                $filter = '';
            }

            // tipos de retorno para asociar
            $icons = Model\Icon::getAll('social');


            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $license = new Model\License(array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'group' => $_POST['group'],
                    'order' => $_POST['order'],
                    'icons' => $_POST['icons']
                ));

				if ($license->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Licencia añadida correctamente';
                            break;
                        case 'edit':
                            $success = 'Licencia editada correctamente';
                            break;
                    }
				}
				else {
                    return new View(
                        'view/admin/licenseEdit.html.php',
                        array(
                            'action'  => $_POST['action'],
                            'license' => $license,
                            'icons'   => $icons,
                            'groups'  => $groups,
                            'errors'  => $errors
                        )
                    );
				}
			}


            if (isset($_GET['up'])) {
                Model\License::up($_GET['up']);
            }

            if (isset($_GET['down'])) {
                Model\License::down($_GET['down']);
            }

            /*
            if (isset($_GET['add'])) {
                $next = Model\License::next();

                return new View(
                    'view/admin/licenseEdit.html.php',
                    array(
                        'action' => 'add',
                        'license' => (object) array('order' => $next, 'icons' => array()),
                        'icons' => $icons,
                        'groups' => $groups
                    )
                );
            }
             *
             */

            if (isset($_GET['edit'])) {
                $license = Model\License::get($_GET['edit']);

                return new View(
                    'view/admin/licenseEdit.html.php',
                    array(
                        'action' => 'edit',
                        'license' => $license,
                        'icons' => $icons,
                        'groups' => $groups
                    )
                );
            }

            /*
            if (isset($_GET['remove'])) {
                Model\License::delete($_GET['remove']);
            }
             * 
             */


            $licenses = Model\License::getAll($filter);

            return new View(
                'view/admin/license.html.php',
                array(
                    'licenses' => $licenses,
                    'groups' => $groups,
                    'filter' => $filter,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         *  administración de nodos y usuarios (segun le permita el ACL al usuario validado)
         */
        public function managing() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            // nodo del usuario
            $node = 'goteo';

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
        public function accounting() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/dashboard");

            // nodo del usuario
            $node = 'goteo';

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
                            $invest->paypalStatus = 'Preaproval listo para ejecutar a los 40/80 dias. ';
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


        /*
         * Gestión de retornos, por ahora en el admin pero es una gestión para los responsables de proyectos
         * Proyectos financiados, puede marcar un retorno cumplido
         */
        public function rewards() {
            if ($_SESSION['user']->role != 1) // @FIXME!!! a ver como se encarga de esto el ACL
                throw new Redirection("/dashboard");

            $errors = array();

            // si no está en edición, recuperarlo
            if (isset($_GET['fulfill'])) {
                $parts = explode(',', $_GET['fulfill']); // invest , reward
                $investId = $parts[0];
                $rewardId = $parts[1];
                if (empty($investId) || empty($rewardId)
                    || !is_numeric($investId) || !is_numeric($rewardId)) {
                    break;
                }
                Model\Invest::setFulfilled($investId, $rewardId);
            }


            $projects = Model\Project::invested();

            foreach ($projects as $kay=>&$project) {

                $project->invests = Model\Invest::getAll($project->id);

                // para cada uno sacar todos sus aportes
                foreach ($project->invests as $key=>&$invest) {
                    if ($invest->status != 1) {
                        unset($project->invests[$key]);
                        continue;
                    }
                }
            }


            return new View(
                'view/admin/rewards.html.php',
                array(
                    'projects' => $projects
                )
            );


        }


	}

}