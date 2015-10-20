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

use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\Config\SqlTranslationLoader;
use Goteo\Application\Config\YamlTranslationLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

class Lang {
    static protected $default = '';
    static protected $groups = array();
    static protected $translator = null;

    // This is overwriten by Config using file Resources/locales.yml
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
    );

    static function factory($lang = null) {
        if(empty($lang)) $lang = Config::get('lang');
        if(!self::$translator) {
            self::$translator = new Translator($lang, new MessageSelector());
            self::$translator->addLoader('sql', new SqlTranslationLoader()); // cached loader
            self::$translator->addLoader('yaml', new YamlTranslationLoader()); // cached loader
        }
    }

    /**
     * Adds a SQL source for translations (texts table)
     * @param string $lang      Language ID (es, en, fr, etc.)
     */
    static public function addSqlTranslation($lang) {
        self::factory($lang);
        self::$translator->addResource('sql', 'texts', $lang, 'sql');
    }

    /**
     * Adds a YAML source text files for translations
     * @param string $lang      Language ID (es, en, fr, etc.)
     * @param [type] $yaml_file [description]
     */
    static public function addYamlTranslation($lang, $yaml_file) {
        self::factory($lang);
        if(is_file($yaml_file)) {
            $group = strtok(basename($yaml_file), '.');
            // Add this translation
            self::$translator->addResource('yaml', $yaml_file, $lang);
            self::$groups[$group][] = [$lang, $yaml_file];
        }
    }

    /**
     * Purgues all cached languages files
     */
    static public function clearCache() {
        // force catalogue loading
        $all = self::translator()->getCatalogue();
        foreach(array_merge(SqlTranslationLoader::$cached_files, YamlTranslationLoader::$cached_files) as $file) {
            unlink($file);
        }
    }

    /**
     * Handy method to retrieve the Symfony Component Translator
     */
    static public function translator() {
        self::factory();
        return self::$translator;
    }

    /**
     * Handy method for the trans() function of the Symfony Component Translator
     */
    static public function trans($id, array $parameters = array(), $locale = null) {
        self::factory();
        // search in SQL first
        $all = self::$translator->getCatalogue($locale)->all('sql');
        if(isset($all[$id])) return $all[$id];
        // Yaml files (message $domain)
        return self::$translator->trans($id, $parameters, null, $locale);
    }

    /**
     * Handy method for the transChoice() function of the Symfony Component Translator
     */
    static public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
        self::factory();
        return self::$translator->transChoice($id, $number, $parameters, $domain, $locale);
    }

    static public function groups($group = null) {
        if($group) {
            return self::$groups[$group];
        }
        return self::$groups;

    }

    static public function getLangsAvailable() {
        return self::$langs_available;
    }

    static public function setLangsAvailable(array $langs) {
        self::$langs_available = $langs;
    }


    /**
     * Sets the default language
     * @param string $lang Language ID (es, en, fr, etc.)
     */
    static public function setDefault($lang = null) {
        if(empty($lang)) $lang = Config::get('lang');
        self::factory($lang);
        if(self::exists($lang)) {
            self::$default = $lang;
        }
    }
    /**
     * Sets the default language
     * @param string $lang Language ID (es, en, fr, etc.)
     */
    static public function setPublic($lang, $public = true) {
        if(self::exists($lang)) {
            self::$langs_available[$lang]['public'] = (bool) $public;
        }
    }

    static public function isPublic($lang) {
        return self::get($lang, 'public');
    }

    /**
     * Returns the default language  or retrieves the fallback language for a language
     * @param  string $lang [description]
     * @return [type]       [description]
     */
    static public function getDefault($lang = '') {
        $default = self::isPublic(self::$default) ? self::$default : '';
        foreach(self::$langs_available as $l => $info) {
            if($info['public']) {
                if(empty($default)) {
                    $default = $l;
                }
                if($lang === $l && array_key_exists('fallback', $info)) {
                    $fallback = $info['fallback'];
                    if($fallback && self::isPublic($fallback)) {
                        $default = $fallback;
                    }
                    break;
                }
            }
        }
        return $default;
    }

    /**
     * set the system lang
     * @param string $lang Language ID (es, en, fr, etc.)
     */
    static public function set($lang) {
        self::factory($lang);
        self::$translator->setLocale($lang);
        $fallbacks = [];
        $fallback = self::getFallback($lang);
        while($fallback !== $lang && !in_array($lang, $fallbacks) && !in_array($fallback, $fallbacks)) {
            $fallbacks[] = $fallback;
            $fallback = self::getFallback($fallback);

        }
        self::$translator->setFallbackLocale($fallbacks);

        // if(!self::isPublic($lang)) {
        if(!self::exists($lang)) {
            // get the default
            $lang = self::getDefault($lang);
        }

        // establecemos la constante
        // TODO: por desaparecer
        // usar Lang::current() en su lugar
        if(!defined('LANG'))
            define('LANG', $lang);

        // cambiamos el locale
        setlocale(LC_TIME, self::getLocale($lang));

        return Session::store('lang', $lang);
    }

    /**
     * Gets the current active language
     * @return [type] [description]
     */
    static public function current($public_only = false) {
        $current = '';
        if(Session::exists('lang')) {
            $current = Session::get('lang');
        }
        if(empty($current) || !self::exists($current)) {
            $current = self::getDefault();
        }
        if($public_only && !self::isPublic($current)) {
            $current = self::getFallback($current);
        }
        return $current;
    }
    /**
     * Get the a language
     * @return [type] [description]
     */
    static public function get($lang, $method = 'object') {
        if(self::exists($lang)) {

            $info = self::$langs_available[$lang];

            if($method === 'id')  return $lang;
            elseif($method === 'name' && $info['name'])       return $info['name'];
            elseif($method === 'short' && $info['short'])  return $info['short'];
            elseif($method === 'locale' && $info['locale'])  return $info['locale'];
            elseif($method === 'public')  return (bool)$info['public'];
            elseif($method === 'array')  return $info;
            elseif($method === 'object') {
                $obj = (object) $info;
                $obj->id = $lang;
                return $obj;
            }

            return $lang;
        }
        return false;
    }

    static public function exists($lang) {
        return array_key_exists((string)$lang, self::$langs_available);
    }
    /**
     * Returns if the lang is currently selected
     * @param  [type]  $lang [description]
     * @return boolean       [description]
     */
    static public function isActive($lang, $public_only = true) {
        return self::current($public_only) === $lang;
    }

    static public function setFromGlobals(Request $request = null) {
        // self::setDefault('es');

        $desired = array();
        // set Lang (forzado para el cron y el admin)
        if($request) {
            $uri = strtok($request->server->get('REQUEST_URI'), '?');
            if(strpos($uri, 'admin') !== false) $desired['forced'] = 'es';
            // set Lang by GET user request
            if($request->query->has('lang')) {
                $desired['get'] = $request->query->get('lang');
            }
        }
        // Las cookies para idiomas pueden ser problematicas, pues cambian el idioma sin enterarte.
        // set lang by cookie if exists
        // if(Cookie::exists('goteo_lang')) {
        //     $desired[] = Cookie::get('goteo_lang');
        // }
        if(Session::exists('lang')) {
            $desired['session'] = Session::get('lang');
        }
        if($request) {
            // set by subdomain
            $subdomain = strtok($request->getHost(), '.');
            if(self::isPublic($subdomain)) {
                $desired['subdomain'] = $subdomain;
            }
            // set by navigator
            $desired['browser'] = substr($request->server->get('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        }

        // set the lang in order of preference
        foreach($desired as $l) {
            $lang = self::set($l);
            if($lang === $l) {
                break;
            }
        }

        //Si el idioma existe (y se ha especificado), guardar preferencias
        if($request && $lang === $request->query->get('lang')) {
            //Enviar cookie
            // Cookie::store('goteo_lang', $lang);
            Session::store('lang', $lang);
            if(Session::isLogged()) {
                //guardar preferencias de usuario
                Session::getUser()->updateLang($lang);
            }
        }
        // print_r($desired);die($lang);

        return $lang;
    }

    /**
     * Retrieve the locale value for a lang
     * @param  string $lang Language ID (es, en, fr, etc.)
     * @return [type]       [description]
     */
    static function getLocale($lang = null) {
        return self::get($lang ? $lang : self::current(), 'locale');
    }

    /**
     * Retrieve the name value for a lang
     * @param  string $lang Language ID (es, en, fr, etc.)
     * @return [type]       [description]
     */
    static function getName($lang = null) {
        return self::get($lang ? $lang : self::current(), 'name');
    }

    /**
     * Retrieve the short name value for a lang
     * @param  string $lang Language ID (es, en, fr, etc.)
     * @return [type]       [description]
     */
    static function getShort($lang = null) {
        return self::get($lang ? $lang : self::current(), 'short');
    }

    /**
     * Retrieve the fallback language a lang
     * @param  string $lang Language ID (es, en, fr, etc.)
     * @return [type]       [description]
     */
    static function getFallback($lang = null) {
        $lang = self::get($lang ? $lang : self::current(), 'object');
        if(isset($lang->fallback) && $lang !== $lang->fallback) {
            if(!self::isPublic($lang->fallback)) {
                return self::getFallback($lang->fallback);
            }
            return $lang->fallback;
        }
        return self::getDefault();
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

    /**
     * Return a list of countries
     * @return [type] [description]
     */
    static function listCountries($lang = null) {
        if(!$lang) $lang = self::current();
        $countries = include(__DIR__ . '/../../../vendor/openclerk/country-list/country/' . $lang . '/country.php');
        if(!$countries) {
            $countries = include(__DIR__ . '/../../../vendor/openclerk/country-list/country/en/country.php');
        }
        return $countries;
    }

    /**
     * Compatibility function to retrieve a 2-digit country code from and manually written country
     * @param  [type] $country [description]
     * @return [type]          [description]
     */
    static function getCountryCode($country) {
        // manual old style country name
        // for a old bug:
        if($country == 'EspaÃ±a') $country = 'Spain';
        foreach(self::listAll('id') as $lang) {
            $countries = Lang::listCountries($lang);
            foreach($countries as $code => $c) {
                if(\Goteo\Core\Model::idealiza($country) == \Goteo\Core\Model::idealiza($c)) {
                    return $code;
                }
            }
        }
        return ''; // not found
    }
}
