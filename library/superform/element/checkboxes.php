<?php

namespace Goteo\Library\SuperForm\Element {
    
    class CheckBoxes extends Named {
                
        public
            $options = array();
        
        public function __construct ($data = array()) {
            
            parent::__construct($data);                        
            
            if (!is_array($this->options)) {
                throw new Exception;
            }
            
            foreach ($this->options as $value => &$option) { 
                
                if (is_string($option)) {
                    
                    $option = new CheckBox(array(
                        'value' => $value,
                        'label' => (string) $option,
                        'name'  => $this->name,                        
                    ));
                    
                } else if (is_array($option)) {
                    
                    $option = new CheckBox($option + array('name' => $this->name));
                    
                }                
                
            }
            
            
        }
        
    }
    
}