<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterBlogPostEvent;

use Goteo\Model\Post;
use Goteo\Library\Text;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Model\Blog\Post\Tag as PostTag;

class BlogApiController extends AbstractApiController {

    protected function validatePost($slug) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $post = BlogPost::getBySlug($slug);

        if(!$post)
            throw new ModelNotFoundException();

        if($this->user->hasPerm('admin-module-blog') || $post->publish) {
            return $post;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-blog-not-published-yet'));
    }

    /**
     * Simple listing of posts
     */
    public function postsAction(Request $request) {
        if(!$this->user) throw new ControllerAccessDeniedException();

        $filters = [];
        $page = max((int) $request->query->get('pag'), 0);
        // General search
        if($request->query->has('q')) {
            $filters['superglobal'] = $request->query->get('q');
        }

        $filters['blog'] = 1;
        $limit = 25;
        $offset = $page * $limit;
        $list = [];

        foreach(Post::getFilteredList($filters, $offset, $limit) as $post) {
            $ob = ['id' => $post->id,
                   'title' => $post->title
                ];
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list,
            'total' => count($list),
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Returns a list of tags for suggestions
     */
    public function tagsAction() {
        $tags = PostTag::getAll();
        $keywords = array_map(function($k, $v) {
            return ['tag' => $v, 'id' => $k];
        }, array_keys($tags), $tags);

        return $this->jsonResponse($keywords);
    }

    /**
     * AJAX upload image for the blog
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || !$this->user->hasPerm('admin-module-blog'))
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request, 'file'); // 'file' is the expected form input name in the post object
        return $this->jsonResponse($result);
    }

    public function deleteImagesAction($id, $image) {
        $post = $this->validatePost($id);

        $vars = array(':post' => $post->id, ':image' => $image);
        Post::query("DELETE FROM post_image WHERE post = :post AND image = :image", $vars);
        $sql = "SELECT COUNT(*) FROM post_image WHERE post = :post AND image = :image";
        $success = (int) Post::query($sql, $vars)->fetchColumn() === 0;

        return $this->jsonResponse(['image' => $image, 'result' => $success]);
    }

    public function postDefaultImagesAction($id, $image) {
        $post = $this->validatePost($id);

        $success = false;
        $msg = Text::get('dashboard-project-image-default-ko');
        if($post->all_galleries) {
            $vars = array(':post' => $post->id, ':image' => $image);
            foreach($post->all_galleries as $key => $gal) {
                foreach($gal as $img) {
                    if($img->imageData->name === $image) {
                        // Set default
                        Post::query("UPDATE post SET image = :image WHERE id = :post", $vars);
                        $sql = "SELECT COUNT(*) FROM post WHERE id = :post AND image = :image";
                        $success = (int) Post::query($sql, $vars)->fetchColumn() === 1;
                        if($success) $msg = '';
                        break;
                    }
                }
                if($success) break;
            }
        }
        return $this->jsonResponse(['msg' => $msg, 'default' => $image, 'result' => $success]);
    }

    /**
     * Individual project updates property checker/updater
     * To update a property, use the PUT method
     */
    public function postPropertyAction($id, $prop, Request $request) {
        $post = $this->validatePost($id);

        if(!$post) throw new ModelNotFoundException();

        $read_fields = [
            'id', 'title', 'text', 'media', 'date', 'author', 'allow', 'publish', 'image', 'header_image', 'section',
            'gallery', 'owner_type', 'owner_id', 'owner_name', 'user_name'
        ];
        $write_fields = ['title', 'text', 'date', 'allow', 'publish'];
        $properties = [];
        foreach($read_fields as $f) {
            if(isset($post->{$f})) {
                $val = $post->{$f};
                if($val instanceOf Image) {
                    $val = $val->getName();
                }
                if(is_array($val)) {
                    foreach($val as $i => $ssub) {
                        if($sub instanceOf Image) {
                            $val[$i] = $sub->getName();
                        }
                    }
                }
                if(in_array($f, ['allow', 'publish'])) {
                    $val = (bool) $val;
                }
                $properties[$f] = $val;
            }
        }
        if(!array_key_exists($prop, $properties)) {
            throw new ModelNotFoundException("Property [$prop] not found");
        }
        $result = ['value' => $properties[$prop], 'error' => false];

        if($request->isMethod('put') && $request->request->has('value')) {

            if(!$this->user || !$this->user->hasPerm('admin-module-blog'))
                throw new ControllerAccessDeniedException();

            if (!in_array($prop, $write_fields)) {
                throw new ModelNotFoundException("Property [$prop] not writeable");
            }
            $post->{$prop} = $request->request->get('value');

            if(in_array($prop, ['allow', 'publish'])) {
                if($post->{$prop} == 'false') $post->{$prop} = false;
                if($post->{$prop} == 'true') $post->{$prop} = true;
                $post->{$prop} = (bool) $post->{$prop};
            }

            $post->dbUpdate([$prop]);
            $result['value'] = $post->{$prop};
            $this->dispatch(AppEvents::BLOG_POST, new FilterBlogPostEvent($post));

            if($errors = Message::getErrors()) {
                $result['error'] = true;
                $result['message'] = implode("\n", $errors);
            }
            if($messages = Message::getMessages()) {
                $result['message'] = implode("\n", $messages);
            }
        }
        return $this->jsonResponse($result);
    }

}
