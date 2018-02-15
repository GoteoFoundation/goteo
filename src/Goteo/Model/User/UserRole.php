<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\User;

use Goteo\Application\Role;
use Goteo\Application\Exception\RoleException;
use Goteo\Model\User;

/**
 * Class UserRole
 * @package Goteo\Model\User
 *
 * Class for handeling user permissions
 *
 */
class UserRole extends \ArrayObject
{
    public $user_id;

    protected $permissions = array();

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        if(!$this->hasRole('user')) {
            $this->addRole('user');
        }
    }

    /**
     * Gets all roles for a user
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public static function getRolesForUser($user_id) {
        if($user_id instanceOf User) $user_id = $user_id->id;

        $sql = "SELECT * FROM user_role WHERE user_id = ?";

        $roles = [];
        if($query = User::query($sql, $user_id)) {
            if($result = $query->fetchAll(\PDO::FETCH_OBJ)) {
                foreach($result as $ob) {
                    if(Role::roleExists($ob->role_id)) $roles[$ob->role_id] = Role::getRolePerms($ob->role_id);
                }
            }
        }
        return new UserRole($roles);
    }

    /**
     * Adds a role to the user
     */
    public function addRole($role) {
        if(Role::roleExists($role)) {
            $this->offsetSet($role, Role::getRolePerms($role));
        }
        return $this;
    }

    /**
     * Removes a role from the user
     * @param  [type] $role [description]
     * @return [type]       [description]
     */
    public function removeRole($role) {
        if($this->hasRole($role)) {
            if($role === 'user') throw new RoleException("Role [user] cannot be removed");

            $this->offsetUnset($role);
        }
        return $this;
    }

    /**
     * Persists the current object to the database
     */
    public function save(&$errors = []) {
        // TODO
    }

    /**
     * Check if some role is set
     * @return boolean [description]
     */
    public function hasRole($role) {
        return $this->offsetExists($role);
    }

    // check if a permission is set
    public function hasPerm($perm) {
        foreach($this as $role => $permissions) {
            if(in_array($perm, $permissions)) return true;
        }
        return false;
    }
}
