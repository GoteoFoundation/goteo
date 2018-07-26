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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;

use Goteo\Model\Post;
use Goteo\Model\Image;
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
        $keywords = array_map(function($k, $v) {
            return ['tag' => $v, 'key' => $k];
        }, array_keys($tags), $tags);

        return $this->jsonResponse($keywords);
    }


    /**
     * AJAX upload image (Generic image uploader for projects with optional project gallery updater)
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || $this->user->hasPerm('admim-module-blog'))
            throw new ControllerAccessDeniedException();

        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = Text::get('all-files-uploaded');
        $result = [];

        $all_success = true;
        foreach($files as $file) {
            if(!$file instanceOf UploadedFile) continue;
            // Process image
            $msg = Text::get('uploaded-ok');
            $success = false;
            if($err = Image::getUploadErrorText($file->getError())) {
                $success = false;
                $msg = $err;
            } else {
                $image = new Image($file);
                $errors = [];
                if ($image->save($errors)) {
                    $success = true;
                }
                else {
                    $msg = implode(', ',$errors['image']);
                    // print_r($errors);
                }
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $image->name,
                'success' => $success,
                'msg' => $msg,
                'error' => $file->getError(),
                'size' => $file->getSize(),
                'maxSize' => $file->getMaxFileSize(),
                'errorMsg' => $file->getError() ? $file->getErrorMessage() : ''
            ];
            if(!$success) {
                $global_msg = Text::get('project-upload-images-some-ko');
                $all_success = false;
            }
        }

        return $this->jsonResponse(['files' => $result, 'cover' => $cover,  'msg' => $global_msg, 'success' => $all_success]);
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

