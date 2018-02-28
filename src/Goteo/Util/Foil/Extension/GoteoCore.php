<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Foil\Extension;

use Symfony\Component\HttpFoundation\Request;
use Foil\Contracts\ExtensionInterface;
use Goteo\Application\Message;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Model\User;
use Goteo\Model\User\UserLocation;
use Goteo\Application\Currency;

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
          'get_user_menu' => [$this, 'get_user_menu'],
          'get_main_menu' => [$this, 'get_main_menu'],
          'get_sidebar_menu' => [$this, 'get_sidebar_menu'],
          'get_user_location' => [$this, 'get_user_location'],
          'is_logged' => [$this, 'is_logged'],
          'has_role' => [$this, 'has_role'],
          'is_admin' => [$this, 'is_admin'],
          'is_module_admin' => [$this, 'is_module_admin'],
          'is_master_node' => [$this, 'is_master_node'],
          'get_query' => [$this, 'get_query'],
          'get_post' => [$this, 'get_post'],
          'has_query' => [$this, 'has_query'],
          'has_post' => [$this, 'has_post'],
          'get_uri' => [$this, 'get_uri'],
          'get_url' => [$this, 'get_url'],
          'get_pathinfo' => [$this, 'get_pathinfo'],
          'get_querystring' => [$this, 'get_querystring'],
          'is_ajax' => [$this, 'is_ajax'],
          'is_pronto' => [$this, 'is_pronto'],
          'currency' => [$this, 'currency'],
          'get_currency' => [$this, 'get_currency'],
          'asset' => [$this, 'asset'],
          'debug' => [$this, 'debug'],

        ];
    }

    public function debug()
    {
        return App::debug();
    }

    public function asset($asset) {
        return SRC_URL . '/assets/' . $asset;
    }

    public function messages($autoexpire = true)
    {
        return Message::getMessages($autoexpire);
    }

    public function errors($autoexpire = true)
    {
        return Message::getErrors($autoexpire);
    }

    //Cookies
    public function get_cookie($var) {
        return Cookie::get($var);
    }

    //Session
    public function get_session($var) {
        return Session::get($var);
    }

    //returns if is a XmlHttpRequest (ajax) petition
    public function is_ajax() {
        return self::getRequest()->isXmlHttpRequest();
    }

    //returns if is a jquery.fs.pronto petition (ajax) petition
    // Pages using pronto must return code like:
    // json_encode(['title' => ...
    //              'content' => ... ]);
    public function is_pronto() {
        if(App::debug()) {
            return self::getRequest()->query->has('pronto');
        }
        return self::getRequest()->isXmlHttpRequest() && self::getRequest()->query->has('pronto');
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

    //Request (_GET) has var
    public function has_query($var) {
        return self::getRequest()->query->has($var);
    }

    //Request (_POST) has var
    public function has_post($var) {
        return self::getRequest()->request->has($var);
    }

    //pathinfo
    public function get_uri() {
        return self::getRequest()->getUri();
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

    //URL
    public function get_url($lang = null) {
        return Config::getUrl($lang);
    }

    //User
    public function get_user() {
        $user = Session::getUser();
        if($user instanceOf User) return $user;
        return null;
    }

    //User Global menu
    public function get_user_menu() {
        return Session::getUserMenu();
    }

    //User Sidebar menu
    public function get_sidebar_menu() {
        return Session::getSidebarMenu();
    }

    public function get_user_location() {
        $user = Session::getUser();
        if($user instanceOf User) {
            $location = $user->getLocation();
        } else {
            $location = UserLocation::createByIp(null, self::getRequest()->getClientIp());
        }

        if(!$location) {
            // TODO: some default?
        }
        return $location;
    }

    //Main Global menu
    public function get_main_menu() {
        return Session::getMainMenu();
    }

    //Currency
    public function currency($currency, $method = 'html') {
        return Currency::get($currency, $method);
    }
    //Currency
    public function get_currency($method = 'html') {
        return Currency::current($method);
    }

    // Checks user role
    public function has_role($role, $node = null, User $user = null) {
        if(empty($user)) $user = Session::getUser();
        if(empty($node)) $node = Config::get('current_node');
        if(Session::isLogged()) {
            if(!is_array($role)) $role = [$role];
            return $user->hasRoleInNode($node, $role);
        }
        return false;
    }

    // Returns if the user can admin anything or not
    public function is_admin() {
        return Session::isAdmin();
    }

    // Returns if the user can admin some specific module
    public function is_module_admin($subcontroller, $node = null, User $user = null) {
        return Session::isModuleAdmin($subcontroller, $node, $user);
    }

    //is logged
    public function is_logged() {
        return Session::isLogged();
    }

    //is master node
    public function is_master_node($node = null) {
        return Config::isMasterNode($node);
    }
}
