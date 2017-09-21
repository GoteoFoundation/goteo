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

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Model\Mail;
use Goteo\Model\User;

/*
 * Clase para hacer envios masivos en segundo plano
 *
 */
class Sender extends \Goteo\Core\Model
{
    protected $Table = 'mailer_content';
    public $id,
           $active = 0,
           $mail,
           $blocked,
           $reply,
           $reply_name;

    public function getMail()
    {
        if(!$this->mailHandler) {
            $this->mailHandler = Mail::get($this->mail);
        }
        return $this->mailHandler;
    }

    //Compatibility, a magic method to retrieve the subject of the email
    public function __get($name)
    {
        if($name == 'subject') {
            return $this->getMail()->subject;
        }
    }

    public function validate(&$errors = [])
    {
        if(empty($this->mail)) {
            $errors[] = 'Empty Mailer ID';
        }

        return empty($errors);
    }

    public function save(&$errors = [])
    {
        if(! $this->validate($errors) ) { return false;
        }

        $sql = "INSERT INTO `mailer_content` (`active`, `mail`, `blocked`, `reply`, `reply_name`)
                VALUES (0, :mail, 0, :reply, :reply_name)";
        $values = [':mail' => $this->mail, ':reply' => $this->reply, ':reply_name' => $this->reply_name];

        try {
            // die(\sqldbg($sql, $values));
            static::query($sql, $values);
            $this->id = static::insertId();
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving into mailer_content: ' . $e->getMessage();
        }
        return false;

    }


    /**
     * Activates the Sender for sending
     */
    public function isActive() {
        return (bool)static::query('SELECT active FROM mailer_content where id = ?', $this->id)->fetchColumn();
    }

    public function setActive($status = null)
    {
        if(!is_null($status)) {
            static::query("UPDATE mailer_content SET active = :lock WHERE id = :id", [':lock' => (bool)$status, ':id' => $this->id]);
            $this->active = $this->isActive();
        }
        return $this;
    }

    public function isLocked() {
        return (bool)static::query('SELECT blocked FROM mailer_content where id = ?', $this->id)->fetchColumn();
    }

    public function setLock($status = null)
    {
        if(!is_null($status)) {
            static::query("UPDATE mailer_content SET blocked = :lock WHERE id = :id", [':lock' => $status ? 1 : null, ':id' => $this->id]);
            $this->blocked = $this->isLocked();
        }
        return $this;
    }

    public function isSent() {
        return (bool)static::query('SELECT sent FROM mailer_content where id = ?', $this->id)->fetchColumn();
    }

    public function setSent($status = null)
    {
        if(!is_null($status)) {
            static::query("UPDATE mailer_content SET sent = :sent WHERE id = :id", [':sent' => (bool)$status, ':id' => $this->id]);
            $this->sent = $this->isSent();
        }
        return $this;
    }

    public function getLink()
    {
        return SITE_URL . '/mail/' . \mybase64_encode(md5(Config::get('secret') . '-' . $this->id . '-' . $this->mail) . '¬' . $this->id . '¬' . $this->mail);
    }

    /**
    *  Returns pending recipients for the mailist
    */
    public function getPendingRecipients($offset= 0, $limit=20, $count = false, $autolock = false, $order = '') {

        $where = "AND (sent IS NULL OR sent = 0)
                AND (blocked IS NULL OR blocked = 0)";
        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM mailer_send
                    WHERE mailing = ? $where";
            return (int) self::query($sql, [$this->id])->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        $extra = '';
        $list = [];
        if($order) {
            $extra .= " ORDER BY $order";
        }
        if($limit) {
            $extra .= " LIMIT $offset, $limit";
        }
        if($autolock) {
            static::query('START TRANSACTION');
        }
        $sql = "SELECT * FROM mailer_send
                WHERE mailing = ? $where$extra"
                ;
        // die(sqldbg($sql, [$this->id]));

        $query = static::query($sql, [$this->id]);
        if($query) {
            $list =  $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Mail\SenderRecipient');
        }

        if($autolock) {
            $values = [':id' => $this->id];
            foreach($list as $i => $ob) {
                $values[":i$i"] = $ob->id;
            }
            $sql = "UPDATE mailer_send SET blocked=1
                WHERE mailing = :id AND id IN (" . implode(',', array_keys($values)) . ")";
            static::query($sql, $values);
            static::query('COMMIT');
        }
        return $list;
    }

