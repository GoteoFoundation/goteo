<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\Event;
use Goteo\Application\App;
use Goteo\Util\Monolog\Processor\WebProcessor;

abstract class Controller {

    /**
     * Handy method to send a response from a view
     */
    public function viewResponse($view, $vars = [], $status = 200) {
        return new Response(\Goteo\Application\View::render($view, $vars), $status);
    }

    /**
     * Handy method to send a response any string
     */
    public function rawResponse($string, $contentType = 'text/plain' , $status = 200) {
        return new Response($string, $status, ['Content-Type' => $contentType]);
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
    public function redirect($path, $status = 302) {
        return new RedirectResponse($path, $status);
    }

    /**
     * Handy method to obtain the view engine object
     */
    public function getViewEngine() {
        return \Goteo\Application\View::getEngine();
    }

    /**
     * Handy method to add context vars to all view
     */
    public function contextVars(array $vars = [], $view_path_context = null) {
        if($view_path_context) {
            \Goteo\Application\View::getEngine()->useContext($view_path_context, $vars);
        } else {
            \Goteo\Application\View::getEngine()->useData($vars);
        }
    }

    /**
     * Handy method to get the service container object
     */
    public function getContainer() {
        return App::getServiceContainer();
    }

    /**
     * Handy method to get the getService function
     */
    public function getService($service) {
        return App::getService($service);
    }

    /**
     * Handy method to get the dispatch function
     */
    public function dispatch($eventName, Event $event = null) {
        return App::dispatch($eventName, $event);
    }

    public function log($message, array $context = [], $func = 'info') {
        $logger = App::getService('logger');
        if (null !== $logger && method_exists($logger, $func)) {
            return $logger->$func($message, WebProcessor::processObject($context));
        }
    }

    public function info($message, array $context = []) {
        return $this->log($message, $context, 'info');
    }

    public function error($message, array $context = []) {
        return $this->log($message, $context, 'error');
    }

    public function warning($message, array $context = []) {
        return $this->log($message, $context, 'warning');
    }

    public function notice($message, array $context = []) {
        return $this->log($message, $context, 'notice');
    }

    public function critical($message, array $context = []) {
        return $this->log($message, $context, 'critical');
    }

    public function debug($message, array $context = []) {
        return $this->log($message, $context, 'debug');
    }
}
