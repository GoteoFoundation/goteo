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
use Goteo\Application\Event\FilterViewEvent;
use Foil\Contracts\ExtensionInterface;

class View {
    static protected $engine;
    static protected $theme = 'default';


    static public function createEngine() {
        $engine = Foil\engine();
        // Register default Goteo extensions
        $engine->loadExtension(new \Goteo\Util\Foil\Extension\GoteoCore(), [], true);
        $engine->loadExtension(new \Goteo\Util\Foil\Extension\TextUtils(), [], true);
        $engine->loadExtension(new \Goteo\Util\Foil\Extension\ModelsData(), [], true);
        $engine->loadExtension(new \Goteo\Util\Foil\Extension\LangUtils(), [], true);
        $engine->loadExtension(new \Goteo\Util\Foil\Extension\Forms(), [], true);
        return $engine;
    }

    /**
     * Initializes Foil View system
     * Go to http://foilphp.it/ for documentation on Foil templates
     */
    static public function factory() {
        if(!self::$engine) {
            self::$engine = static::createEngine();
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
     * Returns folders
     */
    static public function getFolders() {
        self::factory();
        return self::$engine->finder()->dirs();
    }

    static public function loadExtension(ExtensionInterface $extension) {
        self::$engine->loadExtension($extension);
    }

    /**
     * Renders a template view
     */
    static public function render($view, $vars = [], $fire_event = true) {
        if($fire_event) {
            $event = App::dispatch(AppEvents::VIEW_RENDER, new FilterViewEvent($view, $vars));
            $view = $event->getView();
            $vars = $event->getVars();
        }
        //por compatibilidad
        return self::getEngine()->render($view, $vars + array('vars' => $vars)); //por compatibilidad
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

        self::$theme = $theme;

        // Special theme JSON, will not be searched
        if($theme == 'JSON') return;

        $folders = [];
        // Compiled views by grunt
        if(is_dir(GOTEO_WEB_PATH . "templates/$theme"))
            $folders['compiled'] = GOTEO_WEB_PATH . "templates/$theme";

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
    }

    /**
     * Returns the current used theme
     */
    static public function getTheme() {
        return self::$theme;
    }
}
