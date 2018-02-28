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

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Library\Text;
use Goteo\Core\Error as LegacyError;
use Goteo\Core\Redirection as LegacyRedirection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

//

class ExceptionListener extends AbstractListener {

    /**
     * jTraceEx() - provide a Java style exception trace
     * @param $exception
     * @param $seen      - array passed to recursive calls to accumulate trace lines already seen
     *                     leave as NULL when calling this function
     * @return array of strings, one entry per trace line
     */
    static function jTraceEx($e, $with_message = true, $seen = null) {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen) {
            $seen = array();
        }

        $trace = $e->getTrace();
        $prev = $e->getPrevious();
        if($with_message)
            $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        else
            $result[] = sprintf('%s%s', $starter, get_class($e));
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
            if (is_array($seen)) {
                $seen[] = "$file:$line";
            }

            if (!count($trace)) {
                break;
            }

            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev) {
            $result .= "\n" . self::jTraceEx($prev, $with_message, $seen);
        }

        return $result;
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message) {
        if (null !== $this->logger) {
            $message = str_replace(["\n", "\r"],[" ", ""], $message);
            if ($exception instanceof LegacyError) {
                $this->logger->warning('Kernel Exception', ['etype' => 'LegacyError', 'trace' => self::jTraceEx($exception, false), 'exception' => $exception, 'message' => $message]);
            } elseif (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical('Kernel Exception', ['etype' => 'HttpException', 'trace' => self::jTraceEx($exception, false), 'exception' => $exception, 'message' => $message]);
            } elseif ($exception instanceof ModelNotFoundException) {
                $this->logger->warning('Kernel Exception', ['etype' => 'NotFound', 'trace' => self::jTraceEx($exception, false), 'exception' => $exception, 'message' => $message]);
            } elseif ($exception instanceof ControllerAccessDeniedException) {
                $this->logger->warning('Kernel Exception', ['etype' => 'AccessDenied', 'trace' => self::jTraceEx($exception, false), 'exception' => $exception, 'message' => $message]);
            } else {
                $this->logger->error('Kernel Exception', ['etype' => 'Exception', 'trace' => self::jTraceEx($exception, false), 'exception' => $exception, 'message' => $message]);
            }
        }
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
    public function onKernelException(GetResponseForExceptionEvent $event) {
        // close pending buffers
        ob_end_clean();
        // You get the exception object from the received event
        $exception = $event->getException();
        $request = $event->getRequest();

        // Old legacy redirections for compatibility
        if ($exception instanceOf LegacyRedirection) {
            $event->setResponse(new RedirectResponse($exception->getUrl(), $exception->getCode()));
            return;
        }

            // redirect to login on acces denied exception if not logged already
        if ($exception instanceof ControllerAccessDeniedException) {
            $error = $exception->getMessage() ? $exception->getMessage() : Text::get('user-login-required-access');
            // JSON response for ApiController
            if(View::getTheme() === 'JSON') {
                $event->setResponse(new JsonResponse(['error' => $error]));
                return;
            }

            if(!$request->isXmlHttpRequest() && !(App::debug() && $request->query->has('pronto'))) {
                Message::error($error);
                if (!Session::isLogged()) {
                    $event->setResponse(new RedirectResponse('/user/login?return=' . rawurlencode($request->getPathInfo())));
                    return;
                }
            }
        }


        // Customize your response object to display the exception details
        $response = new Response();

        // legacy errors handleing
        if ($exception instanceof LegacyError) {
            $code = $exception->getCode();
        }

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        elseif ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
        } else {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if($code === Response::HTTP_INTERNAL_SERVER_ERROR) {
            // Check if there's some strange char in the URL
            $uri = urldecode($request->getUri());
            if(preg_match('/[^\x20-\x7e]/', $uri)) {
                $redirect = preg_replace('/[^\x20-\x7e]*/', '', $uri);
                if($redirect !== $uri) {
                    $this->logger->warning('Kernel Exception', ['etype' => 'CharError', 'uri' => $uri, 'message' => "Redirected to [$redirect]"]);
                    $event->setResponse(new RedirectResponse($redirect));
                    return;
                }
            }
        }

        $this->logException($exception, sprintf('Exception thrown when handling an exception (%s at %s line %s: %s)', get_class($exception), $exception->getFile(), $exception->getLine(), $exception->getMessage()));

        $response->setStatusCode($code);

        $info = '';
        if ($code === Response::HTTP_NOT_FOUND) {
            $template = 'not_found';
        } elseif ($code === Response::HTTP_FORBIDDEN) {
            $template = 'access_denied';
        } else {
            $template = 'server_error';
        }

        if (App::debug()) {
            $info = '<pre>' . self::jTraceEx($exception) . '</pre>';
        }

        // JSON response for ApiController
        if(View::getTheme() === 'JSON') {
            $ret = ['error' => $exception->getMessage()];
            if($info) $ret['info'] = $info;
            $event->setResponse(new JsonResponse($ret));
            return;
        }


        $view = 'unknown error';

        // TODO:: create a low level path and avoid changing theme here
        try {
            View::setTheme('responsive');
            $view = View::render('errors/' . $template, ['title' => $exception->getMessage(), 'msg' => $info, 'code' => $code], $code);

        } catch (\Exception $e) {
            View::addFolder(__DIR__ . '/../../../../Resources/templates/responsive');
            $view = View::render('errors/internal', ['msg' => $exception->getMessage(), 'file' => $file, 'code' => $code, 'info' => $e->getMessage() . "\n$info"], $code);
        }

        // Send the modified response object to the event
        $response->setContent($view);
        $event->setResponse($response);
    }

    /**
     * Executes expensive code after the response is sent
     * This is effective when php is used in fastcgi mode
     * @param  PostResponseEvent $event Kernel event
     */
    public function onKernelTerminate(PostResponseEvent $event) {
        // send delayed mails from logger
        if (App::isService('logger.mail_handler')) {
            try {
                App::getService('logger.mail_handler')->sendDelayed();
            } catch(\Exception $e) {
                if (App::isService('syslogger')) {
                    App::getService('syslogger')->critical($e->getMessage());
                }
            }
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => ['onKernelException', -256], // low priority for handler
            KernelEvents::TERMINATE => 'onKernelTerminate', // low priority for handler
        );
    }
}
