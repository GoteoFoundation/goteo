<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\SuperForm {

    use Goteo\Library\SuperForm,
        Goteo\Core\View;

    class Element implements \ArrayAccess, \Goteo\Core\Resource {

        public
            $id,
            $type,
            $title,
            $class = '',
            $hint,
            $required = false,
            $ok = array(),
            $errors = array(),
            $children = array(),
            $level = 2,
            $view, // legacy views
            $content, // direct string content
            $data = array();

        public function offsetGet ($name) {
            return isset($this->$name) ? $this->name : null;
        }

        public function offsetSet ($name, $value) {
            $this->$name = $value;
        }

        public function offsetExists ($name) {
            return property_exists($this, $name);
        }

        public function offsetUnset ($name) {
            unset($this->$name);
        }

        public function __construct ($data = array()) {

            foreach ($data as $k => $v) {
                switch ($k) {

                    case 'children':
                        $this->children = $v;
                        break;

                    default:
                        if (property_exists($this, $k)) {
                            $this->$k = $v;
                        }
                }

            }

            $this->children = SuperForm::getChildren($this->children, $this->level + 1);

            $this->type = $this->getType();

            if (!isset($this->view)) {
                $this->view = $this->getView();
            }

            // si hay ok no hay error
            if (!empty($this->ok)) $this->errors = array();
        }

        public function getView () {
            $viewPath = strtolower(str_replace('\\', '/', trim(substr(get_called_class(), strlen(__CLASS__)), '\\')));
            // $viewPath = strtolower(basename(str_replace('\\', '/', get_called_class())));
            if($viewPath) return "superform/element/{$viewPath}.html.php";
        }

        public function getType () {
            $type = str_replace('\\', '-', substr(get_called_class(), strlen(__CLASS__) + 1));
            // $type = basename(str_replace('\\', '/', get_called_class()));
            return $type;
        }

        public function getInnerHTML () {

            // if a direct string content is defined
            if ($this->content) {
                return $this->content;
            }
            elseif ($this->view) {
                if ($this->view instanceof View) {
                    return $this->view->render();
                } else {
                    return View::get($this->view, $this);
                }
            }

            return '';
        }

        static public function get($data = array()) {
            return (new Element($data))->render();
        }

        public function render() {
            return View::get('superform/element.html.php', array('element' => $this));
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
