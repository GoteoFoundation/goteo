<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Model\Image;

class Communication extends \Goteo\Core\Model {

    public
        $id,
        $subject,
        $content,
        $header,
        $type,
        $template = null,
        $sent = 0,
        $error = '',
        $lang,
        $date,
        $filter;

    public static function getLangFields() {
        return ['subject', 'content'];
    }
    

    static public function get($id) {
		if (empty($id)) {
			throw new Exception("Delete error: ID not defined!");
		}
		$class = get_called_class();
		$ob = new $class();
		$query = static::query('SELECT * FROM ' . $ob->getTable() . ' WHERE id = :id', array(':id' => $id));
        $communication = $query->fetchObject($class);

        if (!$communication instanceof Communication) {
            throw ModelNotFoundException("Not found communication [$id]");
        }
        return $communication;
	}

    /**
     * Validar mensaje.
     * @param type array	$errors
     */
	public function validate(&$errors = array()) {
	    if(empty($this->content)) {
	        $errors['content'] = 'El mensaje no tiene contenido.';
	    }
        if(empty($this->subject)) {
            $errors['subject'] = 'El mensaje no tiene asunto.';
        }
        return empty($errors);
    }
    
    /**
     * Communication listing
     *
     * @param array filters
     * @param int offset items
     * @param int limit items per page or 0 for unlimited
     * @param int count the number of instances
     * @return array of communication instances or the number of instances if count == true
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        if(!$lang) $lang = Lang::current();
        $values = [];
        $sqlFilters = [];
        $sql = '';

        foreach(['type', 'template'] as $key) {
            if (isset($filters[$key])) {
                $filter[] = "communication.$key = :$key";
                $values[":$key"] = $filters[$key];
            }
        }

        if(isset($filters['id'])) {
            $filter[] = "communication.id = :id";
            $values[":id"] = '%' . $filters['id'] . '%';
        }
        if(isset($filters['subject'])) {
            $filter[] = "communication.subject LIKE :subject";
            $values["subject"] = '%' . $filters['subject'] . '%';
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if($count) {
            // Return count
            $sql = "SELECT COUNT(id) FROM communication$sql";
            // echo \sqldbg($sql, $values);
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if(!$lang) $lang = Lang::current();
        // $values['lang'] = $lang;
        list($fields, $joins) = self::getLangsSQLJoins($lang);

        $sql ="SELECT
                communication.id as id,
                communication.type as type, 
                $fields,
                communication.lang as lang,
                communication.header as header,
                communication.template as template,
                communication.date as date,
                communication.filter as filter
            FROM communication
            $joins
            $sql
            ORDER BY `id` ASC
            LIMIT $offset,$limit";

        // var_dump($values); var_dump($sql); die(\sqldbg($sql, $values));
        if($query = self::query($sql, $values)) {
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }
        return [];
    }

	/**
	 * Enviar mensaje.
	 * @param type array	$errors
	 */
    public function send(&$errors = array()) {
        if (!self::checkLimit(1)) {
            $errors[] = 'Daily limit reached!';
            return false;
        }
        if (empty($this->id)) {
            $this->save();
        }
        $ok = false;
        if($this->validate($errors)) {
            try {
                if (self::checkBlocked($this->to, $reason)) {
                    throw new \phpmailerException("The recipient is blocked due too many bounces or complaints [$reason]");
                }

                $allowed = false;
                if (Config::get('env') === 'real') {
                    $allowed = true;
                }
                elseif (Config::get('env') === 'beta' && Config::get('mail.beta_senders') && preg_match('/' . str_replace('/', '\/', Config::get('mail.beta_senders')) .'/i', $this->to)) {
                    $allowed = true;
                }

                $communication = $this->buildMessage();

                if ($allowed) {
                    // Envía el mensaje
                    if ($communication->send()) {
                        $ok = true;
                    } else {
                        $errors[] = 'Internal mail server error!';
                    }
                } else {
                    // exit if not allowed
                    // TODO: log this?
                        // add any debug here
                    $this->logger('SKIPPING MAIL SENDING', [$this, 'mail_to' => $this->to, 'mail_from' => $this->from, 'template' => $this->template , 'error' => 'Settings restrictions']);
                    // Log this email
                    $communication->preSend();
                    $path = GOTEO_LOG_PATH . 'mail-send/';
                    @mkdir($path, 0777, true);
                    $path .= $this->id .'-' . str_replace(['@', '.'], ['_', '_'], $this->to) . '.eml';
                    if(@file_put_contents($path, $communication->getSentMIMEMessage())) {
                        $this->logger('Logged email content into: ' . $path);
                    }
                    else {
                        $this->logger('ERROR while logging email content into: ' . $path, [], 'error');
                    }
                    // return true is ok, let's pretend the mail is sent...
                    $ok = true;
                }

            } catch(\PDOException $e) {
                $errors[] = "Error sending message: " . $e->getMessage();
            } catch(\phpmailerException $e) {
                $errors[] = $e->getMessage();
            }
        }
        if(!$this->massive) {
            $this->sent = $ok;
            $this->error = implode("\n", $errors);
            $this->save();
        }
        return $ok;
	}

