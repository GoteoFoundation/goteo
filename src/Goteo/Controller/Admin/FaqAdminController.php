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

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Goteo\Model\Faq;
use Goteo\Model\Faq\FaqSubsection;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Library\Text;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class FaqAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-question-circle-o"></i>';

    public static function getGroup() {
        return 'contents';
    }

    public static function getRoutes()
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/edit/{id}',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/delete/{id}',
                ['_controller' => __CLASS__ . "::deleteAction"]
            ),
            new Route(
                '/{subsection}',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
        ];
    }

    public function listAction($subsection = null, Request $request)
    {
        if ($subsection) {
            $filters['subsection'] = $subsection;
        }

        $limit = 25;
        $page = $request->query->get('pag') ?: 0;

        $faq_subsections = FaqSubsection::getList();
        $list = Faq::getList($filters, $page, $limit);
        $total = Faq::getList($filters, $page, $limit, true);

        return $this->viewResponse('admin/faq/list', [
            'list' => $list,
            'total' => $total,
            'limit' => $limit,
            'faq_subsections' => $faq_subsections,
            'current_subsection' => $subsection
        ]);
        
    }   

    public function editAction($id = null, Request $request)
    {
        if ($id) {
            $faq = Faq::getById($id);
        } else {
            $faq = new Faq();
        }

        $processor = $this->getModelForm('AdminFaq', $faq, (array) $faq, Array(), $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/admin/faq/" . $faq->subsection_id);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/faq/edit', [
            'form' => $form->createView()
        ]);
    }

    public function deleteAction($id, Request $request) {
        
        try {
            $faq = Faq::getById($id);
        } catch (ModelNotFoundException $exception) {
            Message::error($exception->getMessage());
        }


        try {
            $faq->dbDelete();
            Message::info(Text::get('admin-remove-entry-ok'));
        } catch (\PDOException $e) {
          Message::error($e->getMessage());  
        } 

        return $this->redirect('/admin/faq/' . $faq->section);
	}


}
