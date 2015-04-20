<?php

namespace Goteo\Application;

use Foil;

class View {
    static protected $engine;
    static protected $theme = 'default';

    static public function factory($path) {
        if(!self::$engine) {
            self::$engine = Foil\engine(['folders' => [$path]]);
        }
    }

    static public function addFolder($path, $theme = null,  $fallback = false) {
        if(is_dir($path)) {
            self::factory($path);
            self::$engine->addFolder($path, $theme,  $fallback);
        }
    }

    static public function render($view, $vars = null) {
        self::$engine->vars = $vars;
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
