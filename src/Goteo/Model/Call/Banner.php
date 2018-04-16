<?php

namespace Goteo\Model\Call;

use Goteo\Application\Config;
use Goteo\Library\Check;
use Goteo\Model\Image;


class Banner extends \Goteo\Core\Model {
    //table for this model is not banner but call_banner
    protected $Table = 'call_banner';

    public
        $id,
        $call,
        $name,
        $url,
        $image,
        $order;

    static function getLangFields() {
        return ['name'];
    }

    /*
     *  Devuelve datos de un destacado
     */
    public static function get ($id) {
            $sql = static::query("
                SELECT
                    id,
                    `call`,
                    name,
                    url,
                    image,
                    `order`
                FROM    call_banner
                WHERE id = :id
                ", array(':id' => $id));
            $banner = $sql->fetchObject(__CLASS__);

            if (!empty($banner->image)) {
                $banner->image = Image::get($banner->image);
            }

            return $banner;
    }

    /*
     * Lista de patrocinadores
     */
    public static function getAll ($call, $lang = null, $model_lang = null) {

        $list = array();
        if(!$model_lang) $model_lang = Config::get('lang');

        if(self::default_lang($lang) == $model_lang) {
            $different_select=" IFNULL(call_banner_lang.name, call_banner.name) as name";
            }
        else {
                $different_select=" IFNULL(call_banner_lang.name, IFNULL(eng.name, call_banner.name)) as name";
                $eng_join=" LEFT JOIN call_banner_lang as eng
                                ON  eng.id = call_banner.id
                                AND eng.lang = 'en'";
            }

        $sql = static::query("
            SELECT
                call_banner.id,
                call_banner.call,
                $different_select,
                call_banner.url,
                call_banner.image,
                call_banner.order
            FROM    call_banner
            LEFT JOIN call_banner_lang
                ON  call_banner_lang.id = call_banner.id
                AND call_banner_lang.lang = :lang
            $eng_join
            WHERE call_banner.call = :call
            ORDER BY call_banner.order ASC, call_banner.id ASC
            ", array(':call'=>$call, ':lang'=>$lang));

        foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
            $list[] = $banner;
        }

        return $list;
    }

    /*
     * Lista de patrocinadores
     */
    public static function getList ($call, $lang = null, $model_lang = null) {

        $list = array();
        if(!$model_lang) $model_lang = Config::get('lang');

        if(self::default_lang($lang) == $model_lang) {
            $different_select=" IFNULL(call_banner_lang.name, call_banner.name) as name";
            }
        else {
            $different_select=" IFNULL(call_banner_lang.name, IFNULL(eng.name, call_banner.name)) as name";
            $eng_join=" LEFT JOIN call_banner_lang as eng
                            ON  eng.id = call_banner.id
                            AND eng.lang = 'en'";
            }

        $sql = "
            SELECT
                call_banner.id,
                call_banner.call,
                $different_select,
                call_banner.url,
                call_banner.image,
                call_banner.order
            FROM    call_banner
            LEFT JOIN call_banner_lang
                ON  call_banner_lang.id = call_banner.id
                AND call_banner_lang.lang = :lang
            $eng_join
            WHERE call_banner.call = :call
            ORDER BY call_banner.order ASC, call_banner.id ASC
            ";

        // die(\sqldbg($sql, [':call'=>$call, ':lang' => $lang]));

        $query = static::query($sql, array(':call'=>$call, ':lang'=>$lang));


        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
            // imagen
            if (!empty($banner->image)) {
                $banner->image = Image::get($banner->image);
            }

            $list[] = $banner;
        }

        return $list;
    }

    public function validate (&$errors = array()) {
        if (empty($this->call))
            $errors[] = 'Falta convocatoria';
/*
        if (empty($this->name))
            $errors[] = 'Falta nombre';

        if (empty($this->url))
            $errors[] = 'Falta enlace';
*/
        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        // Primero la imagenImagen
        if (is_array($this->image) && !empty($this->image['name'])) {
            $image = new Image($this->image);

            if ($image->save($errors)) {
                $this->image = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        } elseif ($this->image instanceof Image) {
            $this->image = $this->image->id;
        }

        $fields = array(
            'id',
            'call',
            'name',
            'url',
            'image',
            'order'
            );

        $set = '';
        $values = array();

        foreach ($fields as $field) {
            if ($set != '') $set .= ", ";
            $set .= "`$field` = :$field ";
            $values[":$field"] = $this->$field;
        }

        try {
            $sql = "REPLACE INTO call_banner SET " . $set;
            self::query($sql, $values);
            if (empty($this->id)) $this->id = self::insertId();

            return true;
        } catch(\PDOException $e) {
            $errors[] = "HA FALLADO!!! " . $e->getMessage();
            return false;
        }
    }

    public function saveLang (&$errors = array()) {
            $fields = array(
                    'id'=>'id',
                    'lang'=>'lang',
                    'name'=>'name_lang'
                    );

            $set = '';
            $values = array();

            foreach ($fields as $field=>$ffield) {
                    if ($set != '') $set .= ", ";
                    $set .= "$field = :$field ";
                    $values[":$field"] = $this->$ffield;
            }

            try {
                    $sql = "REPLACE INTO call_banner_lang SET " . $set;
                    self::query($sql, $values);

                    return true;
            } catch(\PDOException $e) {
                    $errors[] = "El banner no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
    return false;
            }
    }

    /*
     * Para que salga antes  (disminuir el order)
     */
    public static function up ($id, $call) {
        $extra = array (
                'call' => $call
            );
        return Check::reorder($id, 'up', 'call_banner', 'id', 'order', $extra);
    }

    /*
     * Para que salga despues  (aumentar el order)
     */
    public static function down ($id, $call) {
        $extra = array (
                'call' => $call
            );
        return Check::reorder($id, 'down', 'call_banner', 'id', 'order', $extra);
    }

    /*
     * Orden para añadirlo al final
     */
    public static function next ($call) {
        $sql = self::query('SELECT MAX(`order`) FROM call_banner WHERE `call` = :call', array(':call' => $call));
        $order = $sql->fetchColumn(0);
        return ++$order;

    }

}

