<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Library\Forms\Admin\AdminFaqSectionForm;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Faq\FaqSection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class FaqSectionAdminController extends AbstractAdminController
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


    /**
     * @throws ControllerAccessDeniedException
     */
    private function validateFaqSection(int $id = null): FaqSection
    {

        if (!$this->user)
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));

        if (!$this->user->hasPerm('admin-module-faqs'))
            throw new ControllerAccessDeniedException();

        return $id ? FaqSection::getById($id) : new FaqSection();
    }

    public function listAction(Request $request): Response
    {
        $page = $request->query->getDigits('pag', 0);
        $limit = $request->query->getDigits('limit', 10);

        $total = FaqSection::getListCount([]);
        $list = FaqSection::getList([], $limit * $page, $limit);

        return $this->viewResponse('admin/faq/section/list',[
            'list' => $list,
            'total' => $total,
            'limit' => $limit
        ]);
    }

    public function addAction(Request $request): Response
    {
        $faqSection = $this->validateFaqSection(null);

        return $this->generateSectionFormView($request, $faqSection);
    }

    public function editAction(Request $request, int $id): Response
    {
        $faqSection = $this->validateFaqSection($id);

        return $this->generateSectionFormView($request, $faqSection);
    }

    private function generateSectionFormView(Request $request, FaqSection $faqSection): Response
    {
        $processor = $this->getModelForm(AdminFaqSectionForm::class, $faqSection, (array) $faqSection, [], $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            try {
                $processor->save($form);
                return $this->redirect("/admin/faqsection");
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/faq/section/edit', [
            'form' => $form->createView(),
            'faqSection' => $faqSection
        ]);
    }

    public function deleteAction(Request $request, int $id): Response
    {
        try {
            $faqSection = $this->validateFaqSection($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/faqsection');
        }

        try {
            $faqSection->dbDelete();
            Message::info(Text::get('admin-edit-entry-ok'));
        } catch (\PDOException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect('/admin/faqsection');
    }
}
