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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Message;
use Goteo\Model\Faq;
use Goteo\Model\Faq\FaqSection;
use Goteo\Model\Faq\FaqSubsection;
use Goteo\Library\Forms\Admin\AdminFaqForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class FaqAdminController extends AbstractAdminController
{
    protected static string $icon = '<i class="fa fa-2x fa-question-circle-o"></i>';

    public static function getGroup(): string {
        return 'contents';
    }

    public static function getRoutes(): array
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/subsection/{subsection}',
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

    /**
     * @throws ControllerAccessDeniedException
     */
    private function validateFaq(int $id = null): Faq
    {

        if (!$this->user)
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));

        if (!$this->user->hasPerm('admin-module-faqs'))
            throw new ControllerAccessDeniedException();

        return $id ? Faq::getById($id) : new Faq();
    }

    public function listAction(Request $request, int $subsection = null): Response
    {
        $this->validateFaq();

        $filters = [];
        if ($subsection)
            $filters['subsection'] = $subsection;

        $page = $request->query->getDigits('pag', 0);
        $limit = $request->query->getDigits('pag', 25);

        $subsectionCount = FaqSubsection::getList([], 0, 0, true);
        $faq_subsections = [];
        foreach(FaqSubsection::getList([], 0, $subsectionCount) as $s) {
            $faq_subsections[FaqSection::getById($s->section_id)->name][$s->id] = $s->name;
        }

        $total = Faq::getList($filters,0,0, true);
        $list = Faq::getList($filters, $page * $limit, $limit);
        return $this->viewResponse('admin/faq/list', [
            'list' => $list,
            'total' => $total,
            'limit' => $limit,
            'faq_subsections' => $faq_subsections,
            'current_subsection' => $subsection
        ]);

    }

    public function editAction(Request $request, $id = null): Response
    {
        $faq = $this->validateFaq($id);

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

    public function deleteAction(Request $request, $id): Response
    {
        try {
            $faq = $this->validateFaq();
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
