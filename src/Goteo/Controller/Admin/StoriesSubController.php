<?php
/**
 * Historias exitosas
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Model;

class StoriesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nueva Historia',
      'edit' => 'Editando Historia',
      'translate' => 'Traduciendo Historia',
      'preview' => 'Previsualizando Historia',
    );

    static protected $label = 'Historias exitosas';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function previewAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('preview', $id, $this->getFilters(), $subaction));
    }


    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
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

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
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

        $node = $this->node;

        //Text position for text in general banners

        $text_positions = array (
          'bottom-left' => 'Esquina inferior izquierda',
          'bottom-right' => 'Esquina inferior derecha',
          'top-left' => 'Esquina superior izquierda',
          'top-right' => 'Esquina superior derecha',
        );

        if ($this->isPost()) {

            $el_item = $this->getPost('item');
            error_log($el_item);
            if (!empty($el_item)) {
                $post = $el_item;
            } else {
                $post = null;
            }

            // objeto
            $story = new Model\Stories(array(
                'id' => $this->getPost('id'),
                'node' => $node,
                'project' => $this->getPost('project') ? $this->getPost('project') : null,
                'order' => $this->getPost('order'),
                'image' => $this->getPost('image'),
                'pool_image' => $this->getPost('pool_image'),
                'pool' => (bool) $this->getPost('pool'),
                'text_position' => $this->getPost('text_position'),
                'active' => $this->getPost('active'),
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'review' => $this->getPost('review'),
                'url' => $this->getPost('url'),
                'post' => $post
            ));

            // imagen
            if(!empty($_FILES['image']['name'])) {
                $story->image = $_FILES['image'];
            } else {
                $story->image = $this->getPost('prev_image');
            }

            // pool landing image
            if(!empty($_FILES['pool_image']['name'])) {
                $story->pool_image = $_FILES['pool_image'];
            } else {
                $story->pool_image = $this->getPost('prev_pool_image');
            }

			if ($story->save($errors)) {
                Message::info('Datos guardados');

                if ($this->getPost('action') == 'add') {

                    if($this->getPost('project'))
                    {
                        $projectData = Model\Project::getMini($this->getPost('project'));

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
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Stories::setPending($story->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

                return $this->redirect('/admin/stories');
			}
			else {
                Message::error(implode('<br />', $errors));

                // otros elementos disponibles
                $items = Model\Post::getAutocomplete();

                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'stories',
                                'file' => 'edit',
                                'action' => 'add',
                                'story' => $story,
                                'status' => $status,
                                'items' => $items,
                                'text_positions' => $text_positions,
                                'autocomplete' => true
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'stories',
                                'file' => 'edit',
                                'action' => 'edit',
                                'story' => $story,
                                'items' => $items,
                                'text_positions' => $text_positions,
                                'autocomplete' => true
                        );
                        break;
                }
			}
		}

        switch ($action) {
            case 'active':
                $set = $flag == 'on' ? true : false;
                Model\Stories::setActive($id, $set);
                return $this->redirect('/admin/stories');
                break;
            case 'up':
                Model\Stories::up($id, $node);
                return $this->redirect('/admin/stories');
                break;
            case 'down':
                Model\Stories::down($id, $node);
                return $this->redirect('/admin/stories');
                break;
            case 'remove':
                if (Model\Stories::delete($id)) {
                    Message::info('Historia quitada correctamente');
                } else {
                    Message::error('No se ha podido quitar la historia');
                }
                return $this->redirect('/admin/stories');
                break;
            case 'add':
                // siguiente orden
                $next = Model\Stories::next($node);
                // elementos disponibles
                $items = Model\Post::getAutocomplete();

                return array(
                        'folder' => 'stories',
                        'file' => 'edit',
                        'action' => 'add',
                        'story' => (object) array('order' => $next),
                        'status' => $status,
                        'items' => $items,
                        'text_positions' => $text_positions,
                        'autocomplete' => true
                );

            case 'edit':
                // datos del elemento
                $story = Model\Stories::get($id, Config::get('lang'));
                // elementos disponibles
                $items = Model\Post::getAutocomplete();

                return array(
                        'folder' => 'stories',
                        'file' => 'edit',
                        'action' => 'edit',
                        'story' => $story,
                        'items' => $items,
                        'text_positions' => $text_positions,
                        'autocomplete' => true
                );

                case 'preview':
                        // datos del elemento
                        $story = Model\Stories::get($id, Config::get('lang'));

                        return $this->response('admin/stories/preview', ['story' =>$story]);
        }

        $storyed = Model\Stories::getList($node);

        return array(
                'folder' => 'stories',
                'file' => 'list',
                'storyed' => $storyed,
                'node' => $node
        );

    }

}

