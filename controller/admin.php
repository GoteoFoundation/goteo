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
        Goteo\Library\Blog,
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
            $data = Text::getAll($filters);
            foreach ($data as $key=>$item) {
                $data[$key]->group = $groups[$item->group];
            }

            switch ($action) {
                case 'list':
                    return new View(
                        'view/admin/list.html.php',
                        array(
                            'title' => 'Gestión de textos',
                            'menu' => array(),
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
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo un nuevo texto",
                            'menu' => array(
                                array(
                                    'url'=>'/admin/texts/'.$filter,
                                    'label'=>'Textos'
                                )
                            ),
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
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        $errors = array();

                        $id = $_POST['id'];
                        $text = $_POST['text'];

                        $data = array(
                            'id' => $id,
                            'text' => $_POST['text'],
                            'lang' => \GOTEO_DEFAULT_LANG
                        );

                        if (Text::save($data, $errors)) {
                            throw new Redirection("/admin/texts/$filter");
                        }
                    } else {
                        $text = Text::get($id);
                    }

                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando el texto '$id'",
                            'menu' => array(
                                array(
                                    'url'=>'/admin/texts/'.$filter,
                                    'label'=>'Textos'
                                )
                            ),
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
         *  Lista de proyectos
         */
        public function overview($action = 'list', $id = null) {
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

            $projects = Model\Project::getList($filters);
            $status = Model\Project::status();
            $categories = Model\Project\Category::getAll();

            return new View(
                'view/admin/overview.html.php',
                array(
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
        public function checking($action = 'list', $id = null) {
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
                            
                            throw new Redirection('/admin/checking/' . $filter);
                        }
                    }
                    
                    return new View(
                        'view/admin/reviewEdit.html.php',
                        array(
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
                        'view/review/report.html.php',
                        array(
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
                'view/admin/checking.html.php',
                array(
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
                            // proyectos disponibles para promocionar y su estado
                            $projects = Model\Promote::available();
                            $status = Model\Project::status();

                            return new View(
                                'view/admin/promoEdit.html.php',
                                array(
                                    'action' => 'add',
                                    'promo' => $promo,
                                    'projects' => $projects,
                                    'status' => $status,
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
                    $status = Model\Project::status();

                    // siguiente orden
                    $next = Model\Promote::next();

                    return new View(
                        'view/admin/promoEdit.html.php',
                        array(
                            'action' => 'add',
                            'promo' => (object) array('order' => $next),
                            'projects' => $projects,
                            'status' => $status
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
        public function faq($action = 'list', $id = null) {
            // secciones
            $sections = Model\Faq::sections();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'node';
            }

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
                        'view/admin/faqEdit.html.php',
                        array(
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
                        'view/admin/faqEdit.html.php',
                        array(
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
                        'view/admin/faqEdit.html.php',
                        array(
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
                        'view/admin/criteriaEdit.html.php',
                        array(
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
                        'view/admin/criteriaEdit.html.php',
                        array(
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
                        'view/admin/criteriaEdit.html.php',
                        array(
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
                'view/admin/criteria.html.php',
                array(
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

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
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
                        'view/admin/iconEdit.html.php',
                        array(
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
                        'view/admin/iconEdit.html.php',
                        array(
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
                        'view/admin/licenseEdit.html.php',
                        array(
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
                        'view/admin/licenseEdit.html.php',
                        array(
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
        public function posts($action = 'list', $id = null, $type = 'home') {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $post = new Model\Post(array(
                    'id' => $_POST['id'],
                    'blog' => $_POST['blog'],
                    'title' => $_POST['title'],
                    'text' => $_POST['text'],
                    'media' => $_POST['media'],
                    'order' => $_POST['order'],
                    'home' => $_POST['home'],
                    'footer' => $_POST['footer']
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
                            throw new Redirection('/admin/blog');
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
                                    'type' => $this['type'],
                                    'post' => $post,
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
                            throw new Redirection('/admin/blog');
                            /*
                            return new View(
                                'view/admin/postEdit.html.php',
                                array(
                                    'action' => 'edit',
                                    'type' => $this['type'],
                                    'post' => $post,
                                    'errors' => $errors
                                )
                            );
                             *
                             */
                            break;
                    }
				}
			}


            switch ($action) {
                case 'up':
                    Model\Post::up($id, $type);
                    break;
                case 'down':
                    Model\Post::down($id, $type);
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Post::next($type);

                    return new View(
                        'view/admin/postEdit.html.php',
                        array(
                            'action' => 'add',
                            'post' => (object) array('order' => $next)
                        )
                    );
                    break;
                case 'edit':
                    throw new Redirection('/admin/blog');
                    /*
                    $post = Model\Post::get($id);

                    return new View(
                        'view/admin/postEdit.html.php',
                        array(
                            'action' => 'edit',
                            'post' => $post
                        )
                    );
                     * 
                     */
                    break;
                case 'remove':
                    Model\Post::delete($id);
                    break;
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
         *  Gestión de categorias de proyectos
         *  Si no la usa nadie se puede borrar
         */
        public function categories($action = 'list', $id = null) {

            $model = 'Goteo\Model\Category';
            $url = '/admin/categories';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo una nueva categoría",
                            'menu' => array(
                                array(
                                    'url' => $url,
                                    'label' => 'Categorías'
                                )
                            ),
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
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando una categoría",
                            'menu' => array(
                                array(
                                    'url' => $url,
                                    'label' => 'Categorias'
                                )
                            ),
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
                'view/admin/list.html.php',
                array(
                    'title' => 'Gestión de categorías',
                    'menu' => array(
                        array(
                            'url' => "$url/add",
                            'label' => 'Nueva categoría'
                        )
                    ),
                    'data' => $model::getAll(),
                    'columns' => array(
                        'name' => 'Categoría',
                        'numProj' => 'Proyectos',
                        'numUser' => 'Usuarios',
                        'order' => 'Prioridad',
                        'up' => '',
                        'down' => '',
                        'edit' => '',
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

            $model = 'Goteo\Model\Blog\Post\Tag';
            $url = '/admin/tags';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo un nuevo tag de blog",
                            'menu' => array(
                                array(
                                    'url'   => $url,
                                    'label' => 'Tags'
                                )
                            ),
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
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando un tag de blog",
                            'menu' => array(
                                array(
                                    'url'   => $url,
                                    'label' => 'Tags'
                                )
                            ),
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
                'view/admin/list.html.php',
                array(
                    'title' => 'Gestión de tags para blog',
                    'menu' => array(
                        array(
                            'url' => "$url/add",
                            'label' => 'Nuevo tag'
                        ),
                        array (
                            'url' => '/admin/blog',
                            'label' => 'Gestionar Blog'
                        )
                    ),
                    'data' => $model::getList(1),
                    'columns' => array(
                        'name' => 'Tag',
                        'used' => 'Entradas',
                        'edit' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

        /*
         *  administración de nodos y usuarios (segun le permita el ACL al usuario validado)
         */
        public function managing($action = 'list', $id = null) {
            $filters = array();
            $fields = array('status', 'interest');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $errors = array();

            switch ($action)  {
                case 'ban':
                    $sql = "UPDATE user SET active = 0 WHERE id = ?";
                    Model\User::query($sql, array($id));
                    break;
                case 'unban':
                    $sql = "UPDATE user SET active = 1 WHERE id = ?";
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
            }

            $users = Model\User::getAll($filters);
            $status = array(
                        'active' => 'Activo',
                        'inactive' => 'Inactive'
                    );
            $interests = Model\User\Interest::getAll();

            return new View(
                'view/admin/managing.html.php',
                array(
                    'users'=>$users,
                    'filters' => $filters,
                    'status' => $status,
                    'interests' => $interests,
                    'errors' => $errors
                )
            );

        }

        /*
         *  Revisión de aportes
         *
         * dummy para ejecutar cargos
         */
        public function accounting($action = 'list', $id = null) {
            // estados del proyecto
            $status = Model\Project::status();
            // estados de aporte
            $investStatus = Model\Invest::status();


            /// si piden unos detalles,
            if ($action == 'details') {
                $invest = Model\Invest::get($id);
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
                    'errors' => $errors
                )
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

            $errors = array();

            switch ($action)  {
                case 'fulfill':
                    $sql = "UPDATE reward SET fulsocial = 1 WHERE type= 'social' AND id = ?";
                    Model\Project\Reward::query($sql, array($id));
                    break;
                /*
                case 'unfill':
                    $sql = "UPDATE reward SET fulsocial = 0 WHERE id = ?";
                    Model\Project\Reward::query($sql, array($id));
                    break;
                 * 
                 */
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
                'view/admin/rewards.html.php',
                array(
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
            
            $url = '/admin/blog';

            /*
             * Filtro de fecha
            $filters = array();
            $fields = array('date');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }
             * 
             */

            $errors = array();

            $blog = Model\Blog::get(\GOTEO_NODE, 'node');
            if (!$blog instanceof \Goteo\Model\Blog) {
                $errors[] = 'No tiene espacio de blog, Contacte con nosotros';
                $action = 'list';
            } else {
                if (!$blog->active) {
                    $errors[] = 'Lo sentimos, las actualizaciones para este proyecto estan desactivadas';
                    $action = 'list';
                }
            }

            // primero comprobar que tenemos blog
            if (!$blog instanceof Model\Blog) {
                $errors[] = 'No se ha encontrado ningún blog para este nodo';
                $action = 'list';
            }

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (empty($_POST['blog'])) {
                        break;
                    }
                    
                    $post = new Model\Blog\Post();
                    // campos que actualizamos
                    $fields = array(
                        'id',
                        'blog',
                        'title',
                        'text',
                        'image',
                        'media',
                        'date',
                        'home',
                        'allow'
                    );

                    foreach ($fields as $field) {
                        $post->$field = $_POST[$field];
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image_upload']['name'])) {
                        $post->image = $_FILES['image_upload'];
                    }

                    // tratar si quitan la imagen
                    if (isset($_POST['image-' . $post->image .  '-remove'])) {
                        $image = Model\Image::get($post->image);
                        $image->remove('post');
                        $post->image = '';
                        $removed = true;
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
                        }
                        $action = $removed ? 'edit' : 'list';
                    } else {
                        $errors[] = 'Ha habido algun problema al guardar los datos';
                        ////Text::get('dashboard-project-updates-fail');
                    }
            }

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
                    $posts = Model\Blog\Post::getAll($blog->id);

                    return new View(
                        'view/admin/blog.html.php',
                        array(
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
                                'allow' => true,
                                'tags' => array()
                            )
                        );

                    $message = 'Añadiendo una nueva entrada';

                    return new View(
                        'view/admin/blogEdit.html.php',
                        array(
                            'action' => 'add',
                            'post' => $post,
                            'tags' => Model\Blog\Post\Tag::getAll(),
                            'message' => $message,
                            'errors' => $errors
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        $errors[] = 'No se ha encontrado la entrada';
                        //Text::get('dashboard-project-updates-nopost');
                        $action = 'list';
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
                        'view/admin/blogEdit.html.php',
                        array(
                            'action' => 'edit',
                            'post' => $post,
                            'tags' => Model\Blog\Post\Tag::getAll(),
                            'message' => $message,
                            'errors' => $errors
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
         */
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
                'view/admin/list.html.php',
                array(
                    'title' => 'Moderación de mensajes',
                    'menu' => array(),
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

        /*
         *  Gestión de noticias
         */
        public function news($action = 'list', $id = null) {

            $model = 'Goteo\Model\News';
            $url = '/admin/news';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo una nueva noticia",
                            'menu' => array(
                                array(
                                    'url' => $url,
                                    'label' => 'Noticias'
                                )
                            ),
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
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando una noticia",
                            'menu' => array(
                                array(
                                    'url' => $url,
                                    'label' => 'Noticias'
                                )
                            ),
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
                'view/admin/list.html.php',
                array(
                    'title' => 'Gestión de noticias',
                    'menu' => array(
                        array(
                            'url' => "$url/add",
                            'label' => 'Nueva noticia'
                        )
                    ),
                    'data' => $model::getAll(),
                    'columns' => array(
                        'title' => 'Noticia',
                        'url' => 'Enlace',
                        'order' => 'Posición',
                        'up' => '',
                        'down' => '',
                        'edit' => '',
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

            $model = 'Goteo\Model\Sponsor';
            $url = '/admin/sponsors';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo un nuevo patrocinador",
                            'menu' => array(
                                array(
                                    'url' => $url,
                                    'label' => 'Patrocinadores'
                                )
                            ),
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
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando un patrocinador",
                            'menu' => array(
                                array(
                                    'url' => $url,
                                    'label' => 'Patrocinadores'
                                )
                            ),
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
                'view/admin/list.html.php',
                array(
                    'title' => 'Gestión de patrocinadores',
                    'menu' => array(
                        array(
                            'url' => "$url/add",
                            'label' => 'Nuevo patrocinador'
                        )
                    ),
                    'data' => $model::getAll(),
                    'columns' => array(
                        'name' => 'Patrocinador',
                        'url' => 'Enlace',
                        'image' => 'Imagen',
                        'order' => 'Posición',
                        'up' => '',
                        'down' => '',
                        'edit' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }

	}

}


        /*
         *  Gestión de intereses de usuarios es obsoleta
         *  se usan las mismas categorias de proyecto
         *
        public function interests($action = 'list', $id = null) {

            throw new Redirection('/admin/categories');

            $model = 'Goteo\Model\Interest';
            $url = '/admin/interests';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Añadiendo un nuevo interés de usuarios",
                            'menu' => array(
                                array(
                                    'url'   => $url,
                                    'label' => 'Intereses'
                                )
                            ),
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
                                        'label' => 'Interés',
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
                        'view/admin/edit.html.php',
                        array(
                            'title' => "Editando un interés de usuario",
                            'menu' => array(
                                array(
                                    'url'   => $url,
                                    'label' => 'Intereses'
                                )
                            ),
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
                                        'label' => 'Interés',
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
                'view/admin/list.html.php',
                array(
                    'title' => 'Gestión de intereses de usuarios',
                    'menu' => array(
                        array(
                            'url' => "$url/add",
                            'label' => 'Nuevo interés'
                        )
                    ),
                    'data' => $model::getAll(),
                    'columns' => array(
                        'name' => 'Interes',
                        'used' => 'Usuarios',
                        'order' => 'Prioridad',
                        'up' => '',
                        'down' => '',
                        'edit' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'errors' => $errors
                )
            );
        }
         *
         */
