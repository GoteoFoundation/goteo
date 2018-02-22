<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Library\Translator;

use Goteo\Core\Model;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Text;

/*
 * Class to handle translations
 *
 */
class ModelTranslator implements TranslatorInterface {
    // Some hard-coded disabled fields and types for old-compatibility
    static protected $_disabled_fields = [
        'post' => ['blog', 'media']
    ];
    static protected $_forced_models = [
        'worthcracy' => 'Worth',
        'tag' => 'Blog\Post\Tag',
        'opentag' => 'OpenTag',
        'social_commitment' => 'SocialCommitment',
    ];
    static protected $_forced_types = [
        'post' => 'html',
        'info' => 'html',
        'glossary' => 'html'
    ];
    static protected $_forced_filters = [
        'post' => '`blog` = 1' // Field "blog" in table "post" must be "1" (goteo general posts)
    ];
    static protected $_forced_fields = [
        'post' => ['blog' => 1] // Field "blog" in table "post" must be "1" (goteo general posts)
    ];
    static protected $_orders = [];
    static protected $_fields = [];
    protected $_translations = []; //Cached fields for translated content
    protected $_table = '';
    protected $_table_lang = '';

    public $translations = []; // language list of available translations
    public $pendings = []; // Language list of pending translations
    public $original = ''; // Original language

