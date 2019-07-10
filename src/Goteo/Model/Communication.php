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
use Goteo\Application\Config;
use Goteo\Library\Text;

class Communication extends \Goteo\Core\Model {

    public
        $id,
        $subject,
        $content,
        $header,
        $type,
        $template = null,
        $sent = null,
        $error = '',
        $original_lang,
        $filter;

    public static function getLangFields() {
        return ['subject', 'content'];
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
            'original_lang',
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


}

