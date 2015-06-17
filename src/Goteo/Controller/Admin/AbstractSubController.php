<?php

namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\View;

abstract class AbstractSubController {
    protected $request;
    protected $node;

    /**
     * Some defaults
     */
    public function __construct($node, Request $request) {
        $this->request = $request;
        $this->node = $node;
    }


    public function isDefaultNode() {
        return Config::get('node') === $this->node;
    }

    public function getGet($var = null) {
        if($var) {
            //return requested var
            return $this->request->query->get($var);
        }
        return $this->request->query;
    }
    public function hasGet($var) {
        return $this->request->query->has($var);
    }

    public function getPost($var = null) {
        if($var) {
            //return requested var
            return $this->request->request->get($var);
        }
        //return object
        return $this->request->request;
    }

    public function hasPost($var) {
        return $this->request->request->has($var);
    }

    public function getServer($var = null) {
        if($var) {
            //return requested var
            return $this->request->query->get($var);
        }
        return $this->request->server;
    }

    public function getMethod() {
        return $this->request->getMethod();
    }

    public function isPost() {
        return $this->request->getMethod() === 'POST';
    }

    public function redirect($url = '/admin') {
        return new RedirectResponse($url);
    }

    public function response($view, $data = []) {
        return new Response(View::render($view, $data));
    }
}
