<?php

namespace Goteo\Application;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class CustomRouter implements RouterInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private RouteCollection $routeCollection;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RouteCollection $routeCollection
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->routeCollection = $routeCollection;
    }

    public function setContext(RequestContext $context)
    {
        $this->urlGenerator->setContext($context);
    }

    public function getContext(): RequestContext
    {
        $this->urlGenerator->getContext();
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->routeCollection;
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }

    public function match($pathinfo): array
    {
        // TODO: Implement match() method.
        return [];
    }
}
