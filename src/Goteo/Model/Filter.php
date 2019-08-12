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

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Model\User;
use Goteo\Application\Config;

class Filter extends \Goteo\Core\Model {

    const DONOR = "donor";
    const PROMOTER = "promoter";
    const MATCHER = "matcher";
    const TEST = "test";
    const UNIQUE = "unique";
    const MULTIDONOR = "multidonor";
    const LAST_WEEK = 0;
    const LAST_MONTH = 1;
    const LAST_YEAR = 2;
    const FROM_NEW_YEAR = 3;
    const PREVIOUS_YEAR = 4;
    const TWO_YEARS_AGO = 5;

    public
        $id,
        $name,
        $description,
        $cert,
        $role,
        $startdate,
        $enddate,
        $status,
        $typeofdonor,
        $foundationdonor,
        $wallet,
        $project_latitude,
        $project_longitude,
        $project_radius,
        $project_location,
        $projects = [],
        $calls = [],
        $matchers = [];

    static public function get($id) {
        $query = static::query('SELECT * FROM filter WHERE id = ?', $id);
        $filter = $query->fetchObject(__CLASS__);

        if (!$filter instanceof Filter) {
            throw new ModelNotFoundException("[$id] not found");
        }

        $filter->projects = self::getFilterProject($id);
        $filter->calls = self::getFilterCall($id);
        $filter->matchers = self::getFilterMatcher($id);

        return $filter;
    }

    static public function getAll() {
        $query = static::query('SELECT * FROM filter');
        $filters = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        return $filters;
    }

    static public function getList(){
        $query = static::query('SELECT * FROM filter');
        $filters = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        return $filters;
    }

    static public function getFilterProject ($filter){
        $query = static::query('SELECT `project` FROM filter_project WHERE filter = ?', $filter);
        $projects = $query->fetchAll(\PDO::FETCH_ASSOC);

        $filter_projects = [];

        foreach($projects as $project) {
            foreach($project as $key => $value) {
                $project = Project::getMini($value);
                $filter_projects[$value] = $project->name;
            }
        }

        return $filter_projects;
    }

    static public function getFilterCall ($filter){
        $query = static::query('SELECT `call` FROM filter_call WHERE filter = ?', $filter);
        $calls = $query->fetchAll(\PDO::FETCH_ASSOC);

        $filter_calls = [];

        foreach($calls as $call) {
            foreach($call as $key => $value) {
                $call = Call::getMini($value);
                $filter_calls[$value] = $call->name;
            }
        }

        return $filter_calls;
    }
    
    static public function getFilterMatcher ($filter){
        $query = static::query('SELECT `matcher` FROM filter_matcher WHERE filter = ?', $filter);
        $matchers = $query->fetchAll(\PDO::FETCH_ASSOC);

        $filter_matchers = [];

        foreach($matchers as $matcher) {
            foreach($matcher as $key => $value) {
                $matcher = Matcher::get($value);
                $filter_matchers[$value] = $matcher->name;
            }
        }

        return $filter_matchers;
    }

