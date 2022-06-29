<?php

namespace Goteo\Controller;

use Goteo\Application\App;
use Goteo\Application\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseSymfonyController extends AbstractController
{
    public function renderFoilTemplate(
        $view,
        array $vars = [],
        int $status = 200,
        string $contentType = 'text/html'
    ): Response {
        $view = View::render($view, $vars);
        $request = App::getRequest();

        if ($request->query->has('pronto') && (App::debug() || $request->isXmlHttpRequest())) {
            $contentType = 'application/json';
        }

        return new Response($view, $status, [
            'Content-Type' => $contentType
        ]);
    }
}