    /*
    *  Returns all recipients for the mailist
    */
    public function getRecipients($offset= 0, $limit=20, $count = false, $order = '') {

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM mailer_send
                    WHERE mailing = ?";
            return (int) self::query($sql, [$this->id])->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT * FROM mailer_send
                WHERE mailing = ?" .
                ($order ? " ORDER BY $order" : '') .
                ($limit ? " LIMIT $offset, $limit" : '');

        if ($query = static::query($sql, [$this->id])) {
            return $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\Mail\SenderRecipient');
        }
        return [];
    }

    /**
     * Returns status of the sending
     * @return [type] [description]
     */
    public function getStatusObject()
    {
        if($this->status_object) return $this->status_object;
        try {
            // y el estado
            $query = static::query(
                '
            SELECT
                    COUNT(mailer_send.id) AS receivers,
                    SUM(IF(mailer_send.sent = 1, 1, 0)) AS sent,
                    SUM(IF(mailer_send.sent = 0, 1, 0)) AS failed,
                    SUM(IF(mailer_send.sent IS NULL, 1, 0)) AS pending
            FROM    mailer_send
            WHERE mailer_send.mailing = ?', $this->id
            );
            $sending = $query->fetchObject();
            if($sending->receivers) {
                $sending->percent = 100 * (1 - $sending->pending / $sending->receivers);
                $sending->percent_failed = 100 * ($sending->failed / $sending->receivers);
                $sending->percent_success = 100 * ($sending->sent / $sending->receivers);
            }
            $this->status_object = $sending;
        } catch(\PDOException $e) {
            throw new ModelNotFoundException('Not found recipients for mailingId [' . $this->id . ']' . $e->getMessage());
        }

        return $sending;

    }

    public function getStatus() {

        try {
            $status = $this->getStatusObject();
            // echo "success: {$status->percent_success} failed {$status->percent_failed}]";
            if($status->percent == 100) return 'sent';
            if($this->active) return 'sending';
            return 'inactive';
        } catch(ModelNotFoundException $e) {}
    }

    // TODO: remove this
    static public function initiateSending($mailId, $receivers, $autoactive = 0, $reply = null, $reply_name = null)
    {

        try {
            static::query("START TRANSACTION");

            $sql = "INSERT INTO `mailer_content` (`id`, `active`, `mail`, `blocked`, `reply`, `reply_name`)
                VALUES ('' , '{$autoactive}', :mail, 0, :reply, :reply_name)";
            static::query($sql, array(':mail'=>$mailId, ':reply'=>$reply, ':reply_name'=>$reply_name));
            $mailing = static::insertId();

            // destinatarios
            $sql = "INSERT INTO `mailer_send` (`id`, `mailing`, `user`, `email`, `name`)
             VALUES ('', :mailing, :user, :email, :name)";

            foreach ($receivers as $user) {
                static::query(
                    $sql,
                    array(':mailing'=>$mailing, ':user'=>$user->user, ':email'=>$user->email, ':name'=>$user->name)
                );
            }

            static::query("COMMIT");
            return true;

        } catch(\PDOException $e) {
            echo "HA FALLADO!!" . $e->getMessage();
            die;
            return false;
        }

    }