    public function save(&$errors = []) {
        $this->validate($errors);
        if( !empty($errors) ) return false;
        
        $fields = array(
            'id',
            'subject',
            'content',
            'header',
            'type',
            'template',
            'lang',
            'filter'
        );

        try {
            $this->dbInsertUpdate($fields);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving email to database: ' . $e->getMessage();
        }

        return false;

    }

    public function getImage() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->header);
        }
        return $this->imageInstance;
    }


    public function getOriginalLang(){
        return $this->lang;
    }

    public static function variables () {
        return array(
            'userid' => Text::get('admin-communications-userid-content'),
            'useremail' => Text::get('admin-communications-useremail-content'),
            'username' => Text::get('admin-communications-username-content'),
            'siteurl' => Text::get('admin-communications-siteurl-content', ['%SITEURL%' => SITE_URL]),
            'subscribeurl' => Text::get('admin-communications-subscribeurl-content'),
            'unsubscribeurl' => Text::get('admin-communications-unsubscribeurl-content'),
            'poolamount' => Text::get('admin-communications-poolamount-content')
        );
    }

    public function getLangsAvailable() {
        $langs = [];
        $sql = "
                SELECT lang
                FROM`communication`
                WHERE id = :id
                UNION DISTINCT
                SELECT lang
                FROM `communication_lang`
                WHERE id = :id
              ";
        try {
            $query = static::query($sql, array(':id' => $this->id));
            while ($lang = $query->fetchColumn()) {
                array_push($langs, $lang);
            }
        } catch (\Exception $e) {
        }
        return $langs;
    }

    public function getAllLangs() {
        try {
            $sql = "SELECT a.id, a.lang, a.subject, a.content FROM `{$this->Table}` a WHERE a.id = :id 
                    UNION 
                    SELECT * FROM `{$this->Table}_lang` b WHERE b.id = :id";
            $values = array(':id' => $this->id);
            // die(\sqldbg($sql, $values));
            if($query = static::query($sql, $values)) {
                return $query->fetchAll(\PDO::FETCH_OBJ);
            }
        } catch (\Exception $e) {}
        return [];
    }

    public function getStatus() {
        $mails = Mail::getFromCommunicationId($this->id);
        $success = 0;
        if ($mails) {
            foreach($mails as $mail) {
                $success += round($mail->getStats()->getEmailOpenedCollector()->getPercent());
            }
            return round($success/sizeof($mails));
        }

        return $success;
    }

    public function isActive() {
        $mails = Mail::getFromCommunicationId($this->id);
        $sent = 0;
        
        if ($mails) {
            foreach($mails as $mail) {
                $sent = $sent || $mail->getSender()->isActive();
            }
        }
        
        return $sent;
    }

}

