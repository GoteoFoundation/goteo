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

use Goteo\Core\Model;
use Goteo\Library\Check;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\DuplicatedEventException;

class Milestone extends \Goteo\Core\Model {

    public
        $id,
        $type,
        $link,
        $description,
        $image,
        $image_emoji;
    /*
     *  Get milestone
     */
    public static function get ($id, $lang = null) {

            $lang=self::default_lang_by_id($id, "milestone_lang", $lang);

            $query = static::query("
                SELECT
                    milestone.id,
                    milestone.image,
                    milestone.image_emoji,
                    milestone.type,
                    milestone.link,
                    IFNULL(milestone_lang.description, milestone.description) as description
                FROM milestone
                LEFT JOIN milestone_lang
                    ON  milestone_lang.id = milestone.id
                    AND milestone_lang.lang = :lang
                WHERE milestone.id = :id
                ", array(':id' => $id, ':lang'=>$lang));

            if($milestone = $query->fetchObject('\Goteo\Model\Milestone')) {
                if($milestone->image)
                    $milestone->image = Image::get($milestone->image);
                if($milestone->image_emoji)
                    $milestone->image_emoji = Image::get($milestone->image_emoji);
            }

            return $milestone;
    }

    /*
     * Lista de categorias para proyectos
     * @TODO añadir el numero de usos
     */
    public static function getAll () {

        $list = array();

        if(Lang::current() === Config::get('lang')) {
            $different_select=" IFNULL(milestone_lang.description, milestone.description) as description";
        }
        else {
            $different_select=" IFNULL(milestone_lang.description, IFNULL(eng.description, milestone.description)) as description";
            $eng_join=" LEFT JOIN milestone_lang as eng
                            ON  eng.id = milestone.id
                            AND eng.lang = 'en'";
        }

        $sql="SELECT
                milestone.id as id,
                milestone.type as type,
                milestone.link as link,
                milestone.image as image,
                milestone.image_emoji as image_emoji,
                $different_select
            FROM    milestone
            LEFT JOIN milestone_lang
                ON  milestone_lang.id = milestone.id
                AND milestone_lang.lang = :lang
            $eng_join
            ORDER BY `ID` ASC";

        $query = static::query($sql, array(':lang'=>Lang::current()));

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $milestone) {
            $list[$milestone->id] = $milestone;
        }

        return $list;
    }


    public function validate (&$errors = array()) {
        if (empty($this->description))
            $errors[] = 'Falta descripcion';

        if (empty($errors))
            return true;
        else
            return false;
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

        // Save opcional image
        if (is_array($this->image) && !empty($this->image['name'])) {
            $image = new Image($this->image);

            if ($image->save($errors)) {
                $this->image = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        }
        if (is_null($this->image)) {
            $this->image = '';
        }

         // Save image emoji

        if (is_array($this->image_emoji) && !empty($this->image_emoji['name'])) {
            $image_emoji = new Image($this->image_emoji);

            if ($image_emoji->save($errors)) {
                $this->image_emoji = $image_emoji->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image_emoji = '';
            }
        }
        if (is_null($this->image_emoji)) {
            $this->image_emoji = '';
        }

        $fields = array(
            'type',
            'link',
            'description',
            'image',
            'image_emoji'
        );

        try {
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Milestone save error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Random milestone by type
     * @param   type array
     * @return  type bool
    */
    static public function random_milestone($type, $field='type') {
        if ($query = static::query("SELECT * FROM milestone WHERE `$field` = ? ORDER BY rand() LIMIT 1", $type)) {

            if( $milestone = $query->fetchObject(__CLASS__) )
                return $milestone;
        }

        throw new ModelNotFoundException("Milestone for [$type] not found");
    }

}
