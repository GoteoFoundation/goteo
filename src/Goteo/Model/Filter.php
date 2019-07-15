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
use Goteo\Model\Location\LocationItem;
use Goteo\Model\Project\ProjectLocation;

class Filter extends \Goteo\Core\Model {

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

        $filter->projects = self::getFilterProjects($id);
        $filter->calls = self::getFilterCalls($id);
        $filter->matcher = self::getFilterMatcher($id);

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

    static public function getFilterProjects ($filter){
        $query = static::query('SELECT `project` FROM filter_projects WHERE filter = ?', $filter);
        $projects = $query->fetchAll(\PDO::FETCH_CLASS);

        return $projects;
    }

    static public function getFilterCalls ($filter){
        $query = static::query('SELECT `call` FROM filter_calls WHERE filter = ?', $filter);
        $calls = $query->fetchAll(\PDO::FETCH_OBJ);

        return $calls;
    }
    
    static public function getFilterMatchers ($filter){
        $query = static::query('SELECT `matcher` FROM filter_matchers WHERE filter = ?', $filter);
        $matchers = $query->fetchAll(\PDO::FETCH_OBJ);

        return $matchers;
    }

    public function setFilterProjects($filter, $projects = Array()){
        $values = Array(':filter' => $filter, ':project' => '');

        foreach($projects as $id) {
            $values[':project'] = $id;
            try {
                $query = static::query('REPLACE INTO filter_projects(`filter`, `project`) VALUES(:filter,:project)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter projects " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function setFilterCalls($filter, $calls = Array()){
        $values = Array(':filter' => $filter, ':call' => '');

        foreach($calls as $id) {
            $values[':call'] = $id;
            try {
                $query = static::query('REPLACE INTO filter_calls(`filter`, `call`) VALUES(:filter,:call)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter calls " . $e->getMessage());
                return false;
            }
        }
        return true;
    }


    public function setFilterMatcher($filter, $matchers = Array()){
        $values = Array(':filter' => $filter, ':matcher' => '');

        foreach($matchers as $id) {
            $values[':matcher'] = $id;
            try {
                $query = static::query('REPLACE INTO filter_matchers(`filter`, `matcher`) VALUES(:filter,:matcher)', $values);
            }
            catch (\PDOException $e) {
                Message::error("Error saving filter matchers " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function getReceivers(){
        $receivers = 100; 
        return $receivers;
    }

    public function validate(&$errors = array()) {
        // Estos son errores que no permiten continuar

        // if (empty($this->name))
        //     $errors['name'] = Text::get('filter-without-name');
        return empty($errors);
    }


    public function save (&$errors = array()) {

        // if(!$this->validate($errors)) return false;

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

        } catch(\PDOException $e) {
            print("exception");
            $errors[] = "Error updating filter " . $e->getMessage();
            return false;
        }

        if ($this->role = "donor") {
            $this->setFilterProjects($this->projects);
        } else if ($this->role == "promoter") {
            $this->setFilterCalls($this->calls);
        } else if ($this->role == "matcher") {
            $this->setFilterMatcher($this->matchers);
        }

        return true;

    }

}
