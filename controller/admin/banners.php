<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Banners {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $banner = new Model\Banner(array(
                    'node' => \GOTEO_NODE,
                    'project' => $_POST['project'],
                    'order' => $_POST['order']
                ));

                // imagen
                if(!empty($_FILES['image']['name'])) {
                    $banner->image = $_FILES['image'];
                } else {
                    $banner->image = $_POST['prev_image'];
                }

				if ($banner->save($errors)) {
                    Message::Info('Datos guardados');

                    if ($_POST['action'] == 'add') {
                        $projectData = Model\Project::getMini($_POST['project']);

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('nuevo banner de proyecto destacado en portada (admin)', '/admin/promote',
                            \vsprintf('El admin %s ha %s del proyecto %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Publicado un banner', '/'),
                            Feed::item('project', $projectData->name, $projectData->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    }

				}
				else {
                    Message::Error(implode('<br />', $errors));
                    
                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'banners',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'banner' => $banner,
                                    'status' => $status
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
                                    'banenr' => $banner
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
                    if (Model\Banner::delete($id)) {
                        $projectData = Model\Project::getMini($id);

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('banner de proyecto quitado portada (admin)', '/admin/promote',
                            \vsprintf('El admin %s ha %s del proyecto %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Quitado el banner', '/'),
                                Feed::item('project', $projectData->name, $projectData->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    }
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
                    'bannered' => $bannered
                )
            );
            
        }

    }

}
