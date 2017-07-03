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
 * Revision criteria
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
    Goteo\Model;

class CriteriaSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'criteria-lb-list',
      'add' => 'criteria-lb-add',
      'edit' => 'criteria-lb-edit',
      'translate' => 'criteria-lb-translate',
    );


static protected $label = 'criteria-lb';


    protected $filters = array (
      'section' => 'project',
    );


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


    public function process ($action = 'list', $id = null, $filters = array()) {

        $sections = Model\Criteria::sections();

        if (!isset($sections[$filters['section']])) {
            unset($filters['section']);
        }

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $criteria = new Model\Criteria(array(
                'id' => $this->getPost('id'),
                'section' => $this->getPost('section'),
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'order' => $this->getPost('order'),
                'move' => $this->getPost('move')
            ));

			if ($criteria->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Criterio añadido correctamente');
                        break;
                    case 'edit':
                        Message::info('Criterio editado correctamente');
                        break;
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Criteria::setPending($criteria->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            } else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'criteria',
                        'file' => 'edit',
                        'action' => $this->getPost('action'),
                        'criteria' => $criteria,
                        'sections' => $sections
                );
			}
		}


        switch ($action) {
            case 'up':
                Model\Criteria::up($id);
                break;
            case 'down':
                Model\Criteria::down($id);
                break;
            case 'add':
                $next = Model\Criteria::next($filters['section']);

                return array(
                        'folder' => 'criteria',
                        'file' => 'edit',
                        'action' => 'add',
                        'criteria' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                        'sections' => $sections
                );
                break;
            case 'edit':
                $criteria = Model\Criteria::get($id, Config::get('lang'));

                $cuantos = Model\Criteria::next($criteria->section);
                $criteria->cuantos = ($cuantos -1);

                return array(
                        'folder' => 'criteria',
                        'file' => 'edit',
                        'action' => 'edit',
                        'criteria' => $criteria,
                        'sections' => $sections
                );
                break;
            case 'remove':
                Model\Criteria::delete($id);
                break;
        }

        $criterias = Model\Criteria::getAll($filters['section']);

        return array(
                'folder' => 'criteria',
                'file' => 'list',
                'criterias' => $criterias,
                'sections' => $sections,
                'filters' => $filters
        );

    }

}

