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
    
    class CheckBoxes extends Named {
                
        public
            $options = array();
        
        public function __construct ($data = array()) {
            
            parent::__construct($data);                        
            
            if (!is_array($this->options)) {
                $this->options = array();
            }
            
            $i = 0;
            
            foreach ($this->options as $value => &$option) { 
                
                $i++;
                
                if (!($option instanceof CheckBox)) {
                    
                    if (is_string($option)) {
                    
                        $option = new CheckBox(array(
                            'value' => $value,
                            'label' => (string) $option,
                        ));

                    } else if (is_array($option)) {

                        $option = new CheckBox($option);

                    } else {
                        continue;
                    }
                    
                }                                 
                
                if (isset($this->value)) {
                    $option->checked = ($option->value == $this->value); 
                }
                
                $option->name = $this->name;
                
                if (!isset($option->id)) {
                    $option->id = $this->id . "-{$i}";
                }
                
            }                   
            
        }
        
    }
    
}