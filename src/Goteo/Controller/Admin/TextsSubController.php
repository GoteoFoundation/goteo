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
 * Gestion de textos
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
    Goteo\Application\Message,
    Goteo\Application\Config,
    Goteo\Application\Lang,
	Goteo\Library\Feed;

class TextsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'texts-lb-list',
      'edit' => 'texts-lb-edit',
    );


    static protected $label = 'texts-lb';


    protected $filters = array (
      'group' => '',
      'text' => '',
    );


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


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {


        // valores de filtro
        $groups    = Text::groups();

        // metemos el todos
        array_unshift($groups, 'Todas las agrupaciones');

        //@fixme temporal hasta pasar las agrupaciones a tabal o arreglar en el list.html.php
        $data = Text::getAll($filters, Config::get('lang'));
        foreach ($data as $key=>$item) {
            $data[$key]->group = $groups[$item->group];
        }

        switch ($action) {
            case 'list':
                return array(
                        'folder' => 'texts',
                        'file' => 'list',
                        'data' => $data,
                        'columns' => array(
                            'edit' => '',
                            'text' => 'Texto',
                            'group' => 'Agrupación'
                        ),
                        'url' => '/admin/texts',
                        'filters' => array(
                            'filtered' => $filters['filtered'],
                            'group' => array(
                                    'label'   => 'Filtrar:',
                                    'type'    => 'select',
                                    'options' => $groups,
                                    'value'   => $filters['group']
                                ),
                            'text' => array(
                                    'label'   => 'Texto:',
                                    'type'    => 'input',
                                    'options' => null,
                                    'value'   => $filters['text']
                                )
                            /*,
                            'idfilter' => array(
                                    'label'   => 'Id:',
                                    'type'    => 'input',
                                    'options' => null,
                                    'value'   => $filters['idfilter']
                                )*/
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    $errors = array();

                    $id = $this->getPost('id');
                    $text = $this->getPost('text');

                    $data = array(
                        'id' => $id,
                        'text' => $this->getPost('text'),
                        'lang' => Config::get('lang')
                    );

                    if (Text::save($data, $errors)) {
                        Message::info('El texto ha sido actualizado');

                        // tratar si han marcado pendiente de traducir
                        // no usamos Core\Model porque no es tabla _lang
                        if ($this->getPost('pending') == 1) {
                            $ok = Text::setPending($id, $errors);
                            if (!$ok) {
                                Message::error(implode('<br />', $errors));
                            }
                        }


                        return $this->redirect("/admin/texts");
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                }

                $text = Lang::trans($id);

                return array(
                        'folder' => 'texts',
                        'file' => 'edit',
                        'data' => (object) array (
                            'id' => $id,
                            'text' => $text
                        ),
                        'form' => array(
                            'action' => "/admin/texts/edit" . ($id ? "/$id" : ''),
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Aplicar'
                            ),
                            'fields' => array (
                                'idtext' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden',
                                    'properties' => '',
                                ),
                                'newtext' => array(
                                    'label' => 'Texto',
                                    'name' => 'text',
                                    'type' => 'textarea',
                                    'properties' => 'cols="100" rows="6"',
                                )
                            )
                    )
                );

                break;
            default:
                return $this->redirect("/admin");
        }

    }

}
