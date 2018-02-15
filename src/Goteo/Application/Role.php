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
    private static $roles = [];

    // This is overwriten by Config using file Resources/roles.yml
    // Plugins can add their roles as well
    static protected  $roles_available = [];

    public function addRole($role_id, array $permissions = []) {
        static::$roles[$role_id] = $permissions;
    }

    public function addRolesFromArray(array $roles) {
        $extends = [];
        foreach($roles as $role_id => $parts) {
            if($parts['extends']) {
                $extends[$role_id] = $parts['extends'];
            }

            static::addRole($role_id, $parts['perms'] ? $parts['perms'] : []);
        }

        foreach($extends as $role => $parent) {
            if(isset(static::$roles[$role]) && static::$roles[$parent]) {
                static::$roles[$role] = array_merge(static::$roles[$parent], static::$roles[$role]);
            }
        }
    }

    public function getRoles() {
        return static::$roles;
    }
}
