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
use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Blog;
use Goteo\Model\User;
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
                '/edit/{slug}', [
                    '_controller' => __CLASS__ . "::editAction"
                ]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::slugAction", 'slug' => '']
            )
        ];
    }


    public function listAction(Request $request) {
        $filters = ['superglobal' => $request->query->get('q')];
        $limit = 25;
        $page = $request->query->get('pag') ?: 0;
        $users = BlogPost::getList($filters, false, $page * $limit, $limit, false, Config::get('lang'));
        $total = BlogPost::getList($filters, false, 0, 0, true);

        return $this->viewResponse('admin/blog/list', [
            'list' => $users,
            'link_prefix' => '/blog/edit/',
            'total' => $total,
            'limit' => $limit,
            'filter' => [
                '_action' => '/blog',
                'q' => Text::get('admin-blog-global-search')
            ]
        ]);
    }

    public function editAction($slug = '', Request $request) {
        if(!$slug) {
            $post = new BlogPost();
        } else {
            $post = BlogPost::getBySlug($slug);
        }
        if(!$post) throw new ModelNotFoundException("Not found post [$slug]");

        $defaults = (array)$post;
        $processor = $this->getModelForm('AdminPostEdit', $post, $defaults, [], $request);
        $processor->createForm();
        $processor->getBuilder()
            ->add('submit', 'submit', [
                'label' => $submit_label ? $submit_label : 'regular-submit'
            ]);
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $processor->save($form, true);
                Message::info(Text::get('user-register-success'));
                return $this->redirect('/admin/blog?' . $request->getQueryString());
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/blog/add', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
