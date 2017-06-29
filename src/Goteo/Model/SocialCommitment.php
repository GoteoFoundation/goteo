<?php

/*
* Model for Social Commitment
*/

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;

class SocialCommitment extends \Goteo\Core\Model {

    protected $Table = 'social_commitment';

    public
    $id,
    $name,
    $description,
    $image,
    $modified;

    /**
     * Get data about a social commitment
     *
     * @param   int    $id         social commitment id.
     * @return  Social commitment object
     */
    static public function get($id, $lang = null) {

        //Obtenemos el idioma de soporte
        $lang=self::default_lang_by_id($id, "social_commitment_lang", $lang);

        $sql="SELECT
                    social_commitment.id,
                    social_commitment.image,
                    IFNULL(social_commitment_lang.name, social_commitment.name) as name,
                    IFNULL(social_commitment_lang.description, social_commitment.description) as description
              FROM social_commitment
              LEFT JOIN social_commitment_lang
                    ON  social_commitment_lang.id = social_commitment.id
                    AND social_commitment_lang.lang = :lang
              WHERE social_commitment.id = :id";
        $query = static::query($sql, array(':id' => $id, ':lang'=>$lang));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            return $item;
        }

        throw new ModelNotFoundException("Social commitment not found for ID [$id]");
    }

    /**
     * Social commitment list
     *
     * @param  array  $filters
     * @return mixed            Array of social commitments
     */
    public static function getAll($filters = array()) {

        $lang = Lang::current();

        $values = array();

        $list = array();

        if($sqlFilter) {
            $sqlFilter = 'WHERE ' . $sqlFilter;
            $order='ORDER BY name ASC';
        } else {
            $sqlFilter = '';
            $order='ORDER BY id ASC';
        }

        if(self::default_lang($lang) === Config::get('lang')) {
            $different_select=" IFNULL(social_commitment_lang.name, social_commitment.name) as name,
                                IFNULL(social_commitment_lang.description, social_commitment.description) as description";
        }
        else {
            $different_select=" IFNULL(social_commitment_lang.name, IFNULL(eng.name,social_commitment.name)) as name,
                                IFNULL(social_commitment_lang.description, IFNULL(eng.description,social_commitment.description)) as description";
            $eng_join=" LEFT JOIN social_commitment_lang as eng
                            ON  eng.id = social_commitment.id
                            AND eng.lang = 'en'";
        }

        $values[':lang']=$lang;

        $sql = "SELECT
                    social_commitment.id,
                    social_commitment.image,
                    $different_select
                FROM social_commitment
                LEFT JOIN social_commitment_lang
                    ON  social_commitment_lang.id = social_commitment.id
                    AND social_commitment_lang.lang = :lang
                $eng_join
                $sqlFilter
                $order
                ";

        $query = self::query($sql, $values);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
            if($item->image)
                $item->image=new Image($item->image);
            else
                $item->image=new Image();
            $list[] = $item;
        }
        return $list;
    }

     /**
         * Get the categories related with a social commitment
         * @param varcahr(50) $id  Social commitment idetifier
         * @return array of categories identifiers
         */
        public static function getCategories ($id) {
            $array = array ();
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

        if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);
                if ($image->save($errors)) {
                    $this->image = $image->id;
                } else {
                    $fail = true;
                    $this->image = '';
                }
        }

        $fields = array(
            'id',
            'name',
            'description',
            'image'
        );

        if($this->call_id) $fields[] = 'call_id';

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
            $errors[] = "Emtpy title";
        }
        return empty($errors);
    }


}


