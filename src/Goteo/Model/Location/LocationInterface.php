<?php

namespace Goteo\Model\Location;

interface LocationInterface {
    public static function get($id);
    public function validate(&$errors = array());
    public function save(&$errors = array());
    public function delete(&$errors = array());
    public static function setProperty($user, $prop, $value, &$errors = array());
    public static function getProperty($id, $prop, &$errors = array());
}
