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
use Goteo\Model\Invest;

class FilterInvestModifyEvent extends Event
{
    protected $old_invest;
    protected $new_invest;

    public function __construct(Invest $invest)
    {
        $this->old_invest = $invest;
        $this->new_invest = clone $invest;
    }

    public function getOldInvest()
    {
        return $this->old_invest;
    }

    public function getNewInvest()
    {
        return $this->new_invest;
    }


}
