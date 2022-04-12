<?php

namespace Goteo\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Controller\InvestController;
use Goteo\Controller\InvestRecoverController;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

//
class InvestStatusChangerListener extends AbstractListener {
    private $ok = false;

    // añadiendo custom variables
    public function onController(ControllerEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();
        $invest = Session::get('recover-invest');

        if( !is_array($controller) ) return;

        if( $controller[0] instanceOf InvestController){
            if(!$invest) return;
            $user = $invest->getUser();

            if($controller[1] === 'userDataAction') {
                $request = $event->getRequest();
                $new = Invest::get($request->attributes->get('invest_id'));
                if(!$new instanceOf Invest) {
                    Message::error("Invest [{$new->id}] not found");
                    return;
                }
                if($new->status != Invest::STATUS_CHARGED) {
                    $event->setController(function() use ($invest){
                        return new RedirectResponse('/invest/' . $invest->project . '/recover/' . $invest->id);
                    });
                    return;
                }
                // Set the old recover to status returned
                $invest->setStatus(Invest::STATUS_CANCELLED);
                $invest->status = Invest::STATUS_CANCELLED;
                $invest->returned = date('Y-m-d');
                $errors = [];
                $err = false;
                if(!$invest->save($errors)) {
                    Message::error('Error:' .  implode(',', $errors));
                    $err = true;
                }
                // Assign the same reward if exists
                if($reward = $invest->getFirstReward()) {
                    $new->setResign(false);
                    if(!$new->setRewards([$reward])) {
                        Message::error(Text::get('invest-return-error-reward'));
                        $err = true;
                    }
                }
                Session::del('recover-invest');
                if(!$err) Message::info(Text::get('invest-return-success'));
                return;

            } elseif($controller[1] === 'selectPaymentMethodAction') {
                if(Session::getUserId() !== $user->id) {
                    if(Session::isLogged()) {
                        Session::store('recover-old-user', Session::getUser());
                    } else {
                        Session::store('destroy-session-on-finish', true);
                    }
                    Session::setUser($user);
                }
            }
            Message::info(Text::get('invest-return-compensation', \date_formater($invest->invested)));
            if($reward = $invest->getFirstReward()) {
                Message::info(Text::get('invest-return-reward', $reward->reward));
            }
        } elseif( ! $controller[0] instanceOf InvestRecoverController) {
            // Out of invest scope remove the session
            if($old = Session::get('recover-old-user')) {
                Session::setUser($old);
            } elseif(Session::get('destroy-session-on-finish')) {
                Session::destroy();
            }
            Session::del('recover-old-user');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::CONTROLLER   => 'onController'
        );
    }
}
