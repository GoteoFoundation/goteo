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
use Goteo\Repository\AnnouncementRepository;

class AnnouncementApiController extends AbstractApiController {

    protected AnnouncementRepository $announcementRepository;

    public function __construct() {

        parent::__construct();
        $this->announcementRepository = new AnnouncementRepository();
    }

    protected function validateAnnouncement(int $id) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $announcement = $this->announcementRepository->getById($id);

        if(!$announcement)
            throw new ModelNotFoundException();

        if($this->user->hasPerm('admin-module-announcements')) {
            var_dump("what"); die;
            return $announcement;
        }

        throw new ControllerAccessDeniedException();
    }

    public function announcementPropertyAction($id, $prop, Request $request) {
        $announcement = $this->validateAnnouncement($id);

        if(!$announcement) throw new ModelNotFoundException();

        if (!$prop == 'active')
            return [];

        if($request->isMethod(Request::METHOD_PUT) && $request->request->has('value')) {

            if(!$this->user || !$this->user->hasPerm('admin-module-announcement'))
                throw new ControllerAccessDeniedException();

            $value = $request->request->getBoolean('value');
            $announcement->setActive($value);

            $errors = [];
            $this->announcementRepository->persist($announcement, $errors);

            $result['value'] = $announcement->isActive();

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
