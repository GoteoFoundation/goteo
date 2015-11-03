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
            $this->pool = Pool::get($this->user->id);
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
        if($this->getPool() && $this->getPool()->amount >= $amount) {
            return true;
        }
        return false;
    }

    public function getDesc() {
        $amount = $this->getPool()->amount;
        return Text::get('invest-amount-in-pool', amount_format($amount));
    }


    // Completes purchase if enough amount available
    public function purchase() {
        $invest = $this->getInvest();

        if($this->getPool() && $this->getPool()->amount >= $invest->amount) {
            // remove current quantity from user pool
            $errors = [];
            Pool::withdraw($this->user->id, $invest->amount, $errors);
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
        throw new PaymentException(Text::get('invest-pool-error').'<br>'.Text::get('invest-amount-in-pool', amount_format($this->getPool()->amount)));

    }

    public function refundable() {
        $invest = $this->getInvest();
        if($invest->status == Invest::STATUS_CHARGED) return true;
        return false;
    }

    public function refund() {
        $invest = $this->getInvest();
        // Mark this invest as return-to-pool (this should be redundant)
        $invest->setPoolOnFail(true);
        $errors = [];
        if($this->refundable() && Pool::refundInvest($invest, $errors)) {
            return new EmptySuccessfulResponse();
        }
        else {
            return new EmptyFailedResponse(implode(', ', $errors));
        }
    }
}
