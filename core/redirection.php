<?php

namespace Goteo\Core {
    
    class Redirection extends Error {
        
        const
            TEMPORARY   = 302,
            PERMANENT   = 301;
        
        private $url;
        
        public function __construct ($url, $code = self::TEMPORARY) {
            
            $this->url = $url;
            parent::__construct($code);
            
        }
        
        public function getURL () {
            return $this->url;
        }
        
    }
    
    
}