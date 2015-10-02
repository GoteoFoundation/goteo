<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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
                            throw new SuperForm\Exception("Error in SuperForm, Element is not an Array [" . gettype($element) . "]", 1);
                        }

                         if (empty($element['type'])) {
                             $element['type'] = '';
                         }
                         $type = $element['type'];
                         $hack = array('hidden' => 'Hidden', 'html' => 'HTML', 'checkbox' => 'CheckBox', 'checkboxes' => 'CheckBoxes', 'datebox' => 'DateBox', 'file' => 'File', 'group' => 'Group', 'hidden' => 'Hidden', 'named' => 'Named', 'password' => 'Password', 'radio' => 'Radio', 'radios' => 'Radios', 'select' => 'Select', 'slider' => 'Slider', 'submit' => 'Submit', 'textarea' => 'TextArea', 'textbox' => 'TextBox', 'button' => 'Button');
                         if($hack[$type]) {
                            $type = $hack[$type];
                         }

                        $cls = __NAMESPACE__ . rtrim("\\SuperForm\\Element\\$type", '\\');

                        if (!class_exists($cls)) {
                            throw new SuperForm\Exception("Namespace Error in SuperForm [$cls]\n", 1);
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
                throw new SuperForm\Exception("Error in SuperForm, Children is not an Array [" . gettype($children) ."]");
            }

            return $elements;

        }

        public function __construct ($data = array()) {

            if (is_array($data) || is_object($data)) {

                foreach ($data as $k => $v) {

                    switch ($k) {

                        case 'autoupdate':
                            $this->autoupdate = (bool) $v;
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

        /**
         * Convenient method to shortcut:
         *     echo (new SuperForm())->render();
         * Change by:
         *     echo SuperForm::get();
         *
         * @param  [type] $view [description]
         * @return [type]       [description]
         */
        static public function get($data = array()) {
            return (new SuperForm($data))->render();
        }

        public function render () {

            return View::get('superform/superform.html.php', $this);

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

        public function getMIME () {
            return 'text/html';
        }

    }


}
