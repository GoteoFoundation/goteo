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

class Footprint extends Model {
    use Traits\SdgRelationsTrait;
    use Traits\CategoryRelationsTrait;
    use Traits\SocialCommitmentRelationsTrait;
    use Traits\ImpactDataRelationsTrait;
    use Traits\DataSetRelationsTrait;

    public $id,
           $name,
           $icon,
           $title,
           $description = '',
           $modified;

    protected $iconImage;

    public static function getLangFields(): array
    {
        return ['name', 'title', 'description'];
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
     * Returns array Footprints using relational tables from specified model
     * @param  [type] $model [description]
     * @param  [type] $ids   [description]
     * @return array of Footprint
     */
    static protected function getFromModelIds($model, $ids, $lang = null) {
        if(!is_array($ids)) $ids = [$ids];
        $tb = $model::getTableStatic();
        $values = [];
        $i = 0;
        foreach($ids as $id) {
            if($id instanceOf $model) $id = $id->id;
            if($id) {
                $values[":id$i"] = $id;
                $i++;
            }
        }
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        // If table is not sdg, add extra relation
        $sub = implode(',', array_keys($values));

        if(empty($sub)) return [];

        if($tb !== 'sdg') {
            $sub = "SELECT sdg_id FROM sdg_{$tb} WHERE sdg_{$tb}.{$tb}_id IN ($sub)";
        }
        $sql = "SELECT DISTINCT footprint.id,
                       $fields,
                       footprint.icon,
                       footprint.modified
            FROM `footprint`
            $joins
            INNER JOIN sdg_footprint ON sdg_footprint.footprint_id=footprint.id
            WHERE sdg_footprint.sdg_id IN ($sub)";

        // echo \sqldbg($sql, $values);

        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];

    }

    /**
     * gets an array of footprint objects from an array, object or id of categories
     * @param  mixed $ids  array (or not) of categories
     * @param  string $lang desired lang
     * @return array  of Footprints
     */
    static function getFromCategories($ids, $lang = null) {
        return static::getFromModelIds('\Goteo\Model\Category', $ids, $lang);
    }

    /**
     * gets an array of footprint objects from an array, object or id of Sdgs
     * @param  mixed $ids  array (or not) of Sdgs
     * @param  string $lang desired lang
     * @return array  of Footprints
     */
    static function getFromSdgs($ids, $lang = null) {
        return static::getFromModelIds('\Goteo\Model\Sdg', $ids, $lang);
    }

    /**
     * gets an array of footprint objects from an array, object or id of Spheres
     * @param  mixed $ids  array (or not) of Spheres
     * @param  string $lang desired lang
     * @return array  of Footprints
     */
    static function getFromSpheres($ids, $lang = null) {
        return static::getFromModelIds('\Goteo\Model\Sphere', $ids, $lang);
    }

    /**
     * gets an array of footprint objects from an array, object or id of SocialCommitments
     * @param  mixed $ids  array (or not) of SocialCommitments
     * @param  string $lang desired lang
     * @return array  of Footprints
     */
    static function getFromSocialCommitments($ids, $lang = null) {
        return static::getFromModelIds('\Goteo\Model\SocialCommitment', $ids, $lang);
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

        foreach(['id', 'name', 'title', 'description'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "footprint.$key LIKE :$key";
                $values[":$key"] = '%'.$filters[$key].'%';
            }
        }
        if($filters['global']) {
            $filter[] = "(footprint.name LIKE :global OR footprint.title LIKE :global OR footprint.description LIKE :global)";
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

    // TODO: add files to assets folder!
    public function getIcon($force_asset = false) {
        $asset = "footprint/{$this->id}.svg";

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

        // TODO: handle uploaded files here?
        // If instanceOf Image, means already uploaded (via API probably), just get the name
        if($this->icon instanceOf Image) $this->icon = $this->icon->getName();


        $fields = ['name', 'icon', 'title', 'description'];
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
