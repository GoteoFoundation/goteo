<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

class CriteriaSubController extends AbstractSubController {

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
                $criteria = Model\Criteria::get($id);

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

