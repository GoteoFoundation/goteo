<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Payment\Method;

use Goteo\Payment\PaymentException;
use Goteo\Model\User\Pool;
use Goteo\Model\Invest;
use Goteo\Library\Text;
use Goteo\Util\Omnipay\Message\EmptyFailedResponse;
use Goteo\Util\Omnipay\Message\EmptySuccessfulResponse;
use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestEvent;

/**
 * Creates a Payment Method that uses internal virtuall wallet
 * This method does uses Omnipay Manual method
 */
class PoolPaymentMethod extends AbstractPaymentMethod {
    protected $pool;

    // Uses omnipay manual method, always successful
    public function getGatewayName() {
        return 'Manual';
    }

    public function getPool() {
        if(!$this->pool) {
            $this->pool = $this->user->getPool();
        }
        return $this->pool;
    }

    /**
     * Should return if method must be registered but in a inactive state
     * so it can be shown on the payment page as a temporary non-available method
     * @return boolean status
     */
    public function isActive($amount = 0) {

        // Checking pool status
        if($this->getPool() && $this->getPool()->getAmount() >= $amount) {
            return true;
        }
        return false;
    }

    public function getDesc() {
        $amount = $this->getPool()->getAmount();
        return Text::get('invest-amount-in-pool', amount_format($amount));
    }


    // Completes purchase if enough amount available
    public function purchase() {
        $invest = $this->getInvest();

        if($this->getPool() && $this->getPool()->getAmount() >= $invest->amount) {
            // remove current quantity from user pool
            $errors = [];
            $this->user->getPool()->withdraw($invest->amount, $errors);
            if (empty($errors)) {
                // Sets pool next failed payment go to pool as well
                // Pool payments cannot be returned in cash
                $invest->setPoolOnFail(true);

                // return response
                return $this->getGateway()->authorize([
                            'amount' => (float) $this->getInvest()->amount,
                            'description' => $this->getInvestDescription(),
                            'returnUrl' => $this->getCompleteUrl(),
                            'cancelUrl' => $this->getCompleteUrl(),
                ])->send();
            } else {
                throw new PaymentException("Error Processing Pool: " . implode('<br />, $errors)'));
            }
        }
        throw new PaymentException(Text::get('invest-pool-error').'<br>'.Text::get('invest-amount-in-pool', amount_format($this->getPool()->getAmount())));

    }

    public function refundable() {
        $invest = $this->getInvest();
        if($invest->status == Invest::STATUS_CHARGED) return true;
        return false;
    }

    public function refund() {
        $invest = $this->getInvest();

        // Any plugin can throw a PaymentException here in order to abort the refund process
        App::dispatch(AppEvents::INVEST_REFUND, new FilterInvestEvent($invest, $this));

        // Mark this invest as return-to-pool (this should be redundant)
        $invest->setPoolOnFail(true);
        $errors = [];
        if($this->refundable()) {
            return new EmptySuccessfulResponse();
        }
        else {
            return new EmptyFailedResponse(implode(', ', $errors));
        }
    }

    /**
     * Internal payments does not increased raised amounts
     * (pool)
     * @return boolean
     */
    static public function isInternal() {
        return true;
    }
}
