<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\SuperForm\Element {
    
    class Radios extends Named {
                
        public
            $name,            
            $options = array(),
            $value;
        
        public function __construct ($data = array()) {
            
            parent::__construct($data);
            
            if (!is_array($this->options)) {
                $this->options = array();
            }            
            
            foreach ($this->options as $value => &$option) { 
                
                if (is_string($option)) {
                    
                    $option = new Radio(array(
                        'value' => $value,
                        'label' => (string) $option,
                        'name'  => $this->name
                    ));
                    
                } else if (is_array($option)) {
                    
                    $option = new Radio($option + array('name' => $this->name, 'level' => $this->level + 1));
                    
                } else {
                    
                    continue;
                    
                }
                
                if (isset($this->value)) {
                    $option->checked = ($option->value == $this->value); 
                }                
                
                $option->id = $this->id . '-' . $option->value;
                
            }            
            
        }
        
    }
    
}