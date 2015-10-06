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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Omnipay\Common\Message\RedirectResponseInterface;

use Goteo\Application\Message;
use Goteo\Application\App;
use Goteo\Library\Text;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Model\Invest;

class InvestListener implements EventSubscriberInterface
{
    public function onInvestInit(FilterInvestInitEvent $event)
    {
        $invest = $event->getInvest();
        $method = $event->getMethod();
        $request = $event->getRequest();
        App::getService('paylogger')->info('INVEST INIT: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);

        $method->setInvest($invest);
        $method->setRequest($request);

        // Is this really necessary?
        Invest::setDetail($invest->id, 'init', 'Invest input created');
    }

    public function onInvestInitRequest(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $invest = $method->getInvest();
        App::getService('paylogger')->info('INVEST INIT REQUEST: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);


        Invest::setDetail($invest->id, 'init-request', 'Payment gateway authorised');
    }

    public function onInvestInitRedirect(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        $reward = $invest->getReward();
        App::getService('paylogger')->info('INVEST INIT REDIRECT: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);

        // Goto payment platform...

        // Assign response if not previously assigned
        if(!$event->getHttpResponse()) {
            $event->setHttpResponse($response->getRedirectResponse());
        }

        Invest::setDetail($invest->id, 'init-redirect', 'Redirecting to payment gateway');
    }

    public function onInvestComplete (FilterInvestInitEvent $event)
    {
        $invest = $event->getInvest();
        $method = $event->getMethod();
        $request = $event->getRequest();
        App::getService('paylogger')->info('INVEST COMPLETE: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);

        $method->setInvest($invest);
        $method->setRequest($request);

        // Is this really necessary?
        Invest::setDetail($invest->id, 'complete', 'Redirected from payment gateway');
    }

    public function onInvestCompleteRequest(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $invest = $method->getInvest();
        $response = $event->getResponse();
        App::getService('paylogger')->info('INVEST COMPLETE REQUEST: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);

        // Set transaction ID
        $invest->setTransaction($response->getTransactionReference());

        Invest::setDetail($invest->id, 'complete-request', 'Redirecting to user data');
    }

    public function onInvestNotify(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $invest = $method->getInvest();
        $response = $event->getResponse();
        App::getService('paylogger')->info('INVEST NOTIFY: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);

        // Set transaction ID
        $invest->setTransaction($response->getTransactionReference());

        Invest::setDetail($invest->id, 'notify', 'Contact from payment gateway');
    }

    public function onInvestFailed(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        $reward = $invest->getReward();
        App::getService('paylogger')->info('INVEST FINISH FAILED: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' MESSAGE: ' . $response->getMessage());

        // not making changes on invest status...

        // Goto user start
        Message::error("Payment [{$invest->method}] failed!");

        // Assign response if not previously assigned
        if(!$event->getHttpResponse()) {
            $event->setHttpResponse(new RedirectResponse('/invest/' . $invest->project . '/payment?' . http_build_query(['amount' => $invest->amount, 'reward' => $reward ? $reward->id : '0'])));
        }


        Invest::setDetail($invest->id, 'confirm-fail', 'Invest process failed. Gateway error: ' . $response->getMessage());
    }

    public function onInvestSuccess(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        App::getService('paylogger')->info('INVEST FINISH SUCCEEDED: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project);

        // Invest status to charged
        $invest->status = Invest::STATUS_CHARGED;
        // Set charged date if empty
        if(empty($invest->charged)) {
            $invest->charged = date('Y-m-d');
        }
        $errors = [];
        $invest->save($errors);
        if($errors) {
            throw new \RuntimeException('Error saving Invest details! ' . implode("\n", $errors));
        }

        // update pay cached data
        $invest->keepUpdated();

        // Goto User data fill
        Message::info('Payment completed successfully');

        // Assign response if not previously assigned
        if(!$event->getHttpResponse()) {
            $event->setHttpResponse(new RedirectResponse('/invest/' . $invest->project . '/' . $invest->id));
        }
        Invest::setDetail($invest->id, 'confirmed', 'Invest process completed successfully');
    }

    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::INVEST_INIT => 'onInvestInit',
            AppEvents::INVEST_INIT_REQUEST => 'onInvestInitRequest',
            AppEvents::INVEST_INIT_REDIRECT => 'onInvestInitRedirect',
            AppEvents::INVEST_COMPLETE => 'onInvestComplete',
            AppEvents::INVEST_COMPLETE_REQUEST => 'onInvestCompleteRequest',
            AppEvents::INVEST_NOTIFY => 'onInvestNotify',
            AppEvents::INVEST_FAILED => 'onInvestFailed',
            AppEvents::INVEST_SUCCEEDED => 'onInvestSuccess',
        );
    }
}

