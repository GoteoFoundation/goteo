<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\Invest;

use Goteo\Application\Config;
use Goteo\Application\Lang;

/**
 * Invest Msg Model (sustainable development goals)
 */
class InvestMsg extends \Goteo\Core\Model {

    public $invest,
           $msg;

    protected $Table = 'invest_msg';
    protected static $Table_static = 'invest_msg';

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
    }

    /**
     * Get instance of investMsg already in the table by action
     * @return [type] [description]
     */
    static public function get($invest, $lang = null) {
        $values = [':invest' => $invest];

        $sql = "SELECT m.invest,
                       m.msg
            FROM `invest_msg` as m
            WHERE m.invest = :invest";

        // print(\sqldbg($sql, $values));
        if ($query = static::query($sql, $values)) {
            if( $msg = $query->fetchObject(__CLASS__) ) {
                return $msg;
            }
        }
        return null;
    }

    /**
     * Save.
     * @param   type array  $errors     Error by reference
     * @return  type bool   true|false
     */
    public function save(&$errors = []) {

        if(!$this->validate($errors)) return false;

        
        $fields = ['invest', 'msg'];
        try {
            $this->dbInsertUpdate($fields);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving invest support message: ' . $e->getMessage();
        }

        return false;
    }

    
    /**
     * Validation
     * @param   type array  $errors     Error by reference
     * @return  type bool   true|false
     */
    public function validate(&$errors = []) {
        if(empty($this->msg)) $errors[] = 'Empty msg property';
        return empty($errors);
    }

}
