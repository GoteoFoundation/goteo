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

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class TwigEngine implements EngineInterface
{
    public const TWIG_TEMPLATES_FOLDER = __DIR__ . "/../../../../templates";

    private LoaderInterface $loader;
    private Environment $twig;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->loader = new FilesystemLoader(self::TWIG_TEMPLATES_FOLDER);
        $this->twig = new Environment($this->loader);
        $this->twig->addExtension(new RoutingExtension($urlGenerator));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render($name, array $parameters = []): string
    {
        return $this->twig->render($name, $parameters);
    }

    public function exists($name): bool
    {
        return $this->loader->exists($name);
    }

    public function supports($name): bool
    {
        return $this->exists($name);
    }
}
