<?php

namespace Goteo\Core {
    
    abstract class Model {
        
        protected static 
            $__connections = array();
                
        /**
         *
         * @param   type $key
         * @return  static
         */                
        abstract static public function get ();
        
        public function __construct () {
            
            if (\func_num_args() >= 1) {
                
                $data = \func_get_arg(0);
                
                if (is_array($data) || is_object($data)) {
                    foreach ($data as $k => $v) {
                        $this->$k = $v;
                    }
                }
                
            }
            
        }
        
        /**
         * @return  bool
         */
        abstract public function save ();
                
        /**
         * @return bool
         */
        abstract public function validate ();
        
        public static function query ($query, $params = null) {
            
        }
        
    }
    
}