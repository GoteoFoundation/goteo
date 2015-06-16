<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Application\Session,
	Goteo\Library\Feed,
    Goteo\Model;

class BannersSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

        $errors = array();

        $node = $this->node;

        if ($this->isPost()) {

            // objeto
            $banner = $id ? Model\Banner::get($id) : new Model\Banner;
            $banner->node = $node;
            $banner->project = $this->getPost('item');
            $banner->title = $this->getPost('title');
            $banner->description = $this->getPost('description');
            $banner->url = $this->getPost('url');
            $banner->order = $this->getPost('order');
            $banner->active = $this->getPost('active');

            // tratar si quitan la imagen
            if ($this->getPost('image-' . $banner->image->hash .  '-remove')) {
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
                Message::info('Datos guardados');

                if ($this->getPost('action') == 'add') {
                    $projectData = Model\Project::getMini($this->getPost('project'));

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('nuevo banner de proyecto destacado en portada (admin)', '/admin/promote',
                        \vsprintf('El admin %s ha %s', array(
                        Feed::item('user', Session::getUser()->name, Session::getUserId()),
                        Feed::item('relevant', 'Publicado un banner', '/')
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                }

                // tratar si han marcado pendiente de traducir
                if ($this->hasPost('pending') && $this->getPost('pending') == 1
                    && !Model\Banner::setPending($banner->id, 'banner')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

                return $this->redirect('/admin/banners');
			}
			else {
                Message::error(implode('<br />', $errors));

                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'banners',
                                'file' => 'edit',
                                'action' => 'add',
                                'banner' => $banner,
                                'autocomplete' => true
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'banners',
                                'file' => 'edit',
                                'action' => 'edit',
                                'banner' => $banner,
                                'autocomplete' => true
                        );
                        break;
                }
			}
		}

        switch ($action) {
            case 'active':
                $set = $flag == 'on' ? true : false;
                Model\Banner::setActive($id, $set);
                return $this->redirect('/admin/banners');
                break;
            case 'up':
                Model\Banner::up($id, $node);
                return $this->redirect('/admin/banners');
                break;
            case 'down':
                Model\Banner::down($id, $node);
                return $this->redirect('/admin/banners');
                break;
            case 'remove':
                if (Model\Banner::delete($id)) {
                    Message::info('Banner quitado correctamente');
                } else {
                    Message::error('No se ha podido quitar el banner');
                }
                return $this->redirect('/admin/banners');
                break;
            case 'add':
                // siguiente orden
                $next = Model\Banner::next($node);

                return array(
                        'folder' => 'banners',
                        'file' => 'edit',
                        'action' => 'add',
                        'banner' => (object) array('order' => $next),
                        'autocomplete' => true
                );
                break;
            case 'edit':
                $banner = Model\Banner::get($id);

                return array(
                        'folder' => 'banners',
                        'file' => 'edit',
                        'action' => 'edit',
                        'banner' => $banner,
                        'autocomplete' => true
                );
                break;
        }


        $bannered = Model\Banner::getAll(false, $node);

        return array(
                'folder' => 'banners',
                'file' => 'list',
                'bannered' => $bannered,
                'node' => $node
        );

    }

}

