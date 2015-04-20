<?php

namespace Goteo\Application;

use League\Plates\Engine as Plates;

class View {
    static protected $plates;
    static protected $theme = 'main';

    static public function factory($path) {
        if(!self::$plates instanceOf Plates) {
            self::$plates = new Plates($path);
        }
    }

    static public function addFolder($theme, $path, $fallback = false) {
        self::factory($path);
        self::$plates->addFolder($theme, $path, $fallback);
    }

    static public function render($view, $vars = null) {
        // try {
            // print_r(self::$plates);
            $engine = self::$plates->make($view);
            $engine->vars = $vars;
            return $engine->render($vars + array('vars' => $vars)); //por compatibilidad
        // }
        // catch(\LogicException $e) {
        // //     // print_r(self::$plates);die;
        //     return $e->getMessage();
        // }
    }

    static public function get($view, $vars = null) {
        return self::render(self::getTheme() . '::' . $view, $vars);
    }

    static public function getEngine() {
        return self::$plates;
    }

    static public function setTheme($theme) {
        return self::$theme = $theme;
    }

    static public function getTheme() {
        return self::$theme;
    }
}
