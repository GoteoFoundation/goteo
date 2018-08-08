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
use Goteo\Application\Exception\ModelException;

use Goteo\Model\Stories;
use Goteo\Model\Image;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

class StoriesApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        // De-Activate cache & replica read for this controller by default
        $this->dbReplica(false);
        $this->dbCache(false);
    }

    
    /**
     * AJAX upload image to story
     */
    public function storyUploadImageAction($id, Request $request) {
        if(!($story = Stories::get($id))) {
            throw new ModelNotFoundException();
        }

        $story->project=$story->project->id;

        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = Text::get('all-files-uploaded');
        $result = [];

        $image = $story->image->id ? $story->image->id : null;
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
                $story->image = new Image($file);
                $errors = [];
                if ($story->save($errors)) {
                    $success = true;
                } else {
                    $msg = implode(', ',$errors['image']);
                    // print_r($errors);
                }
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $story->image,
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

        return $this->jsonResponse(['files' => $result, 'image' => $image,  'msg' => $global_msg, 'success' => $all_success]);
    }

    /**
     * AJAX upload pool image to story
     */
    public function storyUploadPoolImageAction($id, Request $request) {
        if(!($story = Stories::get($id))) {
            throw new ModelNotFoundException();
        }

        $story->project=$story->project->id;

        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = Text::get('all-files-uploaded');
        $result = [];

        $pool_image = $story->pool_image->id ? $story->pool_image->id : null;
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
                $story->pool_image = new Image($file);
                $errors = [];
                if ($story->save($errors)) {
                    $success = true;
                } else {
                    $msg = implode(', ',$errors['pool_image']);
                    // print_r($errors);
                }
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $story->pool_image,
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

        return $this->jsonResponse(['files' => $result, 'pool_image' => $pool_image,  'msg' => $global_msg, 'success' => $all_success]);
    }

}
