<?php

namespace Goteo\Controller;

use Goteo\Application\App;
use Goteo\Application\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseSymfonyController extends AbstractController
{
    public function renderFoilTemplate(
        string $templateName,
        array $vars = [],
        int $status = 200,
        string $contentType = 'text/html'
    ): Response {
        $templateContent = View::render($templateName, $vars);
        $request = App::getRequest();

        if ($request->query->has('pronto') && (App::debug() || $request->isXmlHttpRequest())) {
            $contentType = 'application/json';
        }

        return new Response($templateContent, $status, [
            'Content-Type' => $contentType
        ]);
    }
}