    public function setFilterProjects(){
        $values = Array(':filter' => $this->id, ':project' => '');
        
        try {
            $query = static::query('DELETE FROM filter_project WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (\PDOException $e) {
            Message::error("Error deleting previous filter projects for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->projects as $key => $value) {
            $values[':project'] = $value;
            try {
                $query = static::query('INSERT INTO filter_project(`filter`, `project`) VALUES(:filter,:project)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter projects " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterCalls(){
        $values = Array(':filter' => $this->id, ':call' => '');
        
        try {
            $query = static::query('DELETE FROM filter_call WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (\PDOException $e) {
            Message::error("Error deleting previous filter calls for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->calls as $key => $value) {
            $values[':call'] = $value;
            try {
                $query = static::query('INSERT INTO filter_call(`filter`, `call`) VALUES(:filter,:call)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter call " . $e->getMessage());
                return false;
            }
        }
        return true;
    }


    public function setFilterMatcher(){
        $values = Array(':filter' => $this->id, ':matcher' => '');
        
        try {
            $query = static::query('DELETE FROM filter_matcher WHERE filter = :filter', Array(':filter' => $this->id));
        }
        catch (\PDOException $e) {
            Message::error("Error deleting previous filter matcher for filter " . $this->id . " " . $e->getMessage());
        }

        foreach($this->matchers as $key => $value) {
            $values[':matcher'] = $value;
            try {
                $query = static::query('INSERT INTO filter_matcher(`filter`, `matcher`) VALUES(:filter,:matcher)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter matcher " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function validate(&$errors = array()) {

        if (empty($this->name))
            $errors['name'] = Text::get('filter-without-name');
        if (empty($this->description))
            $errors['description'] = Text::get('filter-without-description');
        if (empty($this->role))
            $errors['role'] = Text::get('filter-without-role');
        return empty($errors);
    }


    public function save (&$errors = array()) {

        if(!$this->validate($errors)) return false;

        $fields = array(
            'id',
            'name',
            'description',
            'cert',
            'role',
            'startdate',
            'enddate',
            'status',                    
            'typeofdonor',
            'foundationdonor',
            'wallet',
            'project_latitude',
            'project_longitude',
            'project_radius',
            'project_location'
        );
        
        

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            // return true;

            $this->setFilterProjects();
            $this->setFilterCalls();
            $this->setFilterMatcher();

        } catch(\PDOException $e) {
            print("exception");
            $errors[] = "Error updating filter " . $e->getMessage();
            return false;
        }

        return true;

    }

    public function getDonors($count = false) {

        $receivers = array();

        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';

        $investStatus = Invest::$RAISED_STATUSES;

        if (isset($this->foundationdonor)) {
            if ($this->foundationdonor) $investStatus = Invest::$RAISED_STATUSES_AND_DONATED;
        }
        
        $sqlInner .= "INNER JOIN ( 
            SELECT * from invest 
            WHERE invest.status IN (";
            
            foreach($investStatus as $index => $status) {
                if ($index < 1) {
                    $sqlInner .= ":status_".$status;
                } else {
                    $sqlInner .= ", :status_".$status;
                }
                $values[':status_'.$status] = $status;
            }
            $sqlInner .= " ) 
            GROUP BY invest.user
            ";
            
            if (isset($this->typeofdonor)) {
                if ($this->typeofdonor == $this::UNIQUE) {            
                    $sqlInner .= "  HAVING count(*) = 1
                ";
                } else if ($this->typeofdonor == $this::MULTIDONOR) {
                $sqlInner .= " HAVING count(*) > 1  
                ";
                }
            }

        $sqlInner .= " ) as invest ON invest.user = user.id
            ";
        
        $this->projects = $this->getFilterProject($this->id);

        if (!empty($this->projects)) {
            foreach(array_keys($this->projects) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( invest.project =  :project_".$index;
                } else {
                    $sqlFilter .= " OR invest.project = :project_".$index;
                }
                $values[':project_'.$index] = $id;
            }
            $sqlFilter .= " ) ";
        }
            
        $sqlInner .= " 
            INNER JOIN project
            ON project.id = invest.project
        ";

        $this->calls = $this->getFilterCall($this->id);

        if (!empty($this->calls) && !empty($sqlInner)) {
            $sqlInner .= "INNER JOIN call_project
                on call_project.project = invest.project
            ";

            foreach(array_keys($this->calls) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( call_project.call =  :calls_".$index;
                } else {
                    $sqlFilter .= " OR call_project.call = :calls_".$index;
                }
                $values[':calls_'.$index] = $id;
            }
            $sqlFilter .= " ) ";

        }

        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->matchers) && !empty($sqlInner)) {
            $sqlInner .= "INNER JOIN matcher_project
                on matcher_project.project_id = invest.project
            ";

            foreach(array_keys($this->matchers) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( matcher_project.matcher_id =  :calls_".$index;
                } else {
                    $sqlFilter .= " OR matcher_project.matcher_id = :calls_".$index;
                }
                $values[':calls_'.$index] = $id;
            }
            $sqlFilter .= " ) ";

        }

        if (isset($this->status) && $this->status > -1 && !empty($sqlInner)) { // just one status?
            $sqlFilter .= " AND project.status = :status ";
            $values[':status'] = $this->status;
        }

        if (isset($this->startdate)) {
            $sqlFilter .= " AND invest.invested BETWEEN :startdate";
            $values['startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " AND :enddate";
                $values['enddate'] = $this->enddate;
            } else {
                $sqlFilter .= " AND curdate()";
            }
        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND invest.invested < :enddate";
            $values['enddate'] = $this->enddate;
        }

        if (isset($this->wallet)) {
            
            $sqlInner .= " INNER JOIN (
                SELECT * FROM user_pool ";
            if ($this->wallet) {
                $sqlInner .= " 
                    WHERE amount > 0 ) ";
            } else if (!$this->wallet) {
                $sqlInner .= "
                    WHERE amount = 0 ) ";
            }

            $sqlInner .= " as wallet
            ON user.id = wallet.user ";
        }

        // if (isset($this->cert)) {
        //     if ($this->cert) {
        //         $sqlInner .= " " // user_donation
        //     }
        // }

        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id)) FROM user $sqlInner WHERE user.active = 1 $sqlFilter";
            // if ($this->id == 4) {die(\sqldbg($sql, $values) );}
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                -- LIMIT $offset, $limit
                ";

        //  die( \sqldbg($sql, $values) );

        if ($query = User::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $receiver->id = $receiver->user;
                $receivers[$receiver->id] = $receiver;
            }
        }
    }

    public function getPromoters($count = false) {

        $receivers = array();

        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';

        
        $sqlInner .= "INNER JOIN project 
            ON project.owner = user.id
        ";

        if (isset($this->status) && $this->status > -1) {
            $sqlFilter .= "
                AND project.status = :status
                ";
            $values[':status'] = $this->status;
        }

        $this->projects = $this->getFilterProject($this->id);
        $this->calls = $this->getFilterCall($this->id);
        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->projects)) {
            foreach(array_keys($this->projects) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( project.id =  :project_".$index;
                } else {
                    $sqlFilter .= " OR project.id = :project_".$index;
                }
                $values[':project_'.$index] = $id;
            }
            $sqlFilter .= ") ";
        }


        if (!empty($this->calls)) {
            $sqlInner .= "INNER JOIN call_project
                on call_project.project = project.id
            ";

            foreach(array_keys($this->calls) as $index => $id) {
                if ($index < 1) {
                    $sqlFilter .= " AND ( call_project.call =  :call_".$index;
                } else {
                    $sqlFilter .= " OR call_project.call = :call_".$index;
                }
                $values[':call_'.$index] = $id;
            }
            $sqlFilter .= ") ";
        }

        if (!empty($this->matchers)) {
            if (!empty($this->matchers) && !empty($sqlInner)) {
                $sqlInner .= "INNER JOIN matcher_project
                    on matcher_project.project_id = project.id
                ";
    
                foreach(array_keys($this->matchers) as $index => $id) {
                    if ($index < 1) {
                        $sqlFilter .= " AND ( matcher_project.matcher_id =  :matchers_".$index;
                    } else {
                        $sqlFilter .= " OR matcher_project.matcher_id = :matchers_".$index;
                    }
                    $values[':matchers_'.$index] = $id;
                }
                $sqlFilter .= " ) ";
            }    
        }

        if (isset($this->startdate)) {
            $sqlFilter .= " AND project.created BETWEEN :startdate";
            $values['startdate'] = $this->startdate;

            if(isset($this->enddate)) {
                $sqlFilter .= " AND :enddate";
                $values['enddate'] = $this->enddate;
            } else {
                $sqlFilter .= " AND curdate()";
            }
        } else if (isset($this->enddate)) {
            $sqlFilter .= " AND project.created < :enddate";
            $values['enddate'] = $this->enddate;
        }

        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id)) FROM user $sqlInner WHERE user.active = 1 $sqlFilter";
            // die( \sqldbg($sql, $values) );
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                -- LIMIT $offset, $limit
                ";

         //die( \sqldbg($sql, $values) );

        if ($query = User::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $receiver->id = $receiver->user;
                $receivers[$receiver->id] = $receiver;
            }
        }

    }

    public function getMatchers($count = false) {

        $receivers = array();

        $values = array();
        $sqlInner  = '';
        $sqlFilter = '';

        
        $sqlInner .= "INNER JOIN matcher 
            ON matcher.owner = user.id
            ";

        $this->matchers = $this->getFilterMatcher($this->id);

        if (!empty($this->matchers)) {
            $sqlFilter .= " AND 
                matcher.id IN (:matchers) 
                ";
            $values[':matchers'] = implode(',', array_keys($this->matchers));
        }

        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id)) FROM user $sqlInner WHERE user.active = 1 $sqlFilter";
            // die( \sqldbg($sql, $values) );
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                -- LIMIT $offset, $limit
                ";

         //die( \sqldbg($sql, $values) );

        if ($query = User::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $receiver->id = $receiver->user;
                $receivers[$receiver->id] = $receiver;
            }
        }
    }

    public function getTesters($count = false) {

        $receivers = array();

        $values = array();
        $sqlFields  = '';
        $sqlInner  = '';
        $sqlFilter = '';

        $sqlInner .= "INNER JOIN user_role
            on user_role.user_id = user.id";
        $sqlFilter .= " AND user_role.role_id = 'superadmin' and node_id = :node";
        $values[':node'] = Config::get('node');

        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id)) FROM user $sqlInner WHERE user.active = 1 $sqlFilter";
            //die( \sqldbg($sql, $values) );
            return (int) User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                LIMIT $offset, $limit
                ";

         //die( \sqldbg($sql, $values) );

        if ($query = Model\User::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $receiver->id = $receiver->user;
                $receivers[$receiver->id] = $receiver;
            }
        }
    }

    public function getFiltred($count = false)
    {

        if ($this->role == $this::DONOR) {
            $result = $this->getDonors($count);
        } else if ($this->role == $this::PROMOTER) {
            $result = $this->getPromoters($count);
        } else if ($this->role == $this::MATCHER) {
            $result = $this->getMatchers($count);            
        } else if ($this->role == $this::TEST) {
            $result = $this->getTesters($count);
        }

        return $result;
    }

}
