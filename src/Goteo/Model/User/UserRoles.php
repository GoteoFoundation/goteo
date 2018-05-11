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
use Goteo\Library\Text;

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

    static public function getAllRoleNames() {
        $roles = [];
        foreach(Role::getRoles() as $role => $perms) {
            if($role === 'root') continue;
            $roles[$role] = static::getRoleName($role);
        }
        return $roles;
    }

    static public function getRoleName($key) {
        return Text::get("role-name-$key");
    }

    static public function getPermName($key) {
        return Text::get("role-perm-name-$key");
    }

    static public function getRolePerms($role_id) {
        $perms = [];
        foreach(Role::getRolePerms($role_id) as $perm) {
            $perms[$perm] = static::getPermName($perm);
        }
        return $perms;
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
        if(Role::roleExists($role) && !$this->offsetExists($role)) {
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
     * Returns an array of all roles names for the user
     */
    public function getRoleNames() {
        $roles = [];
        foreach($this as $role => $perms) {
            $roles[$role] = static::getRoleName($role);
        }
        return $roles;
    }

    /**
     * Persists the current object to the database
     */
    public function save(&$errors = []) {
        $values = [':user_id' => $this->user->id];
        $inserts = [];
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
        $sql = "INSERT INTO user_role (role_id, user_id) VALUES (" . implode('), (', $inserts) .")";
        // die(\sqldbg($sql, $values));
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
    public function hasRole($roles) {
        if(!is_array($roles)) $roles = [$roles];
        foreach($roles as $role) {
            if($this->offsetExists($role)) return true;
        }
        return false;
    }


    /**
     * gets the level of a role
     * @return boolean [description]
     */
    public function getLevel($role) {
        if($this->offsetExists($role)) {
            return Role::getRoleLevel($role);
        }
        return 0;
    }

    /**
     * Checks if current user has a role with greater level than the argument
     * @param  [type] $role [description]
     * @return [type]       [description]
     */
    public function greaterThan($role) {
        foreach(array_keys((array)$this) as $r) {
            if(Role::getRoleLevel($r) > Role::getRoleLevel($role)) return true;
        }
        return false;
    }

    /**
     * Same as greaterThan but allows the same role
     * @param  [type] $role [description]
     * @return [type]       [description]
     */
    public function atLeast($role) {
        foreach(array_keys((array)$this) as $r) {
            if(Role::getRoleLevel($r) >= Role::getRoleLevel($role)) return true;
        }
        return false;
    }

    // check if a permission is set
    public function hasPerm($perms, $model_val = null) {
        if(!is_array($perms)) $perms = [$perms];
        foreach($this as $role => $permissions) {
            foreach($perms as $perm) {

                if(in_array($perm, $permissions)) {
                    if($model_val) {
                        return $this->userAssignedInModel($perm, $model_val);
                    }
                    return true;
                }
            }
        }
        return false;
    }

    protected function userAssignedInModel($perm, $model_val) {
        $perms = Role::getPerms();

        if(!isset($perms[$perm])) return false;
        $table = $perms[$perm]['model']['table'];
        $table_id = $perms[$perm]['model']['table_id'];
        $query_id = $perms[$perm]['model']['query_id'];
        $relational_table =  $perms[$perm]['relational']['table'];
        $relational_user_id =  $perms[$perm]['relational']['user_id'];
        $relational_table_id =  $perms[$perm]['relational']['table_id'];
        if(!$relational_table || !$relational_table_id || !$relational_user_id) return false;

        // If a join table exists, get the id first
        if($table) {
            $join = "JOIN `$table` b ON a.`$relational_table_id` = b.`$table_id`";
            $search = "b.`$query_id` = :model_id";
        } else {
            $search = "a.`$relational_table_id` = :model_id";
            $join = '';
        }

        $values = [':user_id' => $this->user->id, ':model_id' => $model_val];
        $sql = "SELECT COUNT(*) as total
            FROM `$relational_table` a
            $join
            WHERE
            a.`$relational_user_id` = :user_id AND
            $search";

        // echo \sqldbg($sql, $values) . "\n";
        if($ob = User::query($sql, $values)->fetchObject()) {
            return $ob->total > 0;
        }
        return false;
    }

    /**
     * Assings this user to a relational table related with a permission
     * @param  [type] $perm      [description]
     * @param  [type] $model_val [description]
     * @return [type]            [description]
     */
    public function assignUserPerm($perm, $model_val) {
        $perms = Role::getPerms();
        if(!isset($perms[$perm])) throw new RoleException("Permission [$perm] not defined!");

        $table = $perms[$perm]['model']['table'];
        $table_id = $perms[$perm]['model']['table_id'];
        $query_id = $perms[$perm]['model']['query_id'];
        $relational_table =  $perms[$perm]['relational']['table'];
        $relational_user_id =  $perms[$perm]['relational']['user_id'];
        $relational_table_id =  $perms[$perm]['relational']['table_id'];

        if(!$relational_table || !$relational_table_id || !$relational_user_id) {
            throw new RoleException("table, user_id or table_id not defined for permission [$perm]");
        }

        // If a join table exists, get the id first
        if($table) {
            $values = [':model_id' => $model_val];
            $sql = "SELECT `$table_id` AS id FROM `$table` WHERE `$query_id` = :model_id LIMIT 1";
            // print(\sqldbg($sql, $values));

            if($ob = User::query($sql, $values)->fetchObject()) {
                $model_val = $ob->id;
            } else {
                throw new RoleException("ID [$model_val] not found in table [$table]");
            }
        }

        $values = [':model_id' => $model_val, ':user_id' => $this->user->id];
        $insert =  ["`$relational_user_id`" => ':user_id', "`$relational_table_id`" => ':model_id'];
        $update = ["`$relational_user_id` = :user_id", "`$relational_table_id` = :model_id"];

        $sql = "INSERT INTO `$relational_table`
            (" . implode(', ', array_keys($insert)) . ")
            VALUES (" . implode(', ', $insert) . ")
            ON DUPLICATE KEY UPDATE " . implode(', ', $update);
        // print(\sqldbg($sql, $values)."\n");
        User::query($sql, $values);
        return $this;
    }
}
