<?php

namespace Goteo\Model\Mail;

class MailStatsLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'mail_stats_location';
    public $mail_stats;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->mail_stats = $this->id;
    }

    public static function get($mail_stats) {
        $id = $mail_stats;
        if($mail_stats instanceOf MailStats) {
            $id = $mail_stats->id;
        }
        return parent::get($id);
    }
}

