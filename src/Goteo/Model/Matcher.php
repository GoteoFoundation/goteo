<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model;

use Goteo\Application\Config;
use Goteo\Core\Model;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;

/**
 * Matcher Model
 */
class Matcher extends \Goteo\Core\Model {
    public $id,
           $name,
           $logo,
           $lang,
           $amount = 0, // Calculated field with the sum of all pools in the Matcher
           $projects = 0, // Calculated field with the total number of active projects in the Matcher
           $created,
           $modified_at;

    public static $statuses = ['pending', 'accepted', 'active', 'rejected'];

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        if(empty($this->lang)) $this->lang = Config::get('lang');
    }

    /**
     * Get instance of matcher already in the table by action
     * @return [type] [description]
     */
    static public function get($id) {
        if ($query = static::query("SELECT * FROM `matcher` WHERE `id` = ?", $id)) {
            if( $matcher = $query->fetchObject(__CLASS__) )
                return $matcher;
        }
        return null;
    }

    /**
     * Get an instance of a Matcher by one of the projects involved
     * @param  [type] $pid Project or id
     */
    static public function getFromProject($pid, $valid_only = true) {
        if($pid instanceOf Project) $pid = $pid->id;
        $sql = "SELECT a.* FROM `matcher` a
            RIGHT JOIN `matcher_project` b ON a.id = b.matcher_id
            WHERE b.project_id = ?";
        if($valid_only) {
            $sql .= " AND b.status = 'active'";
        }
        if ($query = static::query($sql, $pid)) {
            if( $matcher = $query->fetchObject(__CLASS__) )
                return $matcher;
        }
        return null;
    }

    /**
     * Save.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function save(&$errors = []) {

        if(!$this->validate($errors)) return false;

        if(!$this->created) $this->created = date('Y-m-d');

        $this->amount = $this->calculateAmount();
        $this->projects = $this->calculateProjects();

        try {
            if(empty($this->modified_at))
                $this->dbInsert(['id', 'name', 'logo', 'lang', 'amount', 'projects', 'created']);
            else
                $this->dbUpdate(['name', 'logo', 'lang', 'amount', 'projects', 'created']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving matcher: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Validation
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = []) {
        if(empty($this->id)) $errors[] = 'Empty Id for matcher';
        if(empty($this->name)) $errors[] = 'Empty name for matcher';
        return empty($errors);
    }


    protected function calculateAmount() {
        $sql = "SELECT
                SUM(user_pool.amount) AS total
                FROM user_pool
                RIGHT JOIN matcher_user ON matcher_user.user_id = user_pool.user
                WHERE matcher_user.matcher_id = :match AND matcher_user.pool = 1";
        // echo \sqldbg($sql, [':match' => $this->id]);
        return (int) self::query($sql, [':match' => $this->id])->fetchColumn();
    }


    protected function calculateProjects() {
        $sql = "SELECT
                COUNT(*) AS total
                FROM matcher_project
                WHERE matcher_project.matcher_id = :match AND matcher_project.status = 'active'";
        // echo \sqldbg($sql, [':match' => $this->id]);
        return (int) self::query($sql, [':match' => $this->id])->fetchColumn();
    }


    /**
     * Getters & setters
     */

    /**
     * Use to ensure a valid value of total amount
     * @return [type] [description]
     */
    public function getTotalAmount() {
        if(empty($this->amount)) {
            $this->amount = $this->calculateAmount();
        }
        return $this->amount;
    }

    /**
     * Use to ensure a valid value of total projects
     * @return [type] [description]
     */
    public function getTotalProjects() {
        if(empty($this->projects)) {
            $this->projects = $this->calculateProjects();
        }
        return $this->projects;
    }

    /**
     * Add users
     * @param [type]  $users  user or array of users
     * @param boolean $pool whether to use that user's pool as a source of funding or not
     */
    public function addUsers($users, $pool = true) {
        if(!is_array($users)) $users = [$users];
        $inserts = [];
        $values = [':matcher' => $this->id, ':pool' => (bool) $pool];
        $i = 0;
        foreach($users as $user) {
            if($user instanceOf User) {
                $user = $user->id;
            }
            $inserts[] = "(:matcher, :user$i, :pool)";
            $values[":user$i"] = $user;
            $i++;
        }

        $sql = "REPLACE `matcher_user` (matcher_id, user_id, pool) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating amounts: " . implode("\n", $errors));
            }

        } catch (\PDOException $e) {
            throw new ModelException('Failed to add users: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Delete users
     * @param [type]  $users  user or array of users
     */
    public function removeUsers($users) {
        if(!is_array($users)) $users = [$users];
        $deletes = [];
        $values = [':matcher' => $this->id];
        $i = 0;
        foreach($users as $user) {
            if($user instanceOf User) {
                $user = $user->id;
            }
            $deletes[] = ":user$i";
            $values[":user$i"] = $user;
            $i++;
        }

        $sql = "DELETE FROM `matcher_user` WHERE matcher_id = :matcher AND user_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating amounts: " . implode("\n", $errors));
            }

        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove users: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Add projects
     * @param [type]  $projects  project or array of projects
     * @param boolean $active if active, the project will receive funding
     * @param boolean $banned if banned, the project will no receive funding (rejected by)
     */
    public function addProjects($projects, $status = 'pending') {
        if(!is_array($projects)) $projects = [$projects];
        if(!in_array($status, self::$statuses)) {
            throw new ModelException("Status [$status] not valid");
        }

        $inserts = [];
        $values = [':matcher' => $this->id, ':status' => $status];
        $i = 0;
        foreach($projects as $project) {
            if($project instanceOf project) {
                $project = $project->id;
            }
            $inserts[] = "(:matcher, :project$i, :status)";
            $values[":project$i"] = $project;
            $i++;
        }

        $sql = "REPLACE `matcher_project` (matcher_id, project_id, status) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add projects: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Delete projects
     * @param [type]  $projects  project or array of projects
     */
    public function removeProjects($projects) {
        if(!is_array($projects)) $projects = [$projects];
        $deletes = [];
        $values = [':matcher' => $this->id];
        $i = 0;
        foreach($projects as $project) {
            if($project instanceOf Project) {
                $project = $project->id;
            }
            $deletes[] = ":project$i";
            $values[":project$i"] = $project;
            $i++;
        }

        $sql = "DELETE FROM `matcher_project` WHERE matcher_id = :matcher AND project_id IN (" . implode(', ', $deletes) . ")";
        try {
            self::query($sql, $values);
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating totals: " . implode("\n", $errors));
            }

        } catch (\PDOException $e) {
            throw new ModelException('Failed to remove projects: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * [useUserPool description]
     * @param [type] $user [description]
     * @param [type] $bool [description]
     */
    public function useUserPool($user, $bool) {
        if($user instanceOf User) $user = $user->id;
        $sql = "UPDATE matcher_user SET pool = :pool WHERE matcher_id = :matcher AND user_id = :user";
        try {
            self::query($sql, [':matcher' => $this->id, ':user' => $user, ':pool' => (bool) $bool]);
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating amounts: " . implode("\n", $errors));
            }

        } catch (\PDOException $e) {
            throw new ModelException('Failed to change user pool usage: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * [setProjectStatus description]
     * @param [type] $project [description]
     * @param [type] $bool [description]
     */
    public function setProjectStatus($project, $status = 'pending') {
        if($project instanceOf Project) $project = $project->id;
        if(!in_array($status, self::$statuses)) {
            throw new ModelException("Status [$status] not valid");
        }

        $sql = "UPDATE matcher_project SET status = :status WHERE matcher_id = :matcher AND project_id = :project";
        try {
            self::query($sql, [':matcher' => $this->id, ':project' => $project, ':status' => $status]);
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating totals: " . implode("\n", $errors));
            }

        } catch (\PDOException $e) {
            throw new ModelException('Failed to change project matcher status: ' . $e->getMessage());
        }
        return $this;
    }


}
