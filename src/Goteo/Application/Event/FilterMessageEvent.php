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
use Goteo\Model\Message;

class FilterMessageEvent extends Event
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getmessage()
    {
        return $this->message;
    }

}
