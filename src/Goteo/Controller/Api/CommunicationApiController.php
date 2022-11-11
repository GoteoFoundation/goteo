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

use Goteo\Model\Communication;
use Goteo\Model\Mail;


class CommunicationApiController extends AbstractApiController {

    protected function validateCommunication() {
        if(!$this->user)
            throw new ControllerAccessDeniedException();

        if($this->user->hasPerm('admin-module-communication')) {
            return true;
        }
    }

    /**
     * AJAX upload image for the Communication
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || !$this->user->hasPerm('admin-module-communication'))
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request); // 'file' is the expected form input name in the post object
        return $this->jsonResponse($result);
    }

    public function successAction($id) {
        $this->validateCommunication();
        $communication = Communication::get($id);
        return $this->jsonResponse([ 'id' => $id , 'success' => $communication->getStatus()]);
    }

    public function mailStatusAction($mail) {
        $this->validateCommunication();
        $sender = Mail::get($mail);

        $result = [
            'id' => $mail,
            'sent' => $sender->getStatusObject()->sent,
            'failed' => $sender->getStatusObject()->failed,
            'pending' => $sender->getStatusObject()->pending,
            'success' => (int) $sender->getStats()->getEmailOpenedCollector()->getPercent(),
            'status' => $sender->getStatus(),
            'percent' => (int) $sender->getStatusObject()->percent
        ];

        return $this->jsonResponse($result);
    }

}
