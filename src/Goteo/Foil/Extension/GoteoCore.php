<?php

namespace Goteo\Foil\Extension;

use Symfony\Component\HttpFoundation\Request;
use Foil\Contracts\ExtensionInterface;
use Goteo\Application\Message;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Application\Session;

class GoteoCore implements ExtensionInterface
{

    private $args;
    private static $request;

    public static function setRequest(Request $request) {
        self::$request = $request;
    }

    public static function getRequest() {
        if(!self::$request)
            self::$request = Request::create();
        return self::$request;
    }

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'get_messages' => [$this, 'messages'],
          'get_errors' => [$this, 'errors'],
          'get_cookie' => [$this, 'get_cookie'],
          'get_session' => [$this, 'get_session'],
          'get_config' => [$this, 'get_config'],
          'get_user' => [$this, 'get_user'],
          'is_logged' => [$this, 'is_logged'],
          'user_can_admin' => [$this, 'user_can_admin'],
          'get_query' => [$this, 'get_query'],
          'get_post' => [$this, 'get_post'],
          'get_pathinfo' => [$this, 'get_pathinfo'],
          'get_querystring' => [$this, 'get_querystring'],
        ];
    }

    public function messages()
    {
        return Message::getMessages();
    }

    public function errors()
    {
        return Message::getErrors();
    }

    //Cookies
    public function get_cookie($var) {
        return Cookie::get($var);
    }

    //Session
    public function get_session($var) {
        return Session::get($var);
    }

    //Request (_GET) var
    public function get_query($var = null) {
        if($var) return self::getRequest()->query->get($var);
        return self::getRequest()->query->all();
    }

    //Request (_POST) var
    public function get_post($var = null) {
        if($var) return self::getRequest()->request->get($var);
        return self::getRequest()->request->all();
    }

    //pathinfo
    public function get_pathinfo() {
        return self::getRequest()->getPathInfo();
    }

    //querystring
    public function get_querystring() {
        return self::getRequest()->getQueryString();
    }

    //Config
    public function get_config($var) {
        return Config::get($var);
    }

    //User
    public function get_user() {
        return Session::getUser();
    }

    // Returns if the user can admin anything or not
    public function user_can_admin() {
        return \Goteo\Controller\AdminController::isAllowed(Session::getUser());
    }

    //is logged
    public function is_logged() {
        return Session::isLogged();
    }
}
