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

class EmptySuccessfulResponse implements ResponseInterface {
    private $message = '';

    public function __construct($message = '') {
        $this->setMessage($message);
    }

    public function getData() {}
    public function getRequest() {}

    public function isSuccessful(){return true;}

    public function isRedirect() {return false;}

    public function isCancelled(){}

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
    public function getMessage(){
        return $this->message;
    }

    public function getCode(){}

    public function getTransactionReference() {}
}
