<?php

namespace Goteo\Model\Mail;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Model\Mail;

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
    public function setActive($active)
    {
        $this->active = (bool) $active;
        $query = static::query("UPDATE mailer_content SET active = " . ($this->active ? 1 : 0) . " WHERE id = {$this->id}");
        return ($query->rowCount() == 1);
    }

    public function getLink()
    {
        return SITE_URL . '/mail/' . \mybase64_encode(md5(Config::get('secret') . '-' . $this->id . '-' . $this->mail) . '¬' . $this->id . '¬' . $this->mail);
    }

    /**
     * Returns status of the sending
     * @return [type] [description]
     */
    public function getStatus()
    {
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

            $sending->percent   = 100 * (1 - $sending->pending / $sending->receivers);
        } catch(\PDOException $e) {
            throw new ModelNotFoundException('Not found recipients for mailingId [' . $this->id . ']' . $e->getMessage());
        }

        return $sending;

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
            return $query->fetchObject(__CLASS__);

        } catch(\PDOException $e) {
            throw new ModelNotFoundException('Not found sending [' . $id . ']' . $e->getMessage());
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

    static public function addSubscribers(Array $subscribers = [])
    {

    }

    /*
    *  Metodo para obtener la siguiente tanda de destinatarios
    */
    static public function getRecipients($id, $limit=10)
    {
        $list = array();

        $sql = "SELECT
                id,
                user,
                name,
                email
            FROM mailer_send
            WHERE mailing = ?
            AND sent IS NULL
            AND blocked IS NULL
            ORDER BY id
            ";
        if($limit) { $sql .= "LIMIT $limit
            ";
        }

        if ($query = static::query($sql, array($id))) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $list[] = $receiver;
            }
        }

        return $list;

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

