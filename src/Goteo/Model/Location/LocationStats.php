<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Location;

use Goteo\Core\Model;

class LocationStats {
    private $location;
    private $model;
    public $errors = array();

    public function __construct(LocationInterface $location, Model $model) {
        $this->location = $location; // Location instance (could be empty for the count methods), ie: UserLocation
        $this->model = $model; // Model applied to the location, ie: User for UserLocation
    }

    /**
     * Return the number of unlocated items
     * @return [type] [description]
     */
    public function countUnlocated() {
        $model = $this->model;
        $location = $this->location;
        $sql = "SELECT COUNT(id) FROM `" . $model->getTable() . "` WHERE id NOT IN (SELECT id FROM " . $location->getTable() . ")";
        try {
            $query = $model::query($sql);
            return (int) $query->fetchColumn();
        }
        catch(\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }
    /**
     * Return the number of located items
     * @return [type] [description]
     */
    public function countLocated() {
        $model = $this->model;
        $location = $this->location;
        $sql = "SELECT COUNT(id) FROM `" . $model->getTable() . "` WHERE id IN (SELECT id FROM " . $location->getTable() . ")";
        try {
            $query = $model::query($sql);
            return (int) $query->fetchColumn();
        }
        catch(\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }

    /**
     * Return the number of unlocable items
     * @return [type] [description]
     */
    public function countUnlocable() {
        return $this->countFiltered('locable', 0);
    }

    /**
     * Return the number of items filtered by some key
     * @return [type] [description]
     */
    public function countFiltered($key, $value, $inverse = false) {
        $location = $this->location;
        $sql = "SELECT COUNT(id) FROM `" . $location->getTable() . "` WHERE `$key` " . ($inverse ? 'NOT LIKE' : 'LIKE') . " ?";
        // echo "$sql\n";
        try {
            $query = $location::query($sql, array($value));
            return (int) $query->fetchColumn();
        }
        catch(\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }

    /**
     * Returns an Array of counts grouped by key
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function countGroupFiltered($group, $key, $value, $inverse = false) {
        $location = $this->location;
        $sql = "SELECT COUNT(*) AS total,`$group` FROM `" . $location->getTable() . "` WHERE `$key` " . ($inverse ? 'NOT LIKE' : 'LIKE') . " ? GROUP BY `$group` ORDER BY `$group` ASC";
        // echo "$sql\n";
        try {
            $ret = array();
            if($query = $location::query($sql, array($value))) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
                    $ret[$ob->$group] = $ob->total;
                }
            }
            return $ret;
        }
        catch(\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }
    /**
     * Returns an Array of counts grouped by countries
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function countGroupCountries() {
        $location = $this->location;
        $sql = "SELECT COUNT(*) AS total,CONCAT(country_code, ' ', country) AS pais FROM `" . $location->getTable() . "` GROUP BY country ORDER BY country_code ASC";
        // echo "$sql\n";
        try {
            $ret = array();
            if($query = $location::query($sql, array($value))) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
                    $ret[$ob->pais] = $ob->total;
                }
            }
            return $ret;
        }
        catch(\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }

}
