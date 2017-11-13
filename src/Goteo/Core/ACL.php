<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core;

use Goteo\Model\User;
use Goteo\Application\Session;

/**
 * This class may be deprectaded
 * Used for compatiblity with old route system and for
 * controllers that does not handle auth by themselves
 */
class ACL {
    protected static $resources = array(
        //  role,      URL prefix          Active
        '/community' => ['user'],

        '/message' => ['user'],
        '/message/edit' => ['superadmin'],
        '/message/delete' => ['superadmin'],

        '/review' => ['checker'],

        '/sacaexcel' => ['admin', 'superadmin', 'root'],

        '/manage' => ['manager']
        );

    public static function check ($url, $user = null) {

        if(is_null($user)) {
            $user = Session::getUser();
        }

        // URL route valid by default
        $ok = true;

        $roles = [''];

        foreach(self::$resources as $prefix => $valid_roles) {
            // if prexix exists, URL will be invalid by default
            if(strpos($url, $prefix) === 0) {
                $ok = false;
                if($user instanceOf User) {
                    // check if has the role
                    if(array_intersect($valid_roles, array_keys($user->roles))) {
                        $ok = true;
                    }
                    // print_r($valid_roles);print_r(array_keys($user->roles));print_r(array_intersect($valid_roles, array_keys($user->roles)));die;

                }
                break;
            }
        }

        return $ok;

    }

}

