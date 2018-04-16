<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Cookie {

    const DEFAULT_TTL = 31536000; // 3600 * 24 * 365;
    static protected $path = '/';

    static protected $response = null;
    static protected $request = null;

    /**
     * TODO:
     * Initializes session managem with Symfony Request object
     * @return [type] [description]
     */
    static public function factory(Response $response, Request $request) {
        self::$response = $response;
        self::$request = $request;
    }

    static function setPath($path) {
        self::$path = $path;
    }

    static function getDomain() {
        $url = Config::get('url.main');
        if(strpos($url, '//') === 0) $url = "http:$url";
        $host = parse_url($url, PHP_URL_HOST);
        return $host;
    }

    /**
     * Stores some value in cookie
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    static public function store($key, $value, $ttl = null) {
        global $_COOKIE;
        if(is_null($ttl)) $ttl = self::DEFAULT_TTL;
        $ttl = (int) $ttl;
        if (PHP_SAPI !== 'cli') {
            //delete previous cookie
            // setcookie($key, '', time() - 3600, self::$path, self::getDomain());
            //store cookie
            setcookie($key, $value, time() + $ttl, self::$path, self::getDomain());
            // print_r($_COOKIE);
            // die("$key : [".self::$path . '] ['. self::getDomain().'] ['.Config::get('url.main'));
        }
        return $_COOKIE[$key] = $value;
    }

    /**
     * Retrieve some value in cookie
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function get($key) {
        global $_COOKIE;
        return $_COOKIE[$key];
    }

    /**
     * Retrieve all values in cookies
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function getAll() {
        global $_COOKIE;
        return $_COOKIE;
    }

    /**
     * Delete some value in cookie
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function del($key) {
        global $_COOKIE;
        if (PHP_SAPI !== 'cli') {
            setcookie($key, '', time() - 3600, self::$path, self::getDomain());
        }
        unset($_COOKIE[$key]);
        return !self::exists($key);
    }

    /**
     * Check if a value exists in cookie
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    static public function exists($key) {
        global $_COOKIE;
        return is_array($_COOKIE) && array_key_exists($key, $_COOKIE);
    }

}
