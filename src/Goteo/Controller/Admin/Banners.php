<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Banners {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $errors = array();

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $banner = (isset($id)) ? Model\Banner::get($id) : new Model\Banner;
                $banner->node = $node;
                $banner->project = $_POST['item'];
                $banner->title = $_POST['title'];
                $banner->description = $_POST['description'];
                $banner->url = $_POST['url'];
                $banner->order = $_POST['order'];
                $banner->active = $_POST['active'];

                // tratar si quitan la imagen
                if (!empty($_POST['image-' . $banner->image->hash .  '-remove'])) {
                    if ($banner->image instanceof Model\Image) $banner->image->remove($errors);
                    $banner->image = null;
                }

                // nueva imagen
                if(!empty($_FILES['image']['name'])) {
                    if ($banner->image instanceof Model\Image) $banner->image->remove($errors);
                    $banner->image = $_FILES['image'];
                } else {
                    $banner->image = $banner->image->id;
                }

				if ($banner->save($errors)) {
                    Message::Info('Datos guardados');

                    if ($_POST['action'] == 'add') {
                        $projectData = Model\Project::getMini($_POST['project']);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log->populate('nuevo banner de proyecto destacado en portada (admin)', '/admin/promote',
                            \vsprintf('El admin %s ha %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Publicado un banner', '/')
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Banner::setPending($banner->id, 'banner')) {
                        Message::Error('NO se ha marcado como pendiente de traducir!');
                    }

                    throw new Redirection('/admin/banners');
				}
				else {
                    Message::Error(implode('<br />', $errors));

                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'banners',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'banner' => $banner,
                                    'autocomplete' => true
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'banners',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'banner' => $banner,
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
                    Model\Banner::setActive($id, $set);
                    throw new Redirection('/admin/banners');
                    break;
                case 'up':
                    Model\Banner::up($id, $node);
                    throw new Redirection('/admin/banners');
                    break;
                case 'down':
                    Model\Banner::down($id, $node);
                    throw new Redirection('/admin/banners');
                    break;
                case 'remove':
                    if (Model\Banner::delete($id)) {
                        Message::Info('Banner quitado correctamente');
                    } else {
                        Message::Error('No se ha podido quitar el banner');
                    }
                    throw new Redirection('/admin/banners');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Banner::next($node);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'banners',
                            'file' => 'edit',
                            'action' => 'add',
                            'banner' => (object) array('order' => $next),
                            'autocomplete' => true
                        )
                    );
                    break;
                case 'edit':
                    $banner = Model\Banner::get($id);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'banners',
                            'file' => 'edit',
                            'action' => 'edit',
                            'banner' => $banner,
                            'autocomplete' => true
                        )
                    );
                    break;
            }


            $bannered = Model\Banner::getAll(false, $node);

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'banners',
                    'file' => 'list',
                    'bannered' => $bannered,
                    'node' => $node
                )
            );

        }

    }

}
