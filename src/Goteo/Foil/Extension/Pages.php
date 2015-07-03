<?php

namespace Goteo\Foil\Extension;

use Foil\Contracts\ExtensionInterface;

use Goteo\Library\Page;

class Pages implements ExtensionInterface
{

    private $args;

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'page' => [$this, 'get'],
        ];
    }
    public function get($var)
    {
        return Page::get($var);
    }

}
