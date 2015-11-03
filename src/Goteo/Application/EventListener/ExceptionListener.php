<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Application\App;
use Goteo\Application\Config;

//
class ExceptionListener implements EventSubscriberInterface
{
    /**
    * jTraceEx() - provide a Java style exception trace
    * @param $exception
    * @param $seen      - array passed to recursive calls to accumulate trace lines already seen
    *                     leave as NULL when calling this function
    * @return array of strings, one entry per trace line
    */
    static function jTraceEx($e, $seen=null) {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen) $seen = array();
        $trace  = $e->getTrace();
        $prev   = $e->getPrevious();
        $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();
        while (true) {
            $current = "$file:$line";
            // if (is_array($seen) && in_array($current, $seen)) {
            //     $result[] = sprintf(' ... %d more', count($trace)+1);
            //     break;
            // }
            $result[] = sprintf(' at %s%s%s(%s%s%s)',
                                        count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                                        count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                                        count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                                        $line === null ? $file : basename($file),
                                        $line === null ? '' : ':',
                                        $line === null ? '' : $line);
            if (is_array($seen))
                $seen[] = "$file:$line";
            if (!count($trace))
                break;
            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev)
            $result  .= "\n" . jTraceEx($prev, $seen);

        return $result;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        /*
         * Pagina de en mantenimiento
         */
        if (Config::get('maintenance') && $request->getPathInfo() !== '/about/maintenance'
             && !$request->request->has('Num_operacion')
            ) {
            $event->setResponse(new RedirectResponse('/about/maintenance'));
            return;
        }
    }

    /**
     * Fatal exception handling, provides nice message
     * This will be done after ErrorController processing (or if that fails)
     * @param  GetResponseForExceptionEvent $event [description]
     * @return [type]                              [description]
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // close pending buffers
        ob_end_clean();

        // You get the exception object from the received event
        $exception = $event->getException();

        // Customize your response object to display the exception details
        $response = new Response();

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
        } else {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        $response->setStatusCode($code);

        \Goteo\Application\View::addFolder(__DIR__ . '/../../../../Resources/templates/default');

        // Send the modified response object to the event
        if(App::debug()) $info = '<pre>'.self::jTraceEx($exception).'</pre>';
        $response->setContent(\Goteo\Application\View::render('errors/internal', ['msg' => $exception->getMessage(), 'file' => $file, 'code' => $code, 'info' => $info], $code));
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => ['onKernelException', -256], // low priority for handler
        );
    }
}

