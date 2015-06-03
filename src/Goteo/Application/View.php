<?php

namespace Goteo\Application;

use Foil;

class View {
    static protected $engine;
    static protected $theme = 'default';

    static public function factory($path, $theme = 'default') {
        if(!self::$engine) {
            self::$engine = Foil\engine(['folders' => [$theme => $path]]);
        }
    }

    static public function addFolder($path, $theme = null) {
        if(is_dir($path)) {
            self::factory($path, $theme);
            self::$engine->addFolder($path, $theme);
        }
    }

    static public function render($view, $vars = null) {
        if(!self::$engine) {
            self::$engine = Foil\engine();
        }
        //por compatibilidad
        // self::$engine->vars = $vars;
        return self::$engine->render($view, $vars + array('vars' => $vars)); //por compatibilidad
    }

    static public function get($view, $vars = null) {
        return self::render(self::getTheme() . '::' . $view, $vars);
    }

    static public function getEngine() {
        return self::$engine;
    }

    static public function setTheme($theme) {
        return self::$theme = $theme;
    }

    static public function getTheme() {
        return self::$theme;
    }
}
