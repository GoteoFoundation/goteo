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
use Goteo\Application\Config;


class Sponsor extends Model {

    const SPONSOR_NETWORK = 'network';
    const SPONSOR_SUPPORT = 'support';

    static $SPONSORS_LIST = [self::SPONSOR_SUPPORT, self::SPONSOR_NETWORK];

    public
        $id,
        $node,
        $name,
        $url,
        $image,
        $type,
        $order;

    public static function get ($id) {
        $sql = static::query("
                SELECT
                    id,
                    node,
                    name,
                    url,
                    image,
                    type,
                    `order`
                FROM    sponsor
                WHERE id = :id
                ", array(':id' => $id));
        $sponsor = $sql->fetchObject(__CLASS__);

        return $sponsor;
    }

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
                type,
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

    public static function getList(
        $filters = array(),
        int $offset = 0,
        int $limit = 10,
        bool $count = false
    ) {
        $sqlWhere = "";
        $values = [];
        $values[':node'] = ($filters['node'])? $filters['node']: Config::get('current_node');
        $list = array();

        if ($filters['type']) {
            $values['type'] = $filters['type'];
            $sqlWhere = " AND `type` = :type";
        }

        $sql = "
            SELECT
                id,
                name,
                url,
                image,
                type
            FROM    sponsor
            WHERE node = :node
            $sqlWhere
            ORDER BY `order` ASC, name ASC
            LIMIT $offset, $limit
            ";

        if($count) {
            $sql = 'SELECT COUNT(id) FROM sponsor where node = :node' . $sqlWhere;
            return (int) self::query($sql, $values)->fetchColumn();
        }
        $query = static::query($sql, $values);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $sponsor) {
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

        // First the image
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
            'node',
            'name',
            'url',
            'image',
            'type',
            'order'
        );
        try {
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

    public static function getTypes(): array
    {
        return self::$SPONSORS_LIST;
    }

}