    static protected function getTableFromZone($zone) {
        $model = ucfirst($zone);
        if(isset(self::$_forced_models[$zone])) {
            $model = self::$_forced_models[$zone];
        }
        $full_model = "Goteo\\Model\\" . $model;
        if(class_exists($full_model)) {
            $table = (new $full_model)->getTable();
        } else {
            $full_model = "Goteo\\Library\\" . $model;
            if(class_exists($full_model)) {
                $table = $zone;
            }
        }
        if(!$table) {
            throw new ModelException(Text::get('translator-zone-not-found'));
        }
        return $table;
    }
    /**
     * Returns an Array with all the fields available in the Model for translation
     * @return Array fields
     */
    static public function getFields($zone) {
        if(isset(self::$_fields[$zone])) return self::$_fields[$zone]; // cached

        $table = self::getTableFromZone($zone);
        $table_lang = $table . '_lang';

        $sql = "SHOW columns FROM `$table_lang`";
        $query = Model::query($sql);
        $fields = [];
        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
            if(isset(self::$_disabled_fields[$table]) &&
               in_array($ob->Field, self::$_disabled_fields[$table])) continue;
            // print_r($ob);
            $type = 'char';
            if(strpos($ob->Type, 'text') !== false && $ob->Type !== 'tinytext') $type = 'text';
            if(!in_array($ob->Field, ['id', 'lang'])) $fields[$ob->Field] = $type;
            if($ob->Field == 'id') {
                if(strpos($ob->Type, 'int')) {
                    self::$_orders[$table][] = 'id DESC';
                } else {
                    self::$_orders[$table][] = 'id ASC';
                }
            }
        }
        self::$_fields[$zone] = $fields;
        return $fields;
    }

    static public function getOrders($zone) {
        self::getFields($zone);
        return self::$_orders;
    }

    static public function get($zone, $id) {
        $table = self::getTableFromZone($zone);
        $table_lang = $table . '_lang';
        $fields = self::getFields($zone);
        $sql_translations = "SELECT GROUP_CONCAT(`$table_lang`.lang SEPARATOR ',') FROM `$table_lang` WHERE `$table_lang`.id = `$table`.id";
        if(array_key_exists('pending', $fields))
            $sql_pending = "SELECT GROUP_CONCAT(`$table_lang`.lang SEPARATOR ',') FROM `$table_lang` WHERE `$table_lang`.id = `$table`.id AND `$table_lang`.pending=1";
        else $sql_pending = "''";

        $sql = "SELECT `$table`.*,
            ($sql_translations) AS translations,
            ($sql_pending) AS pendings
            FROM `$table`
            WHERE `$table`.id = :id";
        $query = Model::query($sql, [':id' => $id]);
        $ob = $query->fetchObject(__CLASS__);
        $ob->_table = $table;
        $ob->_table_lang = $table_lang;
        return $ob;
    }

    /**
     * Generic paginated getList for any model with translations
     * @param  [type]  $zone    [description]
     * @param  array   $filters [description]
     * @param  integer $offset  [description]
     * @param  integer $limit   [description]
     * @param  boolean $count   [description]
     * @return [type]           [description]
     */
    static public function getList($zone, $filters = [], $offset = 0, $limit = 20, $count = false) {
        $table = self::getTableFromZone($zone);
        $table_lang = $table . '_lang';
        $fields = self::getFields($zone);

        $bind = [];
        $sqlFilter = [];
        $fSqlFilter = [];
        $i = 0;
        if(isset($filters['pending'])) {
            $pending = $filters['pending'];
            unset($filters['pending']);
        }

        foreach($filters as $k => $q) {
            $sqlFilter[] = "`$k` LIKE :q$i";
            $bind[":q$i"] = "%$q%";
            $i++;
        }
        if(isset(self::$_forced_filters[$table])) {
            $fSqlFilter[] = self::$_forced_filters[$table];
        }
        if($pending) {
            $fSqlFilter[] = "(
                1 IN (SELECT pending FROM `$table_lang` WHERE `$table_lang`.id=`$table`.id AND `$table_lang`.lang = :pending)
                OR
                0 IN (SELECT count(id) FROM `$table_lang` WHERE `$table_lang`.id=`$table`.id AND `$table_lang`.lang = :pending)
            )";
            $bind[':pending'] = $pending;
        }
        $sqlFilter = implode(" OR ", $sqlFilter);
        $fSqlFilter = implode(" AND ", $fSqlFilter);
        if($sqlFilter) $sqlFilter = " WHERE ($sqlFilter)";
        if($fSqlFilter) {
            if($sqlFilter) $sqlFilter .= " AND ($fSqlFilter)";
            else $sqlFilter = " WHERE $fSqlFilter";
        }
        if($count) {
            // Return count
            $sql = "SELECT COUNT(id)
                FROM `$table`
                $sqlFilter";
            return (int) Model::query($sql, $bind)->fetchColumn();
        }

        $order = '';
        $orders = self::getOrders($zone);
        if(isset($orders[$table])) {
            $order = ' ORDER BY ' . implode(', ', $orders[$table]);
        }
        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql_translations = "SELECT GROUP_CONCAT(`$table_lang`.lang SEPARATOR ',') FROM `$table_lang` WHERE `$table_lang`.id = `$table`.id";
        if(array_key_exists('pending', $fields))
            $sql_pending = "SELECT GROUP_CONCAT(`$table_lang`.lang SEPARATOR ',') FROM `$table_lang` WHERE `$table_lang`.id = `$table`.id AND `$table_lang`.pending=1";
        else $sql_pending = "''";
        $sql = "SELECT
            `$table`.*,
            ($sql_translations) AS translations,
            ($sql_pending) AS pendings
            FROM `$table`
            $sqlFilter
            $order
            LIMIT $offset, $limit";

        // die(\sqldbg($sql, $bind));

        $query = Model::query($sql, $bind);
        $list = [];
        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $ob) {
            $ob->_table = $table;
            $ob->_table_lang = $table_lang;
            $list[] = $ob;
        }
        return $list;
    }

    // ///////////////////
    // Non-static methods
    // ///////////////////

    public function __construct() {
        if(!is_array($this->translations)) $this->translations = explode(',', $this->translations);
        if(!is_array($this->pendings)) $this->pendings = explode(',', $this->pendings);
        // TODO: some models can have custom original languages (ie: projects)
        $this->original = Config::get('lang');
        $this->original_name = Lang::getName($this->original);
    }

    public function getTranslation($lang, $field = null, $respect_original = false) {
        if($respect_original && $this->isOriginal($lang)) {
            return $this->{$field};
        }
        if(empty($this->_translations)) {
            $sql = "SELECT * FROM `{$this->_table_lang}` WHERE id=:id";
            $bind = [':id' => $this->id];
            // die(\sqldbg($sql, $bind));
            $query = Model::query($sql, $bind);

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
                $this->_translations[$ob->lang] = $ob;
            }
        }
        // print_r($this->_translations);
        if(!isset($this->_translations[$lang])) return '';

        $ob = $this->_translations[$lang];
        if($field && is_object($ob)) {
            return $ob->{$field};
        }

        return $ob;
    }

    /**
     * Returns type of text (markdown, html or plain)
     * @return string md,html,plain
     */
    public function getType() {
        if(isset(self::$_forced_types[$this->_table])) {
            return self::$_forced_types[$this->_table];
        }
        if(isset($this->type) && in_array($this->type, ['md', 'html']))
            return $this->type;
        return 'plain';
    }

    public function isOriginal($lang) {
        return $this->original == $lang;
    }

    public function isTranslated($lang) {
        return in_array($lang, $this->translations);
    }

    public function isPending($lang) {
        return in_array($lang, $this->pendings);
    }

    public function delete($lang) {
        if(!$this->id) throw new ModelException("Error: Missing ID field int `{$this->_table}`");

        $sql = "DELETE FROM `{$this->_table_lang}` WHERE `lang` = :lang AND id = :id";
        $bind = [':lang' => $lang, ':id' => $this->id];
        // die(\sqldbg($sql, $bind));
        Model::query($sql, $bind);
    }

    public function save($lang, $values) {
        if(!$this->id) throw new ModelException("Error: Missing ID field int `{$this->_table}`");

        $bind = [':id' => $this->id];
        if($lang !== $this->original) {
            $bind[':lang'] = $lang;
        }

        if(isset(self::$_forced_fields[$this->_table])) {
            foreach(self::$_forced_fields[$this->_table] as $k => $v) {
                $values[$k] = $v;
            }
        }
        $update = [];
        foreach($values as $key => $val) {
            $bind[":$key"] = $val;
            $update[] = "`$key` = :$key";
        }
        if($lang !== $this->original) {
            $sql = "INSERT INTO `{$this->_table_lang}`
            (`id`, `lang`, `" . implode('`,`', array_keys($values)) . "`)
            VALUES (" . implode(', ', array_keys($bind)) . ")
            ON DUPLICATE KEY UPDATE " . implode(', ', $update);
        } else {
            $sql = "UPDATE `{$this->_table}`
            SET " . implode(', ', $update) ."
            WHERE `id` = :id";

        }
        // die(\sqldbg($sql, $bind));
        Model::query($sql, $bind);
    }
}
