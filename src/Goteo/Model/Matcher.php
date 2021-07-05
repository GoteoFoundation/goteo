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
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Payment\Method\PoolPaymentMethod;
use Goteo\Model\Matcher\MatcherLocation;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Questionnaire;
use Goteo\Model\Questionnaire\Answer;
use Goteo\Model\Questionnaire\Score;
use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\MatcherValidationEvent;

/**
 * Matcher Model
 */
class Matcher extends \Goteo\Core\Model {
    use Traits\SphereRelationsTrait;


    const STATUS_OPEN = 'open';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PITCH_CLOSED = 'pitch_closed';
    const STATUS_PITCH_OPEN = 'open';
    const MINIMUM_WALLET_AMOUNT = 1000;

    public $id,
           $name,
           $description,
           $status = self::STATUS_OPEN,
           $logo,
           $lang,
           $owner,
           $terms,
           $fee = 0,
           $processor = 'duplicateinvest',
           $vars = [
               'max_amount_per_invest' => 100,
               'max_amount_per_project' => 0,
               'max_invests_per_user' => 1,
               'match_factor' => 1
           ],
           $crowd = 0, // Calculated field with the sum of all invests made by the peoplo
           $used = 0, // Calculated field with the sum of all invests made by the matching
           $amount = 0, // Calculated field with the sum of all pools in the Matcher
           $projects = 0, // Calculated field with the total number of active projects in the Matcher
           $active = true,
           $created,
           $modified_at;

