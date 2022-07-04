<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

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
        return $this->getRenderedResponse(
            $this->foilRenderer,
            $templateName,
            $parameters,
            $status,
            $contentType,
        );
    }

    public function renderTwigTemplate(
        string $templateName,
        array $parameters = [],
        int $status = 200,
        string $contentType = 'text/html'
    ): Response {
        return $this->getRenderedResponse(
            $this->twigRenderer,
            $templateName,
            $parameters,
            $status,
            $contentType,
        );
    }

    private function getRenderedResponse(
        EngineInterface $engine,
        string $templateName,
        array $parameters,
        int $status,
        string $contentType
    ): Response {
        $templateContent = $engine->render($templateName, $parameters);
        $response = new Response($templateContent);
        $response->setStatusCode($status);
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }
}
