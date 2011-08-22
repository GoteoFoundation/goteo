<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Text,
		Goteo\Library\Lang,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv,
        Goteo\Library\Page,
        Goteo\Library\Template,
        Goteo\Library\Message,
        Goteo\Library\Blog,
        Goteo\Library\Worth;

	class Admin extends \Goteo\Core\Controller {

        public function index () {
            return new View('view/admin/index.html.php', array('menu'=>self::menu()));
        }

        public function select () {

            $_SESSION['translator_lang'] = isset($_POST['lang']) ? $_POST['lang'] : null;

            return new View('view/admin/index.html.php', array('menu'=>self::menu()));
        }


        /*
         * Gestión de páginas institucionales
         */
		public function pages ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            switch ($action) {
                case 'edit':
                    // si estamos editando una página
                    $page = Page::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $page->content = $_POST['content'];
                        if ($page->save($errors)) {
                            throw new Redirection("/admin/pages");
                        }
                    }


                    // sino, mostramos para editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'edit',
                            'page' => $page,
                            'errors'=>$errors
                        )
                     );
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $pages = Page::getAll();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'list',
                            'pages' => $pages
                        )
                    );
                    break;
            }

		}

		public function texts ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            // no cache para textos
            define('GOTEO_ADMIN_NOCACHE', true);

            // comprobamos los filtros
            $filters = array();
            $fields = array('idfilter', 'group', 'text');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $filter = "?idfilter={$filters['idfilter']}&group={$filters['group']}&text={$filters['text']}";
            
            // valores de filtro
            $idfilters = Text::filters();
            $groups    = Text::groups();

            // metemos el todos
            \array_unshift($idfilters, 'Todos los textos');
            \array_unshift($groups, 'Todas las agrupaciones');

 //@fixme temporal hasta pasar las agrupaciones a tabal o arreglar en el list.html.php
            $data = Text::getAll($filters, 'original');
            foreach ($data as $key=>$item) {
                $data[$key]->group = $groups[$item->group];
            }

            switch ($action) {
                case 'list':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'list',
                            'data' => $data,
                            'columns' => array(
                                'edit' => '',
                                'text' => 'Texto',
                                'group' => 'Agrupación'
                            ),
                            'url' => '/admin/texts',
                            'filters' => array(
                                'idfilter' => array(
                                        'label'   => 'Filtrar por tipo:',
                                        'type'    => 'select',
                                        'options' => $idfilters,
                                        'value'   => $filters['idfilter']
                                    ),
                                'group' => array(
                                        'label'   => 'Filtrar por agrupación:',
                                        'type'    => 'select',
                                        'options' => $groups,
                                        'value'   => $filters['group']
                                    ),
                                'text' => array(
                                        'label'   => 'Buscar texto:',
                                        'type'    => 'input',
                                        'options' => null,
                                        'value'   => $filters['text']
                                    )
                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                /*
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'edit',
                            'data' => (object) array(),
                            'form' => array(
                                'action' => '/admin/texts/edit/'.$filter,
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
                 * 
                 */
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        $errors = array();

                        $id = $_POST['id'];
                        $text = $_POST['text'];

                        $data = array(
                            'id' => $id,
                            'text' => $_POST['text']
                        );

                        if (Text::update($data, $errors)) {
                            throw new Redirection("/admin/texts/$filter");
                        }
                    } else {
                        $text = Text::getPurpose($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'edit',
                            'data' => (object) array (
                                'id' => $id,
                                'text' => $text
                            ),
                            'form' => array(
                                'action' => '/admin/texts/edit/'.$id.'/'.$filter,
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
         * Gestión de plantillas para emails automáticos
         */
		public function templates ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            switch ($action) {
                case 'edit':
                    // si estamos editando una plantilla
                    $template = Template::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $template->title = $_POST['title'];
                        $template->text  = $_POST['text'];
                        if ($template->save($errors))
                            throw new Redirection("/admin/templates");
                    }


                    // sino, mostramos para editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'edit',
                            'template' => $template,
                            'errors'=>$errors
                        )
                     );
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $templates = Template::getAll();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'list',
                            'templates' => $templates
                        )
                    );
                    break;
            }

		}

        /*
         *  Lista de proyectos
         */
        public function overview($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $filters = array();
            $fields = array('status', 'category');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $errors = array();

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            switch ($action) {
                case 'review':
                    // pasar un proyecto a revision
                    $project = Model\Project::get($id);
                    $project->ready($errors);
                    break;
                case 'publish':
                    // poner un proyecto en campaña
                    $project = Model\Project::get($id);
                    $project->publish($errors);
                    break;
                case 'cancel':
                    // descartar un proyecto por malo
                    $project = Model\Project::get($id);
                    $project->cancel($errors);
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

            $projects = Model\Project::getList($filters);
            $status = Model\Project::status();
            $categories = Model\Project\Category::getAll();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'overview',
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
                    'categories' => $categories,
                    'errors' => $errors
                )
            );
        }

        /*
         *  Revision de proyectos
         */
        public function reviews($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $filters = array();
            $fields = array('status', 'checker');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $filter = "?status={$filters['status']}&checker={$filters['checker']}";

            $errors = array();

            switch ($action) {
                case 'add':
                case 'edit':

                    // el get se hace con el id del proyecto
                    $review = Model\Review::get($id);

                    $project = Model\Project::getMini($review->project);

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        // instancia
                        $review->id         = $_POST['id'];
                        $review->project    = $_POST['project'];
                        $review->to_checker = $_POST['to_checker'];
                        $review->to_owner   = $_POST['to_owner'];

                        if ($review->save($errors)) {
                            switch ($action) {
                                case 'add':
                                    $success = 'Revisión iniciada correctamente';
                                    break;
                                case 'edit':
                                    $success = 'Datos editados correctamente';
                                    break;
                            }
                            
                            throw new Redirection('/admin/reviews/' . $filter);
                        }
                    }
                    
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'reviews',
                            'file'   => 'edit',
                            'action' => $action,
                            'review' => $review,
                            'project'=> $project,
                            'errors' => $errors
                        )
                    );

                    break;
                case 'close':
                    // marcamos la revision como completamente cerrada
                    if (Model\Review::close($id, $errors)) {
                        $message = 'La revisión se ha cerrado';
                    }
                    break;
                case 'unready':
                    // se la reabrimos para que pueda seguir editando
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $user_rev = new Model\User\Review(array(
                            'id' => $id,
                            'user' => $user
                        ));
                        $user_rev->unready($errors);
                    }
                    break;
                case 'assign':
                    // asignamos la revision a este usuario
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $assignation = new Model\User\Review(array(
                            'id' => $id,
                            'user' => $user
                        ));
                        $assignation->save($errors);
                    }
                    break;
                case 'unassign':
                    // se la quitamos a este revisor
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $assignation = new Model\User\Review(array(
                            'id' => $id,
                            'user' => $user
                        ));
                        $assignation->remove($errors);
                    }
                    break;
                case 'report':
                    // mostramos los detalles de revision
                    // ojo que este id es la id del proyecto, no de la revision
                    $review = Model\Review::get($id);
                    $review = Model\Review::getData($review->id);

                    $evaluation = array();

                    foreach ($review->checkers as $user=>$user_data) {
                        $evaluation[$user] = Model\Review::getEvaluation($review->id, $user);
                    }


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'reviews',
                            'file' => 'report',
                            'review'     => $review,
                            'evaluation' => $evaluation
                        )
                    );
                    break;
            }

            $projects = Model\Review::getList($filters);
            $status = array(
                'open' => 'Abiertas',
                'closed' => 'Cerradas'
            );
            $checkers = Model\User::getAll(array('role'=>'checker'));

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'reviews',
                    'file' => 'list',
                    'message' => $message,
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
                    'checkers' => $checkers,
                    'errors' => $errors
                )
            );
        }

        /*
         * proyectos destacados
         */
        public function promote($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

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
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'promo' => $promo,
                                    'status' => $status,
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
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
                    // siguiente orden
                    $next = Model\Promote::next();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next),
                            'status' => $status
                        )
                    );
                    break;
                case 'edit':
                    $promo = Model\Promote::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo
                        )
                    );
                    break;
            }


            $promoted = Model\Promote::getAll();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'promote',
                    'file' => 'list',
                    'promoted' => $promoted,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * Banners
         */
        public function banners($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $banner = new Model\Banner(array(
                    'node' => \GOTEO_NODE,
                    'project' => $_POST['project'],
                    'order' => $_POST['order']
                ));

				if ($banner->save($errors)) {
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
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'banners',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'banner' => $banner,
                                    'status' => $status,
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'banners',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'banenr' => $banner,
                                    'errors' => $errors
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'up':
                    Model\Banner::up($id);
                    break;
                case 'down':
                    Model\Banner::down($id);
                    break;
                case 'remove':
                    Model\Banner::delete($id);
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Banner::next();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'banners',
                            'file' => 'edit',
                            'action' => 'add',
                            'banner' => (object) array('order' => $next),
                            'status' => $status
                        )
                    );
                    break;
                case 'edit':
                    $banner = Model\Banner::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'banners',
                            'file' => 'edit',
                            'action' => 'edit',
                            'banner' => $banner
                        )
                    );
                    break;
            }


            $bannered = Model\Banner::getAll();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'banners',
                    'file' => 'list',
                    'bannered' => $bannered,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * preguntas frecuentes
         */
        public function faq($action = 'list', $id = null) {

            // secciones
            $sections = Model\Faq::sections();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'node';
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => '?filter=' . $filter
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $faq = new Model\Faq(array(
                    'id' => $_POST['id'],
                    'node' => \GOTEO_NODE,
                    'section' => $_POST['section'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'move' => $_POST['move']
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
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'faq' => $faq,
                            'filter' => $filter,
                            'sections' => $sections,
                            'errors' => $errors
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Faq::up($id);
                    break;
                case 'down':
                    Model\Faq::down($id);
                    break;
                case 'add':
                    $next = Model\Faq::next($filter);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'add',
                            'faq' => (object) array('section' => $filter, 'order' => $next, 'cuantos' => $next),
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'edit':
                    $faq = Model\Faq::get($id);

                    $cuantos = Model\Faq::next($faq->section);
                    $faq->cuantos = ($cuantos -1);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'edit',
                            'faq' => $faq,
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'remove':
                    Model\Faq::delete($id);
                    break;
            }

            $faqs = Model\Faq::getAll($filter);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'faq',
                    'file' => 'list',
                    'faqs' => $faqs,
                    'sections' => $sections,
                    'filter' => $filter,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * criterios de puntuación Goteo
         */
        public function criteria($action = 'list', $id = null) {

            // secciones
            $sections = Model\Criteria::sections();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'project';
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => '?filter=' . $filter
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $criteria = new Model\Criteria(array(
                    'id' => $_POST['id'],
                    'section' => $_POST['section'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'move' => $_POST['move']
                ));

				if ($criteria->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Criterio añadido correctamente';
                            break;
                        case 'edit':
                            $success = 'Criterio editado correctamente';
                            break;
                    }
				}
				else {
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'criteria',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'criteria' => $criteria,
                            'filter' => $filter,
                            'sections' => $sections,
                            'errors' => $errors
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Criteria::up($id);
                    break;
                case 'down':
                    Model\Criteria::down($id);
                    break;
                case 'add':
                    $next = Model\Criteria::next($filter);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'criteria',
                            'file' => 'edit',
                            'action' => 'add',
                            'criteria' => (object) array('section' => $filter, 'order' => $next, 'cuantos' => $next),
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'edit':
                    $criteria = Model\Criteria::get($id);

                    $cuantos = Model\Criteria::next($criteria->section);
                    $criteria->cuantos = ($cuantos -1);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'criteria',
                            'file' => 'edit',
                            'action' => 'edit',
                            'criteria' => $criteria,
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'remove':
                    Model\Criteria::delete($id);
                    break;
            }

            $criterias = Model\Criteria::getAll($filter);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'criteria',
                    'file' => 'list',
                    'criterias' => $criterias,
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
        public function icons($action = 'list', $id = null) {

            // grupos
            $groups = Model\Icon::groups();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $groups)) {
                $filter = $_GET['filter'];
            } else {
                $filter = '';
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filter) ? '?filter=' . $filter : ''
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $icon = new Model\Icon(array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'group' => empty($_POST['group']) ? null : $_POST['group']
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
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'icons',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'icon' => $icon,
                            'filter' => $filter,
                            'groups' => $groups,
                            'errors' => $errors
                        )
                    );
				}
			}

            switch ($action) {
                case 'add':
/*
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'icons',
                            'file' => 'edit',
                            'action' => 'add',
                            'icon' => (object) array('group' => ''),
                            'groups' => $groups
                        )
                    );
 *
 */
                    break;
                case 'edit':
                    $icon = Model\Icon::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'icons',
                            'file' => 'edit',
                            'action' => 'edit',
                            'icon' => $icon,
                            'filter' => $filter,
                            'groups' => $groups
                        )
                    );
                    break;
                case 'remove':
    //                Model\Icon::delete($id);
                    break;
            }

            $icons = Model\Icon::getAll($filter);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'icons',
                    'file' => 'list',
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
        public function licenses($action = 'list', $id = null) {

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

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? '?filter=' . serialize($filters) : ''
            ));

            define('ADMIN_BCPATH', $BC);

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
                    'url' => $_POST['url'],
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
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'licenses',
                            'file' => 'edit',
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

            switch ($action) {
                case 'up':
                    Model\License::up($id);
                    break;
                case 'down':
                    Model\License::down($id);
                    break;
                case 'add':
                    $next = Model\License::next();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'licenses',
                            'file' => 'edit',
                            'action' => 'add',
                            'license' => (object) array('order' => $next, 'icons' => array()),
                            'icons' => $icons,
                            'groups' => $groups
                        )
                    );
                    break;
                case 'edit':
                    $license = Model\License::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'licenses',
                            'file' => 'edit',
                            'action' => 'edit',
                            'license' => $license,
                            'filters' => $filters,
                            'icons' => $icons,
                            'groups' => $groups
                        )
                    );
                    break;
                case 'remove':
    //                Model\License::delete($id);
                    break;
            }

            $licenses = Model\License::getAll($filters['icon'], $filters['group']);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'licenses',
                    'file' => 'list',
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
         */
        public function posts($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add') {

                // esto es para añadir una entrada en la portada
                

                // objeto
                $post = new Model\Post(array(
                    'id' => $_POST['post'],
                    'order' => $_POST['order'],
                    'home' => $_POST['home']
                ));

				if ($post->update($errors)) {
                    $success[] = 'Entrada colocada en la portada correctamente';
				}
				else {
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'posts',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => $post,
                            'errors' => $errors
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Post::up($id, 'home');
                    break;
                case 'down':
                    Model\Post::down($id, 'home');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Post::next('home');

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'posts',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => (object) array('order' => $next)
                        )
                    );
                    break;
                case 'edit':
                    throw new Redirection('/admin/blog');
                    break;
                case 'remove':
                    // se quita de la portada solamente
                    Model\Post::remove($id, 'home');
                    break;
            }

            $posts = Model\Post::getAll('home');

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'posts',
                    'file' => 'list',
                    'posts' => $posts,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         * posts para pie
         */
        public function footer($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add') {

                // objeto
                $post = new Model\Post(array(
                    'id' => $_POST['post'],
                    'order' => $_POST['order'],
                    'footer' => $_POST['footer']
                ));

				if ($post->update($errors)) {
                    $success[] = 'Entrada colocada en el footer correctamente';
				}
				else {
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'footer',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => $post,
                            'errors' => $errors
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Post::up($id, 'footer');
                    break;
                case 'down':
                    Model\Post::down($id, 'footer');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Post::next('footer');

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'footer',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => (object) array('order' => $next)
                        )
                    );
                    break;
                case 'edit':
                    throw new Redirection('/admin/blog');
                    break;
                case 'remove':
                    Model\Post::remove($id, 'footer');
                    break;
            }

            $posts = Model\Post::getAll('footer');

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'footer',
                    'file' => 'list',
                    'posts' => $posts,
                    'errors' => $errors,
                    'success' => $success
                )
            );
        }

        /*
         *  Gestión de categorias de proyectos
         *  Si no la usa nadie se puede borrar
         */
        public function categories($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $model = 'Goteo\Model\Category';
            $url = '/admin/categories';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array(),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Categoría',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"',

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

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Categoría',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"',

                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'up':
                    $model::up($id);
                    break;
                case 'down':
                    $model::down($id);
                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'model' => 'category',
                    'addbutton' => 'Nueva categoría',
                    'data' => $model::getAll(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Categoría',
                        'numProj' => 'Proyectos',
                        'numUser' => 'Usuarios',
                        'order' => 'Prioridad',
                        'translate' => '',
                        'up' => '',
                        'down' => '',
                        'translate' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

        /*
         *  Gestión de tags de blog
         *  Si no lo usa ningun post se puede borrar
         */
        public function tags($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $model = 'Goteo\Model\Blog\Post\Tag';
            $url = '/admin/tags';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array(),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Tag',
                                        'name' => 'name',
                                        'type' => 'text'
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

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'blog' => 1
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Tag',
                                        'name' => 'name',
                                        'type' => 'text'
                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'model' => 'tag',
                    'addbutton' => 'Nuevo tag',
                    'data' => $model::getList(1),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Tag',
                        'used' => 'Entradas',
                        'translate' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

        /*
         *  administración de usuarios para superadmin
         */
        public function users($action = 'list', $id = null, $subaction = '') {

            $filters = array();
            $fields = array('status', 'interest', 'role', 'name');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $BC = self::menu(array(
                'section' => 'users',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? "?status={$filters['status']}&interest={$filters['interest']}" : ''
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            switch ($action)  {
                case 'add':

                    // si llega post: creamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $errors = array();

                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        $user = new Model\User();
                        $user->userid = $_POST['user'];
                        $user->name = $_POST['name'];
                        $user->email = $_POST['email'];
                        $user->password = $_POST['password'];
                        $user->save($errors);

                        if(empty($errors)) {
                          // mensaje de ok y volvemos a la lista de usuarios
                          Message::Info(Text::get('user-register-success'));
                          throw new Redirection('/admin/users');
                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $data = $_POST;
                        }
                    }

                    // vista de crear usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'add',
                            'data'=>$data,
                            'errors'=>$errors
                        )
                    );

                    break;
                case 'edit':

                    $user = Model\User::get($id);

                    // si llega post: actualizamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $errors = array();

                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        $user->email = $_POST['email'];
                        $user->password = $_POST['password'];

                        if($user->update($errors)) {
                          // mensaje de ok y volvemos a la lista de usuarios
                          Message::Info('Datos actualizados');
                          throw new Redirection('/admin/users');
                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $data = $_POST;
                        }
                    }

                    // vista de editar usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'edit',
                            'user'=>$user,
                            'data'=>$data,
                            'errors'=>$errors
                        )
                    );

                    break;
                case 'manage':

                    // si llega post: ejecutamos + mensaje + seguimos editando

                    /* Esto hay que pasarlo a un modelo */
                    switch ($subaction)  {
                        case 'ban':
                            $sql = "UPDATE user SET active = 0 WHERE id = ?";
                            Model\User::query($sql, array($id));
                            break;
                        case 'unban':
                            $sql = "UPDATE user SET active = 1 WHERE id = ?";
                            Model\User::query($sql, array($id));
                            break;
                        case 'show':
                            $sql = "UPDATE user SET hide = 0 WHERE id = ?";
                            Model\User::query($sql, array($id));
                            break;
                        case 'hide':
                            $sql = "UPDATE user SET hide = 1 WHERE id = ?";
                            Model\User::query($sql, array($id));
                            break;
                        case 'checker':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'checker')";
                            Model\User::query($sql, array(':user'=>$id));
                            break;
                        case 'nochecker':
                            $sql = "DELETE FROM user_role WHERE role_id = 'checker' AND user_id = ?";
                            Model\User::query($sql, array($id));
                            break;
                        case 'translator':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'translator')";
                            Model\User::query($sql, array(':user'=>$id));
                            break;
                        case 'notranslator':
                            $sql = "DELETE FROM user_role WHERE role_id = 'translator' AND user_id = ?";
                            Model\User::query($sql, array($id));
                            break;
                        case 'admin':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'admin')";
                            Model\User::query($sql, array(':user'=>$id));
                            break;
                        case 'noadmin':
                            $sql = "DELETE FROM user_role WHERE role_id = 'admin' AND user_id = ?";
                            Model\User::query($sql, array($id));
                            break;
                    }

                    $user = Model\User::get($id);

                    // vista de gestión de usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'manage',
                            'user'=>$user,
                            'errors'=>$errors,
                            'success'=>$success
                        )
                    );


                    break;
                case 'impersonate':

                    $user = Model\User::get($id);

                    // vista de acceso a suplantación de usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file'   => 'impersonate',
                            'user'   => $user
                        )
                    );

                    break;
                case 'list':
                default:
                    $users = Model\User::getAll($filters);
                    $status = array(
                                'active' => 'Activo',
                                'inactive' => 'Inactivo'
                            );
                    $interests = Model\User\Interest::getAll();
                    $roles = array(
                        'admin' => 'Administrador',
                        'checker' => 'Revisor',
                        'translator' => 'Traductor'
                    );

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'list',
                            'users'=>$users,
                            'filters' => $filters,
                            'name' => $name,
                            'status' => $status,
                            'interests' => $interests,
                            'roles' => $roles,
                            'errors' => $errors
                        )
                    );
                    break;
            }
        }

        /*
         *  Revisión de aportes
         */
        public function invests($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'accounting',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            // si estamos generando aportes cargamos la lista completa de usuarios, proyectos y campañas
           if ($action == 'add') {
               
                // listado de proyectos existentes
                $projects = Model\Project::getAll();
                // usuarios
                $users = Model\User::getAllMini();
                // campañas
                $campaigns = Model\Campaign::getAll();

                // aporte manual
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add']) ) {

                    $userData = Model\User::getMini($_POST['user']);

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $_POST['amount'],
                            'user'      => $userData->id,
                            'project'   => $_POST['project'],
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => 1,
                            'invested'  => date('Y-m-d'),
                            'charged'   => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign'    => 1,
                            'admin'     => $_SESSION['user']->id,
                            'campaign'  => $_POST['campaign']
                        )
                    );

                    if ($invest->save($errors)) {
                        $errors[] = 'Aporte manual creado correctamente';
                    } else{
                        $errors[] = 'Ha fallado algo al crear el aporte manual';
                    }

                }

                 $viewData = array(
                        'folder' => 'invests',
                        'file' => 'add',
                        'users'         => $users,
                        'projects'      => $projects,
                        'campaigns'     => $campaigns,
                        'errors'        => $errors
                    );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia

           } else {

               // sino, cargamos los filtros
                $filters = array();
                $fields = array('methods', 'status', 'investStatus', 'projects', 'users', 'campaigns');
                foreach ($fields as $field) {
                    if (isset($_GET[$field])) {
                        if (\is_numeric($_GET[$field])) {
                            $filters[$field] = (int) $_GET[$field];
                        } else {
                            $filters[$field] = (string) $_GET[$field];
                        }
                    }
                }

                // tipos de aporte
                $methods = Model\Invest::methods();
                // estados del proyecto
                $status = Model\Project::status();
                // estados de aporte
                $investStatus = Model\Invest::status();
                // listado de proyectos
                $projects = Model\Invest::projects();
                // usuarios cofinanciadores
                $users = Model\Invest::users();
                // campañas que tienen aportes
                $campaigns = Model\Invest::campaigns();

           }

            /// si piden unos detalles,
            if ($action == 'report') {
                $invest = Model\Invest::get($id);
                $project = Model\Project::get($invest->project);
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'report',
                        'invest'=>$invest,
                        'project'=>$project,
                        'details'=>$details,
                        'status'=>$status
                    )
                );
            }



            if ($action == 'execute') {
                $invest = Model\Invest::get($id);
                
                switch ($invest->method) {
                    case 'paypal':
                        if (Paypal::pay($invest, $errors)) {
                            $errors[] = 'Cargo paypal correcto';
                        } else {
                            $errors[] = 'Fallo al ejecutar cargo paypal: ' . implode('; ', $errors);
                        }
                        break;
                    case 'tpv':
                        if (Tpv::pay($invest, $errors)) {
                            $errors[] = 'Cargo sermepa correcto';
                        } else {
                            $errors[] = 'Fallo al ejecutar cargo sermepal: ' . implode('; ', $errors);
                        }
                        break;
                }

            }

            if ($action == 'return') {
                $invest = Model\Invest::get($id);

                switch ($invest->method) {
                    case 'paypal':
                        if (Paypal::cancelPreapproval($invest, $errors)) {
                            $errors[] = 'Preaproval paypal cancelado, aporte cancelado.';
                        } else {
                            $errors[] = 'Fallo al cancelar el preapproval en paypal: ' . implode('; ', $errors);
                            if ($invest->cancel()) {
                                $errors[] = 'Aporte cancelado';
                            } else{
                                $errors[] = 'Fallo al cancelar el aporte';
                            }
                        }
                        break;
                    case 'tpv':
                        if (Tpv::cancelPreapproval($invest, $errors)) {
                            $errors[] = 'Transacción sermepa cancelada, aporte cancelado.';
                        } else {
                            $errors[] = 'Fallo al cancelar la transaccion sermepa: ' . implode('; ', $errors);
                            if ($invest->cancel()) {
                                $errors[] = 'Aporte cancelado';
                            } else{
                                $errors[] = 'Fallo al cancelar el aporte';
                            }
                        }
                        break;
                    case 'cash':
                        if ($invest->cancel()) {
                            $errors[] = 'Aporte cancelado';
                        } else{
                            $errors[] = 'Fallo al cancelar el aporte';
                        }
                        break;
                }

            }

            // listado de aportes
             $list = Model\Invest::getList($filters);

             $viewData = array(
                    'folder' => 'invests',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'users'         => $users,
                    'projects'      => $projects,
                    'campaigns'     => $campaigns,
                    'methods'       => $methods,
                    'status'        => $status,
                    'investStatus'  => $investStatus,
                    'errors'        => $errors
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }


        /*
         * Gestión de retornos, por ahora en el admin pero es una gestión para los responsables de proyectos
         * Proyectos financiados, puede marcar un retorno cumplido
         */
        public function rewards($action = 'list', $id = null) {

            $filters = array();
            $fields = array('status', 'icon');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? "?status={$filters['status']}&icon={$filters['icon']}" : ''
            ));

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            switch ($action)  {
                case 'fulfill':
                    $sql = "UPDATE reward SET fulsocial = 1 WHERE type= 'social' AND id = ?";
                    Model\Project\Reward::query($sql, array($id));
                    break;
                case 'unfill':
                    $sql = "UPDATE reward SET fulsocial = 0 WHERE id = ?";
                    Model\Project\Reward::query($sql, array($id));
                    break;
            }

            $projects = Model\Project::published('success');

            foreach ($projects as $kay=>&$project) {
                $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', $filters['status'], $filters['icon']);
            }

            $status = array(
                        'nok' => 'Pendiente',
                        'ok'  => 'Cumplido'
                        
                    );
            $icons = Model\Icon::getAll('social');
            foreach ($icons as $key => $icon) {
                $icons[$key] = $icon->name;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'rewards',
                    'projects'=>$projects,
                    'filters' => $filters,
                    'status' => $status,
                    'icons' => $icons,
                    'errors' => $errors
                )
            );


        }

        /*
         * Gestión de entradas de blog
         */
        public function blog ($action = 'list', $id = null) {
            
            $errors = array();

            $blog = Model\Blog::get(\GOTEO_NODE, 'node');
            if (!$blog instanceof \Goteo\Model\Blog) {
                $errors[] = 'No tiene espacio de blog, Contacte con nosotros';
                $action = 'list';
            } else {
                if (!$blog->active) {
                    $errors[] = 'Lo sentimos, el blog para este nodo esta desactivado';
                    $action = 'list';
                }
            }

            // primero comprobar que tenemos blog
            if (!$blog instanceof Model\Blog) {
                $errors[] = 'No se ha encontrado ningún blog para este nodo';
                $action = 'list';
            }

            $url = '/admin/blog';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (empty($_POST['blog'])) {
                        break;
                    }

                    $editing = false;

                    if (!empty($_POST['id'])) {
                        $post = Model\Blog\Post::get($_POST['id']);
                    } else {
                        $post = new Model\Blog\Post();
                    }
                    // campos que actualizamos
                    $fields = array(
                        'id',
                        'blog',
                        'title',
                        'text',
                        'image',
                        'media',
                        'date',
                        'publish',
                        'home',
                        'footer',
                        'allow'
                    );

                    foreach ($fields as $field) {
                        $post->$field = $_POST[$field];
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image_upload']['name'])) {
                        $post->image = $_FILES['image_upload'];
                        $editing = true;
                    }

                    // tratar las imagenes que quitan
                    foreach ($post->gallery as $key=>$image) {
                        if (!empty($_POST["gallery-{$image->id}-remove"])) {
                            $image->remove('post');
                            unset($post->gallery[$key]);
                            if ($post->image == $image->id) {
                                $post->image = '';
                            }
                            $editing = true;
                        }
                    }

                    if (!empty($post->media)) {
                        $post->media = new Model\Project\Media($post->media);
                    }

                    $post->tags = $_POST['tags'];

                    /// este es el único save que se lanza desde un metodo process_
                    if ($post->save($errors)) {
                        if ($action == 'edit') {
                            $success[] = 'La entrada se ha actualizado correctamente';
                            ////Text::get('dashboard-project-updates-saved');
                        } else {
                            $success[] = 'Se ha añadido una nueva entrada';
                            ////Text::get('dashboard-project-updates-inserted');
                            $id = $post->id;
                        }
                        $action = $editing ? 'edit' : 'list';
                    } else {
                        $errors[] = 'Ha habido algun problema al guardar los datos';
                        ////Text::get('dashboard-project-updates-fail');
                    }
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            switch ($action)  {
                case 'remove':
                    // eliminar una entrada
                    if (Model\Blog\Post::delete($id)) {
                        unset($blog->posts[$id]);
                        $success[] = 'Entrada eliminada';
                    } else {
                        $errors[] = 'No se ha podido eliminar la entrada';
                    }
                case 'list':
                    // lista de entradas
                    // obtenemos los datos
                    $posts = Model\Blog\Post::getAll($blog->id, null, false);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'list',
                            'posts' => $posts,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
                case 'add':
                    // nueva entrada con wisiwig
                    // obtenemos datos basicos
                    $post = new Model\Blog\Post(
                            array(
                                'blog' => $blog->id,
                                'date' => date('Y-m-d'),
                                'publish' => false,
                                'allow' => true,
                                'tags' => array()
                            )
                        );

                    $message = 'Añadiendo una nueva entrada';

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'edit',
                            'action' => 'add',
                            'post' => $post,
                            'tags' => Model\Blog\Post\Tag::getAll(),
                            'message' => $message,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        throw new Redirection('/admin/blog');
//                        $errors[] = 'No se ha encontrado la entrada';
                        //Text::get('dashboard-project-updates-nopost');
//                        $action = 'list';
                        break;
                    } else {
                        $post = Model\Blog\Post::get($id);

                        if (!$post instanceof Model\Blog\Post) {
                            $errors[] = 'La entrada esta corrupta, contacte con nosotros.';
                            //Text::get('dashboard-project-updates-postcorrupt');
                            $action = 'list';
                            break;
                        }
                    }

                    $message = 'Editando una entrada existente';

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'edit',
                            'action' => 'edit',
                            'post' => $post,
                            'tags' => Model\Blog\Post\Tag::getAll(),
                            'message' => $message,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
            }

        }

        /*
         * Moderar mensajes
         * @TODO: está a medias
         *
         *
         *
        public function moderate($action = 'list', $id = null) {
            $filters = array();
            $fields = array('project', 'user', 'blog');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }
            
            // proyectos con mensajes
            $projects = Model\Project::published('available');
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'node';
            }


            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? "?project={$filters['project']}&user={$filters['user']}&blog={$filters['blog']}" : ''
            ));

            define('ADMIN_BCPATH', $BC);

            //blog

            // usuarios
            $users = Model\User::getAll(array('posted'=>true));

            $errors = array();

            switch ($action) {
                case 'remove':
                    // mensaje o comentario
                    // Model\   ::delete($id);
                    break;
            }

//            $list = Model\Message::getAll($filter);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'data' => $list,
                    'columns' => array(
                        'user' => 'Usuario',
                        'project' => 'Proyecto',
                        'message' => 'Mensaje',
                        'remove' => ''
                    ),
                    'url' => "/admin/moderate",
                    'errors' => $errors
                )
            );
        }
         * 
         */

        /*
         *  Gestión de noticias
         */
        public function news($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $model = 'Goteo\Model\News';
            $url = '/admin/news';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array('order' => $model::next()),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'title' => array(
                                        'label' => 'Noticia',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'properties' => 'size="100" maxlength="100"'
                                    ),
                                    'description' => array(
                                        'label' => 'Entradilla',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"'
                                    ),
                                    'url' => array(
                                        'label' => 'Enlace',
                                        'name' => 'url',
                                        'type' => 'text',
                                        'properties' => 'size=100'
                                    ),
                                    'order' => array(
                                        'label' => 'Posición',
                                        'name' => 'order',
                                        'type' => 'text'
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

                        // instancia
                        $item = new $model(array(
                            'id'          => $_POST['id'],
                            'title'       => $_POST['title'],
                            'description' => $_POST['description'],
                            'url'         => $_POST['url'],
                            'order'       => $_POST['order']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'title' => array(
                                        'label' => 'Noticia',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'properties' => 'size="100"  maxlength="80"'
                                    ),
                                    'description' => array(
                                        'label' => 'Entradilla',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"'
                                    ),
                                    'url' => array(
                                        'label' => 'Enlace',
                                        'name' => 'url',
                                        'type' => 'text',
                                        'properties' => 'size=100'
                                    ),
                                    'order' => array(
                                        'label' => 'Posición',
                                        'name' => 'order',
                                        'type' => 'text'
                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'up':
                    $model::up($id);
                    break;
                case 'down':
                    $model::down($id);
                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'model' => 'news',
                    'addbutton' => 'Nueva noticia',
                    'data' => $model::getAll(),
                    'columns' => array(
                        'edit' => '',
                        'title' => 'Noticia',
                        'url' => 'Enlace',
                        'order' => 'Posición',
                        'up' => '',
                        'down' => '',
                        'translate' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

        /*
         *  Gestión de patrocinadores
         */
        public function sponsors($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $model = 'Goteo\Model\Sponsor';
            $url = '/admin/sponsors';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array('order' => $model::next() ),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Patrocinador',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'url' => array(
                                        'label' => 'Enlace',
                                        'name' => 'url',
                                        'type' => 'text',
                                        'properties' => 'size=100'
                                    ),
                                    'image' => array(
                                        'label' => 'Logo',
                                        'name' => 'image',
                                        'type' => 'image'
                                    ),
                                    'order' => array(
                                        'label' => 'Posición',
                                        'name' => 'order',
                                        'type' => 'text'
                                    )
                                )

                            )
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        $errors = array();

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'url' => $_POST['url'],
                            'order' => $_POST['order']
                        ));

                        // tratar la imagen y ponerla en la propiedad image
                        if(!empty($_FILES['image']['name'])) {
                            $item->image = $_FILES['image'];
                        }

                        // tratar si quitan la imagen
                        $current = $_POST['image']; // la acual
                        if (isset($_POST['image-' . $current .  '-remove'])) {
                            $image = Model\Image::get($current);
                            $image->remove('sponsor');
                            $item->image = '';
                            $removed = true;
                        }

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Patrocinador',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'url' => array(
                                        'label' => 'Enlace',
                                        'name' => 'url',
                                        'type' => 'text',
                                        'properties' => 'size=100'
                                    ),
                                    'image' => array(
                                        'label' => 'Logo',
                                        'name' => 'image',
                                        'type' => 'image'
                                    ),
                                    'order' => array(
                                        'label' => 'Posición',
                                        'name' => 'order',
                                        'type' => 'text'
                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'up':
                    $model::up($id);
                    break;
                case 'down':
                    $model::down($id);
                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'addbutton' => 'Nuevo patrocinador',
                    'data' => $model::getAll(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Patrocinador',
                        'url' => 'Enlace',
                        'image' => 'Imagen',
                        'order' => 'Posición',
                        'up' => '',
                        'down' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

        /*
         *  Gestión de campañas
         */
        public function campaigns($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $model = 'Goteo\Model\Campaign';
            $url = '/admin/campaigns';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array(),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Campaña',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"'
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

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Campaña',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"'
                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'addbutton' => 'Nueva campaña',
                    'data' => $model::getList(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Campaña',
                        'used' => 'Aportes',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

        /*
         *  Gestión de nodos
         */
        public function nodes($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            $model = 'Goteo\Model\Node';
            $url = '/admin/nodes';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array(),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Campaña',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"'
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

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Campaña',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"'
                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'addbutton' => 'Nuevo nodo',
                    'data' => $model::getList(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Campaña',
                        'used' => 'Aportes',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }


        /*
         * Menu de secciones, opciones, acciones y config para el panel Admin
         *
         */
        private static function menu($BC = array()) {

            // si el breadcrumbs no es un array vacio,
            //   devolveremos el contenido html para pintar el camino de migas de pan
            //   con enlaces a lo anterior

            $menu = array(
                'contents' => array(
                    'label'   => 'Gestión de Textos y Traducciones',
                    'options' => array (
                        'blog' => array(
                            'label' => 'Blog',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Entrada', 'item' => false),
                                'edit' => array('label' => 'Editando Entrada', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Entrada', 'item' => true)
                            )
                        ),
                        'texts' => array(
                            'label' => 'Textos interficie',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Original', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Texto', 'item' => true)
                            )
                        ),
                        'faq' => array(
                            'label' => 'FAQs',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Pregunta', 'item' => false),
                                'edit' => array('label' => 'Editando Pregunta', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Pregunta', 'item' => true)
                            )
                        ),
                        'pages' => array(
                            'label' => 'Páginas institucionales',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Página', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Página', 'item' => true)
                            )
                        ),
                        'categories' => array(
                            'label' => 'Categorias e Intereses',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Categoría', 'item' => false),
                                'edit' => array('label' => 'Editando Categoría', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Categoría', 'item' => true)
                            )
                        ),
                        'licenses' => array(
                            'label' => 'Licencias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Licencia', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Licencia', 'item' => true)
                            )
                        ),
                        'icons' => array(
                            'label' => 'Tipos de Retorno',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Tipo', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Tipo', 'item' => true)
                            )
                        ),
                        'tags' => array(
                            'label' => 'Tags de blog',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Tag', 'item' => false),
                                'edit' => array('label' => 'Editando Tag', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Tag', 'item' => true)
                            )
                        ),
                        'criteria' => array(
                            'label' => 'Criterios de revisión',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Criterio', 'item' => false),
                                'edit' => array('label' => 'Editando Criterio', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Criterio', 'item' => true)
                            )
                        ),
                        'templates' => array(
                            'label' => 'Plantillas de email',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Plantilla', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Plantilla', 'item' => true)
                            )
                        )
                    )
                ),
                'projects' => array(
                    'label'   => 'Gestión de proyectos',
                    'options' => array (
                        'overview' => array(
                            'label' => 'Listado de proyectos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false)
                            )
                        ),
                        'reviews' => array(
                            'label' => 'Revisión de proyectos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Iniciando briefing', 'item' => false),
                                'edit' => array('label' => 'Editando briefing', 'item' => true),
                                'report' => array('label' => 'Informe', 'item' => true)
                            )
                        ),
                        'rewards' => array(
                            'label' => 'Gestión de retornos colectivos cumplidos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false)
                            )
                        )
                    )
                ),
                'users' => array(
                    'label'   => 'Gestión de usuarios',
                    'options' => array (
                        'users' => array(
                            'label' => 'Listado de usuarios',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add' => array('label' => 'Creando Usuario', 'item' => true),
                                'edit' => array('label' => 'Editando Usuario', 'item' => true),
                                'manage' => array('label' => 'Gestionando Usuario', 'item' => true),
                                'impersonate' => array('label' => 'Suplantando al Usuario', 'item' => true)
                            )
                        )/*,
                        'useradd' => array(
                            'label' => 'Creación de usuarios',
                            'actions' => array(
                                'add'  => array('label' => 'Nuevo Usuario', 'item' => false)
                            )
                        ),
                        'usermod' => array(
                            'label' => 'Gestión de roles y nodos de Usuarios',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando roles y nodos de Usuario', 'item' => true)
                            )
                        )*/
                    )
                ),
                'accounting' => array(
                    'label'   => 'Gestión de transferencias bancarias',
                    'options' => array (
                        'invests' => array(
                            'label' => 'Aportes a Proyectos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Aporte manual', 'item' => false),
                                'report' => array('label' => 'Verificando Aporte', 'item' => true),
                                'execute' => array('label' => 'Ejecución manual', 'item' => true)
                            )
                        )/*,
                        'donations' => array(
                            'label' => 'Gestión de donativos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Donativo manual', 'item' => false),
                                'report' => array('label' => 'Verificando Donativo', 'item' => true),
                                'execute' => array('label' => 'Ejecución manual', 'item' => true)
                            )
                        ),
                        'tips' => array(
                            'label' => 'Propinas a Goteo',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Propina manual', 'item' => false),
                                'report' => array('label' => 'Verificando Propina', 'item' => true),
                                'execute' => array('label' => 'Ejecución manual', 'item' => true)
                            )
                        ),
                        'credits' => array(
                            'label' => 'Gestión de crédito',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo ', 'item' => false),
                                'edit' => array('label' => 'Editando Tag', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Tag', 'item' => true)
                            )
                        )*/
                    )
                ),
                'home' => array(
                    'label'   => 'Portada',
                    'options' => array (
                        'news' => array(
                            'label' => 'Micronoticias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Micronoticia', 'item' => false),
                                'edit' => array('label' => 'Editando Micronoticia', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Micronoticia', 'item' => true)
                            )
                        ),
                        'banners' => array(
                            'label' => 'Banners',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Banner', 'item' => false),
                                'edit' => array('label' => 'Editando Banner', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Banner', 'item' => true)
                            )
                        ),
                        'posts' => array(
                            'label' => 'Carrusel de blog',
                            'actions' => array(
                                'list' => array('label' => 'Ordenando', 'item' => false),
                                'add'  => array('label' => 'Colocando Entrada en la portada', 'item' => false)
                            )
                        ),
                        'promote' => array(
                            'label' => 'Proyectos destacados',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Destacado', 'item' => false),
                                'edit' => array('label' => 'Editando Destacado', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Destacado', 'item' => true)
                            )
                        ),
                        'footer' => array(
                            'label' => 'Entradas en el footer',
                            'actions' => array(
                                'list' => array('label' => 'Ordenando', 'item' => false),
                                'add'  => array('label' => 'Colocando Entrada en el footer', 'item' => false)
                            )
                        )
                    )
                ),
                'sponsors' => array(
                    'label'   => 'Convocatorias de patrocinadores',
                    'options' => array (
                        'sponsors' => array(
                            'label' => 'Apoyos institucionales',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Patrocinador', 'item' => false),
                                'edit' => array('label' => 'Editando Patrocinador', 'item' => true)
                            )
                        ),
                        'campaigns' => array(
                            'label' => 'Gestión de campañas',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Campaña', 'item' => false),
                                'edit' => array('label' => 'Editando Campaña', 'item' => true),
                                'report' => array('label' => 'Informe de estado de la Campaña', 'item' => true)
                            )
                        )/*,
                        'nodes' => array(
                            'label' => 'Gestión de Nodos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Nodo', 'item' => false),
                                'edit' => array('label' => 'Editando Nodo', 'item' => true)
                            )
                        )*/
                    )
                )
            );

            if (empty($BC)) {
                return $menu;
            } else {
                // Los últimos serán los primeros
                $path = '';
                
                // si el BC tiene Id, accion sobre ese registro
                // si el BC tiene Action
                if (!empty($BC['action'])) {

                    // si es una accion no catalogada, mostramos la lista
                    if (!in_array(
                            $BC['action'],
                            array_keys($menu[$BC['section']]['options'][$BC['option']]['actions'])
                        )) {
                        $BC['action'] = 'list';
                        $BC['id'] = null;
                    }

                    $action = $menu[$BC['section']]['options'][$BC['option']]['actions'][$BC['action']];
                    // si es de item , añadir el id (si viene)
                    if ($action['item'] && !empty($BC['id'])) {
                        $path = " &gt; <strong>{$action['label']}</strong> {$BC['id']}";
                    } else {
                        $path = " &gt; <strong>{$action['label']}</strong>";
                    }
                }

                // si el BC tiene Option, enlace a la portada de esa gestión
                if (!empty($BC['option'])) {
                    $option = $menu[$BC['section']]['options'][$BC['option']];
                    $path = ' &gt; <a href="/admin/'.$BC['option'].''.$BC['filter'].'">'.$option['label'].'</a>'.$path;
                }

                // si el BC tiene section, facil, enlace al admin
                if (!empty($BC['section'])) {
                    $section = $menu[$BC['section']];
                    $path = '<a href="/admin#'.$BC['section'].'">'.$section['label'].'</a>' . $path;
                }
                return $path;
            }


        }


	}

}
