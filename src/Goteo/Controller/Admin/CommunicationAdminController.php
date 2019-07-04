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
use Goteo\Model\Communication;
use Goteo\Model\User;
use Goteo\Controller\Message;
use Goteo\Library\Text;
use Goteo\Application\Lang;
use Goteo\Model\User\Translate;
use Goteo\Application\Session;

class CommunicationAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-envelope-o"></i>';

    public static function getGroup() {
        return 'communications';
    }

    public static function getRoutes()
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            )
        ];
    }

    public function listAction(Request $request)
    {
        $filter = new Filter();
        $processor = $this->getModelForm('ProjectFilter', $filter , Array(), Array(), $request);
        $processor->createForm();
        $form_filter = $processor->getForm();
        $form_filter->handleRequest($request);

        $filters = $filter->getAll();

        $template = ['0' => 'General communication', '1' => Text::get('newsletter-lb')];
        $translates = Translate::getLangs(Session::getUserId());
        $langs = Lang::listAll('name', false);
        $editor_types = ['md' => Text::get('admin-text-type-md'), 'html' => Text::get('admin-text-type-html')];


        return $this->viewResponse('admin/communication/list',[
            'filters' => $filter->getAll(),
            'form_filter' => $form_filter->createView(),
            'templates' => $template,
            'languages' => $langs,
            'editor_types' => $editor_types,
            'translations' => $translates,
            'variables' => Communication::variables()
        ]);

    }

    public function editAction(Request $request)
    {
    }

    public function detailAction(Request $request)
    {
    }
}
