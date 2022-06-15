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

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterProjectEvent;
use Goteo\Application\Message;
use Goteo\Model\Questionnaire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProjectChannelListener extends AbstractListener {
    protected $request;

    /**
     * Verify we are in POST in project create controller
     */
    public function onRequest(RequestEvent $event) {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');
        if($controller === 'Goteo\Controller\ProjectController::createAction' && $request->isMethod('post')) {
            $this->request = $request;
        }
    }

    /**
     * Apply channel to a project if needed
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

        if (Questionnaire::getByMatcher($project->node) || Questionnaire::getByChannel($project->node)) {
            $event->setResponse(new RedirectResponse('/channel/'. $project->node .'/apply/'.$project->id ));
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
            AppEvents::PROJECT_CREATED => 'onProjectCreated'
        );
    }
}
