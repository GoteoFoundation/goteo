<?php

namespace Goteo\Application\Templating;

use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;

class TwigEngine implements EngineInterface
{
    private LoaderInterface $loader;
    private Environment $twig;

    public function __construct()
    {
        $this->loader = new ArrayLoader([]);
        $this->twig = new Environment($this->loader);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render($name, array $parameters = []): string
    {
        if (!$this->exists($name)) {
            $this->loader->setTemplate($name, "NEW TWIG TEMPLATE");
        }
        dump("Load Twig template: " . $this->twig->getTemplateClass($name));

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
