<?php

namespace Goteo\Model\User;

class UserLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'user_location';
    public $user;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->user = $this->id;
    }

    public static function get($user) {
        $id = $user;
        if($user instanceOf User) {
            $id = $user->id;
        }
        return parent::get($id);
    }
}
