<?php

namespace Goteo\Model {
    
    class Invest extends \Goteo\Core\Model {

        public static function get ($id) {
            return array();
        }

        public function validate (&$errors = array()) { return true; }

        public function save (&$errors = array()) { return true; }

        public function invested ($project) { return 100; }

        public function investors ($project) { return array(); }

        public function invest ($user, $project, $amount) {
            return true;
        }

    }
    
}