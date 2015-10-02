<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

class Message {

    public
        $type,
        $content;

    function __construct($type, $content) {
        $this->type = $type;
        $this->content = $content;
        $current = self::getAll();
        $current[md5("$type-$content")] = $this;
        Session::store('messages', $current);
    }

    /**
     * Deletes the message from the session
     * @return [type] [description]
     */
    public function del() {
        $current = self::getAll();
        $total = count($current);
        unset($current[md5("{$this->type}-{$this->content}")]);
        Session::store('messages', $current);
        return (count(self::getAll()) < $total);
    }

    ///////////////////////////
    ///    STATIC METHODS   ///
    ///////////////////////////

    public static function info($text) {
        if(is_array($text) && !empty($text)) {
            foreach($text AS $msg) {
                new self('info', $msg);
            }
        }
        elseif(!empty($text)) {
            new self('info', $text);
        }
        return true;
    }

    public static function error($text) {
        if(is_array($text) && !empty($text)) {
            foreach($text AS $msg) {
                new self('error', $msg);
            }
        }
        elseif(!empty($text)) {
            new self('error', $text);
        }
        return false;
    }

    public static function clear() {
        Session::del('messages');
    }


    public static function getAll() {
        $msgs = Session::get('messages');
        if(!is_array($msgs)) $msgs = array();
        return $msgs;
    }
    /**
     * Obtains the message bag
     * @param  boolean $autoexpire Delete messages after retrieving
     * @param  string  $type       info or error
     * @return array               the messages collection
     */
    public static function getMessages($autoexpire = true, $type = 'info') {
        $msgs = array();
        foreach(self::getAll() as $msg) {
            if($msg->type === $type) {
                $msgs[] = $msg->content;
                if($autoexpire) {
                    $msg->del();
                }
            }
        }
        return $msgs;
    }

    public static function getErrors($autoexpire = true) {
        return self::getMessages($autoexpire, 'error');
    }
}
