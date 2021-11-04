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

use Goteo\Library\Forms\Admin\AdminPostEditForm;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Event\FilterBlogPostEvent;
use Goteo\Application\AppEvents;
use Goteo\Library\Text;
use Goteo\Model\Blog;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Library\Forms\FormModelException;

class BlogAdminController extends AbstractAdminController {
    protected static $icon = '<i class="fa fa-2x fa-file-text-o"></i>';

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
                '/edit/{slug}', [
                    '_controller' => __CLASS__ . "::editAction"
                ]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::editAction", 'slug' => '']
            )
        ];
    }

    public function listAction(Request $request) {
        $filters = ['superglobal' => $request->query->get('q')];
        $limit = 25;
        $page = $request->query->get('pag') ?: 0;
        $list = BlogPost::getList($filters, false, $page * $limit, $limit, false, Config::get('lang'));
        $total = BlogPost::getList($filters, false, 0, 0, true);

        return $this->viewResponse('admin/blog/list', [
            'list' => $list,
            'link_prefix' => '/blog/edit/',
            'total' => $total,
            'limit' => $limit,
            'filter' => [
                '_action' => '/blog',
                'q' => Text::get('admin-blog-global-search')
            ]
        ]);
    }

    public function editAction(Request $request, $slug = '') {
        $node = Config::get('node');
        $blog = Blog::get($node, 'node');
        if (!$blog instanceof Blog) {
            $blog = new Blog(array('type'=>'node', 'owner' => $node, 'active'=>1));
            $errors = [];
            if ($blog->save($errors)) {
                Message::info(Text::get('admin-blog-initialized'));
            } else {
                Message::error("Error creating node-blog space for node [$node]");
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
        $processor = $this->getModelForm(AdminPostEditForm::class, $post, $defaults, [], $request);
        $processor->createForm();
        $processor->getBuilder()
            ->add('submit', SubmitType::class, [
                'label' => 'regular-submit'
            ]);
        if($post->id) {
            $processor->getBuilder()
                ->add('remove', SubmitType::class, [
                    'label' => Text::get('admin-remove-entry'),
                    'icon_class' => 'fa fa-trash',
                    'span' => 'hidden-xs',
                    'attr' => [
                        'class' => 'pull-right-form btn btn-default btn-lg',
                        'data-confirm' => Text::get('admin-remove-entry-confirm')
                    ]
                ]);
        }

        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            // Check if we want to remove an entry
            if($form->has('remove') && $form->get('remove')->isClicked()) {
                if($post->publish) {
                    Message::error(Text::get('admin-remove-entry-forbidden'));
                    return $this->redirect('/admin/blog/');
                }

                $post->dbDelete(); //Throws and exception if fails
                Message::info(Text::get('admin-remove-entry-ok'));
                return $this->redirect('/admin/blog/');
            }

            try {
                $processor->save($form); // Do not save the model if it has errors
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
