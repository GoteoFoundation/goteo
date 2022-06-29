<?php

namespace Goteo\Application\Templating;

use Goteo\Application\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class FoilEngine implements EngineInterface
{
    public function render($name, array $parameters = []): Response
    {
        $templateContent = View::render($name, $parameters);

        return new Response($templateContent);
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
