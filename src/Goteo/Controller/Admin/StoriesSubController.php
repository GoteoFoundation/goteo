<?php
/**
 * Historias exitosas
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Library\Feed;
use Goteo\Model;
use Goteo\Model\User;

class StoriesSubController extends AbstractSubController {

    static protected $labels = [
        'list' => 'Listando',
        'add' => 'Nueva Historia',
        'edit' => 'Editando Historia',
        'translate' => 'Traduciendo Historia',
        'preview' => 'Previsualizando Historia',
    ];

    static protected $label = 'Historias exitosas';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(User $user, $node): bool {
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

        $spheres=Model\Sphere::getAll();

        if ($this->isPost()) {

            $el_item = $this->getPost('item');
            error_log($el_item);
            if (!empty($el_item)) {
                $post = $el_item;
            } else {
                $post = null;
            }

            $project = $this->getPost('project') ? Model\Project::getMini($this->getPost('project')) : null;
            // objeto
            $story = new Model\Stories(array(
                'id' => $this->getPost('id'),
                'node' => $node,
                'project' => $project ? $project->id : null,
                'lang' => $project ? $project->lang : null,
                'order' => $this->getPost('order'),
                'image' => $this->getPost('image'),
                'pool_image' => $this->getPost('pool_image'),
                'pool' => (bool) $this->getPost('pool'),
                'text_position' => $this->getPost('text_position'),
                'active' => $this->getPost('active'),
                'landing_pitch' => $this->getPost('landing_pitch'),
                'landing_match' => $this->getPost('landing_match'),
                'type' => $this->getPost('type'),
                'sphere' => $this->getPost('sphere'),
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
                $story->image = new Model\Image($this->getPost('prev_image'));
            }

            // tratar si quitan la imagen
            if ($this->getPost('image-remove')) {
                if ($story->image instanceof Model\Image) $story->image->remove($errors);
                $story->image = '';
            }

            // pool landing image
            if(!empty($_FILES['pool_image']['name'])) {
                $story->pool_image = $_FILES['pool_image'];
            } else {
                $story->pool_image = new Model\Image($this->getPost('prev_pool_image'));
            }

            // tratar si quitan la imagen
            if ($this->getPost('image-pool-remove')) {
                if ($story->pool_image instanceof Model\Image) $story->pool_image->remove($errors);
                $story->pool_image = '';
            }

			if ($story->save($errors)) {
                Message::info('Datos guardados');

                if ($this->getPost('action') == 'add') {
                    if($project) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($project->id);
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
			} else {
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
                                'spheres'       => $spheres,
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
                                'spheres'       => $spheres,
                                'autocomplete' => true
                        );
                        break;
                }
			}
		}

        switch ($action) {
            case 'active':
                $set = $flag == 'on';
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

                $story= new Model\Stories([
                    'order' => $next
                ]);

                return [
                    'folder' => 'stories',
                    'file' => 'edit',
                    'action' => 'add',
                    'story' => $story,
                    'status' => $status,
                    'items' => $items,
                    'text_positions' => $text_positions,
                    'spheres'        => $spheres,
                    'autocomplete' => true
                ];

            case 'edit':
                // datos del elemento
                $story = Model\Stories::get($id, Config::get('lang'));
                // elementos disponibles
                $items = Model\Post::getAutocomplete();

                return [
                    'folder' => 'stories',
                    'file' => 'edit',
                    'action' => 'edit',
                    'story' => $story,
                    'items' => $items,
                    'text_positions' => $text_positions,
                    'spheres' => $spheres,
                    'autocomplete' => true
                ];

            case 'preview':
                // datos del elemento
                $story = Model\Stories::get($id, Config::get('lang'));

                return $this->response('admin/stories/preview', ['story' =>$story]);
        }

        return [
            'folder' => 'stories',
            'file' => 'list',
            'storyed' => Model\Stories::getList([], 0, 1000, false, Config::get('lang')),
            'node' => $node
        ];
    }

}
