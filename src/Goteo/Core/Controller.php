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

use Goteo\Application\App;
use Goteo\Application\View;
use Goteo\Core\Traits\LoggerTrait;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Util\Form\FormFinder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\HttpKernelInterface;


abstract class Controller {
    use LoggerTrait;

    /**
     * Handy method to send a response from a view
     */
    public function viewResponse($view, $vars = [], $status = 200, $contentType = 'text/html'): Response
    {
        $view = View::render($view, $vars);
        $request = App::getRequest();
        if($request->query->has('pronto') && (App::debug() || $request->isXmlHttpRequest())) {
            $contentType = 'application/json';
        }

        return new Response($view, $status, ['Content-Type' => $contentType]);
    }

    /**
     * Handy method to send a response any string
     */
    public function rawResponse($string, $contentType = 'text/plain' , $status = 200, $file_name = '') {
        $response = new Response($string, $status, ['Content-Type' => $contentType]);

        if($file_name) {
            $d = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file_name
            );
            $response->headers->set('Content-Disposition', $d);
        }
        return $response;
    }

    /**
     * **Experimental** method to send a response in json, vars only
     */
    public function jsonResponse($vars = []) {
        $resp = new JsonResponse($vars);
        if(App::debug()) $resp->setEncodingOptions(JSON_PRETTY_PRINT);
        return $resp;
    }

    /**
     * @return Response A Response instance
     */
    public function forward(string $controller, array $path = [], array $query = null)
    {
        $path['_controller'] = $controller;
        $subRequest = App::getRequest()->duplicate($query, null, $path);

        return $this->getService('app')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Handy method to return a redirect response
     */
    public function redirect($path = null, $status = 302) {
        if($path === null) {
            $path = App::getRequest()->getRequestUri();
        }
        return new RedirectResponse($path, $status);
    }


    /**
     * Handy method to enable/disable the SQL cache
     */
    public function dbCache($cache = null) {
        return DB::cache($cache);
    }

    /**
     * Handy method to enable/disable the SQL replica
     */
    public function dbReplica($replica = null) {
        return DB::replica($replica);
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
    public function contextVars(array $vars = [], $view_path_context = null) {
        if($view_path_context) {
            View::getEngine()->useContext($view_path_context, $vars);
        } else {
            View::getEngine()->useData($vars);
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

    public function dispatch($eventName, Event $event = null) {
        return App::dispatch($eventName, $event);
    }

    public function createFormBuilder(
        $defaults = null,
        $name = 'autoform',
        array $options = []
    ): FormBuilder {
        $default_options = [
            'action' => App::getRequest()->getRequestUri(),
            'attr' => ['class' => 'autoform']
        ];
        return $this->getService('app.forms')
            ->createBuilder($defaults, $name, $options + $default_options);
    }

    public function getModelForm(
        string $formClass,
        Model $model,
        array $defaults = [],
        array $options = [],
        Request $request = null,
        array $formBuilderOptions = ['csrf_protection' => false]
    ): FormProcessorInterface {
        /** @var $finder FormFinder */
        $finder = App::getService('app.forms.finder');
        $finder->setModel($model);
        $validate = $mock_validation = false;
        if ($request) {
            $validate = $request->query->has('validate');
            $mock_validation = $validate && $request->isMethod('get');
        }
        // TODO: a better way to create a csrf_protection without showing errors CSRF on mock_validation
        $finder->setBuilder($this->createFormBuilder($defaults, 'autoform', $formBuilderOptions));
        $processor = $finder->getInstanceOfClassForm($formClass, $options);
        // Set full validation if required in Request
        // Do a fake submit of the form on create to test errors (only on GET requests)
        $processor->setFullValidation($validate, $mock_validation);

        return $processor;
    }

    public function getModelFormGuessingClass(
        $form,
        Model $model,
        array $defaults = [],
        array $options = [],
        Request $request = null
    ): FormProcessorInterface {
        /** @var $finder FormFinder */
        $finder = App::getService('app.forms.finder');
        $finder->setModel($model);
        $validate = $mock_validation = false;
        if ($request) {
            $validate = $request->query->has('validate');
            $mock_validation = $validate && $request->isMethod('get');
        }
        // TODO: a better way to create a csrf_protection without showing errors CSRF on mock_validation
        $finder->setBuilder($this->createFormBuilder($defaults, 'autoform', ['csrf_protection' => false]));
        $processor = $finder->getInstance($form, $options);
        // Set full validation if required in Request
        // Do a fake submit of the form on create to test errors (only on GET requests)
        $processor->setFullValidation($validate, $mock_validation);

        return $processor;
    }
}


