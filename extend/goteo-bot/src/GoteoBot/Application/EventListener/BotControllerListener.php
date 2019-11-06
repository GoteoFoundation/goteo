<?php

namespace GoteoBot\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Goteo\Model\Project;
use GoteoBot\Controller\BotProjectDashboardController;

class BotControllerListener implements EventSubscriberInterface
{


    public function onController(FilterControllerEvent $event) {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');
        if(!is_string($controller)) return;

        if( strpos($controller, 'Goteo\Controller\Dashboard\ProjectDashboardController') !== false ||
            strpos($controller, 'Goteo\Controller\Dashboard\TranslateProjectDashboardController') !== false ||
            strpos($controller, 'Goteo\Controller\Dashboard\SettingsDashboardController::profileAction') !== false ||
            $controller === 'Goteo\Controller\ProjectExtrasController::indexAction' ||
            strpos($controller, 'Goteo\Controller\ProjectController') !== false ||
            strpos($controller, 'Goteo\Controller\ProjectExtrasController') !== false ) {

            $pid = $request->attributes->get('pid');
            if(!$pid) return;
            BotProjectDashboardController::createBotSidebar(Project::get($pid));

        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onController',
        );
    }
}

