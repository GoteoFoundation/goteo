<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Omnipay\Message;

use Omnipay\Common\Message\ResponseInterface;

class EmptyFailedResponse implements ResponseInterface {
    public function getData() {}
    public function getRequest() {}

    public function isSuccessful(){return false;}

    public function isRedirect() {return true;}

    public function isCancelled(){}

    public function getMessage(){}

    public function getCode(){}

    public function getTransactionReference() {}
}
