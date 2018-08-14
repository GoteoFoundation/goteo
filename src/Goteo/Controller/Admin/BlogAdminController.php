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
use Goteo\Application\Event\FilterBlogPostEvent;
use Goteo\Application\AppEvents;
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
        $node = Config::get('node');
        $blog = Blog::get($node, 'node');
        if (!$blog instanceof Blog) {
            $blog = new Blog(array('type'=>'node', 'owner' => $node, 'active'=>1));
            $errors = [];
            if ($blog->save($errors)) {
                Message::info(Text::get('admin-blog-initialized'));
            } else {
                Message::error("Error creating node-blog space for node [$node]", implode(',',$errors));
                return $this->redirect('/admin/blog');
            }
        } elseif (!$blog->active) {
            Message::error(Text::get('admin-blog-deactivated'));
            return $this->redirect('/admin/blog');
        }

        // primero comprobar que tenemos blog
        if (!$blog instanceof Blog) {
            throw new ModelNotFoundException("Not found node-blog space for node [$node]!");
        }

        if(!$slug) {
            $post = new BlogPost([
                'blog' => $blog->id,
                'date' => date('Y-m-d'),
                'publish' => false,
                'allow' => true,
                'owner_id' => $node,
                'author' => $this->user->id
            ]);
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
                $this->dispatch(AppEvents::BLOG_POST, new FilterBlogPostEvent($processor->getModel()));
                Message::info(Text::get('admin-blog-edit-success'));
                return $this->redirect('/admin/blog?' . $request->getQueryString());
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('admin/blog/edit', [
            'form' => $form->createView(),
            'post' => $post,
            'user' => $user
        ]);
    }
}
