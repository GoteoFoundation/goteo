<?php

/**
 * Function utils for the templates system in goteo
 */
namespace Goteo\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Goteo\Application\View;

class GoteoCore implements ExtensionInterface
{
    protected $engine;
    public $template; // must be public

    public function register(Engine $engine)
    {
        $this->engine = $engine;
    }

}
