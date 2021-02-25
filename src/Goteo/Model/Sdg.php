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
 * Sdg Model (sustainable development goals)
 */
class Sdg extends \Goteo\Core\Model {
    // Provides addCategories, removeCategories, getCategories reusable functions
    use Traits\CategoryRelationsTrait;
    use Traits\SphereRelationsTrait;
    use Traits\SocialCommitmentRelationsTrait;
    use Traits\FootprintRelationsTrait;

    public $id,
           $name,
           $icon,
           $description = '',
           $link = '',
           $modified;

    protected $iconImage;

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

        if ($filters['footprint']) {
            $sqlJoins .="INNER JOIN sdg_footprint ON sdg_footprint.sdg_id = sdg.id ";
            $filter[] = "sdg_footprint.footprint_id = :footprint";
            $values[':footprint'] = $filters['footprint'];

        }

        // print_r($filter);die;
        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM sdg $sqlJoins $sql";
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
                $sqlJoins
        $sql LIMIT $offset,$limit";

        // print(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

    public function getIcon($force_asset = false) {
        $asset = "sdg/square/{$this->id}.png";

        if($force_asset) return Image::get($asset)->setAsset(true);

        if(!$this->iconImage instanceOf Image) {
            $this->iconImage = Image::get($this->icon ?: $asset);
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

        $fields = ['name', 'icon', 'description', 'link'];
        try {
            // We don't use simply $this->dbInsertUpdate($fields);
            // because we may specify the id field on inserts
            if(empty($this->modified)) {
                $this->modified = date('Y-m-d H:i:s');
                if($this->id) $fields[] = 'id';
                $this->dbInsert($fields);
                if(!$this->id) $this->id = static::insertId();
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
        if(empty($this->name)) $errors[] = 'Empty name property';
        return empty($errors);
    }

}
