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

use Goteo\Model\Post;
use Goteo\Library\Text;
use Goteo\Model\Communication;

class CommunicationApiController extends AbstractApiController {

    protected function validateCommunication() {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        if($this->user->hasPerm('admin-module-Communication')) {
            return true;
        }
    }

    /**
     * AJAX upload image for the Communication
     */
    public function uploadImagesAction(Request $request) {
        // if(!$this->user || !$this->user->hasPerm('admin-module-communication'))
        //     throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request, 'file'); // 'file' is the expected form input name in the post object
        return $this->jsonResponse($result);
    }

}

