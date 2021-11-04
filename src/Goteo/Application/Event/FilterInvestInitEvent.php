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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;
use Goteo\Model\Invest;
use Goteo\Payment\Method\PaymentMethodInterface;

class FilterInvestInitEvent extends Event
{
    protected $invest;
    protected $request;
    protected $method;

    public function __construct(Invest $invest, PaymentMethodInterface $method, Request $request)
    {
        $this->invest = $invest;
        $this->method = $method;
        $this->request = $request;
    }

    public function getInvest()
    {
        return $this->invest;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getMethod()
    {
        return $this->method;
    }


}
