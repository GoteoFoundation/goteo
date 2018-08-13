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

        $this->admin = $this->user->hasPerm('admim-module-blog');

        if($this->admin || $post->published) {
            return $post;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-blog-not-published-yet'));
    }

    /**
     * Returns a list of tags for suggestions
     */
    public function tagsAction(Request $request) {

        $tags = PostTag::getAll();
        // uksort($tags, function() { return rand() > rand(); });
        $keywords = array_map(function($k, $v) {
            return ['tag' => $v, 'id' => $k];
        }, array_keys($tags), $tags);

        return $this->jsonResponse($keywords);
    }


    /**
     * AJAX upload image for the blog
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || $this->user->hasPerm('admim-module-blog'))
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request, 'file'); // 'file' is the expected form input name in the post object
        return $this->jsonResponse($result);
    }

    public function deleteImagesAction($id, $image, Request $request) {
        $post = $this->validatePost($id);

        $vars = array(':post' => $post->id, ':image' => $image);
        Post::query("DELETE FROM post_image WHERE post = :post AND image = :image", $vars);
        $sql = "SELECT COUNT(*) FROM post_image WHERE post = :post AND image = :image";
        $success = (int) Post::query($sql, $vars)->fetchColumn() === 0;

        return $this->jsonResponse(['image' => $image, 'result' => $success]);
    }

    public function postDefaultImagesAction($id, $image, Request $request) {
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

}

