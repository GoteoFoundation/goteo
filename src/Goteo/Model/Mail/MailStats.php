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

use Goteo\Model\Mail\Metric;
use Goteo\Application\Exception\ModelException;

class MailStats extends \Goteo\Core\Model
{
    protected $Table = 'mail_stats';
    public
        $id,
        $mail_id,
        $email,
        $metric_id,
        $created_at,
        $modified_at,
        $counter = 0;

    public function getMetric()
    {
        return Metric::get($this->metric_id);
    }

    public function inc()
    {
        $this->counter++;
    }
    /**
     * Returns a Stat value from mail_stats, creates a new one if empty
     * @param  [type] $mail_id [description]
     * @param  [type] $email   [description]
     * @param  Metric $metric  [description]
     * @return Stat          [description]
     */
    static public function getStat($mail_id, $email, Metric $metric, $auto_create = true)
    {
        $query = static::query(
            '
            SELECT *
            FROM  mail_stats
            WHERE mail_stats.mail_id = :mail_id
            AND   mail_stats.email = :email
            AND   mail_stats.metric_id = :metric_id
            ', array(':mail_id' => $mail_id, ':email' => $email, ':metric_id' => $metric->id)
        );

        $obj = $query->fetchObject(__CLASS__);
        if(! ($obj instanceOf \Goteo\Model\Mail\MailStats) ) {
            if($auto_create) {
                $obj = new self([
                    'metric_id' => $metric->id,
                    'mail_id' => $mail_id,
                    'email' => $email,
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
            else {
                return false;
            }
        }
        return $obj;

    }

    /*
     *  Devuelve datos de un elemento
     */
    static public function getFromMailId($mail_id, $offset = 0, $limit = 10, $count = false)
    {
        $values = array(':mail_id' => $mail_id);
        if($count) {
            $sql = "SELECT COUNT(*) FROM mail_stats WHERE mail_stats.mail_id = :mail_id";
            return (int) static::query($sql, $values)->fetchColumn();
        }
        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "
            SELECT *
            FROM  mail_stats
            WHERE mail_stats.mail_id = :mail_id
            LIMIT $offset, $limit";

        $list = [];
        if ($query = static::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * Increments a metric
     * @param  [type] $mail_id    [description]
     * @param  [type] $email      [description]
     * @param  string $metric_val [description]
     * @return [type]          [description]
     */
    static public function incMetric($mail_id, $email, $metric_val = 'EMAIL_OPENED', $only_if_empty = false)
    {
        //check Mail existance (non-foreign key added here)
        if(! (int) static::query('SELECT count(id) FROM mail WHERE id = ?', $mail_id)->fetchColumn()) {
            throw new ModelException("Not found mail_id [$mail_id]");
        }

        $metric = Metric::getMetric($metric_val);
        $stat = static::getStat($mail_id, $email, $metric);
        if (!$only_if_empty || $stat->counter == 0) {
            $stat->inc();
        }
        $errors = [];
        if(!$stat->save($errors)) {
            throw new ModelException(implode("\n", $errors));
        }

        return $stat;
    }


    /**
     * Guardar.
     * @param   type array $errors Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
     public function save(&$errors = array())
     {

        if(!$this->validate($errors) ) { return false;
        }

        try {
            $this->dbInsertUpdate(['mail_id', 'email', 'metric_id', 'counter'], ['mail_id', 'email', 'metric_id']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'MailStats saving error: ' . $e->getMessage();
        }

        return false;

     }

        /**
     * Validar.
     * @param   type array $errors Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
        public function validate(&$errors = array())
        {
            if(empty($this->metric_id)) {
                $errors[] = 'Empty Metric ID';
            }
            if(empty($this->mail_id)) {
                $errors[] = 'Empty Mailer ID';
            }
            if(empty($this->email)) {
                $errors[] = 'Empty Email';
            }
            return empty($errors);
        }

        /*
        *  Return a list of stats
        *  Not really used, use StatsCollector instead to collect nice stats
        */
        public function getList($offset = 0, $limit = 50, $count = false)
        {
            $values = array(':mail_id' => $this->mail->id);
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
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Mail\MailStats') as $item) {
                    $list[] = $item;
                }
            }
            return $list;
        }
}
