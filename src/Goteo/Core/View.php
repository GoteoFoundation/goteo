<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core {

    class View extends \ArrayObject implements Resource, Resource\MIME {

        private
            $file, $vars = array();

        protected static $views_path = array();


        public function __construct ($file, $vars = null) {

            if (!is_string($file)) {
                throw new View\Exception("Unknow file [$file]", 1);
            }

            $this->file = $file;

            if (isset($vars)) {
                $this->set($vars);
            }
        }

        static public function addViewPath($path) {
            if(is_dir($path)) {
                if(substr($path,-1, 1) !== '/' ) $path .= '/';
                if(!in_array($path, self::$views_path)) self::$views_path[] = $path;
            }
        }

        public function set ($var) {

            if (is_array($var) || is_object($var)) {
                foreach ($var as $name => $value) {
                    $this[$name] = $value;
                    $this->vars[$name] = $value;
                }
            } else if (is_string($var) && func_num_args() >= 2) {
                $this[$var] = func_get_arg(1);
                $this->vars[$name] = func_get_arg(1);
            } else {
                throw new View\Exception("Error args number in var [$var]", 1);
            }

        }

        public function getMIME () {

            // @TODO Adivinar por la extensión
            return 'text/html';
        }

        public function getViewPath($exception = true) {
            foreach(self::$views_path as $path) {
                if(is_file($path . $this->file)) return $path . $this->file;
            }
            if($exception) {
                throw new View\Exception("View [{$this->file}] not found!", 1);
            }
            return false;
        }

        public function render() {
            try {
                $file = $this->getViewPath();
                ob_start();
                $vars = $this;
                include $file;

                return ob_get_clean();
            }
            catch(\Goteo\Core\Redirection $e) {
                // echo "La vista [$view] lanza una exception de redireccion!\nEsto no deberia hacerse aqui!\n";
                return ob_get_clean();
            }
            // Catch not found models
            catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
                // echo "La vista [$view] lanza una exception de modelo!\nEsto no deberia hacerse aqui!";
                ob_get_clean();
                throw $e;
            }
            catch(\Exception $e) {
                ob_get_clean();
                throw new View\Exception("Error in Included view [{$this->file}]\nView Exception Message:\n" . $e->getMessage()."\n", 1);
            }
            // catch(View\Exception $e) {
            //     throw new View\Exception("Error in View [{$this->file}]\nView Exception Message:\n" . $e->getMessage()."\n", 1);
            //     // print($e);
            //     // die($e->getMessage());
            // }

        }

        /**
         * Convenient method to shortcut:
         *     echo (new View('my_view.html.php'))->render();
         * Change by:
         *     echo View::get('my_view.html.php');
         *
         * @param  [type] $view [description]
         * @return [type]       [description]
         */
        static public function get($view, $vars = null) {
            return (new View($view, $vars))->render();
        }

        /**
         * This method should be avoided, cannot throw an exception
         * @return string [description]
         */
        public function __toString () {
            //__toString method shall not throw an Exception
            try {
                return $this->render();
            }
            catch(\Exception $e) {
                // print($e);
                die($e->getMessage());
            }
        }


    }
}
