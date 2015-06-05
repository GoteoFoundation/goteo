<?php

namespace Goteo\Controller;

use Goteo\Application\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\FlattenException;

class ErrorController extends \Goteo\Core\Controller {

    public function exceptionAction(FlattenException $exception, Request $request)
    {
        /////////////////
        // Try legacy controller for not founds
        if(defined('USE_LEGACY_DISPACHER') && USE_LEGACY_DISPACHER) {

            $non_legacy_routes = array(
                '/discover',
                '/user',
                '/glossary',
                '/about',
                '/service',
                '/blog',
                '/project',
                '/channel',
                );

            $legacy = true;
            $path = $request->getPathInfo();
            foreach($non_legacy_routes as $route) {
                if(strpos($path, $route) === 0) {
                    $legacy = false;
                    break;
                }
            }
            if($legacy && $exception->getClass() === 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException') {
                //TODO: mirar que la ruta no estigui ja dins del sistema de routing
                try {
                    ob_start();
                    // Get buffer contents
                    $res = include __DIR__ . '/../../../src/legacy_dispatcher.php';
                    $content = ob_get_contents();
                    ob_get_clean();
                    if($res instanceOf Response || $res instanceOf RedirectResponse) {
                        return $res;
                    }

                    return new Response($content);
                }
                catch(\Exception $e) {
                    return new Response(View::render('errors/not_found', ['msg' => $e->getMessage() ? $e->getMessage() : 'Not found', 'code' => $e->getCode()]), $e->getCode());
                }
            }
        }
        //////////////

        $msg = 'Something went wrong! ('.$exception->getMessage().')';
        $code = $exception->getStatusCode();
        return new Response(View::render('errors/not_found', ['msg' => $msg, 'code' => $code], $code));
    }
}
