<?php
/**
 * Gestion del catalogo para navidad
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Config,
    Goteo\Model;

class BazarSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo Elemento',
      'edit' => 'Editando Elemento',
      'translate' => 'Traduciendo Elemento',
    );


    static protected $label = 'Gestión del Catálogo';


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }

    public function activeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('active', $id, $this->getFilters(), $subaction));
    }

    public function upAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('up', $id, $this->getFilters(), $subaction));
    }

    public function downAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('down', $id, $this->getFilters(), $subaction));
    }

    public function removeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove', $id, $this->getFilters(), $subaction));
    }

    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

        $errors = array();



        if ($this->isPost()) {

            $el_item = $this->getPost('item');
            if (!empty($el_item)) {
                list($el_reward, $el_project, $el_amount) = explode('¬', $el_item);
            } else {
                $el_reward = $el_project = $el_amount = null;
            }

            // objeto
            $promo = new Model\Bazar(array(
                'id' => $this->getPost('id'),
                'reward' => $el_reward,
                'project' => $el_project,
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'amount' => $el_amount,
                'order' => $this->getPost('order'),
                'active' => $this->getPost('active')
            ));
            // imagen
            if(!empty($_FILES['image']['name'])) {
                $promo->image = $_FILES['image'];
            } else {
                $promo->image = $this->getPost('prev_image');
            }

            if ($promo->save($errors)) {

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Bazar::setPending($promo->id, 'bazar')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

                return $this->redirect('/admin/bazar');
			}
			else {

                Message::error(implode(', ', $errors));

                // otros elementos disponibles
                $items = Model\Bazar::available($promo->reward);

                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'bazar',
                                'file' => 'edit',
                                'action' => 'add',
                                'promo' => $promo,
                                'items' => $items,
                                'autocomplete'  => true
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'bazar',
                                'file' => 'edit',
                                'action' => 'edit',
                                'promo' => $promo,
                                'items' => $items,
                                'autocomplete'  => true
                        );
                        break;
                }
			}
		}

        switch ($action) {
            case 'active':
                $set = $flag == 'on' ? true : false;
                Model\Bazar::setActive($id, $set);
                return $this->redirect('/admin/bazar');
                break;
            case 'up':
                Model\Bazar::up($id);
                return $this->redirect('/admin/bazar');
                break;
            case 'down':
                Model\Bazar::down($id);
                return $this->redirect('/admin/bazar');
                break;
            case 'remove':
                if (Model\Bazar::delete($id)) {
                    Message::info('elemento quitado correctamente');
                } else {
                    Message::error('No se ha podido quitar el elemento');
                }
                return $this->redirect('/admin/bazar');
                break;
            case 'add':
                // siguiente orden
                $next = Model\Bazar::next();
                // elementos disponibles
                $items = Model\Bazar::available();

                return array(
                        'folder' => 'bazar',
                        'file' => 'edit',
                        'action' => 'add',
                        'promo' => (object) array('order' => $next),
                        'items' => $items,
                        'autocomplete'  => true
                );
                break;
            case 'edit':
                // datos del elemento
                $promo = Model\Bazar::get($id);
                // otros elementos disponibles
                $items = Model\Bazar::available($promo->reward);

                return array(
                        'folder' => 'bazar',
                        'file' => 'edit',
                        'action' => 'edit',
                        'promo' => $promo,
                        'items' => $items,
                        'autocomplete'  => true
                );
                break;
        }


        $items = Model\Bazar::getList();

        return array(
                'folder' => 'bazar',
                'file' => 'list',
                'items' => $items
        );

    }

}
