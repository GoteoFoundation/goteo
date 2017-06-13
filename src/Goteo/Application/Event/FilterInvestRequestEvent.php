<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Omnipay\Common\Message\ResponseInterface;
use Goteo\Payment\Method\PaymentMethodInterface;


class FilterInvestRequestEvent extends Event
{
    protected $method;
    protected $response;
    protected $http_response;
    protected $skipMail = false;

    public function __construct(PaymentMethodInterface $method, ResponseInterface $response)
    {
        $this->method = $method;
        $this->response = $response;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getHttpResponse()
    {
        if(empty($this->http_response) && $this->method && $this->response) {
            $this->http_response = $this->method->getDefaultHttpResponse($this->response);
        }
        return $this->http_response;
    }

    public function setHttpResponse(Response $response)
    {
        $this->http_response = $response;
        return $this;
    }

    public function skipMail($skip = null) {
        if(!is_null($skip)) {
            $this->skip = (bool) $skip;
        }
        return $this->skip;
    }


}
