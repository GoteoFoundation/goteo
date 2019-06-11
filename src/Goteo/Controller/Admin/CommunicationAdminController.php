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

use Goteo\Model\Filter;
use Goteo\Controller\Message;
use Goteo\Library\Text;

class CommunicationAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-envelope-o"></i>';

    public static function getGroup() {
        return 'communications';
    }

    public static function getRoutes()
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/detail/{id}', 
                ['_controller' => __CLASS__ . "::detailAction"]
            ),
            new Route(
                '/edit/{template}',
                ['_controller' => __CLASS__ . "::editAction"]
            )

        ];
    }

    public function listAction(Request $request)
    {
        // $list = Sender::getMailingList();
        
		// $filter = $id ? Filter::get($id) : new Filter();

		// if (!$filter) {
		// 	throw new ModelNotFoundException("Not found filter [$id]");
		// }

        $filter = new Filter();
        $processor = $this->getModelForm('ProjectFilter', $filter , Array(), Array(), $request);
        $processor->createForm();
        $form = $processor->getForm();

        $form->handleRequest($request);
        $filters = $filter->getAll();

        return $this->viewResponse('admin/communication/list',[
            // 'filters' => $filters
            'filters' => $filter->getAll(),
            'form' => $form->createView()
        ]);

    }

    public function editAction(Request $request)
    {
    }

    public function detailAction(Request $request)
    {
    }
}
