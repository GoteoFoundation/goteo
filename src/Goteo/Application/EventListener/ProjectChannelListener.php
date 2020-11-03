<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\AppEvents;
use Goteo\Application\Message;

use Goteo\Application\Event\FilterProjectEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Model\Questionnaire;

class ProjectChannelListener extends AbstractListener {
    protected $request;

    /**
     * Verify we are in POST in project create controller
     * @param  GetResponseEvent $event [description]
     * @return [type]                  [description]
     */
    public function onRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');
        if($controller === 'Goteo\Controller\ProjectController::createAction' && $request->isMethod('post')) {
            $this->request = $request;
        }
    }

    /**
     * Apply channel to a project if needed
     * @param  FilterProjectEvent $event [description]
     * @return [type]                    [description]
     */
    public function onProjectCreated(FilterProjectEvent $event) {
        if(!$this->request) return;
        if(!$this->request->request->has('node')) return;

        $project = $event->getProject();
        // Change node
        $project->node = $this->request->request->get('node');
        $errors = [];
        if(!$project->save($errors)) {
            Message::error("Error applying your project to the channel!\n" . implode(',',$errors));
        }

        if (Questionnaire::getByChannel($project->node)) {
            $event->setResponse(new RedirectResponse('/channel/'. $project->node .'/apply/'.$project->id ));
            return;
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => 'onRequest',
            AppEvents::PROJECT_CREATED => 'onProjectCreated'
        );
    }
}
