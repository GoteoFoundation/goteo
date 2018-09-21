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
use Goteo\Model\Image;

/**
 * Footprint Model (sustainable development goals)
 */
class Footprint extends \Goteo\Core\Model {

    public $id,
           $name,
           $icon,
           $description = '',
           $modified;

    protected $iconImage;

    public static function getLangFields() {
        return ['name', 'description'];
    }

    /**
     * Get instance of footprint already in the table by action
     * @return [type] [description]
     */
    static public function get($id, $lang = null) {
        $values = [':id' => $id];

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT footprint.id,
                       $fields,
                       footprint.icon,
                       footprint.modified
            FROM `footprint`
            $joins
            WHERE footprint.id = :id";

        // print(\sqldbg($sql, $values));
        if ($query = static::query($sql, $values)) {
            if( $footprint = $query->fetchObject(__CLASS__) ) {
                return $footprint;
            }
        }
        return null;
    }


    /**
     * Lists available footprints
     * @param  array   $filters [description]
     * @param  [type]  $offset  [description]
     * @param  integer $limit   [description]
     * @param  boolean $count   [description]
     * @return array
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {
        $values = [];
        $filter = [];

        foreach(['id', 'name', 'description'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "footprint.$key LIKE :$key";
                $values[":$key"] = '%'.$filters[$key].'%';
            }
        }
        if($filters['global']) {
            $filter[] = "(footprint.name LIKE :global OR footprint.description LIKE :global)";
            $values[':global'] = '%'.$filters['global'].'%';
        }
        // print_r($filter);die;
        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM footprint$sql";
            // echo \sqldbg($sql, $values);
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT footprint.id,
                       $fields,
                       footprint.icon,
                       footprint.modified
                FROM footprint
                $joins
        $sql LIMIT $offset,$limit";

        // print(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

    public function getIcon() {
        if(!$this->iconImage instanceOf Image) {
            $this->iconImage = Image::get($this->icon ?: "footprint/square/{$this->id}.png");
            if(!$this->icon) $this->iconImage->setAsset(true);
        }
        return $this->iconImage;
    }

    public function setIcon($icon) {
        $this->icon = $icon instanceOf Image ? $icon->id : $icon;
        $this->iconImage = null;
        return $this;
    }

    /**
     * Save.
     * @param   type array  $errors     Error by reference
     * @return  type bool   true|false
     */
    public function save(&$errors = []) {

        if(!$this->validate($errors)) return false;

        $fields = ['name', 'icon', 'description'];
        try {
            $this->dbInsertUpdate($fields);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving footprint: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Validation
     * @param   type array  $errors     Error by reference
     * @return  type bool   true|false
     */
    public function validate(&$errors = []) {
        if(empty($this->name)) $errors[] = 'Empty name property';
        return empty($errors);
    }

}
