<?php

namespace Goteo\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Model\Invest;

use Goteo\Application\AppEvents;
use Goteo\Application\Session;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Event\FilterInvestFinishEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Event\FilterInvestEvent;
use Goteo\Application\Event\FilterInvestModifyEvent;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Payment\PaymentException;

//
class InvestStatusChangerListener extends \Goteo\Application\EventListener\AbstractListener {
    private $ok = false;


    // añadiendo custom variables
    public function onController(FilterControllerEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();
        $invest = Session::get('recover-invest');
        // if(!$invest) return;
        // if($invest->isReturned()) {
        //     return;
        // }
        if( !is_array($controller) ) return;

        if( $controller[0] instanceOf \Goteo\Controller\InvestController){
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
                // print_r($controller);die;
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
            // print_r($invest);
            // print_r($controller);die;
        } elseif( ! $controller[0] instanceOf \Goteo\Controller\InvestRecoverController) {
            // print_r($controller);die("out of {$invest->id}");
            // Out of invest scope remove the session
            if($old = Session::get('recover-old-user')) {
                Session::setUser($old);
            } elseif(Session::get('destroy-session-on-finish')) {
                Session::destroy();
            }
            Session::del('recover-old-user');
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER   => 'onController'
        );
    }
}
