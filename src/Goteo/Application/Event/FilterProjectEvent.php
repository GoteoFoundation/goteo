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

use Goteo\Application\Session;
use Symfony\Component\HttpFoundation\Response;

class FilterProjectEvent extends \Goteo\Console\Event\FilterProjectEvent
{
    protected $user;
    protected $response = null;

    public function getUser() {
        return Session::getUser();
    }

    public function setResponse(Response $response) {
        $this->response = $response;
        return $this;
    }

    public function getResponse() {
        return $this->response;
    }
}
