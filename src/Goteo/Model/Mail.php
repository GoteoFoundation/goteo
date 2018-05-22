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

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Model\Template;
use Goteo\Model\Mail\MailStats;
use Goteo\Model\Mail\StatsCollector;
use Goteo\Model\Mail\Metric;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Message as Comment;
use Goteo\Library\FileHandler\File;
use Goteo\Library\Newsletter;
use Goteo\Util\Monolog\Processor\WebProcessor;

class Mail extends \Goteo\Core\Model {
    protected $Table = 'mail';

    public
        $id, // id registro en tabla mail
        $from,
        $fromName,
        $email,
        $sender_id,
        $to,
        $toName,
        $subject,
        $content,
        $node,
        $cc = false,
        $bcc = false,
        $reply,
        $replyName,
        $attachments = array(),
        $html = true,
        $massive = false,
        $template = null,
        $sent = null,
        $error = '',
        $log,
        $status = 'pending',
        $message_id = null,
        $lang = null;

    /**
     * Constructor.
     */
    function __construct($exceptions = false) {
        if($this->sent == 1) $this->status = 'sent';
        if($this->sent === 0 || $this->sent === '0') $this->status = 'failed'; // NULL values are 'pending'
        if($this->email === 'any') $this->massive = true;

        $this->node = Config::get('current_node');

        // Inicializa la instancia PHPMailer.
        $mail = new \PHPMailer($exceptions);
        $this->from = Config::get('mail.transport.from');
        // $this->to = Config::get('mail.transport.from');
        // $this->toName = Config::get('mail.transport.name');
        $this->reply = Config::get('mail.transport.from');
        $this->fromName = Config::get('mail.transport.name');
        $this->replyName = Config::get('mail.transport.name');
        $this->node = Config::get('current_node');

        // If sender defined, lets create the object
        if($this->sender_id) {
            $this->sender = new Sender([
                        'mail' => $this->id,
                        'id' => $this->sender_id,
                        'active' => $this->sender_active,
                        'datetime' => $this->sender_datetime,
                        'blocked' => $this->sender_blocked,
                        'reply' => $this->sender_reply,
                        'reply_name' => $this->sender_reply_name
                        ]);
        }
        // Define  el idioma para los mensajes de error.
        $mail->setLanguage("es");

        // Define la codificación de caracteres del mensaje.
        $mail->CharSet = "UTF-8";

        // Define el ajuste de texto a un número determinado de caracteres en el cuerpo del mensaje.
        $mail->WordWrap = 50;

        // Define el tipo de gestor de correo
        switch(Config::get('mail.transport.type')) {
            default:
            case "mail":
                $mail->isMail(); // set mailer to use PHP mail() function.
                break;
            case "sendmail":
                $mail->isSendmail(); // set mailer to use $Sendmail program.
                break;
            case "qmail":
                $mail->isQmail(); // set mailer to use qmail MTA.
                break;
            case "smtp":
                $mail->isSMTP(); // set mailer to use SMTP
                $mail->SMTPAuth = Config::get('mail.transport.smtp.auth'); // enable SMTP authentication
                $mail->SMTPSecure = Config::get('mail.transport.smtp.secure'); // sets the prefix to the servier
                $mail->Host = Config::get('mail.transport.smtp.host'); // specify main and backup server
                $mail->Port = Config::get('mail.transport.smtp.port'); // set the SMTP port
                $mail->Username = Config::get('mail.transport.smtp.username');  // SMTP username
                $mail->Password = Config::get('mail.transport.smtp.password'); // SMTP password
                break;
        }
        $this->mail = $mail;
    }

    private function logger($message, array $context = [], $func = 'warning') {
        if(!$this->log) {
            $this->log = App::getService('syslogger');
        }
        if($this->log) {
            $this->log->$func($message, WebProcessor::processObject($context));
        }
    }

    public function setReply($email, $name = '') {
        $this->reply = $email;
        $this->replyName = $name;
        return $this;
    }
    public function setSubject($subject = null) {
        if($subject) $this->subject = $subject;
        return $this;
    }
    public function getSubject() {
        if(!$this->subject && $this->template) {
            $tpl = Template::get($this->template);
            $this->subject = $tpl->title;
        }
        return $this->subject;
    }

