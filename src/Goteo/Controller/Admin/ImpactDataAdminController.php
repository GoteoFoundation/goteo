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
use Goteo\Library\Text;
use Goteo\Library\Forms\Admin\AdminImpactDataForm;
use Goteo\Model\ImpactData;
use PDOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class ImpactDataAdminController extends AbstractAdminController
{
    protected static string $icon = '<i class="fa fa-2x fa-table"></i>';

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

    public function listAction(Request $request): Response
    {
        $page = $request->query->getDigits('pag', 0);
        $limit = 10;

        $list = ImpactData::getList([], $page * $limit, $limit, false);
        $total = ImpactData::getList([], 0, 0, true);

        return $this->viewResponse('admin/impact_data/list',[
            'list' => $list,
            'total' => $total
        ]);
    }

    public function editAction(Request $request, $id = ''): Response
    {
        try  {
            $impact_data = $id ? ImpactData::get($id) : new ImpactData();
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/impactdata');
        }

        $defaults = (array) $impact_data;
        $processor = $this->getModelForm(AdminImpactDataForm::class, $impact_data, $defaults, [], $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            try {
                $processor->save($form); // Allow save event if does not validate
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/admin/impactdata/" . $request->getQueryString());
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/impact_data/edit',[
            'form' => $form->createView()
        ]);

    }

    public function deleteAction(Request $request, $id): Response
    {
        try {
            $impact_data = ImpactData::get($id);
            $impact_data->dbDelete();
            Message::info(Text::get('admin-remove-entry-ok'));
        } catch (ModelNotFoundException| PDOException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect('/admin/impactdata/');
    }


}
