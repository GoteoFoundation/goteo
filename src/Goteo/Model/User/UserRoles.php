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
 * Class UserRoles
 * @package Goteo\Model\User
 *
 * Class for handeling user permissions
 *
 */
class UserRoles extends \ArrayObject
{
    protected $user;

    public function __construct(User $user, $roles = []) {
        $this->user = $user;
        parent::__construct($roles);
        if(!$this->hasRole('user')) {
            $this->addRole('user');
        }
    }

    /**
     * Gets all roles for a user
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public static function getRolesForUser(User $user) {

        $sql = "SELECT * FROM user_role WHERE user_id = ?";

        $roles = [];
        if($query = User::query($sql, $user->id)) {
            if($result = $query->skipCache()->fetchAll(\PDO::FETCH_OBJ)) {
                foreach($result as $ob) {
                    if(Role::roleExists($ob->role_id)) $roles[$ob->role_id] = Role::getRolePerms($ob->role_id);
                }
            }
        }
        return new UserRoles($user, $roles);
    }

    /**
     * Adds a role to the user
     */
    public function addRole($role) {
        if(Role::roleExists($role)) {
            if($role === 'root') throw new RoleException("Role [root] cannot be added");

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
        $values = [':user_id' => $this->user->id];
        $inserts = '';
        $i = 0;
        foreach($this as $role => $permissions) {
            if($role !== 'user') {
                $values[":role_$i"] = $role;
                $inserts[] = ":role_$i, :user_id";
                $i++;
            }
        }
        $sql = "DELETE FROM user_role WHERE user_id = ?";
        // die(\sqldbg($sql, [$this->user->id]));
        User::query($sql, $this->user->id);

        if(!$inserts) {
            return true;
        }
        $sql = "INSERT INTO user_role (role_id, user_id) VALUES (" . implode(',(', $inserts) .")";
        try {
            User::query($sql, $values);
            return true;
        } catch(\PDOException $e) {
            $errors[] = $e->getMessage();
        }
        return false;
    }

    /**
     * Check if some role is set
     * @return boolean [description]
     */
    public function hasRole($role) {
        return $this->offsetExists($role);
    }

    // check if a permission is set
    public function hasPerm($perms) {
        if(!is_array($perms)) $perms = [$perms];
        foreach($this as $role => $permissions) {
            if(array_intersect($perms, $permissions)) return true;
        }
        return false;
    }
}
