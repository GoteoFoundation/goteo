<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Library\Feed;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\Model\PageForm;
use Goteo\Library\Text;
use Goteo\Model\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class PagesAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-file-text-o"></i>';

    public static function getGroup(): string
    {
        return 'contents';
    }

    public static function getRoutes(): array
    {
        return [
            new Route('/',
                [
                    '_controller' => __CLASS__ . '::indexAction',
                    'method' => Request::METHOD_GET
                ]
            ),
            new Route('/add',
                [
                    '_controller' => __CLASS__ . '::editAction',
                ]
            ),
            new Route('/edit/{id}',
                [
                    '_controller' => __CLASS__ . '::editAction',
               ]
            ),
            new Route('/delete/{id}',
                [
                    '_controller' => __CLASS__ . '::deleteAction',
                ]
            ),
        ];
    }

    public function indexAction(Request $request): Response
    {
        $total = Page::count();
        $pages = Page::getList();

        return $this->viewResponse('admin/pages/list', [
            'list' => $pages,
            'total' => $total
        ]);
    }

    public function editAction(Request $request, string $id = null): Response
    {
        if ($page = Page::get($id)) {
        } else {
            $page = new Page();
        }

        $defaults = (array) $page;
        $processor = $this->getModelForm(PageForm::class, $page, $defaults, [], $request);
        $processor->createForm();

        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-pages-edit-success'));
                return $this->redirect('/admin/pages');
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/pages/edit', [
            'page' => $page,
            'form' => $form->createView()
        ]);
    }

    public function deleteAction(Request $request, string $id): Response
    {
        $page = Page::get($id);

        try {
            $page->dbDelete();
            Message::info(Text::get('admin-pages-delete-success'));
        } catch (\PDOException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect('/admin/pages');
    }
}
