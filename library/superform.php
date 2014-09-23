<?php

namespace Goteo\Library {

    use Goteo\Core\View;

    class SuperForm implements \Goteo\Core\Resource, \Goteo\Core\Resource\MIME {

        public
            $title,
            $hint,
            $action = '',
            $method = 'post',
            $class,
            $id,
            $elements = array(),
            $footer = array(),
            $level = 1,
            $autoupdate = true; //si es false, no se pondra la clase autoupdate en el tag <form> y los elementos no autoactualizaran el formulario (pero continua siendo posible usara manualmente $().superform(); )

        public static function uniqId ($prefix) {
            return $prefix . substr(md5(uniqid($prefix, true)), 0, 5);
        }

        public static function getChildren ($children, $level) {

            $elements = array();

            if (is_array($children)) {

                foreach ($children as $k => $element) {

                    if (!($element instanceof SuperForm\Element)) {

                        if (!is_array($element)) {
                            throw new SuperForm\Exception;
                        }

                         if (empty($element['type'])) {
                             $element['type'] = '';
                         }

                        $cls = __NAMESPACE__ . rtrim("\\SuperForm\\Element\\{$element['type']}", '\\');

                        if (!class_exists($cls)) {
                            throw new SuperForm\Exception;
                        }

                        if (!isset($element['id'])) {
                            $element['id'] = $k;
                        }

                        $element['level'] = $level;

                        $element = new $cls($element);
                    }

                    $elements[] = $element;
                }
            } else {
                throw new SuperForm\Exception;
            }

            return $elements;

        }
        public function __construct ($data = array()) {

            if (is_array($data) || is_object($data)) {

                foreach ($data as $k => $v) {

                    switch ($k) {

                        case 'autoupdate':
                            $this->autoupdate = (bool) $autoupdate;
                            break;

                        case 'elements':
                            $this->elements = $v;
                            break;

                        case 'footer':
                            $this->footer = $v;
                            break;

                        default:

                            if (property_exists($this, $k)) {
                                $this->$k = $v;
                            }
                            break;

                    }

                }

                $this->elements = static::getChildren($this->elements, $this->level + 1);
                $this->footer = static::getChildren($this->footer, $this->level + 1);

            }

            if (!isset($this->id)) {
                $this->id = static::uniqId('superform-');
            }

        }

        public function __toString () {

            return (string) (new View('library/superform/view/superform.html.php', $this));

        }

        public function getMIME () {
            return 'text/html';
        }

    }


}