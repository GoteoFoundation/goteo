<?php

// src/goteo/Controller/RedirectingController.php
namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectingController extends \Goteo\Core\Controller
{
    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        // return new RedirectResponse($url, 301); //permanent ?
        return new RedirectResponse($url, 302);
    }
}
