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
 * Gestion de preguntas frecuentes
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Application\Config,
    Goteo\Model;

class FaqSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'faq-lb-list',
      'add' => 'faq-lb-add',
      'edit' => 'faq-lb-edit',
      'translate' => 'faq-lb-translate',
    );


    static protected $label = 'faq-lb';


    protected $filters = array (
      'section' => 'node',
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

        $sections = Model\Faq::sections();

        if (!isset($sections[$filters['section']])) {
            unset($filters['section']);
        }

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $faq = new Model\Faq(array(
                'id' => $this->getPost('id'),
                'node' => $this->node,
                'section' => $this->getPost('section'),
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'order' => $this->getPost('order'),
                'move' => $this->getPost('move')
            ));

			if ($faq->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Pregunta añadida correctamente');
                        break;
                    case 'edit':
                        Message::info('Pregunta editado correctamente');
                        break;
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Faq::setPending($faq->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            } else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'faq',
                        'file' => 'edit',
                        'action' => $this->getPost('action'),
                        'faq' => $faq,
                        'filter' => $filter,
                        'sections' => $sections
                );
			}
		}


        switch ($action) {
            case 'up':
                Model\Faq::up($id);
                return $this->redirect('/admin/faq');
                break;
            case 'down':
                Model\Faq::down($id);
                return $this->redirect('/admin/faq');
                break;
            case 'add':
                $next = Model\Faq::next($filters['section']);

                return array(
                        'folder' => 'faq',
                        'file' => 'edit',
                        'action' => 'add',
                        'faq' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                        'sections' => $sections
                );
                break;
            case 'edit':
                $faq = Model\Faq::get($id, Config::get('lang'));

                $cuantos = Model\Faq::next($faq->section);
                $faq->cuantos = ($cuantos -1);

                return array(
                        'folder' => 'faq',
                        'file' => 'edit',
                        'action' => 'edit',
                        'faq' => $faq,
                        'sections' => $sections
                );
                break;
            case 'remove':
                Model\Faq::remove($id);
                break;
        }

        $faqs = Model\Faq::getAll($filters['section']);

        return array(
                'folder' => 'faq',
                'file' => 'list',
                'faqs' => $faqs,
                'sections' => $sections,
                'filters' => $filters
        );

    }

}

