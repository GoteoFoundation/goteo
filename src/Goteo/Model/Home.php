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
use Goteo\Library\Check;

class Home extends \Goteo\Core\Model {

    public
        $item,
        $type,
        $node,
        $order;

    /*
     *  Devuelve datos de un elemento
     */
    public static function get ($item, $node = null) {
        if(empty($node)) $node = Config::get('node');
        $query = self::query('
            SELECT *
            FROM    home
            WHERE home.item = :item
            AND home.node = :node
            ', array(':item' => $item, ':node'=>$node));
        $home = $query->fetchObject(__CLASS__);

        return $home;
    }

    /*
     * Devuelve elementos en portada
     */
    public static function getAll ($node = null, $type = 'main') {
        if(empty($node)) $node = Config::get('node');
        $array = array();
        $values = array(':node' => $node, ':type' => $type);
        $sql = 'SELECT
                    home.item as item,
                    home.node as node,
                    home.type as type,
                    home.order as `order`
                FROM home
                WHERE home.node = :node
                AND type = :type
                ORDER BY `order` ASC
                ';

        $query = self::query($sql, $values);
        foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $home) {
            $array[$home->item] = $home;
        }
        return $array;
    }

    public function validate (&$errors = array()) {
        if (empty($this->item))
            $errors[] = 'Falta elemento';

        if (empty($this->node))
            $errors[] = 'Falta nodo';

        if (empty($this->type))
            $this->type = 'main';

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        $fields = array(
            'item',
            'type',
            'node',
            'order'
            );
        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);
            $extra = array(
                'node' => $this->node,
                'type' => $this->type
            );
            Check::reorder($this->item, $this->move, 'home', 'item', 'order', $extra);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Save error " . $e->getMessage();
            return false;
        }
    }

    /*
     * Para quitar un elemento
     */
    public static function remove ($item, $node, $type = 'main') {

        $sql = "DELETE FROM home WHERE item = :item AND node = :node AND type = :type";
        try {
            self::query($sql, array(':item'=>$item, ':node'=>$node, ':type'=>$type));
        } catch (\PDOException $e) {
            // throw new Exception("Delete error in $sql");
            return false;
        }
        return true;
    }

    /*
     * Para que un elemento salga antes  (disminuir el order)
     */
    public static function up ($item, $node = null, $type = 'main') {
        if(empty($node)) $node = Config::get('node');
        $extra = array(
            'node' => $node,
            'type' => $type
        );
        return Check::reorder($item, 'up', 'home', 'item', 'order', $extra);
    }

    /*
     * Para que un elemento salga despues  (aumentar el order)
     */
    public static function down ($item, $node = null, $type = 'main') {
        if(empty($node)) $node = Config::get('node');
        $extra = array(
            'node' => $node,
            'type' => $type
        );
        return Check::reorder($item, 'down', 'home', 'item', 'order', $extra);
    }

    /*
     * Orden para añadirlo al final
     */
    public static function next ($node = null, $type = 'main') {
        if(empty($node)) $node = Config::get('node');
        $query = self::query('SELECT MAX(`order`) FROM home WHERE node = :node AND type = :type'
            , array(':node'=>$node, ':type'=>$type));
        $order = $query->fetchColumn(0);
        return ++$order;

    }

}