    /*
    * Método para obtener el siguiente envío a tratar
    */
    static public function get($id = null)
    {
        try {
            $values = [];
            if ($id === 'last') {
                $sqlFilter = " ORDER BY active DESC, id DESC ";
            } else {
                $sqlFilter = " WHERE id = :id";
                $values[':id'] = $id;
            }

            // recuperamos los datos del envío
            $sql = "SELECT
                    mailer_content.id as id,
                    mailer_content.active as active,
                    mailer_content.mail as mail,
                    DATE_FORMAT(mailer_content.datetime, '%d/%m/%Y %H:%i:%s') as date,
                    mailer_content.blocked as blocked,
                    mailer_content.reply as reply,
                    mailer_content.reply_name as reply_name
                FROM mailer_content
                $sqlFilter
                LIMIT 1
                ";

            $query = static::query($sql, $values);
            $sender = $query->fetchObject(__CLASS__);
            if( ! $sender instanceOf Sender) {
                throw new ModelNotFoundException('Not found sender [' . $id . ']');
            }
            return $sender;
        } catch(\PDOException $e) {
            throw new ModelException('SQL error while getting sender [' . $id . ']' . $e->getMessage());
        }
        return $mailing;
    }

    /*
    * Obtains a sender from a Mail id
    */
    static public function getFromMailId($mail_id = null)
    {
        try {
            $values = [':mail' => $mail_id];

            // recuperamos los datos del envío
            $sql = "SELECT
                    mailer_content.id as id,
                    mailer_content.active as active,
                    mailer_content.mail as mail,
                    DATE_FORMAT(mailer_content.datetime, '%d/%m/%Y %H:%i:%s') as date,
                    mailer_content.blocked as blocked,
                    mailer_content.reply as reply,
                    mailer_content.reply_name as reply_name
                FROM mailer_content
                WHERE mail = :mail
                LIMIT 1
                ";

            $query = static::query($sql, $values);
            return $query->fetchObject(__CLASS__);

        } catch(\PDOException $e) {
            throw new ModelNotFoundException('Not found sending [' . $id . ']' . $e->getMessage());
        }
        return $mailing;
    }
    /*
    * Método para obtener el listado de envios programados
    */
    static public function getMailingList($offset = 0, $limit = 10, $count = false)
    {

        if($count) {
            $sql = "SELECT COUNT(mailer_content.id) FROM mailer_content";
            return (int) static::query($sql, $values)->fetchColumn();
        }
        $offset = (int) $offset;
        $limit = (int) $limit;

        $list = array();

        // recuperamos los datos del envío
        $sql = "SELECT
                mailer_content.id as id,
                mailer_content.active as active,
                mailer_content.mail as mail,
                mail.subject as subject,
                DATE_FORMAT(mailer_content.datetime, '%d/%m/%Y %H:%i:%s') as date,
                mailer_content.blocked as blocked
            FROM mailer_content
            LEFT JOIN mail ON mail.id = mailer_content.mail
            ORDER BY id DESC
            LIMIT $offset, $limit
            ";

        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

    }

    /**
     * Expects a SELECT clause with 4 components:
     *  mailingId, user, email, name
     * @param [type] $sql [description]
     */
    static public function addSubscribersFromSQL($sql)
    {
        $sql = 'INSERT INTO `mailer_send` (`mailing`, `user`, `name`, `email`) ' .$sql;
        if(static::query($sql)) {
            return true;
        }
        throw new ModelException('Inserting SQL [' . $sql .'] has failed!');
    }

    public function addSubscribers(array $subscribers = []) {
        foreach($subscribers as $user) {
            if(!$user instanceOf User) {
                throw new ModelException('[' . $user .'] is not an User instance!');
            }
            $sql = 'INSERT INTO `mailer_send` (`mailing`, `user`, `name`, `email`) VALUES (:id, :user, :name, :email) ';
            $values = [':id' => $this->id, ':user' => $user->id, ':name' => $user->name, ':email' => $user->email];
            if(!static::query($sql, $values)) {
                throw new ModelException('Inserting SQL [' . $sql .'] has failed!');
            }
        }
    }



    /*
    *  Metodo para limpieza de envíos masivos enviados y sus destinatarios
    */
    static public function cleanOld($days = 7)
    {
        $days = (int) $days;
        // eliminamos los envíos de hace más de dos días
        static::query(
            "DELETE FROM mailer_content WHERE active = 0
         AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(datetime)), '%j') > $days"
        );

    }



}