    public function getReply() {
        if($this->sender) {
            return $this->sender->reply;
        }
        return $this->reply;
    }

    public function getReplyName() {
        if($this->sender) {
            return $this->sender->reply_name;
        }
        return $this->replyName;
    }

    public function setMessage($message) {
        if($message instanceOf Comment) {
            $this->message_id = $message->id;
        } else {
            $this->message_id = $message;
        }
        return $this;
    }

    /**
     * Get instance of mail already on table
     * @return [type] [description]
     */
    static public function get($id) {
        if ($query = static::query('SELECT * FROM mail WHERE id = ?', $id)) {
            if( ! ($mail = $query->fetchObject(__CLASS__)) ) return null;
            $mail->to = $mail->email;
            // $mail->toName = $to_name; // TODO: add name from users

            return $mail;
        }
        return null;
    }

    /**
     * Get instance of mail already on table using message_id identifier
     * @return [type] [description]
     */
    static public function getFromMessageId($message_id) {
        if ($query = static::query('SELECT * FROM mail WHERE message_id = ?', $message_id)) {
            if( ! ($mail = $query->fetchObject(__CLASS__)) ) return null;
            $mail->to = $mail->email;
            return $mail;
        }
        return null;
    }

    /**
     * Creates a new instance of Mail from common vars
     * @return [type] [description]
     */
    static public function createFromText($to, $to_name, $subject, $body = '') {
        $mail = new static();
        $mail->to = $to;
        $mail->toName = $to_name;
        $mail->subject = $subject;
        $mail->content = $body;
        $mail->html = false;
        return $mail;
    }

    /**
     * Creates a new instance of Mail from common vars
     * @return [type] [description]
     */
    static public function createFromHtml($to, $to_name, $subject, $body = '') {
        $mail = new static();
        $mail->to = $to;
        $mail->toName = $to_name;
        $mail->subject = $subject;
        $mail->content = $body;
        $mail->html = true;
        return $mail;
    }

    /**
     * Creates a new instance of Mail from a template
     * @return [type] [description]
     */
    static public function createFromTemplate($to, $to_name, $template, $vars =[], $lang = null) {
        $mail = new static();
        $mail->to = $to;
        $mail->toName = $to_name;
        $mail->html = true;
        if($to == 'any') $mail->massive = true;

        // Obtenemos la plantilla para asunto y contenido
        if(empty($lang)) $mail->lang = Lang::current();
        $tpl = Template::get($template, $mail->lang);
        // Sustituimos los datos
        $mail->subject = $tpl->title;
        $mail->template = $tpl->id;
        $text = $tpl->parseText();
        $mail->content = ($template == Template::NEWSLETTER) ? Newsletter::getContent($text, $mail->lang) : $content = $text;
        // En el contenido:
        if($vars) {
            $mail->content = str_replace(array_keys($vars), array_values($vars), $mail->content);
            $mail->subject = str_replace(array_keys($vars), array_values($vars), $mail->subject);

        }

        return $mail;
    }

