<?php

namespace Goteo\Core;

use Goteo\Application\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Controller {

    /**
     * Handy method to send a response from a view
     */
    public function viewResponse($view, $vars = [], $status = 200) {
        return new Response(View::render($view, $vars), $status);
    }

    /**
     * **Experimental** method to send a response in json, vars only
     */
    public function jsonResponse($vars = []) {
        return new JsonResponse($vars);
    }

    /**
     * Handy method to return a redirect response
     */
    public function redirect($path) {
        return new RedirectResponse($path);
    }

    /**
     * Handy method to obtain the view engine object
     */
    public function getViewEngine() {
        return View::getEngine();
    }
    /**
     * Handy method to add context vars to all view
     */
    public function contextVars(array $vars = [], $view_path_context = '/') {
        View::getEngine()->useContext($view_path_context, $vars);
    }
}
