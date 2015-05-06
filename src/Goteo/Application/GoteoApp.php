<?php

namespace Goteo\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class GoteoApp
{
    protected $matcher;
    protected $resolver;

    public function __construct(UrlMatcherInterface $matcher, ControllerResolverInterface $resolver)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
    }

    public function handle(Request $request)
    {
        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->resolver->getController($request);
            $arguments = $this->resolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            if(defined('USE_LEGACY_DISPACHER') && USE_LEGACY_DISPACHER) {
                //Try legacy controller
                try {
                    ob_start();
                    // Get buffer contents
                    include __DIR__ . '/../../../src/legacy_dispatcher.php';
                    $content = ob_get_contents();
                    ob_get_clean();
                    return new Response($content);
                }
                catch(\Goteo\Core\Error $e) {
                    return new Response(View::render('errors/not_found', ['msg' => $e->getMessage() ? $e->getMessage() : 'Not found', 'code' => $e->getCode()]), $e->getCode());
                }
            }
            else {
                return new Response(View::render('errors/not_found', ['msg' => $e->getMessage(), 'code' => 404]), 404);
            }
        } catch(\LogicException $e) {
            return new Response(View::render('errors/not_found', ['msg' => 'Not found', 'code' => 500]), 500);
        } catch (\Exception $e) {
            return new Response(View::render('errors/default', ['msg' => $e->getMessage(), 'code' => 500]), 500);
            // return new Response('An error occurred', 500);
        }
    }
}
