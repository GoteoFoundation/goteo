<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Mail;

class MailStatsLocation extends \Goteo\Model\Location\LocationItem
{
    protected $Table = 'mail_stats_location';
    protected static $Table_static = 'mail_stats_location';
    public $mail_stats;

    public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->mail_stats = $this->id;
    }

    public static function get($mail_stats)
    {
        $id = $mail_stats;
        if($mail_stats instanceOf MailStats) {
            $id = $mail_stats->id;
        }
        return parent::get($id);
    }
}

