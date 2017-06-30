<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Tags for blog
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Library\Feed;
use Goteo\Model;

class TagsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'tags-lb-list',
      'add' => 'tags-lb-add',
      'edit' => 'tags-lb-edit',
      'translate' => 'tags-lb-translate',
    );


    static protected $label = 'tags-lb';


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


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
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

        $model = 'Goteo\Model\Blog\Post\Tag';
        $url = '/admin/tags';

        $errors = array();

        switch ($action) {
            case 'add':
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => (object) array(),
                        'form' => array(
                            'action' => "$url/edit",
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
                                'name' => array(
                                    'label' => 'Tag',
                                    'name' => 'name',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    $errors = array();

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name')
                    ));

                    if ($item->save($errors)) {

                        // tratar si han marcado pendiente de traducir
                        if ($this->getPost('pending') == 1 && !Model\Blog\Post\Tag::setPending($item->id, 'post')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        return $this->redirect($url);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $item = $model::get($id, Config::get('lang'));
                }

                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit" . ($id ? "/$id" : ''),
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
                                'name' => array(
                                    'label' => 'Tag',
                                    'name' => 'name',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'remove':
                if ($model::delete($id)) {
                    return $this->redirect($url);
                }
                break;
        }

        return array(
                'folder' => 'base',
                'file' => 'list',
                'model' => 'tag',
                'addbutton' => 'Nuevo tag',
                'data' => $model::getList(1),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Tag',
                    'used' => 'Entradas',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}

