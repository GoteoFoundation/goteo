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
            $elements = array();            
        
        public function __construct ($data = array()) {
            
            if (is_array($data) || is_object($data)) {
                
                foreach ($data as $k => $v) {
                    
                    switch ($k) {
                        
                        case 'elements':
                            if (is_array($v)) {
                                
                                foreach ($v as $k => $element) {
                                    
                                    if (!($element instanceof SuperForm\Element)) {
                                        
                                        if (!is_array($element) || empty($element['type'])) {
                                            throw new SuperForm\Exception;
                                        }                                        
                                                                                
                                        $cls = __NAMESPACE__ . "\\SuperForm\\Element\\{$element['type']}";
                                        
                                        if (!class_exists($cls)) {
                                            throw new SuperForm\Exception;
                                        }                                                                                
                                        
                                        $element = new $cls($element);                                        
                                    }
                                    
                                    if (!isset($element->id)) $element->id = $k;                                    
                                                                                                            
                                    $this->elements[] = $element;
                                    
                                }
                                
                            } else {
                                throw new SuperForm\Exception;
                            }
                            break;
                        
                        default:
                            
                            if (property_exists($this, $k)) {
                                $this->$k = $v;
                            }
                            break;
                        
                    }
                    
                }
                
            }
            
            if (!isset($this->id)) {
                $this->id = 'superform-' . substr(md5(uniqid('superform', true)), 0, 5);
            }
            
        }
        
        public function __toString () {
            
            return (string) (new View('library/superform/view.html.php', $this));
            
        }        
        
        public function getMIME () {
            return 'text/html';
        }
        
    }
    
    
}