<?php

namespace Goteo\Payment\Method;

use Goteo\Library\Text;
use Goteo\Payment\Method\AbstractPaymentMethod;

class RedsysPaymentMethod extends AbstractPaymentMethod
{
    public const PAYMENT_METHOD_ID = 'redsys';

    public static function getId(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getIdNonStatic(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getName(): string
    {
        return 'Redsys';
    }

    public function getDesc(): string
    {
        return 'Redsys gateway via Colonya';
    }

    public function getIcon(): string
    {
        return SRC_URL . '/assets/img/pay/card.png';
    }
}
