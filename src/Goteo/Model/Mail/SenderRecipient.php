<?php

namespace Goteo\Model\Mail;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Model\Mail\Sender;

/*
 * SenderRecipient for Sender class
 *
 */
class SenderRecipient extends \Goteo\Core\Model {
    protected $Table = 'mailer_send';
    public $id,
           $mailing,
           $user,
           $email,
           $name,
           $datetime,
           $sent,
           $error,
           $blocked;

    public function validate(&$errors = []) {
        if(empty($this->email)) {
            $errors[] = 'Empty Email';
        }
        if(empty($this->mailing)) {
            $errors[] = 'Empty Mailer ID';
        }
        if(!empty($this->blocked)) {
            $errors[] = 'Sender Recipient blocked!';
        }

        return empty($errors);
    }

    public function save(&$errors = []) {
        //TODO...
    }

    /**
     * Send the email if ready
     * @return [type] [description]
     */
    public function send(&$errors = array()) {
        if( ! $this->validate($errors) ) return false;
        if(!empty($this->sent)) {
            $errors[] = 'This recipient is already sent!';
            return false;
        }

        // cogemos el contenido y la plantilla desde el historial
        if ( ! ($sender = Sender::get($this->mailing)) ) {
            die("Error obtaining Sender Instance [$id]\n");
        }
        if (!$sender->active) {
            die("Error, sender ID [{$sender->id}] inactive!\n");
        }
        if ( ! ($mail = Mail::get($sender->mail)) ) {
            $errors[] = "Error obtaining Mail Instance [{$sender->mail}]\n";
            return false;
        }

        if (!empty($sender->reply)) {
            $mail->reply = $sender->reply;
            if (!empty($sender->reply_name)) {
                $mail->replyName = $sender->reply_name;
            }
        }

        $mail->to = \trim($this->email);
        $mail->toName = $this->name;
        $mail->subject = $sender->subject;
        $mail->content = str_replace(
            array('%USERID%', '%USEREMAIL%', '%USERNAME%', '%SITEURL%'),
            array($this->user, $this->email, $this->name, SITE_URL),
            $mail->content);

        // send mail to recipient
        if($mail->send($errors)) {
            if($this->setSent(true)) {
                return true;
            }
            $errors[] = 'Error on marking SenderRecipient as sent!';
        }
        return false;

    }

    public function setLock($status = null) {
        if(!is_null($status)) {
            static::query("UPDATE mailer_send SET blocked = :lock WHERE id = :id", [':lock' => (bool)$status, ':id' => $this->id]);
        }
        return (bool)static::query('SELECT blocked FROM mailer_send where id = ?', $this->id)->fetchColumn();
    }

    public function setSent($status = null) {
        if(!is_null($status)) {
            static::query("UPDATE mailer_send SET sent = :sent WHERE id = :id", [':sent' => (bool)$status, ':id' => $this->id]);
        }
        return (bool)static::query('SELECT sent FROM mailer_send where id = ?', $this->id)->fetchColumn();
    }


    static public function get($id) {
        $sql = 'SELECT * FROM mailer_send WHERE id = ?';

        if ($query = static::query($sql, array($id))) {
            return $query->fetchObject(__CLASS__);
        }
        return false;
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

}

