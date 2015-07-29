<?php

namespace Goteo\Model;

use Goteo\Application\Config;

class MailStats extends \Goteo\Core\Model {

    public
        $mail_id,
        $email,
        $metric,
        $created_at,
        $modified_at,
        $counter;

    /*
     *  Devuelve datos de un elemento
     */
    public static function getFromMail($id) {
        $query = self::query('
            SELECT *
            FROM    mail_stats
            WHERE mail_stats.mail_id = :id
            ', array(':id' => $id));

        if($query) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        return [];
    }

}
