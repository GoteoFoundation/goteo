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

use Goteo\Core\Controller;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Model\Image;
use Goteo\Library\Text;

abstract class AbstractApiController extends Controller
{
    protected $is_admin = false;
    protected ?User $user = null;

    public function __construct() {
        // changing to a json theme here (not really a theme)
        View::setTheme('JSON');
        $this->user = Session::getUser();
        $this->is_admin = Session::isAdmin();
        // cache active only on non-logged users
        if(!$this->user) $this->dbCache(true);
    }

    /**
     * Generic file uploader to be used by other api controllers
     */
    protected function genericFileUpload(Request $request, $field_name = 'file') {
        $files = $request->files->get($field_name);
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
                } else {
                    $msg = implode(', ',$errors['image']);
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

        return ['files' => $result, 'cover' => $cover,  'msg' => $global_msg, 'success' => $all_success];
    }

}
