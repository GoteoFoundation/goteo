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
use Goteo\Application\Session;
use Goteo\Application\App;
use Goteo\Library\Text;
use Goteo\Library\Currency;
use Goteo\Library\Feed;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Model\Invest;

class InvestListener implements EventSubscriberInterface
{
    public function onInvestInit(FilterInvestInitEvent $event)
    {
        $invest = $event->getInvest();
        $method = $event->getMethod();
        $request = $event->getRequest();
        App::getService('paylogger')->info('INVEST INIT: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());

        $method->setInvest($invest);
        $method->setRequest($request);

        // Is this really necessary?
        Invest::setDetail($invest->id, 'init', 'Invest input created');
    }

    public function onInvestInitRequest(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $invest = $method->getInvest();
        App::getService('paylogger')->info('INVEST INIT REQUEST: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());


        Invest::setDetail($invest->id, 'init-request', 'Payment gateway authorised');
    }

    public function onInvestInitRedirect(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        $reward = $invest->getFirstReward();
        App::getService('paylogger')->info('INVEST INIT REDIRECT: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());
        Invest::setDetail($invest->id, 'init-redirect', 'Redirecting to payment gateway');

        // Goto payment platform...

        // Assign response if not previously assigned
        if(!$event->getHttpResponse()) {
            $event->setHttpResponse($response->getRedirectResponse());
        }
    }

    public function onInvestComplete (FilterInvestInitEvent $event)
    {
        $invest = $event->getInvest();
        $method = $event->getMethod();
        $request = $event->getRequest();
        App::getService('paylogger')->info('INVEST COMPLETE: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());

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
        App::getService('paylogger')->info('INVEST COMPLETE REQUEST: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());

        // Set transaction ID
        $invest->setTransaction($response->getTransactionReference());

        Invest::setDetail($invest->id, 'complete-request', 'Redirecting to user data');
    }

    public function onInvestNotify(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $invest = $method->getInvest();
        $response = $event->getResponse();
        App::getService('paylogger')->info('INVEST NOTIFY: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());

        // Set transaction ID
        $invest->setTransaction($response->getTransactionReference());

        Invest::setDetail($invest->id, 'notify', 'Contact from payment gateway');
    }

    public function onInvestFailed(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        $reward = $invest->getFirstReward();
        App::getService('paylogger')->info('INVEST FINISH FAILED: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId() . ' MESSAGE: ' . $response->getMessage());

        // not making changes on invest status...

        // Feed this failed payment
        // Admin Feed
        $coin = Currency::getDefault()['html'];
        $log = new Feed();
        $project = $invest->getProject();
        $user = $invest->getUser();
        $log->setTarget($project->id)
            ->populate(
                    Text::sys('feed-invest-by', strtoupper($method::getId())) ,
                    '/admin/invests',
                    Text::get('feed-user-invest-error',
                        ['%MESSAGE%' => $response->getMessage(),
                         '%USER%' => Feed::item('user', $user->name, $user->id),
                         '%AMOUNT%' => Feed::item('money', $invest->amount.' ' . $coin),
                         '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                         '%METHOD%' => strtoupper($method::getId())])
                    )
            ->doAdmin('money');

        // Message
        Message::error("Payment [{$invest->method}] failed!");

        Invest::setDetail($invest->id, 'confirm-fail', 'Invest process failed. Gateway error: ' . $response->getMessage());

        // Assign response if not previously assigned
        // Goto user start
        if(!$event->getHttpResponse()) {
            $event->setHttpResponse(new RedirectResponse('/invest/' . $invest->project . '/payment?' . http_build_query(['amount' => $invest->amount, 'reward' => $reward ? $reward->id : '0'])));
        }


    }

    public function onInvestSuccess(FilterInvestRequestEvent $event)
    {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        App::getService('paylogger')->info('INVEST FINISH SUCCEEDED: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());

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

        // Feed this succeeded payment
        // Admin Feed
        $coin = Currency::getDefault()['html'];
        $log = new Feed();
        $project = $invest->getProject();
        $user = $invest->getUser();
        $log->setTarget($project->id)
            ->populate(
                    Text::sys('feed-invest-by', strtoupper($method::getId())),
                    '/admin/invests',
                    Text::get('feed-user-invest',
                        ['%USER%' => Feed::item('user', $user->name, $user->id),
                         '%AMOUNT%' => Feed::item('money', $invest->amount.' ' . $coin),
                         '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                         '%METHOD%' => strtoupper($method::getId())])
                    )
            ->doAdmin('money');

        // Public Feed
        $log_html = Text::get('feed-invest',
                            ['%AMOUNT%' => Feed::item('money', $invest->amount.' ' . $coin),
                             '%PROJECT%' => Feed::item('project', $project->name, $project->id)]);
        if ($invest->anonymous) {
            $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
        } else {
            $log->populate($user->name, '/user/profile/'.$user->id, $log_html, $user->avatar->id);
        }
        $log->doPublic('community');

        // update cached data
        $invest->keepUpdated();

        // Goto User data fill
        Message::info('Payment completed successfully');

        Invest::setDetail($invest->id, 'confirmed', 'Invest process completed successfully');

        // Assign response if not previously assigned
        if(!$event->getHttpResponse()) {
            $event->setHttpResponse(new RedirectResponse('/invest/' . $invest->project . '/' . $invest->id));
        }
    }

    /**
     * Cancels and invest for other reasons than failed projects
     * @param  FilterInvestRefundEvent $event
     */
    public function onInvestRefundCancel(FilterInvestRefundEvent $event)
    {
        $method = $event->getMethod();
        $invest = $event->getInvest();
        App::getService('paylogger')->info('INVEST REFUND CANCEL: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());
        if($invest->cancel(false)) {
            Invest::setDetail($invest->id, $method::getId() .'-cancel', 'Invest process manually cancelled successfully');
            // update cached data
            $invest->keepUpdated();
        }
        else {
            Invest::setDetail($invest->id, $method::getId() .'-cancel-fail', 'Error while cancelling invest');
        }

    }

    /**
     * Cancels and invest for failed projects
     * @param  FilterInvestRefundEvent $event
     */
    public function onInvestRefundReturn(FilterInvestRefundEvent $event)
    {
        $method = $event->getMethod();
        $invest = $event->getInvest();
        App::getService('paylogger')->info('INVEST REFUND CANCEL: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId());
        if($invest->cancel(true)) {
            Invest::setDetail($invest->id, $method::getId() .'-cancel', 'Invest refunded successfully');
            // update cached data
            $invest->keepUpdated();
        }
        else {
            Invest::setDetail($invest->id, $method::getId() .'-cancel-fail', 'Error while cancelling invest');
        }

    }
    /**
     * Handles failed refund process
     * @param  FilterInvestRefundEvent $event
     */
    public function onInvestRefundFailed(FilterInvestRefundEvent $event)
    {
        $method = $event->getMethod();
        $invest = $event->getInvest();
        $response = $event->getResponse();
        App::getService('paylogger')->info('INVEST REFUND FAILED: ' . $invest->id . ' METHOD: ' . $method::getId() . ' PROJECT: ' . $invest->project . ' USER: ' . Session::getUserId(). ' MESSAGE: ' . $response->getMessage());
        Invest::setDetail($invest->id, $method::getId() .'-return-fail', 'Error while refunding invest: ' . $response->getMessage());

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
            AppEvents::INVEST_CANCELLED => 'onInvestRefundCancel',
            AppEvents::INVEST_CANCEL_FAILED => 'onInvestRefundFailed', // same action as return at this moment
            AppEvents::INVEST_RETURNED => 'onInvestRefundReturn',
            AppEvents::INVEST_RETURN_FAILED => 'onInvestRefundFailed',
        );
    }
}

