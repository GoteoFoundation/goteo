<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Application\Message,
        Goteo\Model;

    class Bazar {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $errors = array();



            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

//die(\trace($_FILES));

                $el_item = $_POST['item'];
                if (!empty($el_item)) {
                    list($el_reward, $el_project, $el_amount) = explode('¬', $el_item);
                } else {
                    $el_reward = $el_project = $el_amount = null;
                }

                // objeto
                $promo = new Model\Bazar(array(
                    'id' => $_POST['id'],
                    'reward' => $el_reward,
                    'project' => $el_project,
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'amount' => $el_amount,
                    'order' => $_POST['order'],
                    'active' => $_POST['active']
                ));
                // imagen
                if(!empty($_FILES['image']['name'])) {
                    $promo->image = $_FILES['image'];
                } else {
                    $promo->image = $_POST['prev_image'];
                }

                if ($promo->save($errors)) {

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Bazar::setPending($promo->id, 'bazar')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                    throw new Redirection('/admin/bazar');
				}
				else {

                    Message::error(implode(', ', $errors));

                    // otros elementos disponibles
                    $items = Model\Bazar::available($promo->reward);

                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'bazar',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'promo' => $promo,
                                    'items' => $items,
                                    'autocomplete'  => true
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'bazar',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'promo' => $promo,
                                    'items' => $items,
                                    'autocomplete'  => true
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Bazar::setActive($id, $set);
                    throw new Redirection('/admin/bazar');
                    break;
                case 'up':
                    Model\Bazar::up($id);
                    throw new Redirection('/admin/bazar');
                    break;
                case 'down':
                    Model\Bazar::down($id);
                    throw new Redirection('/admin/bazar');
                    break;
                case 'remove':
                    if (Model\Bazar::delete($id)) {
                        Message::info('elemento quitado correctamente');
                    } else {
                        Message::error('No se ha podido quitar el elemento');
                    }
                    throw new Redirection('/admin/bazar');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Bazar::next();
                    // elementos disponibles
                    $items = Model\Bazar::available();

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'bazar',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next),
                            'items' => $items,
                            'autocomplete'  => true
                        )
                    );
                    break;
                case 'edit':
                    // datos del elemento
                    $promo = Model\Bazar::get($id);
                    // otros elementos disponibles
                    $items = Model\Bazar::available($promo->reward);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'bazar',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo,
                            'items' => $items,
                            'autocomplete'  => true
                        )
                    );
                    break;
            }


            $items = Model\Bazar::getList();

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'bazar',
                    'file' => 'list',
                    'items' => $items
                )
            );

        }

    }

}
