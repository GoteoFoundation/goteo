<?php

namespace Goteo\Model;

use Goteo\Library\Check;
use Goteo\Model\Image;
use Goteo\Application\Config;


class Sponsor extends \Goteo\Core\Model {

    public
        $id,
        $node,
        $name,
        $url,
        $image,
        $order;

    /*
     *  Devuelve datos de un destacado
     */
    public static function get ($id) {
            $sql = static::query("
                SELECT
                    id,
                    node,
                    name,
                    url,
                    image,
                    `order`
                FROM    sponsor
                WHERE id = :id
                ", array(':id' => $id));
            $sponsor = $sql->fetchObject(__CLASS__);

            return $sponsor;
    }

    /*
     * Lista de patrocinadores (para panel admin)
     */
    public static function getAll ($node = null) {
        if(empty($node)) $node = Config::get('current_node');

        $list = array();

        $sql = static::query("
            SELECT
                id,
                node,
                name,
                url,
                image,
                `order`
            FROM    sponsor
            WHERE node = :node
            ORDER BY `order` ASC, name ASC
            ", array(':node'=>$node));

        foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {
            $list[] = $sponsor;
        }

        return $list;
    }

    /*
     * Lista de patrocinadores
     */
    public static function getList ($node = null) {
        if(empty($node)) $node = Config::get('current_node');

        $list = array();

        $sql = "
            SELECT
                id,
                name,
                url,
                image
            FROM    sponsor
            WHERE node = :node
            ORDER BY `order` ASC, name ASC
            ";

        // echo $sql;

        $query = static::query($sql, array(':node'=>$node));

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {

           // echo \trace($sponsor);

            // imagen
            $sponsor->image = Image::get($sponsor->image);

            $list[] = $sponsor;
        }

        return $list;
    }

    public function validate (&$errors = array()) {
        if (empty($this->name))
            $errors[] = 'Falta nombre';

        if (empty($this->url))
            $errors[] = 'Falta url';

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;
        $fail = false;
        // Primero la imagenImagen
        if (is_array($this->image) && !empty($this->image['name'])) {
            $image = new Image($this->image);

            if ($image->save($errors)) {
                $this->image = $image->id;
            } else {
                //mmmm
                $fail = true;
                $this->image = '';
            }
        }

        $fields = array(
            'id',
            'node',
            'name',
            'url',
            'image',
            'order'
            );
        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            Check::reorder($this->id, 'up', 'sponsor');

            return !$fail;
        } catch(\PDOException $e) {
            $errors[] = 'Save error ' . $e->getMessage();
            return false;
        }
    }

    /*
     * Para que salga antes  (disminuir el order)
     */
    public static function up ($id, $node = null) {
        if(empty($node)) $node = Config::get('current_node');
        $extra = array (
                'node' => $node
            );
        return Check::reorder($id, 'up', 'sponsor', 'id', 'order', $extra);
    }

    /*
     * Para que salga despues  (aumentar el order)
     */
    public static function down ($id, $node = null) {
        if(empty($node)) $node = Config::get('current_node');
        $extra = array (
                'node' => $node
            );
        return Check::reorder($id, 'down', 'sponsor', 'id', 'order', $extra);
    }

    /*
     * Orden para añadirlo al final
     */
    public static function next ($node = null) {
        if(empty($node)) $node = Config::get('current_node');
        $sql = self::query('SELECT MAX(`order`) FROM sponsor WHERE node = :node', array(':node' => $node));
        $order = $sql->fetchColumn(0);
        return ++$order;

    }

}

