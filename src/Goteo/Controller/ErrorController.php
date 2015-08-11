<?php

namespace Goteo\Controller;

use Goteo\Application\App;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\FlattenException;

class ErrorController extends \Goteo\Core\Controller {

    /**
     * Handles errors and exceptions
     * @param  FlattenException $exception [description]
     * @param  Request          $request   [description]
     * @return [type]                      [description]
     */
    public function exceptionAction(FlattenException $exception, Request $request)
    {
        $title = 'Something went wrong!';
        $msg = $exception->getMessage();
        // Compatibility
        if($exception->getClass() === 'Goteo\Core\Redirection') {
            return new RedirectResponse($exception->getMessage());
        }
        // redirect to login on accesdenied exception if not logged already
        if($exception->getClass() === 'Goteo\Application\Exception\ControllerAccessDeniedException') {
            Message::error($exception->getMessage() ? $exception->getMessage() : 'Access denied, please log in!');
            if(!Session::isLogged())
                return new RedirectResponse('/user/login?return=' . rawurlencode($request->getPathInfo()));
        }
        $code = $exception->getStatusCode();
        if($code === Response::HTTP_NOT_FOUND) {
            $template = 'not_found';
        }
        elseif($code === Response::HTTP_FORBIDDEN) {
            $template = 'access_denied';
        }
        else {
            $template = 'server_error';
            foreach($exception->getTrace() as $trace) {
                $msg .= '<br>';
                $msg .= ' FILE <b>' . $trace['file'] . '</b> LINE <b>' . $trace['line'] .'</b>';
                if($trace['class']) $msg .= ' CLASS ' . $trace['class'] .'';
                if($trace['function']) $msg .= ' FUNCTION ' . $trace['function'] .'()';
            }
        }
        return new Response(View::render('errors/' . $template, ['title' => $title, 'msg' => $msg, 'code' => $code], $code));
    }

    /**
     * Redirect routes ending in / to the equivalent non-ending /
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);
        // if is a POST let's show a message
        if($request->getMethod() === 'POST') {
            Message::error("[$requestUri] has been redirected to [$url]. Please remove final slash in the action form!");
        }
        // return new RedirectResponse($url, 301); //permanent ?
        return new RedirectResponse($url, Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * Redirect routes starting in // (or more) to the equivalent non-ending /
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function removeStartingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = '/' . str_replace($pathInfo, ltrim($pathInfo, '/'), $requestUri);
        // return new RedirectResponse($url, 301); //permanent ?
        return new RedirectResponse($url, Response::HTTP_PERMANENTLY_REDIRECT);
    }

    /**
     * Executes the legacy subsistem for old controllers
     * based on /controller/action => Controller->action()
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function legacyControllerAction(Request $request) {
        $legacy = true;
        if(App::debug() && $request->query->has('no-legacy')) $legacy = false;
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
        return new Response(View::render('errors/not_found', ['msg' => 'Route not found', 'code' => Response::HTTP_NOT_FOUND]), Response::HTTP_NOT_FOUND);
    }
}
