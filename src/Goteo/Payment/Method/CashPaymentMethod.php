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

use Goteo\Application\Config;

/**
 * Creates a Payment Method that uses Paypal provider
 */
class CashPaymentMethod extends AbstractPaymentMethod {

    // Uses omnipay manual method, always successful
    public function getGatewayName() {
        return 'Manual';
    }

    /**
     * This payment method is only for administrative purposes
     */
    public function isPublic($amount = 0) {
        return false;
    }

}
