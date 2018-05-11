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

use Goteo\Application\Lang;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;

class Log extends \Goteo\Core\Model {

    public $scope,
           $user_id,
           $target_type,
           $target_id,
           $text,
           $url,
           $datetime;

    /**
     * Returns a log entry by ID
     */
    public static function get ($id) {
        $sql = "SELECT * FROM `log` WHERE log.id=?";

        if($query = self::query($sql, $id)) {
            if($log = $query->fetchObject(__CLASS__)) {
                return $log;
            }
        }
        return null;
    }

    /**
     * Appends an entry to the log, throws exception on failure
     * Automatically adds the user if in session
     * @return Log instance created
     */
    public static function append($vars = []) {
        if(Session::isLogged()) $vars['user_id'] = Session::getUserId();
        $log = new self($vars);
        $errors = [];
        if(!$log->save($errors)) {
            throw new ModelException(implode("\n", $errors));
        }
        return $log;
    }

    public function validate(&$errors = []) {
        if(empty($this->scope)) $errors[] = '[scope] is mandatory';
        return empty($errors);
    }

    public function save(&$errors = []) {
        if(!$this->validate($errors)) return false;

        $fields = ['scope', 'user_id', 'target_type', 'target_id', 'text', 'url'];

        try {
            $this->dbInsertUpdate($fields);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving log entry: ' . $e->getMessage();
        }

        return false;

    }
}
