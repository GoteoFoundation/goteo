<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Library\Forms\Admin\AdminFaqSubsectionForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Faq\FaqSection;
use Goteo\Model\Faq\FaqSubsection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class FaqSubsectionAdminController extends AbstractAdminController
{
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
                '/section/{section}',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::addAction"]
            ),
            new Route(
                '/{id}/edit',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/{id}/delete',
                ['_controller' => __CLASS__ . "::deleteAction"]
            ),
        ];
    }

    public function listAction(Request $request, int $section = null): Response
    {
        $page = $request->query->getDigits('pag', 0);
        $limit = $request->query->getDigits('limit', 10);
        $filters = [];

        if ($section) {
            $filters['section'] = $section;
        }

        $sectionsCount = FaqSection::getList([],0, 0, true);
        $sections = FaqSection::getList([], 0, $sectionsCount);

        $total = FaqSubsection::getList($filters,0,0, true);
        $list = FaqSubsection::getList($filters, $limit * $page, $limit);

        return $this->viewResponse('admin/faq/subsection/list',[
            'list' => $list,
            'total' => $total,
            'limit' => $limit,
            'faq_sections' => $sections,
            'current_section' => $section
        ]);
    }

    public function addAction(Request $request): Response
    {
        $faqSubsection = new FaqSubsection();

        return $this->generateSubsectionFormView($request, $faqSubsection);
    }

    public function editAction(Request $request, int $id): Response
    {
        $faqSubsection = FaqSubsection::get($id);

        return $this->generateSubsectionFormView($request, $faqSubsection);
    }

    private function generateSubsectionFormView(Request $request, FaqSubsection $faqSubsection): Response
    {
        $processor = $this->getModelForm(AdminFaqSubsectionForm::class, $faqSubsection, (array) $faqSubsection, [], $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            try {
                $processor->save($form);
                return $this->redirect("/admin/faqsubsection");
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/faq/subsection/edit', [
            'form' => $form->createView(),
            'faqSubsection' => $faqSubsection
        ]);
    }

    public function deleteAction(Request $request, int $id): Response
    {
        try {
            $faqSubsection = FaqSubsection::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/faqsubsection');
        }

        try {
            $faqSubsection->dbDelete();
            Message::info(Text::get('admin-edit-entry-ok'));
        } catch (\PDOException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect('/admin/faqsubsection');
    }
}
