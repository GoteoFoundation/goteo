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
    protected $delayed = false; // used to specify mail sending as a mailing

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function setMessage(Message $message)
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setDelayed($bool)
    {
        $this->delayed = (bool) $bool;
        return $this;
    }

    public function getDelayed()
    {
        return $this->delayed;
    }

}
