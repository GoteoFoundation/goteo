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
use Goteo\Model\Project;
use Goteo\Model\Matcher;

class FilterMatcherProjectEvent extends \Goteo\Console\Event\FilterProjectEvent
{
    protected $user;
    protected $matcher;
    protected $project;

    public function __construct(Matcher $matcher, Project $project) {
        $this->project = $project;
        $this->matcher = $matcher;
    }

    public function getMatcher() {
        return $this->matcher;
    }

    public function getUser() {
        return Session::getUser();
    }

}
