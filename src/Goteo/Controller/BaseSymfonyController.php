<?php

namespace Goteo\Controller;

use Goteo\Application\Templating\FoilEngine;
use Goteo\Application\Templating\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class BaseSymfonyController extends AbstractController
{
    private EngineInterface $foilRenderer;
    private EngineInterface $twigRenderer;

    public function __construct()
    {
        $this->foilRenderer = new FoilEngine();
        $this->twigRenderer = new TwigEngine();
    }

    public function renderFoilTemplate(
        string $templateName,
        array $parameters = [],
        int $status = 200,
        string $contentType = 'text/html'
    ): Response {
        $response = $this->foilRenderer->render($templateName, $parameters);
        $response->setStatusCode($status);
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }

    public function renderTwigTemplate(
        string $templateName,
        array $parameters = [],
        int $status = 200,
        string $contentType = 'text/html'
    ): Response {
        $response = $this->twigRenderer->render($templateName, $parameters);
        $response->setStatusCode($status);
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }
}
