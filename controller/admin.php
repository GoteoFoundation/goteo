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
            return new View('view/admin/index.html.php');
        }


        /*
         * Gestión de páginas institucionales
         */
		public function pages ($action = 'list', $id = null) {
            // idioma que estamos gestionando
            $lang = GOTEO_DEFAULT_LANG;

			$using = Lang::get($lang);

            $errors = array();

            switch ($action) {
                case 'edit':
                    // si estamos editando una página
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
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $pages = Page::getAll();

                    return new View(
                        'view/admin/pages.html.php',
                        array(
                            'using' => $using,
                            'pages' => $pages
                        )
                    );
                    break;
            }

		}

		public function texts ($action = 'list', $id = null) {

            // no cache para textos
            define('GOTEO_ADMIN_NOCACHE', true);

            // comprobamos el filtro
            $filters = Text::filters();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $filters)) {
                $filter = $_GET['filter'];
            } else {
                $filter = null;
            }

            // metemos el todos
            \array_unshift($filters, 'Todos los textos');

            switch ($action) {
                case 'list':
                    return new View(
                        'view/admin/list.html.php',
                        array(
                            'title' => 'Gestión de textos',
                            'menu' => array(),
                            'data' => Text::getAll($filter),
                            'row' => array(
                                'id' => 'id',
                                'value' => 'text'
                            ),
                            'urlEdit' => '/admin/texts/edit/',
                            'filters' => array(
                                'action' => '/admin/texts',
                                'label'  => 'Filtrar los textos de:',
                                'values' => $filters
                            ),
                            'filter' => $filter,
                            'errors' => $errors
                        )
                    );

                    break;
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo un nuevo texto",
                            'menu' => array(
                                array(
                                    'url'=>'/admin/texts?filter='.$filter,
                                    'label'=>'Textos'
                                )
                            ),
                            'data' => (object) array(),
                            'form' => array(
                                'action' => '/admin/texts/edit/?filter='.$filter,
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Aplicar'
                                ),
                                'fields' => array (
                                    'newtext' => array(
                                        'label' => 'Texto',
                                        'name' => 'text',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="6"',
                                        
                                    )
                                )

                            )
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        $errors = array();

                        $id = $_POST['id'];

                        $data = array(
                            'id' => $id,
                            'text' => $_POST['text'],
                            'lang' => \GOTEO_DEFAULT_LANG
                        );

                        if (Text::save($data, $errors)) {
                            throw new Redirection("/admin/texts?filter=$filter");
                        }
                    }

                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando el texto '$id'",
                            'menu' => array(
                                array(
                                    'url'=>'/admin/texts?filter='.$filter,
                                    'label'=>'Textos'
                                )
                            ),
                            'data' => (object) array(
                                'id' => $id,
                                'text' => Text::get($id)
                            ),
                            'form' => array(
                                'action' => '/admin/texts/edit/'.$id.'?filter='.$filter,
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Aplicar'
                                ),
                                'fields' => array (
                                    'idtext' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden',
                                        'properties' => '',

                                    ),
                                    'newtext' => array(
                                        'label' => 'Texto',
                                        'name' => 'text',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="6"',

                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                default:
                    throw new Redirection("/admin");
            }
		}

        /*
         *  Revisión de proyectos, aqui llega con un nodo y si no es el suyo a la calle (o al suyo)
         */
        public function checking($action = 'list', $id = null) {
            $errors = array();

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            switch ($action) {
                case 'publish':
                    // poner un proyecto en campaña
                    $project = Model\Project::get($id);
                    $project->publish($errors);
                    break;
                case 'cancel':
                    // dar un proyecto por fallido / cerrado  manualmente
                    $project = Model\Project::get($id);
                    $project->fail($errors);
                    break;
                case 'enable':
                    // si no está en edición, recuperarlo
                    $project = Model\Project::get($id);
                    $project->enable($errors);
                    break;
                case 'complete':
                    // dar un proyecto por financiado manualmente
                    $project = Model\Project::get($id);
                    $project->succeed($errors);
                    break;
                case 'fulfill':
                    // marcar todos los retornos cunmplidos
                    $project = Model\Project::get($id);
                    $project->satisfied($errors);
                    break;
            }

            $projects = Model\Project::getList();
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
        public function promote($action = 'list', $id = null) {
            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $promo = new Model\Promote(array(
                    'node' => \GOTEO_NODE,
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

            switch ($action) {
                case 'up':
                    Model\Promote::up($id);
                    break;
                case 'down':
                    Model\Promote::down($id);
                    break;
                case 'remove':
                    Model\Promote::delete($id);
                    break;
                case 'add':
                    // proyectos publicados para promocionar
                    $projects = Model\Promote::available();

                    // siguiente orden
                    $next = Model\Promote::next();

                    return new View(
                        'view/admin/promoEdit.html.php',
                        array(
                            'action' => 'add',
                            'promo' => (object) array('order' => $next),
                            'projects' => $projects
                        )
                    );
                    break;
                case 'edit':
                    $promo = Model\Promote::get($id);

                    return new View(
                        'view/admin/promoEdit.html.php',
                        array(
                            'action' => 'edit',
                            'promo' => $promo
                        )
                    );
                    break;
            }


            $promoted = Model\Promote::getAll();

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
                    'node' => \GOTEO_NODE,
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

                $next = Model\Faq::next($section);

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


            $faqs = Model\Faq::getAll($filter);

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
            if (isset($_GET['filters'])) {
                foreach (\unserialize($_GET['filters']) as $field=>$value) {
                    $filters[$field] = $value;
                }
            } else {
                $filters = array();
            }

            $fields = array('group', 'icon');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            // agrupaciones de mas a menos abertas
            $groups = Model\License::groups();

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
                            'filters' => $filters,
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
                        'filters' => $filters,
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


            $licenses = Model\License::getAll($filters['icon'], $filters['group']);

            return new View(
                'view/admin/license.html.php',
                array(
                    'licenses' => $licenses,
                    'filters'  => $filters,
                    'groups' => $groups,
                    'icons'    => $icons,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * posts para portada
         * Es una idea de blog porque luego lo que salga en la portada
         *  seran los posts de cierta categoria, o algo así
         */
        public function posts() {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $post = new Model\Post(array(
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'media' => $_POST['media'],
                    'order' => $_POST['order']
                ));

                if (!empty($post->media)) {
                    $post->media = new Model\Project\Media($post->media);
                }

				if ($post->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Entrada creada correctamente';
                            break;
                        case 'edit':
                            $success = 'Entrada editada correctamente';
                            break;
                    }
				}
				else {
                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'view/admin/postEdit.html.php',
                                array(
                                    'action' => 'add',
                                    'post' => $post,
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/postEdit.html.php',
                                array(
                                    'action' => 'edit',
                                    'post' => $post,
                                    'errors' => $errors
                                )
                            );
                            break;
                    }
				}
			}


            if (isset($_GET['up'])) {
                Model\Post::up($_GET['up']);
            }

            if (isset($_GET['down'])) {
                Model\Post::down($_GET['down']);
            }

            if (isset($_GET['add'])) {
                // siguiente orden
                $next = Model\Post::next();

                return new View(
                    'view/admin/postEdit.html.php',
                    array(
                        'action' => 'add',
                        'post' => (object) array('order' => $next)
                    )
                );
            }

            if (isset($_GET['edit'])) {
                $post = Model\Post::get($_GET['edit']);

                return new View(
                    'view/admin/postEdit.html.php',
                    array(
                        'action' => 'edit',
                        'post' => $post
                    )
                );
            }

            if (isset($_GET['remove'])) {
                Model\Post::delete($_GET['remove']);
            }


            $posts = Model\Post::getAll();

            return new View(
                'view/admin/post.html.php',
                array(
                    'posts' => $posts,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         *  administración de nodos y usuarios (segun le permita el ACL al usuario validado)
         */
        public function managing() {
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
            // estados del proyecto
            $status = Model\Project::status();
            // estados de aporte
            $investStatus = Model\Invest::status();


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
                    'investStatus' => $investStatus
                )
            );
        }


        /*
         * Gestión de retornos, por ahora en el admin pero es una gestión para los responsables de proyectos
         * Proyectos financiados, puede marcar un retorno cumplido
         */
        public function rewards() {

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