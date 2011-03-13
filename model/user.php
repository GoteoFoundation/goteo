<?php

namespace Goteo\Model {
    
    class User extends \Goteo\Core\Model {
        
        public 
        
            // Profile data
            $id,
            $email,
            $image,
            $about,
            $interests = array(),
            $twitter,
            $facebook,
            $linkedIn,
                
            // Personal data
            $firstName,
            $lastName,
            $nif,
            $phone,
            $postalCode,
            $city,
            $country;
        
        public static function get ($id) {
            
            return new static(array(                
                'id'        => 'johndoe',
                'email'     => 'johndoe@example.org',
                'name'      => 'John Doe'
            ));
                                    
        }
        
        public function save () {}
                
    }
    
}