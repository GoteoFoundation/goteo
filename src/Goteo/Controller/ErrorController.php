<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application\App;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller {

    /**
     * Redirect routes ending in / to the equivalent non-ending /
     */
    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);
        if(empty($url)) $url = '/';

        if($request->getMethod() === 'POST') {
            Message::error("[$requestUri] has been redirected to [$url]. Please remove final slash in the action form!");
        }

        return new RedirectResponse($url, Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * Redirect routes starting in // (or more) to the equivalent non-ending /
     */
    public function removeStartingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();
        $url = '/' . str_replace($pathInfo, ltrim($pathInfo, '/'), $requestUri);

        return new RedirectResponse($url, Response::HTTP_PERMANENTLY_REDIRECT);
    }

    /**
     * Executes the legacy subsystem for old controllers
     * based on /controller/action => Controller->action()
     */
    public function legacyControllerAction(Request $request) {
        $legacy = true;
        if(App::debug() && $request->query->has('no-legacy')) $legacy = false;
        if($legacy) {
            ob_start();
            // Get buffer contents
            $res = include __DIR__ . '/../../../src/legacy_dispatcher.php';
            $content = ob_get_contents();
            ob_get_clean();
            if( ! ($res instanceOf Response) ) {
                $res = new Response($content);
                $res->setStatusCode(Response::HTTP_OK);
            }
            return $res;
        }
        View::setTheme('responsive');
        return new Response(View::render('errors/not_found', ['msg' => 'Route not found', 'code' => Response::HTTP_NOT_FOUND]), Response::HTTP_NOT_FOUND);
    }
}
