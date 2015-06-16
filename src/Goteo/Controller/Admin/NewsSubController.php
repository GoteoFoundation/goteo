<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
    Goteo\Application\Message,
    Goteo\Application\Session,
    Goteo\Model;

class NewsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $model = 'Goteo\Model\News';
        $url = '/admin/news';

        $errors = array();

        switch ($action) {
            case 'add':
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => (object) array('order' => '0'),
                        'form' => array(
                            'action' => "$url/edit/",
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Añadir'
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'title' => array(
                                    'label' => 'Noticia',
                                    'name' => 'title',
                                    'type' => 'text',
                                    'properties' => 'size="100" maxlength="100"'
                                ),
                                'description' => array(
                                    'label' => 'Entradilla',
                                    'name' => 'description',
                                    'type' => 'textarea',
                                    'properties' => 'cols="100" rows="2"'
                                ),
                                'url' => array(
                                    'label' => 'Enlace',
                                    'name' => 'url',
                                    'type' => 'text',
                                    'properties' => 'size=100'
                                ),
                                'image' => array(
                                    'label' => 'Imagen<br/>(150x85)',
                                    'name' => 'image',
                                    'type' => 'image'
                                ),
                                'media_name' => array(
                                    'label' => 'Medio',
                                    'name' => 'media_name',
                                    'type' => 'text'
                                ),
                                'order' => array(
                                    'label' => '',
                                    'name' => 'order',
                                    'type' => 'hidden'
                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost()) {

                    //compruebo si está en press_banner
                    $press_banner=$model::in_press_banner($this->getPost('id'));

                    // instancia
                    $item = new $model(array(
                        'id'          => $this->getPost('id'),
                        'title'       => $this->getPost('title'),
                        'description' => $this->getPost('description'),
                        'url'         => $this->getPost('url'),
                        'image'       => $this->getPost('image'),
                        'media_name'  => $this->getPost('media_name'),
                        'order'       => $this->getPost('order'),
                        'press_banner'=> $press_banner
                    ));


                // tratar si quitan la imagen
                    if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                        $image = Model\Image::get($item->image);
                        $image->remove($errors);
                        $item->image = null;
                        $removed = true;
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image']['name'])) {
                        $item->image = $_FILES['image'];
                    }

                    if ($item->save($errors)) {

                        if ($this->getPost('id')) {
                            // Evento Feed
                            $log = new Feed();
                            $log->populate('nueva micronoticia (admin)', '/admin/news', \vsprintf('El admin %s ha %s la micronoticia "%s"', array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('relevant', 'Publicado'),
                                Feed::item('news', $this->getPost('title'), '#news'.$item->id)
                            )));
                            $log->doAdmin('admin');
                            unset($log);
                        }

                        // tratar si han marcado pendiente de traducir
                        if ($this->getPost('pending') == 1 && !Model\News::setPending($item->id, 'post')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        return $this->redirect($url);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $item = $model::get($id);
                }

                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit/$id",
                            'submit' => array(
                                'name' => 'update',
                                'label' => Text::get('regular-save')
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'title' => array(
                                    'label' => 'Noticia',
                                    'name' => 'title',
                                    'type' => 'text',
                                    'properties' => 'size="100"  maxlength="80"'
                                ),
                                'description' => array(
                                    'label' => 'Entradilla',
                                    'name' => 'description',
                                    'type' => 'textarea',
                                    'properties' => 'cols="100" rows="2"'
                                ),
                                'url' => array(
                                    'label' => 'Enlace',
                                    'name' => 'url',
                                    'type' => 'text',
                                    'properties' => 'size=100'
                                ),
                                'image' => array(
                                    'label' => 'Imagen<br/>(150x85)',
                                    'name' => 'image',
                                    'type' => 'image'
                                ),
                                'media_name' => array(
                                    'label' => 'Medio',
                                    'name' => 'media_name',
                                    'type' => 'text'
                                ),
                                'order' => array(
                                    'label' => '',
                                    'name' => 'order',
                                    'type' => 'hidden'
                                )
                            )
                    )
                );

                break;
            case 'up':
                $model::up($id);
                break;
            case 'down':
                $model::down($id);
                break;
            case 'remove':
                $tempData = $model::get($id);
                if ($model::delete($id)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->populate('micronoticia quitada (admin)', '/admin/news',
                        \vsprintf('El admin %s ha %s la micronoticia "%s"', array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('relevant', 'Quitado'),
                            Feed::item('blog', $tempData->title)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                    return $this->redirect($url);
                }
                break;

            case 'add_press_banner':
                  if (Model\News::add_press_banner($id)) {
                    return $this->redirect('/admin/news');
                }
                break;

             case 'remove_press_banner':
                  if (Model\News::remove_press_banner($id)) {
                    return $this->redirect('/admin/news');
                }
                break;
        }

        /*return array(
                'folder' => 'news',
                'file' => 'list',
                'model' => 'news',
                'addbutton' => 'Nueva noticia',
                'data' => $model::getAll(),
                'columns' => array(
                    'edit' => '',
                    'title' => 'Noticia',
//                        'url' => 'Enlace',
                    'order' => 'Posición',
                    'up' => '',
                    'down' => '',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );*/

         return array(
                'folder' => 'news',
                'file' => 'list',
                'news' => $model::getList()
        );

    }

}
