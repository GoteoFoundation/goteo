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
use Goteo\Application\Currency;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Entity\Invest\InvestOrigin;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Mail;
use Goteo\Model\Template;
use Goteo\Model\User;
use Goteo\Repository\InvestOriginRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DonateInvestListener extends AbstractListener {

    public function onInvestFailed(FilterInvestRequestEvent $event) {
        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();
        // Only for donate organization payments
        if($invest->getProject()||!$invest->donate_amount) {
            return;
        }

        // Set amount and donate_amount right
        //$invest->setDonateAmount();

        $this->warning('DonateInvest finish failed', [$invest, 'project' => '', 'reward' => '', $invest->getUser(), 'message' => $response->getMessage()]);

        // not making changes on invest status...

        // Feed this failed payment
        // Admin Feed
        $coin = Currency::getDefault()['html'];
        $log = new Feed();

        $user = $invest->getUser();
        $log->populate(
                Text::sys('feed-invest-by', strtoupper($method->getIdNonStatic())),
                '/admin/invests',
                    new FeedBody (null, null, 'feed-user-invest-error', [
                       '%MESSAGE%' => $response->getMessage(),
                       '%USER%' => Feed::item('user', $user->name, $user->id),
                       '%AMOUNT%' => Feed::item('money', $invest->amount . ' ' . $coin),
                       '%PROJECT%' => Feed::item('project', 'DONATE'),
                       '%METHOD%' => strtoupper($method->getIdNonStatic())
                    ])
            )
            ->doAdmin('money');

        Invest::setDetail($invest->id, 'confirm-fail', 'DonateInvest process failed. Gateway error: ' . $response->getMessage());

        $investOriginRepository = new InvestOriginRepository();
        try {
            $investOrigin = $investOriginRepository->getByInvestId($invest->id);
            $event->setHttpResponse(
                new RedirectResponse('/donate/payment?' . http_build_query(['amount' => $invest->amount_original . $invest->currency, 'source' => $investOrigin->getSource(), 'detail' => $investOrigin->getDetail(), 'allocated' => $investOrigin->getAllocated()]))
            );
        } catch (ModelNotFoundException $e) {
            // If there is no Invest Origin we continue by default
        }

        // Assign response if not previously assigned
        // Goto user start
        if (!$event->getHttpResponse()) {
            //Credit recharge
            $event->setHttpResponse(new RedirectResponse('/donate/payment?' . http_build_query(['amount' => $invest->amount_original . $invest->currency])));
        }
    }

    public function onInvestSuccess(FilterInvestRequestEvent $event) {

        $method = $event->getMethod();
        $response = $event->getResponse();
        $invest = $method->getInvest();

        //If is a invest project change listener
        if($invest->getProject()||!$invest->donate_amount) {
            return ;
        }

        $user = $invest->getUser();

        $this->notice('DonateInvest finish succeeded', [$invest, 'project' => '', 'reward' => '', $invest->getUser()]);

        // Invest status to charged
        $invest->status = Invest::STATUS_DONATED;

        // Set charged date if empty
        if (empty($invest->charged)) {
            $invest->charged = date('Y-m-d');
        }
        $errors = [];
        $invest->save($errors);
        if ($errors) {
            throw new \RuntimeException('Error saving DonateInvest details! ' . implode("\n", $errors));
        }

        // Send mail with amount recharged

        $original_lang = $lang = User::getPreferences($user)->comlang;

        if( Mail::createFromTemplate($user->email, $user->name, Template::DONATE_ORGANIZATION_THANKS, [
              '%USERNAME%'   => $user->name,
              '%DONATE_AMOUNT%'   => Currency::amountFormat($invest->donate_amount),
              '%CERTIFICATE_URL%'     => Config::getMainUrl() . '/dashboard/wallet/certificate'
               ], $lang)
        ->send($errors)) {
            // Sent successfully
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
                Text::sys('feed-invest-by', strtoupper($method->getIdNonStatic())),
                '/admin/invests',
                new FeedBody(null, null, 'feed-user-invest', [
                        '%USER%' => Feed::item('user', $user->name, $user->id),
                        '%AMOUNT%' => Feed::item('money', $invest->donate_amount . ' ' . $coin),
                        '%PROJECT%' => Feed::item('project', 'DONATE FOUNDATION'),
                        '%METHOD%' => strtoupper($method->getIdNonStatic())
                    ])
            )
            ->doAdmin('money');

        // Public Feed
        $log_html = new FeedBody(null, null, 'feed-invest-donate', [
                '%AMOUNT%' => Feed::item('money', $invest->donate_amount . ' ' . $coin)
                ]);
        if ($invest->anonymous) {
            $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
        } else {
            $log->populate($user->name, '/user/profile/' . $user->id, $log_html, $user->avatar->id);
        }
        $log->doPublic('community');

        Invest::setDetail($invest->id, 'confirmed', 'DoanteInvest process completed successfully');

        // Assign response if not previously assigned

        if (!$event->getHttpResponse()) {
            $event->setHttpResponse(new RedirectResponse('/donate/' . $invest->id));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            AppEvents::INVEST_FAILED => 'onInvestFailed',
            AppEvents::INVEST_SUCCEEDED => 'onInvestSuccess',
        );
    }
}
