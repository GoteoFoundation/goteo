<?php

namespace Goteo\Library\SuperForm {
    
    use Goteo\Library\SuperForm,
        Goteo\Core\View;
    
    class Element extends \ArrayObject implements \Goteo\Core\Resource {
                
        public            
            $id,
            $type,
            $title,            
            $class = '',
            $hint,
            $required = false,
            $errors = array(),
            $children = array(),
            $extraHTML = '',
            $level = 2,
            $view,
            $data = array();
                        
        public function __construct ($data = array()) {
            
            foreach ($data as $k => $v) {
                
                $this[$k] = $v; // Copio todo para que pueda leerlo la vista
                
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
            
        }
        
        public function getView () {
            $viewPath = strtolower(str_replace('\\', '/', trim(substr(get_called_class(), strlen(__CLASS__)), '\\')));                
            return realpath("library/superform/view/element/{$viewPath}.html.php");            
        }
        
        public function getType () {            
            return strtolower(str_replace('\\', '-', substr(get_called_class(), strlen(__CLASS__) + 1)));
        }        
        
        public function getInnerHTML () {
            
            if ($this->view !== false) {                                
                return (string) (new View($this->view, $this));                
            }
            
            return '';
        }
        
        public function __toString () {                                               
            return (string) (new View('library/superform/view/element.html.php', array('element' =>$this)));
            
        }
        
    }
    
}