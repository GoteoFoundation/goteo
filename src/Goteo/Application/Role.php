<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

use Goteo\Application\Exception\RoleException;

class Role {
    // This is set by Config using file Resources/roles.yml
    // Plugins can add their roles as well
    private static $roles = [];

    public static function addRole($role_id, array $permissions = []) {
        static::$roles[$role_id] = $permissions;
    }

    public static function addRolePerms($role_id, $permissions) {
        if(!is_array($permissions)) $permissions = [$permissions];

        if(!isset(static::$roles[$role_id])) throw new RoleException("Role [$role_id] does not exist!");

        static::$roles[$role_id] = array_merge($permissions, static::$roles[$role_id]);
    }

    /**
     * Sets a full collection of roles from an array styled as Resources/roles.yml
     */
    public static function addRolesFromArray(array $roles) {
        $extends = [];
        foreach($roles as $role_id => $parts) {
            if($parts['extends']) {
                $extends[$role_id] = $parts['extends'];
            }

            static::addRole($role_id, $parts['perms'] ? $parts['perms'] : []);
        }

        foreach($extends as $role => $parent) {
            if(isset(static::$roles[$role]) && static::$roles[$parent]) {
                static::addRolePerms($role, static::$roles[$parent]);
            }
        }
    }

    public static function getRoles() {
        return static::$roles;
    }

    public static function roleExists($role_id) {
        return isset(static::$roles[$role_id]);
    }

    public static function getRolePerms($role_id) {
        return isset(static::$roles[$role_id]) ? static::$roles[$role_id] : [];
    }

    public static function roleHasPerm($role_id, $permission) {
        return isset(static::$roles[$role_id]) && in_array($permission, static::$roles[$role_id]);
    }
}