    // status for projects assigned to matcher
    public static $statuses = ['pending', 'accepted', 'rejected', 'active', 'discarded', 'completed'];

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        if(empty($this->lang)) $this->lang = Config::get('lang');
    }

    public static function getLangFields() {
        return ['name', 'description', 'terms'];
    }

    /**
     * Get instance of matcher already in the table by action
     * @return Matcher|null
     */
    static public function get(
        $id,
        bool $active_only = true,
        $lang = null
    ) {
        $values = [':id' => $id];
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql = "SELECT matcher.id,
                       $fields,
                       matcher.status,
                       matcher.logo,
                       matcher.lang,
                       matcher.owner,
                       matcher.fee,
                       matcher.processor,
                       matcher.vars,
                       matcher.amount,
                       matcher.used,
                       matcher.crowd,
                       matcher.projects,
                       matcher.matcher_location,
                       matcher.active,
                       matcher.created,
                       matcher.modified_at
            FROM `matcher`
            $joins
            WHERE matcher.id = :id";

        if ($active_only) {
            $sql .= " AND matcher.active=:active";
            $values[':active'] = true;
        }

        if ($query = static::query($sql, $values)) {
            if( $matcher = $query->fetchObject(__CLASS__) ) {
                $matcher->viewLang = $lang;
                $matcher->logo = Image::get($matcher->logo);
                return $matcher;
            }
        }

        return null;
    }

    /**
     * Get an instance of a Matcher by one of the projects involved
     * @param  mixed $pid Project or id
     * @param  status   if true: list active projects in active matchers
     *                  if false: list all projects in all matchers
     *                  if 'string' search for that status in active matchers
     *                  if 'array' search for all of that statuses in active matchers
     * @return array of Matchers available for the project
     */
    static public function getFromProject($pid, $status = true, $filters = array()) {
        if($pid instanceOf Project) $pid = $pid->id;
        $values = [':pid' => $pid];
        $sql = "SELECT a.* FROM `matcher` a
            RIGHT JOIN `matcher_project` b ON a.id = b.matcher_id
            WHERE b.project_id = :pid";

        if($filters['has_channel']) {
            $sql .= " AND EXISTS ( SELECT node.id
                                    FROM node
                                    WHERE node.id = a.id )";
        }

        if((is_bool($status) && $status) || $status == 'all') {
            $sql .= " AND a.active=1 AND b.status = 'active'";
        } elseif($status) {
            if(!is_array($status)) $status = [$status];
            $keys = [];
            foreach($status as $i => $s) {
                if(in_array($s, self::$statuses)) {
                    $keys[] = ":status$i";
                    $values[":status$i"] = $s;
                }
            }
            $sql .= " AND a.active=1 AND b.status IN (" . implode(',', $keys) . ")";
        }
        $list = [];
        if ($query = static::query($sql, $values)) {
            if( $matcher = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) ) {
                return $matcher;
            }
        }
        return $list;
    }

    public function getLogo() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->logo);
        }
        return $this->imageInstance;
    }

    static public function getList(
        array $filters = [],
        int $offset = 0,
        int $limit = 10,
        $count = false,
        $lang = null
    ) {
        $values = [];
        $filter = [];
        foreach(['owner', 'active', 'processor'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "matcher.$key = :$key";
                $values[":$key"] = $filters[$key];
            }
        }
        foreach(['id', 'name', 'terms', 'status'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "matcher.$key LIKE :$key";
                $values[":$key"] = '%'.$filters[$key].'%';
            }
        }

        if ($filters['global']) {
            $filter[] = "(matcher.name LIKE :global OR matcher.matcher_location LIKE :global)";
            $values[':global'] = '%'.$filters['global'].'%';
        }

        if ($filters['has_channel']) {
            $filter[] = "EXISTS ( SELECT node.id
                                    FROM node
                                    WHERE node.id = matcher.id AND node.active )";
        }

        if ($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if ($count === 'money') {
            $sql = "SELECT SUM(amount) FROM matcher$sql";
            return (int) self::query($sql, $values)->fetchColumn();
        } elseif ($count) {
            $sql = "SELECT COUNT(id) FROM matcher$sql";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        if(!$lang) $lang = Lang::current();
        $values['viewLang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql = "SELECT matcher.id,
                       $fields,
                       matcher.status,
                       matcher.logo,
                       matcher.lang,
                       matcher.owner,
                       matcher.processor,
                       matcher.vars,
                       matcher.amount,
                       matcher.used,
                       matcher.crowd,
                       matcher.projects,
                       matcher.matcher_location,
                       matcher.active,
                       matcher.created,
                       matcher.modified_at,
                       :viewLang as viewLang
                FROM matcher
                $joins
        $sql LIMIT $offset,$limit";

        if ($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

    /**
     * Save.
     * @param   array  $errors     Errores devueltos pasados por referencia.
     * @return  bool   true|false
     */
    public function save(&$errors = []) {

        if(!$this->validate($errors)) return false;

        if(!$this->created) $this->created = date('Y-m-d');


        $fields = ['name', 'description', 'status', 'logo', 'lang', 'owner', 'terms', 'processor', 'vars', 'amount', 'used', 'crowd', 'active', 'projects', 'created', 'matcher_location'];
        try {
            // Update pool amounts
            foreach($this->getUserPools() as $pool) {
                $pool->calculate()->save($errors);
            }

            $this->used = $this->calculateUsedAmount();
            $this->crowd = $this->calculateCrowdAmount();
            $this->amount = $this->calculatePoolAmount() + $this->calculateUsedAmount();
            $this->projects = $this->calculateProjects();
            $this->status = ($this->calculatePoolAmount()) ? self::STATUS_OPEN : self::STATUS_COMPLETED;

            if($this->matcher_location instanceOf MatcherLocation) {
                $this->matcher_location->id = $this->id;
                if($this->matcher_location->save($errors)) {
                    $this->matcher_location = $this->matcher_location->location ? $this->matcher_location->location : $this->matcher_location->name;
                } else {
                    $fail = true;
                    unset($this->matcher_location);
                }
            }

            if (is_array($this->vars))
                $this->setVars($this->vars);

            if(empty($this->modified_at)) {
                $this->modified_at = date('Y-m-d H:i:s');
                $fields[] = 'id';
                $this->dbInsert($fields);
            }
            else {
                $this->dbUpdate($fields);
            }

            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving matcher: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * @param array $errors Errors array returned by reference
     * @return bool
     */
    public function validate(&$errors = []): bool
    {
        if(empty($this->id)) $errors[] = 'Empty Id for matcher';
        if(empty($this->name)) $errors[] = 'Empty name for matcher';
        return empty($errors);
    }

    /**
     * Gets the % of the filled matcher. 100% means it can be activated
     * @return stdClass Object with parts and globals percents
     */
    public function getValidation() {

        $event = App::dispatch(AppEvents::MATCHER_VALIDATION, new MatcherValidationEvent($this));

        $scores = $event->calculate()->getScores();
        $scores->errors = $event->getErrors();
        $scores->fields = $event->getFields();
        $scores->matcher = $this->id;

        return $scores;
    }



    /**
     * Obtains matcher donating user pools
     * @return [type] [description]
     */
    public function getUserPools() {
        $sql = "SELECT
            matcher_user.user_id AS user, user_pool.amount
            FROM user_pool
            RIGHT JOIN matcher_user ON matcher_user.user_id = user_pool.user
            WHERE matcher_user.matcher_id = :match AND matcher_user.pool = 1";
        return self::query($sql, [':match' => $this->id])
                    ->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\User\Pool');
    }

    /**
     * Gets the total amount available fot the matching by adding up they users pool
     * @return int amount
     */
    protected function calculatePoolAmount() {
        $sql = "SELECT
                SUM(user_pool.amount) AS total
                FROM user_pool
                RIGHT JOIN matcher_user ON matcher_user.user_id = user_pool.user
                WHERE matcher_user.matcher_id = :match AND matcher_user.pool = 1";
        // echo \sqldbg($sql, [':match' => $this->id]);
        return (int) self::query($sql, [':match' => $this->id])->fetchColumn();
    }

    /**
     * Gets the used amount fot the matching by adding up currently made invests
     * @return int amount
     */
    protected function calculateUsedAmount() {
        $sql = "SELECT
                SUM(invest.amount) AS total
                FROM invest
                RIGHT JOIN matcher_user ON matcher_user.user_id = invest.user AND matcher_user.pool = 1
                WHERE
                invest.matcher = :match
                AND invest.method = :method
                AND invest.campaign = 1
                AND invest.status IN (" . implode(', ', Invest::$ACTIVE_STATUSES) . ") ";
        $values = [':match' => $this->id, ':method' => PoolPaymentMethod::getId()];
        // echo \sqldbg($sql, $values);
        return (int) self::query($sql, $values)->fetchColumn();
    }

    /**
     * Gets the civic funding amount
     * @return int amount
     */
    protected function calculateCrowdAmount() {
        $sql = "SELECT
                SUM(invest.amount) AS total
                FROM invest
                RIGHT JOIN matcher_project ON matcher_project.project_id = invest.project
                WHERE matcher_project.matcher_id = :match AND matcher_project.status = 'active'
                AND invest.campaign = 0
                AND invest.status IN (" . implode(', ', Invest::$ACTIVE_STATUSES) . ") ";
        $values = [':match' => $this->id];
        // echo \sqldbg($sql, $values);
        return (int) self::query($sql, $values)->fetchColumn();
    }


    /**
     * Gets the total number of active projects available for the matching
     * @return int num of projects
     */
    protected function calculateProjects() {
        $sql = "SELECT
                COUNT(*) AS total
                FROM matcher_project
                WHERE matcher_project.matcher_id = :match AND matcher_project.status = 'active'";
        // echo \sqldbg($sql, [':match' => $this->id]);
        return (int) self::query($sql, [':match' => $this->id])->fetchColumn();
    }

     /**
     * Gets the used amount in a project for the matching by adding up currently made invests
     * @return int amount
     */
    public function calculateProjectAmount($project) {
         $sql = "SELECT
                SUM(invest.amount) AS total
                FROM invest
                RIGHT JOIN matcher_user ON matcher_user.user_id = invest.user AND matcher_user.pool = 1
                WHERE
                invest.matcher = :match
                AND invest.project = :project
                AND invest.method = :method
                AND invest.campaign = 1
                AND invest.status IN (" . implode(', ', Invest::$ACTIVE_STATUSES) . ") ";
        $values = [':match' => $this->id, ':method' => PoolPaymentMethod::getId(), ':project' => $project];
        // echo \sqldbg($sql, $values);
        return (int) self::query($sql, $values)->fetchColumn();
    }

     /**
     * Check if the matcher is owned (or co-owned) by the user id
     * @param  Goteo\Model\User $user  the user to check
     * @return boolean          true if success, false otherwise
     */
    public function userIsOwner($user = null) {
        if(empty($user)) return false;
        if(!$user instanceOf User) return false;
        // owns the project
        if($this->owner === $user->id) {
            return true;
        }
        return false;
    }



     /**
     * Check if the matcher is editable by the user id
     * @param  Goteo\Model\User $user  the user to check
     */
    public function userCanEdit($user = null) {

        if(empty($user)) return false;
        if(!$user instanceOf User) return false;

        // owns the matcher
        if($this->userIsOwner($user)) return true;

        if($user->hasPerm('edit-any-matcher')) return true;

        // matcher admin or matcher consultant
        if($user->hasPerm('edit-matcher', $this->id)) return true;

        return false;
    }

    /**
     * Check if the project is removable by the user id
     * @param  Goteo\Model\User $user  the user to check
     * @return boolean          true if success, false otherwise
     */
    public function userCanDelete($user = null) {
        if(empty($user)) return false;
        if(!$user instanceOf User) return false;
        if(!in_array($this->status, array(self::STATUS_DRAFT, self::STATUS_REJECTED, self::STATUS_EDITING))) return false;
        // owns the project
        if($this->userIsOwner($user)) return true;

        if($user->hasPerm('delete-any-matcher')) return true;

        // matcher admin or matcher consultant
        if($user->hasPerm('remove-matcher', $this->id)) return true;

        return false;
    }

    /**
     * Check if the matcher can be created by the user id
     * @param  Goteo\Model\User $user  the user to check
     * @return boolean          true if success, false otherwise
     */
    public static function userCanCreate($user = null) {
        if(empty($user)) return false;
        if(!$user instanceOf User) return false;

        if ($user->getPool()->getAmount() >= self::MINIMUM_WALLET_AMOUNT && empty(self::getList(['owner' => $user->id]))) {
            return true;
        }
        return false;
    }



    /**
     * Getters & setters
     */

    // returns the current user
    public function getOwner() {
        if($this->userInstance) return $this->userInstance;
        $this->userInstance = User::get($this->owner);
        return $this->userInstance;
    }

    public function setVars(array $vars) {
        $this->vars = $vars ? json_encode($vars) : '';
        return $this;
    }

    public function getVars() {
        if($this->vars) return json_decode($this->vars, true);
        return [];
    }

    /**
     * Use to ensure a valid value of total amount
     */
    public function getTotalAmount(): int
    {
        if(empty($this->amount)) {
            // Pool reflects the amount still available, not the total
            $this->amount = $this->calculatePoolAmount() + $this->calculateUsedAmount();
        }
        return $this->amount;
    }

    /**
     * Use to ensure a valid value of total used amount
     */
    public function getUsedAmount(): int
    {
        if(empty($this->used)) {
            $this->used = $this->calculateUsedAmount();
        }
        return $this->used;
    }

    /**
     * Use to ensure a valid value of total available amount
     */
    public function getAvailableAmount(): int
    {
        return $this->getTotalAmount() - $this->getUsedAmount();
    }

    /**
     * Use to ensure a valid value of total crowd amount
     */
    public function getCrowdAmount(): int
    {
        if(empty($this->crowd)) {
            $this->crowd = $this->calculateCrowdAmount();
        }
        return $this->crowd;
    }

    /**
     * Use to ensure a valid value of total projects
     */
    public function getTotalProjects(): int
    {
        if(empty($this->projects)) {
            $this->projects = $this->calculateProjects();
        }
        return $this->projects;
    }

    public static function getTotalRaised(): int
    {
        return self::getList([], 0, 10, 'money');
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
     * Return users
     * @return [type] [description]
     */
    public function getUsers($with_pool = true) {
        $sql = "SELECT a.*,b.pool as use_pool FROM user a
                RIGHT JOIN matcher_user b ON a.id = b.user_id
                WHERE b.matcher_id = :matcher " . ($with_pool ? ' AND b.pool = 1' : '');
        $values = [':matcher' => $this->id];

        // die(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            if( $users = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\User') ) {
                return $users;
            }
        }
        return [];
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
            if($project instanceOf Project) {
                $project = $project->id;
            }
            $inserts[] = "(:matcher, :project$i, :status)";
            $values[":project$i"] = $project;
            $i++;
        }

        $sql = "INSERT INTO `matcher_project` (matcher_id, project_id, status) VALUES " . implode(', ', $inserts);
        try {
            self::query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException('Failed to add projects: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * Return projects
     * @return [type] [description]
     */
    public function getProjects($status = 'active') {
        $sql = "SELECT a.*,b.status AS matcher_status, b.score FROM project a
                RIGHT JOIN matcher_project b ON a.id = b.project_id
                WHERE b.matcher_id = :matcher AND a.id NOT REGEXP '[0-9a-f]{32}'
                ";
        $values = [':matcher' => $this->id];
        if($status && $status !== 'all') {
            if(!in_array($status, self::$statuses)) {
                throw new ModelException("Status [$status] not valid");
            }
            $sql .= ' AND b.status = :status';
            $values[':status'] = $status;
        }
        $sql .= ' ORDER BY b.score DESC';
        // die(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            if( $projects = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Project') ) {
                return $projects;
            }
        }
        return [];
    }

    /**
     * Return projects
     * @return [type] [description]
     */
    public function getListProjects($status = 'active') {
        $sql = "SELECT a.*,b.status AS matcher_status FROM project a
                RIGHT JOIN matcher_project b ON a.id = b.project_id
                WHERE b.matcher_id = :matcher AND a.status IN (2,3,4,5,6)";
        $values = [':matcher' => $this->id];
        if($status && $status !== 'all') {
            if(!in_array($status, self::$statuses)) {
                throw new ModelException("Status [$status] not valid");
            }
            $sql .= ' AND b.status = :status';
            $values[':status'] = $status;
        }

        // die(\sqldbg($sql, $values));
        $query = self::query($sql, $values);

        $projects = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Project');

        foreach ($projects as $proj) {
                    $projects_list[] = Project::getWidget($proj);
                }
        return $projects_list;
    }

    /*
     *   Porcentage of sucess projects
    */
    public function getSuccessPorcentage(){
        return Project::getSucessfulPercentage(false, $this->id);
    }

    /**
     * [findProject description]
     * @return [type] [description]
     */
    public function findProject($pid, $status = 'active') {
        if($pid instanceOf Project) $pid = $pid->id;
        $sql = "SELECT a.*,b.status AS matcher_status FROM project a
                RIGHT JOIN matcher_project b ON a.id = b.project_id
                WHERE b.matcher_id = :matcher AND b.project_id = :project";
        $values = [':matcher' => $this->id, ':project' => $pid];
        if($status && $status !== 'all') {
            if(!in_array($status, self::$statuses)) {
                throw new ModelException("Status [$status] not valid");
            }
            $sql .= ' AND b.status = :status';
            $values[':status'] = $status;
        }
        // die(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchObject('Goteo\Model\Project');
        }
        return null;
    }

    public function getProjectStatus($pid) {
        if($pid instanceOf Project) $pid = $pid->id;
        $sql = "SELECT status FROM matcher_project WHERE project_id = :pid AND matcher_id = :match";
        $values = [':pid' => $pid, ':match' => $this->id];
        if($query = self::query($sql, $values)) {
            return $query->fetchColumn();
        }
        throw new ModelException('This project has no matcher assigned');
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
    public function setProjectStatus($pid, $status = 'pending') {
        if($pid instanceOf Project) $pid = $pid->id;
        if(!in_array($status, self::$statuses)) {
            throw new ModelException("Status [$status] not valid");
        }

        $sql = "UPDATE matcher_project SET status = :status WHERE matcher_id = :matcher AND project_id = :project";
        $values = [':matcher' => $this->id, ':project' => $pid, ':status' => $status];
        try {
            self::query($sql, $values);
            if($this->getProjectStatus($pid) !== $status) {
                throw new ModelException("Error setting status [$status] with project [$pid]");
            }
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating totals: " . implode("\n", $errors));
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to change project matcher status: ' . $e->getMessage());
        }
        return $this;
    }

    /**
     * [updateProjectScore description]
     * @param [type] $project [description]
     * @param [type] $bool [description]
     */
    public function updateProjectScore($pid) {
        if($pid instanceOf Project) $pid = $pid->id;
        $questionnaire = Questionnaire::getByMatcher($this->id);
        $answers = Answer::getList(['project' => $pid, 'questionnaire' => $questionnaire->id]);
        $answers_id = [];
        foreach($answers as $index => $answer) {
            $answers_id[] = $answer->id;
        }
        $score = Score::getScoreByAnswers($answers_id);

        $sql = "UPDATE matcher_project SET score = :score WHERE matcher_id = :matcher AND project_id = :project";
        $values = [':matcher' => $this->id, ':project' => $pid, ':score' => $score];
        try {
            self::query($sql, $values);
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException("Error updating totals: " . implode("\n", $errors));
            }
        } catch (\PDOException $e) {
            throw new ModelException('Failed to change project matcher status: ' . $e->getMessage());
        }
        return $this;
    }



    /*
    *  Get the matchers that a given user administrates
    */
    public static function getUserMatchersList($user) {
        $sql = "SELECT *
        FROM matcher
        RIGHT JOIN matcher_user ON matcher_user.matcher_id = matcher.id
        WHERE matcher_user.user_id = :user AND matcher_user.pool";
        return self::query($sql, [':user' => $user->id])
                ->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Matcher');

    }

    /*
     *   Get matchers available for a project
    */
    static public function getMatchersAvailable(Project $project, $max_distance = null, $filters = ['status' => self::STATUS_OPEN, 'active' => true]){

        $matchers = [];
        foreach(self::getList($filters) as $matcher) {
            if($matcher_config = $matcher->getVars()) {
                if ($matcher_config['pitch_status'] == self::STATUS_PITCH_OPEN) {
                    if ($matcher_config['filter_by_location'] ) {
                        if($matcher_loc = MatcherLocation::get($matcher)) {
                            if($location = ProjectLocation::get($project)) {
                                $max = is_null($max_distance) ? ($matcher_loc->radius ? $matcher_loc->radius :  100) : $max_distance;
                                $distance = MatcherLocation::haversineDistance($location->latitude, $location->longitude, $matcher_loc->latitude, $matcher_loc->longitude);
                                if($distance < $max) {
                                    $matcher->distance = $distance;
                                    $matchers[] = $matcher;
                                }
                            }
                        }
                    } else {
                        $matcher->distance = 0;
                        $matchers[] = $matcher;
                    }
                }
            }
            usort($matchers, function($a, $b) {
                return $a->distance > $b->distance;
            });
        }
        // TODO Filter by location
        return $matchers;
    }

    public function hasQuestionnaire() {
        $questionnaire = Questionnaire::getByMatcher($this->id);
        return isset($questionnaire);
    }

    public function getQuestionnaire() {
        return Questionnaire::getByMatcher($this->id);
    }

}
