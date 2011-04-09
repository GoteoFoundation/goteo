<?php

namespace Goteo\Library {
    
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