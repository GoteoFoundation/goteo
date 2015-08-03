<?php

namespace Goteo\Model;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
/*
 * Clase para hacer envios masivos en segundo plano
 *
 */
class Sender extends \Goteo\Core\Model {
    protected $Table = 'mailer_content';
    public $id,
           $active = 0,
           $mail,
           $subject,
           $blocked,
           $reply,
           $reply_name;
    /**
     * Creates a new sending cue
     */
    public function __construct($mailId = null, $subject = null, $reply = null, $reply_name = null) {
        if($mailId)     $this->mail = $mailId;
        if($subject)    $this->subject = $subject;
        if($reply)      $this->reply = $reply;
        if($reply_name) $this->reply_name = $reply_name;
    }

    public function validate(&$errors = []) {
        //TODO....
        return true;
    }
    public function save(&$errors = []) {
        $sql = "INSERT INTO `mailer_content` (`active`, `mail`, `subject`, `blocked`, `reply`, `reply_name`)
                VALUES (0, :mail, :subject, 0, :reply, :reply_name)";
        static::query($sql, array(':subject' => $this->subject, ':mail'=>$this->mail, ':reply'=>$this->reply, ':reply_name'=>$this->reply_name));

        if($this->id = static::insertId()) {
            return $this;
        }
        else {
            throw new ModelException('The creation of a new sending cue has failed!');
        }
    }

    /**
     * Expects a SELECT clause with 4 components:
     *  mailingId, user, email, name
     * @param [type] $sql [description]
     */
    public function addSubscribersFromSQL($sql) {
        $sql = 'INSERT INTO `mailer_send` (`mailing`, `user`, `name`, `email`) ' .$sql;
        if(static::query($sql)) {
            return true;
        }
        throw new ModelException('Inserting SQL [' . $sql .'] has failed!');
    }

    public function addSubscribers(Array $subscribers = []) {

    }

    /**
     * Activates the Sender for sending
     */
    public function setActive($active) {
        $this->active = (bool) $active;
        $query = static::query("UPDATE mailer_content SET active = " . ($this->active ? 1 : 0) . " WHERE id = {$this->id}");
        return ($query->rowCount() == 1);
    }

    /**
     * Returns status of the sending
     * @return [type] [description]
     */
    public function getStatus() {
        try {
            // y el estado
            $query = static::query('
            SELECT
                    COUNT(mailer_send.id) AS receivers,
                    SUM(IF(mailer_send.sent = 1, 1, 0)) AS sent,
                    SUM(IF(mailer_send.sent = 0, 1, 0)) AS failed,
                    SUM(IF(mailer_send.sent IS NULL, 1, 0)) AS pending
            FROM    mailer_send
            WHERE mailer_send.mailing = ?', $this->id);
            $sending = $query->fetchObject();

            $sending->percent   = 100 * (1 - $sending->pending / $sending->receivers);
        } catch(\PDOException $e) {
            throw new ModelNotFoundException('Not found mailingId [' . $this->id . ']' . $e->getMessage());
        }

        return $sending;

    }

    public function getLink() {
        return SITE_URL . '/mail/' . \mybase64_encode(md5(Config::get('secret') . '-' . $this->id . '-' . $this->mail) . '¬' . $this->id . '¬' . $this->mail);
    }

    /*
    *  Metodo para obtener la siguiente tanda de destinatarios
    */
    static public function getRecipients ($id, $limit=10) {
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
            if($limit) $sql .= "LIMIT $limit
            ";

        if ($query = static::query($sql, array($id))) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $list[] = $receiver;
            }
        }

        return $list;

    }


    // TODO: remove this
	static public function initiateSending ($mailId, $subject, $receivers, $autoactive = 0, $reply = null, $reply_name = null) {

        try {
            static::query("START TRANSACTION");

            $sql = "INSERT INTO `mailer_content` (`id`, `active`, `mail`, `subject`, `blocked`, `reply`, `reply_name`)
                VALUES ('' , '{$autoactive}', :mail, :subject, 0, :reply, :reply_name)";
            static::query($sql, array(':subject'=>$subject, ':mail'=>$mailId, ':reply'=>$reply, ':reply_name'=>$reply_name));
            $mailing = static::insertId();

            // destinatarios
            $sql = "INSERT INTO `mailer_send` (`id`, `mailing`, `user`, `email`, `name`)
             VALUES ('', :mailing, :user, :email, :name)";

            foreach ($receivers as $user) {
                static::query($sql,
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
    static public function get($id = null) {
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
                    mailer_content.subject as subject,
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
    * Método para obtener el listado de envios programados
    */
	static public function getMailingList ($offset = 0, $limit = 10, $count = false) {

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
                mailer_content.subject as subject,
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

    /*
     * Listado completo de destinatarios/envaidos/fallidos/pendientes
     */
	static public function getList ($mailing, $detail = 'receivers', $offset = 0, $limit = 10, $count = false) {

        $list = array();

        $sqlFilter = " WHERE mailer_send.mailing = {$mailing}";

        switch ($detail) {
            case 'sent':
                $sqlFilter .= " AND mailer_send.sent = 1";
                break;
            case 'failed':
                $sqlFilter .= " AND mailer_send.sent = 0";
                break;
            case 'pending':
                $sqlFilter .= " AND mailer_send.sent IS NULL";
                break;
            case 'receivers':
            default:
                break;
        }

        if($count) {
            $sql = "SELECT COUNT(mailer_send.id) FROM mailer_send $sqlFilter";
            return (int) static::query($sql, $values)->fetchColumn();
        }
        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT
                mailer_send.*,
                IF(mailer_send.sent = 1, 'sent', IF(mailer_send.sent = 0, 'failed', 'pending')) AS status
            FROM  mailer_send
                $sqlFilter
            ORDER BY sent ASC
            LIMIT $offset,$limit";

        if ($query = static::query($sql)) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $user) {
                $list[] = $user;
            }
        }

        return $list;

    }


    /*
    *  Metodo para limpieza de envíos masivos enviados y sus destinatarios
    */
    static public function cleanOld() {

        // eliminamos los envíos de hace más de dos días
        static::query("DELETE FROM mailer_content WHERE active = 0
         AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(datetime)), '%j') > 2");

    }



}

