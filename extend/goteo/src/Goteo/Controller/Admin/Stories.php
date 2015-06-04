<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Stories {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $errors = array();

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $el_item = $_POST['item'];
                error_log($el_item);
                if (!empty($el_item)) {
                    $post = $el_item;
                } else {
                    $post = null;
                }

                // objeto
                $story = new Model\Stories(array(
                    'id' => $_POST['id'],
                    'node' => $node,
                    'project' => $_POST['project'],
                    'order' => $_POST['order'],
                    'image' => $_POST['image'],
                    'active' => $_POST['active'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'review' => $_POST['review'],
                    'url' => $_POST['url'],
                    'post' => $post
                ));

                // imagen
                if(!empty($_FILES['image']['name'])) {
                    $story->image = $_FILES['image'];
                } else {
                    $story->image = $_POST['prev_image'];
                }

				if ($story->save($errors)) {
                    Message::info('Datos guardados');

                    if ($_POST['action'] == 'add') {
                        $projectData = Model\Project::getMini($_POST['project']);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log->populate('nueva historia exitosa en portada (admin)', '/admin/promote',
                            \vsprintf('El admin %s ha %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Publicado una historia exitosa', '/')
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Stories::setPending($story->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                    throw new Redirection('/admin/stories');
				}
				else {
                    Message::error(implode('<br />', $errors));

                    // otros elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'stories',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'story' => $story,
                                    'status' => $status,
                                    'items' => $items,
                                    'autocomplete' => true
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'stories',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'story' => $story,
                                    'items' => $items,
                                    'autocomplete' => true
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Stories::setActive($id, $set);
                    throw new Redirection('/admin/stories');
                    break;
                case 'up':
                    Model\Stories::up($id, $node);
                    throw new Redirection('/admin/stories');
                    break;
                case 'down':
                    Model\Stories::down($id, $node);
                    throw new Redirection('/admin/stories');
                    break;
                case 'remove':
                    if (Model\Stories::delete($id)) {
                        Message::info('Historia quitada correctamente');
                    } else {
                        Message::error('No se ha podido quitar la historia');
                    }
                    throw new Redirection('/admin/stories');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Stories::next($node);
                    // elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'stories',
                            'file' => 'edit',
                            'action' => 'add',
                            'story' => (object) array('order' => $next),
                            'status' => $status,
                            'items' => $items,
                            'autocomplete' => true
                        )
                    );
                    break;
                case 'edit':
                    // datos del elemento
                    $story = Model\Stories::get($id);
                    // elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'stories',
                            'file' => 'edit',
                            'action' => 'edit',
                            'story' => $story,
                            'items' => $items,
                            'autocomplete' => true
                        )
                    );
                    break;
                    case 'preview':
                            // datos del elemento
                            $story = Model\Stories::get($id);

                            return new View(
                                'admin/stories/preview.html.php',
                                array(
                                    'story' =>$story,
                                    'action' => 'preview'

                                )
                            );
                            break;
            }

            $storyed = Model\Stories::getList($node);

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'stories',
                    'file' => 'list',
                    'storyed' => $storyed,
                    'node' => $node
                )
            );

        }

    }

}
