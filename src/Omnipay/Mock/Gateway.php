<?php

namespace Omnipay\Mock;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'Mock';
    }
}
