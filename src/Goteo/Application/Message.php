<?php

namespace Goteo\Application;

class Message {

    public
        $type,
        $content;

    function __construct($type, $content) {
        $this->type = $type;
        $this->content = $content;
        $current = self::getAll();
        $current[md5($content)] = $this;
        Session::store('messages', $current);
    }

    public static function Info($text) {
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

    public static function Error($text) {
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
    public static function getMessages() {
        $msgs = array();
        foreach(self::getAll() as $msg) {
            if($msg->type === 'info') $msgs[] = $msg->content;
        }
        return $msgs;
    }
    public static function getErrors() {
        $msgs = array();
        foreach(self::getAll() as $msg) {
            if($msg->type === 'error') $msgs[] = $msg->content;
        }
        return $msgs;
    }
}
