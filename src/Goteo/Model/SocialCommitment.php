<?php

/*
* Model for Social Commitment
*/

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class SocialCommitment extends \Goteo\Core\Model {
    use Traits\SdgRelationsTrait;
    use Traits\FootprintRelationsTrait;

    protected $Table = 'social_commitment';
    static protected $Table_static = 'social_commitment';

    public
    $id,
    $name,
    $description = '',
    $icon = '',
    $modified;


    static public function getLangFields() {
        return ['name', 'description'];
    }

    /**
     * Get data about a social commitment
     *
     * @param   int    $id         social commitment id.
     * @return  Social commitment object
     */
    static public function get($id, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="SELECT
                social_commitment.id,
                social_commitment.icon,
                $fields
            FROM social_commitment
            $joins
            WHERE social_commitment.id = :id";

        $values = [':id' => $id];

        // print(\sqldbg($sql, $values));
        if ($query = static::query($sql, $values)) {
            if( $sdg = $query->fetchObject(__CLASS__) ) {
                return $sdg;
            }
        }
        return null;

    }

    /**
     * Social commitment list
     *
     * @param  array  $filters
     * @return mixed            Array of social commitments
     */
    public static function getAll($filters = array(), $lang = null) {

        $values = array();

        $list = array();

        if($sqlFilter) {
            $sqlFilter = 'WHERE ' . $sqlFilter;
            $order='ORDER BY name ASC';
        } else {
            $sqlFilter = '';
            $order='ORDER BY id ASC';
        }

        if(!$lang) $lang = Lang::current();
        $values['viewLang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql = "SELECT
                    social_commitment.id,
                    social_commitment.icon,
                    $fields
                FROM social_commitment
                $joins
                $sqlFilter
                $order
                ";

        $query = self::query($sql, $values);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
            // For compatibility
            $item->image = $item->getIcon();
            $list[$item->id] = $item;
        }
        return $list;
    }

    public function getIcon($force_asset = false) {
        $asset = "social-commitment/square/{$this->id}.png";

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
     * Get the categories related with a social commitment
     * @param varcahr(50) $id  Social commitment idetifier
     * @return array of categories identifiers
     */
    public static function getCategories ($id) {
        $array = [];
        try {
            $query = static::query("SELECT id FROM category WHERE social_commitment = ?", array($id));
            $categories = $query->fetchAll();
            foreach ($categories as $cat) {
                $array[$cat[0]] = $cat[0];
            }

            return $array;
        } catch(\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

    /**
     * Save.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if (!$this->validate($errors))
            return false;

        // TODO: handle uploaded files here?
        // If instanceOf Image, means already uploaded (via API probably), just get the name
        if($this->icon instanceOf Image) $this->icon = $this->icon->getName();

        $fields = ['id', 'name', 'description', 'icon'];

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Social commitment save error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        if(empty($this->name)) {
            $errors[] = "Emtpy name";
        }
        return empty($errors);
    }


}


