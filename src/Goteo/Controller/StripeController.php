<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application\Config;
use Goteo\Core\Controller;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Stripe\Event;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends Controller
{
    public function subscriptionsWebhook(Request $request)
    {
        $event = Webhook::constructEvent(
            $request->getContent(),
            $request->headers->get('HTTP_STRIPE_SIGNATURE'),
            Config::get('payments.stripe.webhookSecret')
        );

        switch ($event->type) {
            case Event::TYPE_INVOICE_PAID:
                $this->createInvest(json_decode($request->getContent()['data']['object']));
                break;
            case Event::TYPE_INVOICE_PAYMENT_FAILED:
                break;
            case Event::TYPE_CUSTOMER_SUBSCRIPTION_DELETED:
                break;
            default:
                break;
        }
    }

    private function createInvest(array $invoice)
    {
        /** @var User */
        $user = User::getByEmail($invoice['customer_email']);

        $invest = new Invest([
            'amount' => $invoice['amount_paid'] / 100,
            'currency' => $invoice['currency'],
            'user' => $user->id,
            'project' => explode('-', $invoice['subscription']['items']['data'][0]['product'])[0],
            'method' => 'stripe',
            'status' => Invest::STATUS_PAID,
            'invested' => date('Y-m-d')
        ]);

        $errors = array();
        if (!$invest->save($errors)) {
            throw new \RuntimeException(Text::get('invest-create-error') . '<br />' . implode('<br />', $errors));
        }
    }
}
