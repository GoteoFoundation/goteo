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
    // Plugins can add their roles as well using addRole and addRolePerms
    private static $roles = [];
    private static $levels = [];

    //
    private static $permission_tables = [];

    public static function addRole($role_id, array $permissions = [], $level = 0) {
        static::$roles[$role_id] = $permissions;
        static::$levels[$role_id] = $level;
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
        $level = 0;
        foreach($roles as $role_id => $parts) {
            if(isset($parts['extends'])) {
                $extends[$role_id] = $parts['extends'];
            }
            if(isset($parts['level'])) {
                $level = (int) $parts['level'];
            }

            static::addRole($role_id, isset($parts['perms']) ? $parts['perms'] : [], $level);
            $level++;
        }

        foreach($extends as $role => $parent) {
            if(isset(static::$roles[$role]) && static::$roles[$parent]) {
                static::addRolePerms($role, static::$roles[$parent]);
            }
        }
    }

    public static function addPerm($perm_id, $model = null, $relational = null) {
        if(!$relational || !is_array($relational)) {
            $relational = [];
        }
        if(!$model || !is_array($model)) {
            $model = [];
        }
        if(!isset($model['table'])) $model['table'] = null;
        if(!isset($model['table_id'])) $model['table_id'] = null;
        if(!isset($model['query_id'])) $model['query_id'] = $model['table_id'];
        if(!isset($relational['table'])) $relational['table'] = null;
        if(!isset($relational['user_id'])) $relational['user_id'] = null;
        if(!isset($relational['table_id'])) $relational['table_id'] = null;

        static::$permission_tables[$perm_id] = ['model' => $model, 'relational' => $relational];
    }

    public static function addPermsFromArray(array $permissions) {
        foreach($permissions as $perm_id => $parts) {
            static::addPerm($perm_id, $parts['model'], $parts['relational']);
        }
    }

    public static function getRoles() {
        return static::$roles;
    }

    public static function getPerms() {
        return static::$permission_tables;
    }

    public static function roleExists($role_id) {
        return isset(static::$roles[$role_id]);
    }

    public static function getRolePerms($role_id) {
        return isset(static::$roles[$role_id]) ? static::$roles[$role_id] : [];
    }

    public static function getRoleLevel($role_id) {
        return isset(static::$levels[$role_id]) ? static::$levels[$role_id] : 0;
    }

    public static function roleHasPerm($role_id, $permission) {
        return isset(static::$roles[$role_id]) && in_array($permission, static::$roles[$role_id]);
    }
}
