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

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Request;


class WorkshopsApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        $this->dbReplica(true);
        $this->dbCache(true);
    }

    protected function validateWorkshops($tab, $id) {
        $this->dbReplica(false);
        $this->dbCache(false);

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $class = '\\Goteo\Model\\'.ucfirst($tab);
        $model = $class::get($id);

        if(!$model)
            throw new ModelNotFoundException();

        if($this->user->hasPerm('admin-module-workshops')) {
            return $model;
        }

        throw new ControllerAccessDeniedException();
    }

    /**
     * AJAX upload header image for the workshop
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || !$this->user->hasPerm('admin-module-workshops'))
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request); // 'file' is the expected form input name in the story object
        return $this->jsonResponse($result);
    }

}
