<?php

namespace Goteo\Controller {

    use Goteo\Model;

    class Ws extends \Goteo\Core\Controller {
        
        public function get_faq_order($section) {
            $next = Model\Faq::next($section);

            header ('HTTP/1.1 200 Ok');
            echo $next;
            die;
        }

    }
    
}