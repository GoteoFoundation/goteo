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
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;

/**
 * Sdg Model (sustainable development goals)
 */
class Sdg extends \Goteo\Core\Model {
    public $id,
           $name,
           $icon,
           $description = '',
           $link = '',
           $modified;

    public static function getLangFields() {
        return ['name', 'description', 'link'];
    }

    /**
     * Get instance of sdg already in the table by action
     * @return [type] [description]
     */
    static public function get($id, $lang = null) {
        $values = [':id' => $id];

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT sdg.id,
                       $fields,
                       sdg.icon,
                       sdg.modified
            FROM `sdg`
            $joins
            WHERE sdg.id = :id";

        // print(\sqldbg($sql, $values));
        if ($query = static::query($sql, $values)) {
            if( $sdg = $query->fetchObject(__CLASS__) ) {
                return $sdg;
            }
        }
        return null;
    }


    /**
     * Lists available sdgs
     * @param  array   $filters [description]
     * @param  [type]  $offset  [description]
     * @param  integer $limit   [description]
     * @param  boolean $count   [description]
     * @return array
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {
        $values = [];
        $filter = [];

        foreach(['id', 'name', 'description', 'link'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "sdg.$key LIKE :$key";
                $values[":$key"] = '%'.$filters[$key].'%';
            }
        }
        if($filters['global']) {
            $filter[] = "(sdg.name LIKE :global OR sdg.description OR sdg.link LIKE :global)";
            $values[':global'] = '%'.$filters['global'].'%';
        }
        // print_r($filter);die;
        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM sdg$sql";
            // echo \sqldbg($sql, $values);
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT sdg.id,
                       $fields,
                       sdg.icon,
                       sdg.modified
                FROM sdg
                $joins
        $sql LIMIT $offset,$limit";

        // print(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

    /**
     * Save.
     * @param   type array  $errors     Error by reference
     * @return  type bool   true|false
     */
    public function save(&$errors = []) {

        if(!$this->validate($errors)) return false;

        $fields = ['name', 'icon', 'description', 'link'];
        try {
            if(empty($this->modified)) {
                $this->modified = date('Y-m-d H:i:s');
                $fields[] = 'id';
                $this->dbInsert($fields);
            }
            else {
                $this->dbUpdate($fields);
            }

            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving sdg: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Validation
     * @param   type array  $errors     Error by reference
     * @return  type bool   true|false
     */
    public function validate(&$errors = []) {
        if(empty($this->id)) $errors[] = 'Empty Id for sdg';
        if(empty($this->name)) $errors[] = 'Empty name for sdg';
        return empty($errors);
    }

}
