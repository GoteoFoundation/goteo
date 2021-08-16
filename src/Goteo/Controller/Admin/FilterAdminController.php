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
use Goteo\Application\Message;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\Model\FilterForm;
use Goteo\Library\Text;
use Goteo\Model\Filter;
use PDOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class FilterAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-filter"></i>';

    public static function getGroup(): string
    {
        return 'communications';
    }

    public static function getRoutes(): array
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/edit/{id}',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/delete/{id}',
                ['_controller' => __CLASS__ . "::deleteAction"]
            )
        ];
    }

    public function listAction(Request $request)
    {
        $page = $request->query->get('pag') ?: 0;
        $limit = 10;
        $list = Filter::getList(array(), $page * $limit, $limit, false);
        $total = Filter::getList(array(), 0, 0, true);

        return $this->viewResponse('admin/filter/list',[
            'list' => $list,
            'total' => $total
        ]);
    }

    public function editAction(Request $request, $id = '')
    {
        try  {
            $filter = $id ? Filter::get($id) : new Filter();
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/filter');
        }

        $defaults = (array) $filter;
        $processor = $this->getModelForm(FilterForm::class, $filter, $defaults, Array(), $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            // Check if we want to remove an entry
            try {
                $processor->save($form); // Allow save event if does not validate
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/admin/filter/" . $request->getQueryString());
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/filter/edit',[
            'form' => $form->createView()
        ]);
    }

    public function deleteAction($id) {

        try {
            $filter = Filter::get($id);
        } catch (ModelNotFoundException $exception) {
            Message::error($exception->getMessage());
        }

        try {
            if ($filter->isUsed()) {
                Message::error(Text::get('admin-remove-filters-forbidden'));
            }
            else {
                $filter->dbDelete();
                Message::info(Text::get('admin-remove-entry-ok'));
            }
        } catch (PDOException $e) {
          Message::error($e->getMessage());
        }

        return $this->redirect('/admin/filter/');
	}
}
