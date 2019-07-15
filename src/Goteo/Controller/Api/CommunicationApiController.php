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

use Goteo\Model\Filter;
use Goteo\Library\Text;
use Goteo\Library\Check;

class CommunicationApiController extends AbstractApiController {

    protected function validateCommunication($id) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $communication = $id ? Communication::get($id) : new Communication();
        
        if($this->user->hasPerm('admin-module-communication') ) {
            return $communication;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-Communication-not-active-yet'));
    }

    public function filterAction($id, Request $request) {
        // if(!$this->user && !$this->user->hasPerm('admin-module-Communication') )
        //     throw new ControllerAccessDeniedException();

        if($request->isMethod('GET')) {
            $filter = Filter::get($id);
        }

        return $this->jsonResponse($filter);
    }

    public function addFilterAction(Request $request) {

        // if(!$this->user && !$this->user->hasPerm('admin-module-communication') )
        //     throw new ControllerAccessDeniedException();        

        $filter = new Filter();

        if($request->isMethod('POST')) {
            
            if ($request->request->has('id')) {
                $filter = Filter::get($request->request->get('id'));
            }

            $filter->name = $request->request->get('name');
            $filter->description = $request->request->get('description');
            $filter->cert = $request->request->get('cert');
            $filter->role = $request->request->get('role');
            $filter->startdate = \DateTime::createFromFormat("d/m/Y", $request->request->get('startdate'));
            $filter->enddate = \DateTime::createFromFormat("d/m/Y", $request->request->get('enddate'));
            $filter->status = $request->request->get('status');
            $filter->typeofdonor = $request->request->get('typeofdonor');
            $filter->wallet = $request->request->get('wallet');
            $filter->project_latitude = $request->request->get('project_latitude');
            $filter->project_longitude = $request->request->get('project_longitude');
            $filter->project_radius = $request->request->get('project_radius');
            $filter->project_location = $request->request->get('project_location');

            $filter->save($errors);
        }
        return $this->jsonResponse($filter);
    }
    
}