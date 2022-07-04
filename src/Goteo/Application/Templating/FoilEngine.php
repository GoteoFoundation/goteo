<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Application\Templating;

use Goteo\Application\View;
use Symfony\Component\Templating\EngineInterface;

class FoilEngine implements EngineInterface
{
    public function render($name, array $parameters = []): string
    {
        return View::render($name, $parameters);
    }

    public function exists($name): bool
    {
        return View::render($name) != "";
    }

    public function supports($name): bool
    {
        return $this->exists($name);
    }
}