    /**
     * Validar mensaje.
     * @param type array	$errors
     */
	public function validate(&$errors = array()) {
	    if(empty($this->to)) {
            $errors['email'] = 'El mensaje no tiene destinatario.';
        }
        elseif(!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
	        $errors['email'] = 'Email destinatario inválido ['. $this->to. ']';
	    }
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

                $mail = $this->buildMessage();

                if ($allowed) {
                    // Envía el mensaje
                    if ($mail->send()) {
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
                    $mail->preSend();
                    $path = GOTEO_LOG_PATH . 'mail-send/';
                    @mkdir($path, 0777, true);
                    $path .= $this->id .'-' . str_replace(['@', '.'], ['_', '_'], $this->to) . '.eml';
                    if(@file_put_contents($path, $mail->getSentMIMEMessage())) {
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

    /**
     * Construye el mensaje
     * @return [type] [description]
     */
    public function buildMessage() {
        $mail = $this->mail;
        // Construye el mensaje
        $mail->From = $this->from;
        $mail->FromName = $this->fromName;

        $address = $this->to;
        $mail->addAddress($address, $this->toName);
        // copia a mail log si no es masivo
        if (Config::get('env') === 'real' && !$this->massive && Config::get('bcc_verifier')) {
            $mail->addBCC(Config::get('bcc_verifier'), 'Verifier');
        }
        if($this->cc) {
            $mail->addCC($this->cc);
        }
        if($this->bcc) {
            if (is_array($this->bcc)) {
                foreach ($this->bcc as $ml) {
                    $mail->addBCC($ml);
                }
            } else {
                $mail->addBCC($this->bcc);
            }
        }
        if($this->reply) {
            $mail->addReplyTo($this->reply, $this->replyName);
        }
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                if (!empty($attachment['filename'])) {
                    $mail->addAttachment($attachment['filename'], $attachment['name'], $attachment['encoding'], $attachment['type']);
                } else {
                    $mail->addStringAttachment($attachment['string'], $attachment['name'], $attachment['encoding'], $attachment['type']);
                }
            }
        }

        $mail->Subject = $this->subject;
        if (Config::get('env') === 'local') {
            $mail->Subject = '[LOCAL] ' . $mail->Subject;
        }
        elseif (Config::get('env') === 'beta') {
            $mail->Subject = '[BETA] ' . $mail->Subject;
        }

        if($this->html) {
            $mail->isHTML(true);
            $mail->Body    = $this->bodyHTML();
            $mail->AltBody = $this->bodyText();
        }
        else {
            $mail->isHTML(false);
            $mail->Body    = $this->bodyHTML(true);
        }
        return $mail;
    }

    public function getToken($tracker = true, $force_to = '', $encode = true) {
        $to = $this->to ? $this->to : $this->email;
        if($force_to) $to = $force_to;
        $tracker = $tracker ? '1' : '0';
        return self::encodeToken([$to, $this->id, $tracker], $encode);
        // $token = md5(Config::get('secret') . '-' . $to . '-' . $this->id . '-' . $tracker) . '¬' . $to . '¬' . $this->id . '¬' . $tracker;

        // if($encode) {
        //     return \mybase64_encode($token);
        // }
        // return $token;
    }

    static public function encodeToken(array $vars, $encode = true) {
        $token = md5(Config::get('secret') . '-' . implode('-', $vars)) . '¬' . implode('¬', $vars);
        if($encode) {
            return \mybase64_encode($token);
        }
        return $token;
    }

    static public function decodeToken($token, $validate = true, $decode = true) {
        if($decode) {
            $token = \mybase64_decode($token);
        }
        if(strpos($token, '¬') !== false) {
            $decoded = explode('¬', $token);
            $md5 = array_shift($decoded);
            if($validate) {
                if(md5(Config::get('secret') . '-' . implode('-', $decoded)) !== $md5) {
                    return [];
                }
            }
            return $decoded;
        }
        return [];
    }

    /**
     * Cuerpo del mensaje en texto plano para los clientes de correo sin formato.
     */
    private function bodyText() {
        return preg_replace("/[\n]{2,}/", "\n\n" ,strip_tags(str_ireplace(['<br', '<p'], ["\n<br", "\n<p"], $this->content)));
    }

    /**
     * Cuerpo del texto en HTML para los clientes de correo con formato habilitado.
     *
     * Se mete el contenido alrededor del diseño de email de Diego
     *
     */
    private function bodyHTML($plain = false) {
        $token = $this->getToken();
        return $this->render($plain, [
                    'alternate' => SITE_URL . '/mail/' . $token,
                    'tracker' => SITE_URL . '/mail/track/' . $token . '.gif'
                ]);

    }

    /**
     * Renders the appropiated view for the mail
     * @param  boolean     $plain      [description]
     * @param  Array|array $extra_vars [description]
     * @return [type]                  [description]
     */
    public function render($plain = false, Array $extra_vars = [], $process_links = true) {
        $content = $this->content;
        // Process links if tracker var present
        if($process_links) {
            // compilar links y cambiarlos por redirecciones a un controlador
            $content = preg_replace_callback([
                '/(<a.*)href=(")([^"]*)"([^>]*)>/U',
                "/(<a.*)href=(')([^']*)'([^>]*)>/U"
                ],
                function ($matches){
                    $url = $matches[3];
                    $new = SITE_URL . '/mail/url/' . \mybase64_encode(md5(Config::get('secret') . '-' . $this->to . '-' . $this->id. '-' . $url) . '¬' . $this->to  . '¬' . $this->id . '¬' . $url);
                    return $matches[1] . 'href="' . $new . '"'. $matches[4] . '>';
                },
                $content);
        }

        $extra_vars['content'] = $content;
        $extra_vars['subject'] = $this->subject;

        $extra_vars['unsubscribe'] = SITE_URL . '/user/leave?email=' . $this->to;

        if ($plain) {
            return strip_tags($this->content) . ($extra_vars['alternate'] ? "\n\n" . $extra_vars['alternate'] : '');
        }
        // Render in a separate instance of Foil to avoid some unexpected problems
        $engine = View::createEngine();
        $engine->setFolders(View::getFolders());
        // para plantilla boletin
        if ($this->template == Template::NEWSLETTER) {
            $extra_vars['unsubscribe'] = SITE_URL . '/user/unsubscribe/' . $this->getToken(); // ????
            return $engine->render('email/newsletter', $extra_vars, false);
        }
        return $engine->render('email/default', $extra_vars, false);
    }

    /**
     * Save email metadata to DB
     * @param $email
     * @return int ID of the inserted email
     */
    public function save(&$errors = []) {
        $this->validate($errors);
        if($this->massive) unset($errors['email']);
        if( !empty($errors) ) return false;

        $this->email = ($this->massive) ? 'any' : $this->to;

        try {
            $this->dbInsertUpdate(['email', 'subject', 'content', 'template', 'node', 'lang', 'sent', 'error', 'message_id']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving email to database: ' . $e->getMessage();
        }

        return false;

    }

    /**
     * Store HTML email body generating previously an unique ID for the filename
     * TODO: remove this, convert to a backup old emails to a filesystem
     * @param $sendId
     * @param $filename
     * @return
     */
/*    public function saveContentToFile() {

        $email = ($this->massive) ? 'any' : $this->to;
        $path = ($this->massive) ? '/news/' : '/sys/';
        $contentId = md5("{$this->id}_{$email}_{$this->template}_" . Config::get('secret')) . ".html";

        // Necesitamos constante de donde irán los mails: MAIL_PATH = /data/mail
        // MAIL_PATH + $path
        if (FILE_HANDLER == 'file') {
            $path = 'mail' . $path;
        }

        // Guardar al sistema de archivos
        $fpremote = File::factory(array('bucket' => AWS_S3_BUCKET_MAIL));
        $fpremote->setPath($path);

        $headers = array("Content-Type" => "text/html; charset=UTF-8");
        if($fpremote->put_contents($contentId, $this->content, 0, 'public-read', array(), $headers)) {
            return $path . $contentId;
        }
        return false;
    }
*/
    /**
     *
     * Adjuntar archivo.
     * @param type string	$filename
     * @param type string	$name
     * @param type string	$encoding
     * @param type string	$type
     */
    private function attachFile($filename, $name = false, $encoding = 'base64', $type = 'application/pdf') {
        $this->attachments[] = array(
            'filename' => $filename,
            'name' => $name,
            'encoding' => $encoding,
            'type' => $name
        );
    }

    /**
     *
     * Adjuntar cadena como archivo.
     * @param type string	$string
     * @param type string	$name
     * @param type string	$encoding
     * @param type string	$type
     */
    private function attachString($string, $name = false, $encoding = 'base64', $type = 'application/pdf') {
        $this->attachments[] = array(
            'string' => $string,
            'name' => $name,
            'encoding' => $encoding,
            'type' => $name
        );
    }

    public function getStats() {
        if(!$this->stats_collector) {
            $this->stats_collector = new StatsCollector($this);
        }
        return $this->stats_collector;
    }

    public function getSender() {
        if(!$this->sender) {
            try {
                $this->sender = Sender::getFromMailId($this->id);
            } catch(ModelNotFoundException $e) {}
        }
        return $this->sender;
    }
    public function getStatus() {
        if($this->getSender()) {
            return $this->getSender()->getStatus();
        }

        return $this->status;
    }
    /**
     *
     * @param array $filters    user (nombre o email),  template
     */
    public static function getSentList($filters = array(), $offset = 0, $limit = 10, $count = false) {

        $values = array();
        $sqlFilter = [];

        if (!empty($filters['email'])) {
            $sqlFilter[] = $and . " mail.email = :email";
            $and = " AND";
            $values[':email'] = $filters['email'];
        }

        if (!empty($filters['user'])) {
            $sqlFilter[] = "mail.email LIKE :user";
            $values[':user'] = "%{$filters['user']}%";
        }

        if (!empty($filters['reply'])) {
            $sqlFilter[] = "(mailer_content.reply LIKE :reply OR mailer_content.reply_name LIKE :reply)";
            $values[':reply'] = "%{$filters['reply']}%";
        }

        if (isset($filters['message'])) {
            if(is_bool($filters['message'])) {
                $sqlFilter[] = ($filters['message'] ? '!' : '') . "ISNULL(mail.message_id)";
            }
            else {
                $parts = [];
                if(!is_array($filters['message'])) $filters['message'] = [$filters['message']];
                foreach($filters['message'] as $i => $m) {
                    $parts[] = ':message' . $i;
                    $values[':message' . $i] = is_object($m) ? $m->id : $m;
                }
                $sqlFilter[] = "mail.message_id IN (" . implode(',', $parts) . ")";
                $values[':message'] = $filters['message'];
            }
        }

        if (isset($filters['template'])) {
            if(is_bool($filters['template'])) {
                $sqlFilter[] = ($filters['template'] ? '!' : '') . "ISNULL(mail.template)";
            }
            else {
                $parts = [];
                if(!is_array($filters['template'])) $filters['template'] = [$filters['template']];
                foreach($filters['template'] as $i => $t) {
                    if($t) {
                        $parts[] = ':template' . $i;
                        $values[':template' . $i] = $t;
                    }
                }
                if($parts) {
                    $sqlFilter[] = "mail.template IN (" . implode(',', $parts) . ")";
                }
            }
        }

        if (!empty($filters['subject'])) {
            $sqlFilter[] = "mail.subject = :subject";
            $values[':subject'] = $filters['subject'];
        }

        if (!empty($filters['node'])) {
            $sqlFilter[] = "mail.node = :node";
            $values[':node'] = $filters['node'];
        }

        if (!empty($filters['date_from'])) {
            $sqlFilter[] = "mail.date >= :date_from";
            $values[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_until'])) {
            if (!empty($filters['date_from']) && $filters['date_from'] == $filters['date_until']) {
                $sqlFilter[] = "mail.date = :date";
                $values[':date'] = $filters['date'];
            }
            else {
                $sqlFilter[] = "mail.date <= :date_until";
                $values[':date_until'] = $filters['date_until'];
            }
        }

        $sqlFilter = $sqlFilter ? ' WHERE ' . implode(' AND ', $sqlFilter) : '';
        // Return total count for pagination
        if($count) {
            $sql = "SELECT COUNT(mail.id)
                    FROM mail
                    LEFT JOIN mailer_content ON mailer_content.mail = mail.id
                    $sqlFilter";
            // die(\sqldbg($sql, $values));
            return (int) static::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT mail.*,
                mailer_content.id as sender_id,
                mailer_content.active as sender_active,
                mailer_content.datetime as sender_datetime,
                mailer_content.blocked as sender_blocked,
                mailer_content.reply as sender_reply,
                mailer_content.reply_name as sender_reply_name
                FROM mail
                LEFT JOIN mailer_content ON mailer_content.mail = mail.id
                $sqlFilter
                ORDER BY mail.date DESC
                LIMIT $offset,$limit";

        // print_r($filters);print_r($values);die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);

    }

    /**
     * Control de límite de mails que se pueden enviar al día
     */
    public static function checkLimit($add = null, $ret = false, $limit = null) {

        if((int)$limit) $LIMIT = (int) $limit;
        else            $LIMIT = (Config::get('mail.quota') ? Config::get('mail.quota') : 50000);

        $hora = date('H:i');
        $modified = date('Y-m-d H:i:s', time() - 24*3600);

        //total de envios las ultimas 24 horas
        $sql = "SELECT SUM(num) AS total FROM mailer_limit WHERE `modified` > :modified";
        $query = static::query($sql, array(':modified' => $modified));
        $cuantos = (int) $query->fetchColumn();

        //añadir
        if (isset($add)) {
            $cuantos += $add;
            $sql = "SELECT num FROM mailer_limit WHERE `modified` > :modified AND `hora` = :hora";
            $query = static::query($sql, array(':modified' => $modified, ':hora' => $hora));
            $current = (int) $query->fetchColumn();

            $values= array(':hora' => $hora, ':num' => ($current + $add), ':modified' => date('Y-m-d H:i:s'));
            static::query("REPLACE INTO mailer_limit (`hora`, `num`, `modified`) VALUES (:hora, :num, :modified)", $values);
        }

        return ($ret) ? ($LIMIT - $cuantos) : ($cuantos < $LIMIT);
    }

    /**
     * Comprueba si un email esta bloqueado por bounces o complaints
     * @param  string $email  email a comprobar
     * @param  string $reason razon de bloqueo
     * @return boolean        true o false
     */
    static public function checkBlocked($email, &$reason = '') {
        $query = static::query("SELECT * FROM mailer_control WHERE email=:email AND action='deny'", array(':email' => $email));
        if($ob = $query->fetchObject()) {
            $reason = $ob->last_reason;
            return ($ob->complaints > $ob->bounces ? $ob->complaints : $ob->bounces);
        }
        return false;
    }

    /**
     * Deletes an email from the control table
     * @param  string $email  email a comprobar
     */
    static public function removeBlocked($email) {
        static::query("DELETE FROM mailer_control WHERE email=:email", array(':email' => $email));
        return !static::query("SELECT COUNT(*) FROM mailer_control WHERE email=:email", array(':email' => $email))->fetchColumn();
    }
    /**
     * Añade un email a la table de control (tipo bounce), con bloqueo de futuros envios si se especifica
     * @param string  $email  email a controlar
     * @param string  $reason razon de inclusion en la lista
     * @param boolean $block  true o false, si se bloquea para envios o solo se incluye informativamente
     */
    static public function addBounce($email, $reason = '', $block = false) {
        $query = static::query("SELECT bounces FROM mailer_control WHERE email=:email", array(':email' => $email));
        $bounces = (int) $query->fetchColumn();
        $values = array(':email' => $email,
            ':bounces' => $bounces+1,
            ':reason' => $reason,
            ':action' => ($block ? 'deny' : 'allow')
            );
        static::query("REPLACE INTO mailer_control (`email`, `bounces`, `last_reason`, `action`) VALUES (:email, :bounces, :reason, :action)", $values);
    }

    /**
     * Añade un email a la table de control (tipo complaint), con bloqueo de futuros envios
     * @param string  $email  email a controlar
     * @param string  $reason razon de inclusion en la lista
     */
    static public function addComplaint($email, $reason = '') {
        $query = static::query("SELECT complaints FROM mailer_control WHERE email=:email", array(':email' => $email));
        $complaints = (int) $query->fetchColumn();
        $values = array(':email' => $email,
            ':complaints' => $complaints+1,
            ':reason' => $reason,
            ':action' => 'deny'
            );
        static::query("REPLACE INTO mailer_control (`email`, `complaints`, `last_reason`, `action`) VALUES (:email, :complaints, :reason, :action)", $values);
    }

}

