<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class StoriesSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

        $errors = array();

        $node = $this->node;

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
                'project' => $this->getPost('project'),
                'order' => $this->getPost('order'),
                'image' => $this->getPost('image'),
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

			if ($story->save($errors)) {
                Message::info('Datos guardados');

                if ($this->getPost('action') == 'add') {
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
                        'autocomplete' => true
                );

            case 'edit':
                // datos del elemento
                $story = Model\Stories::get($id);
                // elementos disponibles
                $items = Model\Post::getAutocomplete();

                return array(
                        'folder' => 'stories',
                        'file' => 'edit',
                        'action' => 'edit',
                        'story' => $story,
                        'items' => $items,
                        'autocomplete' => true
                );

                case 'preview':
                        // datos del elemento
                        $story = Model\Stories::get($id);

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

