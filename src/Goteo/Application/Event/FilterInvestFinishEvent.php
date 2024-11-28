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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\EventDispatcher\Event;
use Goteo\Application\Session;
use Goteo\Library\Domain;
use Goteo\Model\Invest;

class FilterInvestFinishEvent extends Event
{
    protected $invest;
    protected $response;
    protected $request;

    public function __construct(Invest $invest, Request $request)
    {
        $this->invest = $invest;
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

    public function setHttpResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    public function getHttpResponse() {
        if($this->response) return $this->response;

        $return_to = Session::get('return_to');
        if ($return_to && Domain::isAllowedDomain($return_to)) {
            Session::del('return_to');
            return new RedirectResponse($return_to);
        }

        // Default is a redirection
        if($this->invest->project) {
            return new RedirectResponse('/invest/' . $this->invest->project . '/' . $this->invest->id . '/share');
        }
        elseif(!$this->invest->donate_amount) {
            return new RedirectResponse('/pool/'  . $this->invest->id . '/share');
        }
        else {
            return new RedirectResponse('/donate/'  . $this->invest->id . '/share');
        }
    }

}
