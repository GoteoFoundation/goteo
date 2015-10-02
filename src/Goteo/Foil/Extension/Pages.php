<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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
