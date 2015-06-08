<?php

namespace Goteo\Foil\Extension;

use Foil\Contracts\ExtensionInterface;
use Goteo\Application\Message;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Application\Session;

class GoteoCore implements ExtensionInterface
{

    private $args;

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
        ];
    }

    public function messages($var)
    {
        return Message::getMessages();
    }

    public function errors($var)
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

    //Config
    public function get_config($var) {
        return Config::get($var);
    }

    //User
    public function get_user() {
        return Session::getUser();
    }

    //is logges
    public function is_logged() {
        return Session::isLogged();
    }
}
