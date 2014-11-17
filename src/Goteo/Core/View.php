<?php

namespace Goteo\Core {

    class View extends \ArrayObject implements Resource, Resource\MIME {

        private
            $file;

        public function __construct ($file, $vars = null) {

            if (!is_file($file)) {
                //TODO: no deberia ser una excepcion????
                die("La vista `{$file}` no existe. ");
            }

            $this->file = $file;

            if (isset($vars)) {
                $this->set($vars);
            }

        }

        public function set ($var) {

            if (is_array($var) || is_object($var)) {
                foreach ($var as $name => $value) {
                    $this[$name] = $value;
                }
            } else if (is_string($var) && func_num_args() >= 2) {
                $this[$var] = func_get_arg(1);
            } else {
                throw new View\Exception;
            }

        }

        public function getMIME () {

            // @todo Adivinar por la extensión
            return 'text/html';
        }

        public function __toString () {
            try {
                ob_start();

                include $this->file;

                return ob_get_clean();
            } catch(Exception $e) {
                print($e);
                die($e->getMessage());
            }

        }


    }
}
