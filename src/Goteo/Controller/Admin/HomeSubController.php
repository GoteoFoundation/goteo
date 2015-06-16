<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Model;

class HomeSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array(), $type = 'main') {

        $node = $this->node;
        if ($node === Config::get('node') || empty($type)) {
            $type = 'main';
        }

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $item = new Model\Home(array(
                'item' => $this->getPost('item'),
                'type' => $this->getPost('type'),
                'node' => $node,
                'order' => $this->getPost('order'),
                'move' => 'down'
            ));

			if ($item->save($errors)) {
                // ok, sin mensaje porque todo se gestiona en la portada
                // Message::info('Elemento añadido correctamente');
			} else {
                Message::error(implode('<br />', $errors));
            }
		}


        switch ($action) {
            case 'remove':
                Model\Home::delete($id, $node, $type);
                return $this->redirect('/admin/home');
                break;
            case 'up':
                Model\Home::up($id, $node, $type);
                return $this->redirect('/admin/home');
                break;
            case 'down':
                Model\Home::down($id, $node, $type);
                return $this->redirect('/admin/home');
                break;
            /*
            case 'add':
                $next = Model\Home::next($node, 'main');
                $availables = Model\Home::available($node);

                if (empty($availables)) {
                    Message::info('Todos los elementos disponibles ya estan en portada');
                    return $this->redirect('/admin/home');
                    break;
                }
                return array(
                        'folder' => 'home',
                        'file' => 'add',
                        'action' => 'add',
                        'home' => (object) array('node'=>$node, 'order'=>$next, 'type'=>'main'),
                        'availables' => $availables
                );
                break;
            case 'addside':
                $next = Model\Home::next($node, 'side');
                $availables = Model\Home::availableSide($node);

                if (empty($availables)) {
                    Message::info('Todos los elementos laterales disponibles ya estan en portada');
                    return $this->redirect('/admin/home');
                    break;
                }
                return array(
                        'folder' => 'home',
                        'file' => 'add',
                        'action' => 'add',
                        'home' => (object) array('node'=>$node, 'order'=>$next, 'type'=>'side'),
                        'availables' => $availables
                );
                break;
             *
             */
        }

        $viewData = array(
            'folder' => 'home',
            'file' => 'list'
        );

        $viewData['items'] = Model\Home::getAll($node);

        /* Para añadir nuevos desde la lista */
        $viewData['availables'] = Model\Home::available($node);
        $viewData['new'] = (object) array('node'=>$node, 'order'=>Model\Home::next($node, 'main'), 'type'=>'main');

        // laterales
        $viewData['side_items'] = Model\Home::getAllSide($node);
        $viewData['side_availables'] = Model\Home::availableSide($node);
        $viewData['side_new'] = (object) array('node'=>$node, 'order'=>Model\Home::next($node, 'side'), 'type'=>'side');

        return $viewData;

    }

}

