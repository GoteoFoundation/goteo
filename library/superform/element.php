<?php

namespace Goteo\Library\SuperForm {
    
    abstract class Element implements \Goteo\Core\Resource {
        
        public 
            $label,
            $id,
            $class,
            $children = array();
                        
        public function __construct ($data = array()) {
            
            foreach ($data as $k => &$v) {
                
                switch ($k) {
                    
                    case 'children':
                        $this->addChildren($v);
                        break;                    
                    
                    default:
                        if (property_exists($this, $k)) {
                            $this->$k = $v;
                        }
                                            
                }
                
            }
            
            if (!isset($this->view)) {                
                $viewPath = strtolower(str_replace('\\', '/', trim(substr(get_called_class(), strlen(__CLASS__)), '\\')));                
                $this->view = "library/superform/element/{$viewPath}/view.html.php";                
            }
            
            
        }
        
        public function addChildren ($elements) {
            
            foreach (func_get_args() as $child) {
                        
                if (!($child instanceof self)) {
                    
                    if (is_array($child)) {
                        
                        $cls = '\\' . __CLASS__;
                        
                        if (isset($child['type'])) {
                            $cls  .= '\\' . $child['type'];
                        } 
                                                                        
                        if (class_exists($cls)) {                        
                            $child = new $cls($child);
                            continue;
                        } else {
                            throw new Exception;
                        }
                        
                    } else {
                        throw new Exception;
                    }                    
                }
                
                $this->children[] = $child;
                
            }
            
        }                                        
        
        public function getInnerMarkup () {            
            return (string) (new View($this->view));            
        }
        
        public function __toString () {           

            $markup = '';
            
            if (isset($this->label)) {
                $markup .= PHP_EOL . '<h4>' . htmlspecialchars($this->label) . '</h4>';
            }
            
            if (isset($this->hint)) {
                $markup .= PHP_EOL . '<div class="hint">' . $this->hint . '</div>';
            }
            
            $markup .= PHP_EOL . '<div class="contents">' . $this->getInnerMarkup() . '</div>';

            
        }
        
    }
    
}