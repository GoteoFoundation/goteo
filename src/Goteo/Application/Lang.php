<?php

namespace Goteo\Application;

use Goteo\Model\User;

class Lang {
    static protected $fallback = 'en'; //TODO: by config?
    static protected $default = 'es';

    static protected  $langs_available = array(
        'en' => array(
                    'name' => 'English',
                    'short' => 'ENG',
                    'public' => true,
                    'locale' => 'en_GB'
                    ),
        'es' => array(
                    'name' => 'Español',
                    'short' => 'ES',
                    'public' => true,
                    'locale' => 'es_ES'),
        'ca' => array(
                    'name' => 'Català',
                    'short' => 'CAT',
                    'public' => true,
                    'locale' => 'ca_ES',
                    'fallback' => 'es'
                    ),
        'eu' => array(
                    'name' => 'Euskara',
                    'short' => 'EUSK',
                    'public' => true,
                    'locale' => 'eu_ES',
                    'fallback' => 'es'
                    ),
        'gl' => array(
                    'name' => 'Galego',
                    'short' => 'GAL',
                    'public' => true,
                    'locale' => 'gl_ES',
                    'fallback' => 'es'
                    ),
        'fr' => array(
                    'name' => 'Français',
                    'short' => 'FRA',
                    'public' => true,
                    'locale' => 'fr_FR'),
        'it' => array(
                    'name' => 'Italiano',
                    'short' => 'ITA',
                    'public' => true,
                    'locale' => 'it_IT',
                    'fallback' => 'es'
                    ),
        'nl' => array(
                    'name' => 'Dutch',
                    'short' => 'NL',
                    'public' => true,
                    'locale' => 'nl_NL'
                    ),
        'de' => array(
                    'name' => 'Deutsch',
                    'short' => 'N',
                    'public' => false,
                    'locale' => 'N'),
        'el' => array(
                    'name' => 'Ελληνικά',
                    'short' => 'GRK',
                    'public' => false,
                    'locale' => 'el_GR'),
        'pl' => array(
                    'name' => 'Polski',
                    'short' => 'POL',
                    'public' => false,
                    'locale' => 'pl_PL'),
        'pt' => array(
                    'name' => 'Português',
                    'short' => 'PORT',
                    'public' => false,
                    'locale' => 'pt_PT'
                    ),
    );

    // TODO: method to override by config??


    /**
     * Returns the default language for a language
     * @param  string $lang [description]
     * @return [type]       [description]
     */
    static public function getDefault($lang = '') {
        $default = self::$default;
        if(array_key_exists($lang, self::$langs_available) && array_key_exists('fallback', self::$langs_available[$lang])) {
            $default = self::$langs_available[$lang]['fallback'];
        }

        return $default;
    }

    /**
     * set the system lang
     * @param [type] $lang [description]
     */
    static public function set($lang) {
        if(!array_key_exists($lang, self::$langs_available)) {
            // get the default
            $lang = self::getDefault($lang);
        }

        return Session::store('lang', $lang);
    }

    /**
     * Gets the current active language
     * @return [type] [description]
     */
    static public function current() {
        if(Session::exists('lang')) return Session::get('lang');
        return self::$default;
    }
    /**
     * Get the a language
     * @return [type] [description]
     */
    static public function get($lang, $method = 'object') {
        if(array_key_exists($lang, self::$langs_available)) {

            $info = self::$langs_available[$lang];

            if($method === 'name' && $info['name'])       return $info['name'];
            elseif($method === 'short' && $info['short'])  return $info['short'];
            elseif($method === 'locale' && $info['locale'])  return $info['locale'];
            elseif($method === 'array')  return $info;
            elseif($method === 'object') return (object) $info;

            return $lang;
        }
        return false;
    }

    /**
     * Returns if the lang is currently selected
     * @param  [type]  $lang [description]
     * @return boolean       [description]
     */
    static public function isActive($lang) {
        return self::current() === $lang;
    }

    static public function setFromGlobals() {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        // set Lang (forzado para el cron y el admin)
        $forceLang = (strpos($uri, 'cron') !== false || strpos($uri, 'admin') !== false) ? 'es' : null;
        if($forcelang) {
            $lang = self::set($forceLang);
        }
        // set Lang by GET user request
        elseif(isset($_GET['lang'])) {
            $lang = self::set($_GET['lang']);
            //Si el idioma existe, guardar preferencias
            if($lang === $_GET['lang']) {
                //Enviar cookie
                Cookie::store('goteo_lang', $lang);
                if(Session::isLogged()) {
                    //guardar preferencias de usuario
                    User::updateLang(Session::getUserId(), $lang);
                }
            }
        }
        // set lang by cookie if exists
        elseif(Cookie::exists('goteo_lang')) {
            $lang = self::set(Cookie::get('goteo_lang'));
        }
        // set by navigator
        else {
            $lang = self::set(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        }

        // establecemos la constante
        // TODO: por desaparecer
        // usar Lang::get() en su lugar
        define('LANG', $lang);

        // cambiamos el locale
        setlocale(LC_TIME, self::getLocale($lang));

        return $lang;
    }

    /**
     * Retrieve the locale value for a lang
     * @param  [type] $lang [description]
     * @return [type]       [description]
     */
    static function getLocale($lang) {
        return self::get($lang ? $lang : self::current(), 'locale');
    }

    /**
     * Retrieve the name value for a lang
     * @param  [type] $lang [description]
     * @return [type]       [description]
     */
    static function getName($lang) {
        return self::get($lang ? $lang : self::current(), 'name');
    }

    /**
     * Retrieve the short name value for a lang
     * @param  [type] $lang [description]
     * @return [type]       [description]
     */
    static function getShort($lang = null) {
        return self::get($lang ? $lang : self::current(), 'short');
    }


    /**
     * Returns an array of langs => lang-name
     * @return [type] [description]
     */
    static function listAll($method = 'name', $public_only = true) {
        $ret = array();
        foreach(self::$langs_available as $lang => $info) {

            if(empty($info['public']) && $public_only) continue;

            $ret[$lang] = self::get($lang, $method);
        }
        return $ret;
    }
}
