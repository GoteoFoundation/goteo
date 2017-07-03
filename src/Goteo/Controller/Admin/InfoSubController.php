<?php
/**
 * Gestion de "ideas about"? no estoy seguro que significa...
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
    Goteo\Application\Message,
    Goteo\Application\Session,
	Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Model;

class InfoSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'info-lb-list',
      'add' => 'info-lb-add',
      'edit' => 'info-lb-edit',
      'translate' => 'info-lb-translate',
    );


    static protected $label = 'info-lb';


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
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


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null) {

        $errors = array();
        $user = Session::getUser();

        $url = '/admin/info';

		if ($this->isPost()) {

                $editing = false;

                if ($this->getPost('id')) {
                    $post = Model\Info::get($this->getPost('id'), Config::get('lang'));
                } else {
                    $post = new Model\Info();
                }
                // campos que actualizamos
                $fields = array(
                    'id',
                    'node',
                    'title',
                    'text',
                    'media',
                    'legend',
                    'publish',
                    'order',
                    'share_twitter',
                    'share_facebook'
                );

                foreach ($fields as $field) {
                    $post->$field = $this->getPost($field);
                }

                // tratar la imagen y ponerla en la propiedad image
                if(!empty($_FILES['image_upload']['name'])) {
                    $post->image = $_FILES['image_upload'];
                    $editing = true;
                }

                // tratar las imagenes que quitan
                foreach ($post->gallery as $key=>$image) {
                    if ($this->getPost("gallery-{$image->hash}-remove")) {
                        $image->remove($errors, 'info');
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

                /// este es el único save que se lanza desde un metodo process_
                if ($post->save($errors)) {
                    if ($action == 'edit') {
                        Message::info('La entrada se ha actualizado correctamente');

                        if ((bool) $post->publish) {
                            $log_action = 'Publicado';
                        } else {
                            $log_action = 'Modificado';
                        }

                    } else {
                        Message::info('Se ha añadido una nueva entrada');
                        $id = $post->id;
                        $log_action = 'Añadido';
                    }
                    $action = $editing ? 'edit' : 'list';

                    // Evento Feed
                    $log = new Feed();
                    $log->populate('modificacion de idea about (admin)', '/admin/info',
                        \vsprintf('El admin %s ha %s la Idea de fuerza "%s"', array(
                            Feed::item('user', $user->name, $user->id),
                            Feed::item('relevant', $log_action),
                            Feed::item('relevant', $post->title, '/about#info'.$post->id)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                    // tratar si han marcado pendiente de traducir
                    if ($this->getPost('pending') == 1 && !Model\Info::setPending($post->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                } else {
                    Message::error(implode('<br />', $errors));
                    Message::error('Ha habido algun problema al guardar los datos');
                }
        }

        switch ($action)  {
            case 'up':
                Model\Info::up($id);
                break;
            case 'down':
                Model\Info::down($id);
                break;
            case 'remove':
                $tempData = Model\Info::get($id, Config::get('lang'));
                // eliminar un término
                if (Model\Info::delete($id)) {
                    Message::info('Entrada eliminada');

                    // Evento Feed
                    $log = new Feed();
                    $log->populate('quitar de idea about (admin)', '/admin/info',
                        \vsprintf('El admin %s ha %s la Idea de fuerza "%s"', array(
                            Feed::item('user', $user->name, $user->id),
                            Feed::item('relevant', 'Eliminado'),
                            Feed::item('relevant', $tempData->title)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                } else {
                    Message::error('No se ha podido eliminar la entrada');
                }
                break;
            case 'add':
                // nueva entrada con wisiwig
                // obtenemos datos basicos
                if (!$post instanceof Model\Info) {
                    $post = new Model\Info();
                }

                $message = 'Añadiendo una nueva entrada';

                return array(
                        'folder' => 'info',
                        'file' => 'edit',
                        'action' => 'add',
                        'post' => $post,
                        'message' => $message
                );
                break;
            case 'edit':
                if (empty($id)) {
                    return $this->redirect('/admin/info');
                    break;
                } else {
                    $post = Model\Info::get($id, Config::get('lang'));

                    if (!$post instanceof Model\Info) {
                        Message::error('La entrada esta corrupta, contacte con nosotros.');
                        $action = 'list';
                        break;
                    }
                }

                $message = 'Editando una entrada existente';

                return array(
                        'folder' => 'info',
                        'file' => 'edit',
                        'action' => 'edit',
                        'post' => $post,
                        'message' => $message
                );
                break;
        }

        // lista de términos
        $posts = Model\Info::getAll();

        return array(
                'folder' => 'info',
                'file' => 'list',
                'posts' => $posts
        );

    }

}
