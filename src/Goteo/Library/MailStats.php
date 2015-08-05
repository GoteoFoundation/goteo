<?php

namespace Goteo\Library;

use Goteo\Core\Model;

class MailStats {
    protected $Table = 'mail_stats';
    public
        $mail_id,
        $email,
        $metric,
        $created_at,
        $modified_at,
        $counter;

    /**
     * Returns one value from:
     * @param  [type] $mail_id [description]
     * @param  [type] $email   [description]
     * @param  [type] $metric  [description]
     * @return [type]          [description]
     */
    static public function getMetric($mail_id, $email, $metric) {
        $query = Model::query('
            SELECT *
            FROM  mail_stats
            WHERE mail_stats.mail_id = :mail_id
            AND   mail_stats.email = :email
            AND   mail_stats.metric = :metric
            ', array(':mail_id' => $mail_id, ':email' => $email, ':metric' => $metric));

        if($query) {
            return $query->fetchObject(__CLASS__);
        }

        return false;

    }
    /*
     *  Devuelve datos de un elemento
     */
    static public function getFromMailId($mail_id, $offset = 0, $limit = 10, $count = false) {
        $values = array(':mail_id' => $mail_id);
        if($count) {
            $sql = "SELECT COUNT(*) FROM mail_stats WHERE mail_stats.mail_id = :mail_id";
            return (int) Model::query($sql, $values)->fetchColumn();
        }
        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "
            SELECT *
            FROM  mail_stats
            WHERE mail_stats.mail_id = :mail_id
            LIMIT $offset, $limit";

        $list = [];
        if ($query = Model::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * Increments a metric
     * @param  [type] $mail_id [description]
     * @param  [type] $email   [description]
     * @param  string $metric  [description]
     * @return [type]          [description]
     */
    static public function incrMetric($mail_id, $email, $metric = 'read') {
        try {
            if($mail = static::getMetric($mail_id, $email, $metric)) {
                $mail->counter = $mail->counter + 1;
                return Model::query('
                    UPDATE mail_stats
                    SET counter = :counter
                    WHERE mail_stats.mail_id = :mail_id
                    AND   mail_stats.email = :email
                    AND   mail_stats.metric = :metric
                    ', array(':counter' => $mail->counter, ':mail_id' => $mail->mail_id, ':email' => $mail->email, ':metric' => $mail->metric));
            }
            else {
                $mail = new MailStats();
                $mail->mail_id = $mail_id;
                $mail->email = $email;
                $mail->metric = $metric;
                $mail->counter = 1;
                $mail->created_at = date("Y-m-d H:i:s");
                return Model::query('
                INSERT INTO mail_stats
                (counter, mail_id, email, metric, created_at)
                VALUES
                (:counter, :mail_id, :email, :metric, :created_at)
                ', array(':counter' => $mail->counter, ':mail_id' => $mail->mail_id, ':email' => $mail->email, ':metric' => $mail->metric, ':created_at' => $mail->created_at));

            }
        }
        catch(\PDOException $e) {
            // TODO: log this
        }
        return false;
    }

}
