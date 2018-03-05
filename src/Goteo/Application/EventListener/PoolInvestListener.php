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
use Goteo\Application\Config;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Currency;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\User\Pool;
use Goteo\Model\Mail;
use Goteo\Model\Template;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PoolInvestListener extends AbstractListener {

    public function onInvestFailed(FilterInvestRequestEvent $event) {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        // Only for pool payments
        if($invest->getProject()) {
            return;
        }

        $this->warning('PoolInvest finish failed', [$invest, 'project' => '', 'reward' => '', $invest->getUser(), 'message' => $response->getMessage()]);

        // not making changes on invest status...

        // Feed this failed payment
        // Admin Feed
        $coin = Currency::getDefault()['html'];
        $log = new Feed();

        $user = $invest->getUser();
        $log->populate(
                Text::sys('feed-invest-by', strtoupper($method::getId())),
                '/admin/invests',
                    new FeedBody (null, null, 'feed-user-invest-error', [
                       '%MESSAGE%' => $response->getMessage(),
                       '%USER%' => Feed::item('user', $user->name, $user->id),
                       '%AMOUNT%' => Feed::item('money', $invest->amount . ' ' . $coin),
                       '%PROJECT%' => Feed::item('project', 'POOL'),
                       '%METHOD%' => strtoupper($method::getId())
                    ])
            )
            ->doAdmin('money');

        Invest::setDetail($invest->id, 'confirm-fail', 'PoolInvest process failed. Gateway error: ' . $response->getMessage());

        // Assign response if not previously assigned
        // Goto user start
        if (!$event->getHttpResponse()) {
            //Credit rechargue
            $event->setHttpResponse(new RedirectResponse('/pool/payment?' . http_build_query(['amount' => $invest->amount_original . $invest->currency])));
        }

    }

    public function onInvestSuccess(FilterInvestRequestEvent $event) {

        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();

        //If is a invest project change listener
        if($invest->getProject()) {
            return ;
        }

        $user = $invest->getUser();
        $pool = $user->getPool();

        $this->notice('PoolInvest finish succeeded', [$invest, 'project' => '', 'reward' => '', $invest->getUser()]);

        // Invest status to charged
        $invest->status = Invest::STATUS_TO_POOL;
        $invest->pool = true;
        // Set charged date if empty
        if (empty($invest->charged)) {
            $invest->charged = date('Y-m-d');
        }
        $errors = [];
        $invest->save($errors);
        if ($errors) {
            throw new \RuntimeException('Error saving PoolInvest details! ' . implode("\n", $errors));
        }

        //recalulate the pool
        $pool->calculate(true);

        // Amount in virtual wallet
        $amount_pool = $pool->getAmount();

        // Send mail with amount rechargued

        if( Mail::createFromTemplate($user->email, $user->name, Template::POOL_RECHARGUE_THANKS, [
              '%USERNAME%'   => $user->name,
              '%AMOUNT_RECHARGUED%'   => Currency::amountFormat($invest->amount),
              '%AMOUNT_POOL%'     => Currency::amountFormat($amount_pool),
              '%WALLET_URL%'     => Config::getUrl($lang) . '/dashboard/wallet',
              '%CERTIFICATE_URL%'     => Config::getUrl($lang) . '/dashboard/wallet/certificate'
               ], $user->lang)
        ->send($errors)) {
            // Sent succesfully
         }
          else {
              $vars['error'] .= implode("\n", $errors);
          }

        // Feed this succeeded payment
        // Admin Feed
        $coin = Currency::getDefault('html');
        $log = new Feed();
        $user = $invest->getUser();
        $log->populate(
                Text::sys('feed-invest-by', strtoupper($method::getId())),
                '/admin/invests',
                new FeedBody(null, null, 'feed-user-invest', [
                        '%USER%' => Feed::item('user', $user->name, $user->id),
                        '%AMOUNT%' => Feed::item('money', $invest->amount . ' ' . $coin),
                        '%PROJECT%' => Feed::item('project', 'POOL'),
                        '%METHOD%' => strtoupper($method::getId())
                    ])
            )
            ->doAdmin('money');

        // Public Feed
        $log_html = new FeedBody(null, null, 'feed-invest-pool', [
                '%AMOUNT%' => Feed::item('money', $invest->amount . ' ' . $coin)
                ]);
        if ($invest->anonymous) {
            $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
        } else {
            $log->populate($user->name, '/user/profile/' . $user->id, $log_html, $user->avatar->id);
        }
        $log->doPublic('community');

        Invest::setDetail($invest->id, 'confirmed', 'PoolInvest process completed successfully');

        // Assign response if not previously assigned

        if (!$event->getHttpResponse()) {
            $event->setHttpResponse(new RedirectResponse('/pool/' . $invest->id));
        }
    }


    public static function getSubscribedEvents() {
        return array(
            AppEvents::INVEST_FAILED => 'onInvestFailed',
            AppEvents::INVEST_SUCCEEDED => 'onInvestSuccess',
        );
    }
}
