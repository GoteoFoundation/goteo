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

class FilterViewEvent extends Event
{
    protected $view;
    protected $vars = [];

    public function __construct($view, array $vars = [])
    {
        $this->view = $view;
        $this->vars = $vars;
    }

    public function getView()
    {
        return $this->view;
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    public function setVars(array $vars)
    {
        $this->vars = $vars;
        return $this;
    }

}
