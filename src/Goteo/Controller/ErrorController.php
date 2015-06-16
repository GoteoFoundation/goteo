<?php

namespace Goteo\Controller;

use Goteo\Application\App;
use Goteo\Application\View;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\FlattenException;

class ErrorController extends \Goteo\Core\Controller {

    public function exceptionAction(FlattenException $exception, Request $request)
    {
        $msg = 'Something went wrong! ('.$exception->getMessage().')';
        // Compatibility
        if($exception->getClass() === 'Goteo\Core\Redirection') {
            return new RedirectResponse($exception->getMessage());
        }
        $code = $exception->getStatusCode();
        $template = 'not_found';
        if($code === 403) {
            $template = 'access_denied';
        }
        return new Response(View::render('errors/' . $template, ['msg' => $msg, 'code' => $code], $code));
    }

    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        // return new RedirectResponse($url, 301); //permanent ?
        return new RedirectResponse($url, 302);
    }

    public function legacyControllerAction(Request $request) {
        // Try legacy controller for not handled routes
        $non_legacy_routes = array(
            '/discover',
            '/user',
            '/glossary',
            '/about',
            '/service',
            '/blog',
            '/project',
            '/channel',
            '/image',
            '/img',
            );

        $legacy = true;
        if(App::debug() && $request->query->has('no-legacy')) $legacy = false;
        $path = $request->getPathInfo();
        foreach($non_legacy_routes as $route) {
            if(strpos($path, $route) === 0) {
                $legacy = false;
                break;
            }
        }
        if($legacy) {
            try {
                ob_start();
                // Get buffer contents
                $res = include __DIR__ . '/../../../src/legacy_dispatcher.php';
                $content = ob_get_contents();
                ob_get_clean();
                if( ! ($res instanceOf Response) ) {
                    $res = new Response($content);
                    $res->setStatusCode(Response::HTTP_OK);
                }
                // print_r($res);
                return $res;

            }
            catch(\Exception $e) {
                return new Response(View::render('errors/not_found', ['msg' => $e->getMessage() ? $e->getMessage() : 'Not found', 'code' => $e->getCode()]), $e->getCode());
            }
        }
    }
}
