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

use Goteo\Application\App;
use Goteo\Application\Templating\FoilEngine;
use Goteo\Application\View;
use Goteo\Core\DB;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class BaseSymfonyController extends AbstractController
{
    private EngineInterface $foilRenderer;
    private EngineInterface $twigRenderer;

    public function __construct()
    {
        $this->container = App::getServiceContainer();
        $this->foilRenderer = new FoilEngine();
        $this->twigRenderer = $this->container->get('twig');
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

    /**
     * Adds context vars to all views
     */
    protected function contextVars(array $vars = [], $view_path_context = null)
    {
        if ($view_path_context) {
            View::getEngine()->useContext($view_path_context, $vars);
        } else {
            View::getEngine()->useData($vars);
        }
    }

    public function dbCache($cache = null): bool
    {
        return DB::cache($cache);
    }

    public function dbReplica($replica = null): bool
    {
        return DB::replica($replica);
    }
}
