<?php

namespace Goteo\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Goteo\Library\Page;

class Pages implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('page', [$this, 'get']);
    }

    public function get($var)
    {
        return Page::get($var);
    }

}
