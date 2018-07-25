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

use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Blog;
use Goteo\Model\Post;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Library\Forms\FormModelException;

class BlogAdminController extends AbstractAdminController {
    protected static $icon = '<i class="fa fa-2x fa-file-text-o"></i>';

    public static function getRoutes() {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/edit/{pid}', [
                    '_controller' => __CLASS__ . "::editAction"
                ]
            ),
        ];
    }


    public function listAction(Request $request) {
        $filters = ['q' => $request->query->get('q')];
        $limit = 25;
        $page = $request->query->get('pag') ?: 0;
        $users = BlogPost::getList($filters, false, $page * $limit, $limit);
        $total = BlogPost::getList($filters, false, 0, 0, true);

        return $this->viewResponse('admin/users/list', [
            'list' => $users,
            'link_prefix' => '/users/manage/',
            'total' => $total,
            'limit' => $limit,
            'filter' => [
                '_action' => '/users',
                'q' => Text::get('admin-users-global-search')
            ]
        ]);
    }
}
