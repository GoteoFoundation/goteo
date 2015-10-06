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

use Foil;

class View {
    static protected $engine;
    static protected $theme = 'default';

    /**
     * Initializes Foil View system
     * Go to http://foilphp.it/ for documentation on Foil templates
     */
    static public function factory() {
        if(!self::$engine) {
            self::$engine = Foil\engine();
        }
    }

    /**
     * Add a folder to the view system at the end of the list
     */
    static public function addFolder($path, $name = null) {
        self::factory();
        if(is_dir($path)) {
            self::$engine->addFolder($path, $name);
        }
    }

    /**
     * Same as addFolder but adds the folder in the beggining of the dir search array
     */
    static public function prependFolder($path, $name) {
        self::factory();
        self::$engine->setFolders(array_merge([$name => $path] , self::$engine->finder()->dirs()));

    }


    /**
     * Renders a template view
     */
    static public function render($view, $vars = []) {
        if(!self::$engine) {
            self::$engine = Foil\engine();
        }
        //por compatibilidad
        // self::$engine->vars = $vars;
        return self::$engine->render($view, $vars + array('vars' => $vars)); //por compatibilidad
    }


    /**
     * Gets the Foil engine
     */
    static public function getEngine() {
        self::factory();
        return self::$engine;
    }

    /**
     * Searchs the theme into available dirs and puts it as a "default" view
     * extend/plugin-name/Resources/templates
     * templates
     */
    static public function setTheme($theme) {
        self::factory();

        $folders = [];
        // Compiled views by grunt
        if(is_file(GOTEO_WEB_PATH . "templates/$theme")) $folders['compiled'] = GOTEO_WEB_PATH . "templates/$theme";

        // Search the theme in the plugins and add it first
        foreach(Config::getPlugins() as $plugin => $vars) {
            if(is_dir(__DIR__ . "/../../../extend/$plugin/Resources/templates/$theme")) {
            // echo $plugin;
                $folders["default-$plugin"] = __DIR__ . "/../../../extend/$plugin/Resources/templates/$theme";
            }
        }
        // Search the theme in the default and add it later
        if(is_dir(__DIR__ . "/../../../Resources/templates/$theme")) {
            $folders['default'] = __DIR__ . "/../../../Resources/templates/$theme";
        }
        if(empty($folders)) {
            if($theme !== 'default') self::setTheme('default');
            throw new Config\ConfigException("Theme [$theme] not found in any path!");
        }

        $dirs = self::$engine->finder()->dirs();
        $prepend_dirs = $append_dirs = [];
        $append = false;
        foreach($dirs as $name => $folder) {
            if(substr($name, 0, 7) === 'default') {
                $append = true;
            }
            else {
                if($append) $append_dirs[$name] = $folder;
                else        $prepend_dirs[$name] = $folder;
            }
        }
        // print_r(array_merge($prepend_dirs, $folders, $append_dirs));die;
        self::$engine->setFolders(array_merge($prepend_dirs, $folders, $append_dirs));
        self::$theme = $theme;
        return false;
    }

    /**
     * Returns the current used theme
     */
    static public function getTheme() {
        return self::$theme;
    }
}
