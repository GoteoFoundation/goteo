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
use Goteo\Application\Currency;
use Goteo\Core\Controller;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Payment\Method\StripeSubscriptionPaymentMethod;
use Goteo\Repository\InvestRepository;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StripeSubscriptionController extends Controller
{
    private StripeClient $stripe;

    private InvestRepository $investRepository;

    public function __construct()
    {
        $this->stripe = new StripeClient(Config::get('payments.stripe.secretKey'));
        $this->investRepository = new InvestRepository();
    }

    public function subscriptionsWebhook(Request $request)
    {
        $event = Webhook::constructEvent(
            $request->getContent(),
            $request->headers->get('STRIPE_SIGNATURE'),
            'whsec_f8fdfc93e7f59cada1595902eb679dcdd442181f4464317c84be2d09f587be83' //Config::get('payments.stripe.webhookSecret')
        );

        switch ($event->type) {
            case Event::TYPE_INVOICE_PAYMENT_SUCCEEDED:
                $response = $this->createInvest($event->data->object->id);
            case Event::CHARGE_REFUNDED:
                $response = $this->chargeRefunded($event);
            case Event::TYPE_INVOICE_PAYMENT_FAILED:
                break;
            case Event::TYPE_CUSTOMER_SUBSCRIPTION_DELETED:
                break;
            default:
                break;
        }

        return new JsonResponse($response);
    }

    private function chargeRefunded(Event $event): array
    {
        $object = $event->data->object;
        if (!$object || !$object->invoice) {
            return [];
        }

        $invoice = $this->stripe->invoices->retrieve($object->invoice);
        $subscription = $this->stripe->subscriptions->retrieve($invoice->subscription);

        $invests = $this->investRepository->getListByPayment($subscription->id);
        foreach ($invests as $key => $invest) {
            $invest->setStatus(Invest::STATUS_CANCELLED);
            $invest->save();
        }

        return $invests;
    }

    private function createInvest(string $invoiceId): Invest
    {
        $invoice = $this->stripe->invoices->retrieve($invoiceId);
        $subscription = $this->stripe->subscriptions->retrieve($invoice->subscription);

        /** @var User */
        $user = User::getByEmail($invoice->customer_email);

        $invest = new Invest([
            'amount' => $invoice->amount_paid / 100,
            'donate_amount' => 0,
            'currency' => $invoice->currency,
            'currency_rate' => Currency::rate(strtoupper($invoice->currency)),
            'user' => $user->id,
            'project' => explode('_', $subscription->items->data[0]->price->product)[0],
            'method' => StripeSubscriptionPaymentMethod::PAYMENT_METHOD_ID,
            'status' => Invest::STATUS_CHARGED,
            'invested' => date('Y-m-d'),
            'payment' => $subscription->id
        ]);

        $errors = array();
        if (!$invest->save($errors)) {
            throw new \RuntimeException(Text::get('invest-create-error') . '<br />' . implode('<br />', $errors));
        }

        return $invest;
    }
}
