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

use Goteo\Library\Text;
use Goteo\Model\Contract\BaseDocument;
use Goteo\Model\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class QuestionnaireApiController extends AbstractApiController {

    /**
     * AJAX upload document to a questionnaire
     */
    public function questionnaireUploadDocumentsAction(Request $request) {
        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = Text::get('all-files-uploaded');
        $result = [];

        $all_success = true;
        foreach($files as $file) {
            if(!$file instanceOf UploadedFile) continue;
            // Process image
            $msg = Text::get('uploaded-ok');
            if($err = Image::getUploadErrorText($file->getError())) {
                $success = false;
                $msg = $err;
            } else {
                $errors = [];
                $doc = new BaseDocument($file);
                if ($doc->save($errors)) {
                    $success = true;
                } else {
                    $msg = implode(', ',$errors['image']);
                }
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $doc->getName(),
                'success' => $success,
                'msg' => $msg,
                'regularFile' => true,
                'type' => $doc->getType(),
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

        return $this->jsonResponse(['doc' => $doc, 'files' => $result, 'msg' => $global_msg, 'success' => $all_success]);
  }

}
