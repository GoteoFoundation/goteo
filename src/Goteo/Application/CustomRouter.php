<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

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
