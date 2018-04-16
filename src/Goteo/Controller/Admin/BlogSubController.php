<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Model;

class BlogSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'blog-lb-list',
      'add' => 'blog-lb-add',
      'edit' => 'blog-lb-edit',
      'translate' => 'blog-lb-translate',
      'reorder' => 'blog-lb-reorder',
      'footer' => 'blog-lb-footer',
    );


    static protected $label = 'blog-lb';


    protected $filters = array (
      'show' => 'owned',
      'blog' => '',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }


    public function reorderAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('reorder', $id, $this->getFilters(), $subaction));
    }


    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }

    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
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

    public function add_homeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add_home', $id, $this->getFilters(), $subaction));
    }

    public function remove_homeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove_home', $id, $this->getFilters(), $subaction));
    }

    public function footerAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('footer', $id, $this->getFilters(), $subaction));
    }

    public function up_footerAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('up_footer', $id, $this->getFilters(), $subaction));
    }

    public function down_footerAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('down_footer', $id, $this->getFilters(), $subaction));
    }

    public function add_footerAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add_footer', $id, $this->getFilters(), $subaction));
    }

    public function remove_footerAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove_footer', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {

        $errors = array();

        $node = $this->node;

        $blog = Model\Blog::get($node, 'node');
        if (!$blog instanceof \Goteo\Model\Blog) {
            $blog = new Model\Blog(array('type'=>'node', 'owner'=>$node, 'active'=>1));
            if ($blog->save($errors)) {
                Message::info('Se ha inicializado su espacio de blog');
            } else {
                Message::error('No tiene espacio de blog, contacte con nosotros');
                return $this->redirect('/admin');
            }
        } elseif (!$blog->active) {
            Message::error('Lo sentimos, la gestión de blog esta desactivada');
            return $this->redirect('/admin');
        }

        // primero comprobar que tenemos blog
        if (!$blog instanceof Model\Blog) {
            Message::error('No se ha encontrado ningún blog, contacte con nosotros');
            return $this->redirect('/admin');
        }

        $url = '/admin/blog';

		if ($this->isPost()) {
                if (empty($this->getPost('blog'))) {
                    Message::error('Hemos perdido de vista el blog!!!');
                    return $this->redirect('/admin/blog');
                }

                $editing = false;

                if (!empty($this->getPost('id'))) {
                    $post = Model\Blog\Post::get($this->getPost('id'), Config::get('lang'));
                } else {
                    $post = new Model\Blog\Post();
                }
                // campos que actualizamos
                $fields = array(
                    'id',
                    'blog',
                    'title',
                    'subtitle',
                    'text',
                    'image',
                    'media',
                    'legend',
                    'date',
                    'publish',
                    'home',
                    'footer',
                    'allow',
                    'author'
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
                    if ($this->getPost('gallery-' . $image->hash . '-remove')) {
                        $image->remove($errors, 'post');
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

                $post->tags = $this->getPost('tags');

                // si tenemos un nuevio tag hay que añadirlo
                if($this->getPost('new-tag_save') && $this->getPost('new-tag')) {

                    // grabar el tag en la tabla de tag,
                    $new_tag = new Model\Blog\Post\Tag(array(
                        'id' => '',
                        'name' => $this->getPost('new-tag')
                    ));

                    if ($new_tag->save($errors)) {
                        $post->tags[] = $new_tag->id; // asignar al post
                    } else {
                        Message::error(implode('<br />', $errors));
                    }

                    $editing = true; // seguir editando
                }


                /// este es el único save que se lanza desde un metodo process_
                if ($post->save($errors)) {
                    if ($action == 'edit') {
                        Message::info('La entrada se ha actualizado correctamente');
                    } else {
                        Message::info('Se ha añadido una nueva entrada');
                        $id = $post->id;
                    }
                    $action = $editing ? 'edit' : 'list';

                    if ((bool) $post->publish) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget('goteo', 'blog');
                        $log->setPost($post->id);
                        $log->populate('nueva entrada blog Goteo (admin)', '/admin/blog',
                            \vsprintf('El admin %s ha %s en el blog Goteo la entrada "%s"', array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('relevant', 'Publicado'),
                            Feed::item('blog', $post->title, $post->id)
                        )), $post->image
                        );
                        $log->doAdmin('admin');

                        // evento público
                        $log->unique = true;
                        $log->populate($post->title, '/blog/'.$post->id, Text::recorta($post->text, 250), $post->gallery[0]->id);
                        $log->doPublic('goteo');

                        unset($log);
                    } else {
                        //sino lo quitamos
                        \Goteo\Core\Model::query("DELETE FROM feed WHERE post = '{$post->id}' AND scope = 'public' AND type = 'goteo'");
                    }

                    // tratar si han marcado pendiente de traducir
                    if ($this->getPost('pending') === 1 && !Model\Blog\Post::setPending($post->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                } else {
                    Message::error('Ha habido algun problema al guardar los datos:<br />' . \implode('<br />', $errors));
                }
        }

        switch ($action)  {
            case 'list':
                // lista de entradas
                // obtenemos los datos
                $filters['node'] = $node;
                $show = array(
                    'all' => 'Todas las entradas existentes',
                    'published' => 'Solamente las publicadas en el blog',
                    'owned' => 'Solamente las del propio nodo',
                    'home' => 'Solamente las de portada',
                    'entries' => 'Solamente las de cierto nodo',
                    'updates' => 'Solamente las de proyectos'
                );

                // filtro de blogs de proyectos/nodos
                switch ($filters['show']) {
                    case 'updates':
                        $blogs = Model\Blog::getListProj();
                        break;

                    case 'entries':
                        $blogs = Model\Blog::getListNode();
                        break;
                }

                if ( !in_array($filters['show'], array('entries', 'updates')) || !isset($blogs[$filters['blog']]) ) {
                    unset($filters['blog']);
                }

                $posts = Model\Blog\Post::getList($filters, false);
                $homes = Model\Post::getList('home', $node);
                $footers = Model\Post::getList('footer', $node);

                if ($this->isMasterNode()) {
                    $show['footer'] = 'Solamente las del footer';
                }

                return array(
                        'folder' => 'blog',
                        'file' => 'list',
                        'posts' => $posts,
                        'filters' => $filters,
                        'show' => $show,
                        'blogs' => $blogs,
                        'homes' => $homes,
                        'footers' => $footers,
                        'node' => $node
                );
                break;
            case 'add':
                // nueva entrada con wisiwig
                // obtenemos datos basicos
                $post = new Model\Blog\Post(
                        array(
                            'blog' => $blog->id,
                            'date' => date('Y-m-d'),
                            'publish' => false,
                            'allow' => true,
                            'tags' => array(),
                            'author' => $this->user->id
                        )
                    );

                $message = 'Añadiendo una nueva entrada';

                return array(
                        'folder' => 'blog',
                        'file' => 'edit',
                        'action' => 'add',
                        'post' => $post,
                        'tags' => Model\Blog\Post\Tag::getAll(),
                        'message' => $message
                );
                break;
            case 'edit':
                if (empty($id)) {
                    Message::error('No se ha encontrado la entrada');
                    return $this->redirect('/admin/blog');
                    break;
                } else {
                    $post = Model\Blog\Post::get($id, Config::get('lang'));

                    if (!$post instanceof Model\Blog\Post) {
                        Message::error('La entrada esta corrupta, contacte con nosotros.');
                        return $this->redirect('/admin/blog/list');
                    } elseif (!$this->isMasterNode() && $post->owner_type == 'node' && $post->owner_id != $node) {
                        Message::error('No puedes editar esta entrada.');
                        return $this->redirect('/admin/blog/list');
                    }
                }

                $message = 'Editando una entrada existente';

                return array(
                        'folder' => 'blog',
                        'file' => 'edit',
                        'action' => 'edit',
                        'post' => $post,
                        'tags' => Model\Blog\Post\Tag::getAll(),
                        'message' => $message
                );
                break;
            case 'remove':
                // eliminar una entrada
                $tempData = Model\Blog\Post::get($id);
                if (!$this->isMasterNode() && $tempData->owner_type == 'node' && $tempData->owner_id != $node ) {
                    Message::error('No puedes eliminar esta entrada.');
                    return $this->redirect('/admin/blog');
                }
                if (Model\Blog\Post::delete($id)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget('goteo', 'blog');
                    $log->populate('Quita entrada de blog (admin)', '/admin/blog',
                        \vsprintf('El admin %s ha %s la entrada "%s" del blog de Goteo', array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('relevant', 'Quitado'),
                            Feed::item('blog', $tempData->title)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::info('Entrada eliminada');
                } else {
                    Message::error('No se ha podido eliminar la entrada');
                }
                return $this->redirect('/admin/blog/list');
                break;

            // acciones portada
            case 'reorder':
                // lista de entradas en portada
                // obtenemos los datos
                $posts = Model\Post::getAll('home', $node);

                return array(
                        'folder' => 'blog',
                        'file' => 'order',
                        'posts' => $posts
                );
                break;
            case 'up':
                if (!$this->isMasterNode()) {
                    Model\Post::up_node($id, $node);
                } else {
                    Model\Post::up($id, 'home');
                }
                return $this->redirect('/admin/blog/reorder');
                break;
            case 'down':
                if (!$this->isMasterNode()) {
                    Model\Post::up_node($id, $node);
                } else {
                    Model\Post::down($id, 'home');
                }
                return $this->redirect('/admin/blog/reorder');
                break;
            case 'add_home':
                // siguiente orden
                if (!$this->isMasterNode()) {
                    $next = Model\Post::next_node($node);
                    $data = (object) array('post' => $id, 'node' => $node, 'order' => 0);
                    if (Model\Post::update_node($data, $errors)) {
                         //Reorder posts with up
                        Model\Post::up_node($id, $node);
                        Message::info('Entrada colocada en la portada correctamente');
                    } else {
                        Message::error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                } else {
                    $next = Model\Post::next('home');
                    $post = new Model\Post(array(
                        'id' => $id,
                        'order' => 0,
                        'home' => 1
                    ));

                    if ($post->update($errors)) {
                        //Reorder posts with up
                        Model\Post::up($id, 'home');
                        Message::info('Entrada colocada en la portada correctamente');
                    } else {
                        Message::error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                }
                return $this->redirect('/admin/blog/list');
                break;
            case 'remove_home':
                // se quita de la portada solamente
                $ok = false;
                if (!$this->isMasterNode()) {
                    $ok = Model\Post::remove_node($id, $node);
                } else {
                    $ok = Model\Post::remove($id, 'home');
                }
                if ($ok) {
                    Message::info('Entrada quitada de la portada correctamente');
                } else {
                    Message::error('No se ha podido quitar esta entrada de la portada');
                }
                return $this->redirect('/admin/blog/list');
                break;

            // acciones footer (solo para superadmin y admins de goteo
            case 'footer':
                if ($this->isMasterNode()) {
                    // lista de entradas en el footer
                    // obtenemos los datos
                    $posts = Model\Post::getAll('footer');

                    return array(
                            'folder' => 'blog',
                            'file' => 'footer',
                            'posts' => $posts
                    );
                } else {
                    return $this->redirect('/admin/blog/list');
                }
                break;
            case 'up_footer':
                if ($this->isMasterNode()) {
                    Model\Post::up($id, 'footer');
                    return $this->redirect('/admin/blog/footer');
                } else {
                    return $this->redirect('/admin/blog');
                }
                break;
            case 'down_footer':
                if ($this->isMasterNode()) {
                    Model\Post::down($id, 'footer');
                    return $this->redirect('/admin/blog/footer');
                } else {
                    return $this->redirect('/admin/blog');
                }
                break;
            case 'add_footer':
                if ($this->isMasterNode()) {
                    // siguiente orden
                    $next = Model\Post::next('footer');
                    $post = new Model\Post(array(
                        'id' => $id,
                        'order' => $next,
                        'footer' => 1
                    ));

                    if ($post->update($errors)) {
                        Message::info('Entrada colocada en el footer correctamente');
                    } else {
                        Message::error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                }
                return $this->redirect('/admin/blog');
                break;
            case 'remove_footer':
                if ($this->isMasterNode()) {
                    // se quita del footer solamente
                    if (Model\Post::remove($id, 'footer')) {
                        Message::info('Entrada quitada del footer correctamente');
                    } else {
                        Message::error('No se ha podido quitar esta entrada del footer');
                    }
                }
                return $this->redirect('/admin/blog/list');
                break;
        }

    }

}

