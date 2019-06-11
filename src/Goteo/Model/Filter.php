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
        $cert,
        $role,
        $startdate,
        $enddate,
        $status,
        $typeofdonor,
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

        if (!$filter instanceof \Goteo\Model\Filter) {
            throw new ModelNotFoundException("[$id] not found");
        }

        $filter->projects = self::get_filterprojects($id);
        $filter->calls = self::get_filtercalls($id);
        $filter->matcher = self::get_filtermatcher($id);

        return $filter;
    }

    static public function getAll() {
        $query = static::query('SELECT id, name FROM filter');
        $filters = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        return $filters;
    }

    static public function getFilterProjects ($filter){
        $query = static::query('SELECT project FROM filter_projects WHERE filter = ?', $filter);
        $projects = $query->fetchAll(\PDO::FETCH_CLASS);

        return $projects;
    }

    static public function getFilterCalls ($filter){
        $query = static::query('SELECT call FROM filter_calls WHERE filter = ?', $filter);
        $calls = $query->fetchAll(\PDO::FETCH_OBJ);

        return $calls;
    }
    
    static public function getFilterMatcher ($filter){
        $query = static::query('SELECT matcher FROM filter_matcher WHERE filter = ?', $filter);
        $matchers = $query->fetchAll(\PDO::FETCH_OBJ);

        return $matchers;
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
            // 'id',
            'name',
            'cert',
            'role',
            'startdate',
            'enddate',
            'status',
            'typeofdonor',
            'wallet',
            'project_latitude',
            'project_longitude',
            'project_radius',
            'project_location'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            return true;

        } catch(\PDOException $e) {
            print("exception");
            $errors[] = "Error updating filter " . $e->getMessage();
            return false;
        }

    }

}
