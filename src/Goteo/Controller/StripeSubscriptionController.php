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
use Stripe\Invoice;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            Config::get('payments.stripe.webhookSecret')
        );

        switch ($event->type) {
            case Event::TYPE_INVOICE_PAYMENT_SUCCEEDED:
                return $this->processInvoice($event->data->object->id);
            case Event::CHARGE_REFUNDED:
                return $this->processRefund($event);
            default:
                return new JsonResponse(
                    ['data' => sprintf("The event %s is not supported.", $event->type)],
                    Response::HTTP_BAD_REQUEST
                );
                break;
        }
    }

    private function processRefund(Event $event): JsonResponse
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

        return new JsonResponse(['data' => $invests], Response::HTTP_OK);
    }

    private function processInvoice(string $invoiceId): JsonResponse
    {
        $invoice = $this->stripe->invoices->retrieve($invoiceId);
        if ($invoice->billing_reason === Invoice::BILLING_REASON_SUBSCRIPTION_CREATE) {
            return new JsonResponse([
                'data' => Invest::get($invoice->lines->data[0]->price->metadata->invest),
                Response::HTTP_OK
            ]);
        }

        /** @var User */
        $user = User::getByEmail($invoice->customer_email);

        $subscription = $this->stripe->subscriptions->retrieve($invoice->subscription);

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

        return new JsonResponse(['data' => $invest], Response::HTTP_CREATED);
    }
}
