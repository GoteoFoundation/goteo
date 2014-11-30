<?php

namespace Goteo\Library\SuperForm\Element {
    
    class HTML extends \Goteo\Library\SuperForm\Element {
        
        public 
            $view = false,
            $html = '';
        
         public function getInnerHTML () {             
             return $this->html;
        }                
        
    }
    
}