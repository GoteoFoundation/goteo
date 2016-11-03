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
class TextTranslator implements TranslatorInterface {
    public $translations = []; // language list of available translations
    public $pendings = []; // Language list of pending translations
    public $original = ''; // Original language
    static protected $_translations = [];
    static protected $_pendings = [];

    static protected function getAllCatalogue() {

        if(self::$_translations) return self::$_translations; // Cached

        foreach(Lang::listAll('name', false) as $lang => $name) {
            $all = Lang::translator()->getCatalogue($lang)->all('messages');
            foreach($all as $k => $v) {
                self::$_translations[$k][$lang] = $v;
            }
            // $all = Lang::translator()->getCatalogue($lang)->all('sql');
            // foreach($all as $k => $v) {
            //     self::$_translations[$k][$lang] = [$v, false];
            // }
            $sql = "SELECT * FROM text
             WHERE lang = :lang";
            $values = array(':lang' => $lang);
            $query = Model::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
                self::$_translations[$ob->id][$lang] = $ob->text;
                if($ob->pending)
                    self::$_pendings[$ob->id][] = $lang;
            }
        }
        return self::$_translations;
    }

    static public function getFields($zone) {
        return ['text' => 'text', 'pending' => 'int'];
    }

    static public function getOrders($zone) {
        return [];
    }

    static public function get($zone, $id) {
        $translator = new self($id);
        return $translator;
    }

    static public function getList($zone, $filters = [], $offset = 0, $limit = 20, $count = false) {
        $catalogue = self::getAllCatalogue();
        $i = 0;
        $list = [];
        if(isset($filters['pending'])) {
            $pending = $filters['pending'];
            unset($filters['pending']);
        }
        foreach($catalogue as $id => $trans) {
            $ok = empty($filters);
            if(isset($filters['id'])) {
                if(stripos($id, $filters['text']) !== false) $ok = true;
            }
            if(isset($filters['text'])) {
                foreach($trans as $l => $v) {
                    if(stripos($v, $filters['text']) !== false) $ok = true;
                }
            }
            if(!$ok) continue;
            if($pending) {
                if(array_key_exists($pending, $trans)) continue;
            }

            $i++;
            if($i <= $offset) continue;
            if($i <= $offset + $limit) {
                $t = new self($id);
                $list[] = $t;
            }
        }
        if($count) return $i;
        return $list;
    }

    public function __construct($id) {
        $this->id = $id;
        $this->original = Config::get('lang');
        $this->original_name = Lang::getName($this->original);
        $this->translations = [];
        $this->pendings = [];
        $catalogue = self::getAllCatalogue();
        $this->text = $catalogue[$this->id][$this->original];
        $this->translations = array_keys($catalogue[$this->id]);
        $this->pendings = isset(self::$_pendings[$this->id]) ? self::$_pendings[$this->id] : [];
    }

    public function getTranslation($lang, $field = null, $respect_original = false) {
        if($respect_original && $this->isOriginal($lang)) {
            return $this->{$field};
        }
        $catalogue = self::getAllCatalogue();
        return $catalogue[$this->id][$lang];

    }
    public function getType() {
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
        $q = Model::query("SELECT COUNT(*) FROM `text` WHERE id=:id AND lang = :lang", [':id' => $this->id, ':lang' => $lang]);
        if((int) $q->fetchColumn())
            Text::delete($this->id, $lang);
        else throw new ModelException(Text::get('translator-cannot-delete-system'));

    }

    public function save($lang, $values) {
        $values['id'] = $this->id;
        $values['lang'] = $lang;
        return Text::save($values);
    }
}
