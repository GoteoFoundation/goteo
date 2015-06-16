<?php

namespace Goteo\Controller\Admin;

use Goteo\Core\Redirection,
	Goteo\Application\Message,
    Goteo\Model;

class FaqSubController extends AbstractSubController {

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
                'node' => \GOTEO_NODE,
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
                $faq = Model\Faq::get($id);

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
                Model\Faq::delete($id);
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

