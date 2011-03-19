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
        abstract static public function get ($id);
        
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
        // abstract public function validate ();
        
        /**
         * Devuelve un objeto de la clase PDOStatement
         * http://www.php.net/manual/es/class.pdostatement.php
         * 
         * @param string $query
         * @param array $params
         */
        public static function query ($query, $params = null) {
			$db = new DB;
			$result = $db->prepare($query);
			
			# @TODO: Modo de recoger los parámetros, forma A o B
			
			# Forma A
			$result->execute($params);

			# Forma B
			//$args = func_get_args();
        	//array_shift($args);
        	//var_dump($args);
			//$result->execute($args);
			
			return $result;
        }
        
    }
    
}