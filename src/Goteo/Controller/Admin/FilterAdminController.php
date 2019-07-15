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
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Application\Exception\ModelNotFoundException;

class FilterAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-filter"></i>';

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
                '/edit/{id}',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::editAction"]
            ),
        ];
    }

    public function listAction(Request $request)
    {
        $list = Filter::getAll();
        
        return $this->viewResponse('admin/filter/list',[
            'list' => $list,
        ]);
    }

    public function editAction($id = '', Request $request)
    {        
        $filter = $id ? Filter::get($id) : new Filter(); 
		if (!$filter) {
			throw new ModelNotFoundException("Not found filter [$id]");
		}


        $defaults = (array) $filter;
        $processor = $this->getModelForm('ProjectFilter', $filter, $defaults, Array(), $request);
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

}
