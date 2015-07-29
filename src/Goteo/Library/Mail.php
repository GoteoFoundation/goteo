<?php

namespace Goteo\Library;

use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Application\Message;
use Goteo\Core\Model;
use Goteo\Model\Template;
use Goteo\Library\FileHandler\File;

class Mail {

    public
        $id, // id registro en tabla mail
        $from,
        $fromName,
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
        $lang = null;

    /**
     * Constructor.
     */
    function __construct($exceptions = false) {

        // Inicializa la instancia PHPMailer.
        $mail = new \PHPMailer($exceptions);
        $this->from = Config::get('mail.transport.from');
        $this->to = Config::get('mail.transport.from');
        $this->reply = Config::get('mail.transport.from');
        $this->fromName = Config::get('mail.transport.name');
        $this->toName = Config::get('mail.transport.name');
        $this->replyName = Config::get('mail.transport.name');
        $this->node = Config::get('current_node');

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

    /**
     * Get instance of mail already on table
     * @return [type] [description]
     */
    static public function get($id) {
        if ($query = Model::query('SELECT * FROM mail WHERE id = ?', $id)) {
            $ob = $query->fetchObject();
            $mail = new Mail();
            $mail->html = true;
            $mail->id = $ob->id;
            $mail->to = $ob->email;

            $tpl = Template::get($ob->template);
            $mail->template = $tpl->id;
            $mail->subject = $tpl->title;

            $mail->content = $ob->html;

            // $mail->toName = $to_name; // TODO: add name from users

            return $mail;
        }
        return false;
    }

    /**
     * Creates a new instance of Mail from common vars
     * @return [type] [description]
     */
    static public function createFromText($to, $to_name, $subject, $body) {
        $mail = new Mail();
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
    static public function createFromHtml($to, $to_name, $subject, $body) {
        $mail = new Mail();
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
    static public function createFromTemplate($to, $to_name, $template, $vars =[]) {
        $mail = new Mail();
        $mail->to = $to;
        $mail->toName = $to_name;
        $mail->html = true;

        // Obtenemos la plantilla para asunto y contenido
        $tpl = Template::get($template);
        // Sustituimos los datos
        $mail->subject = $tpl->title;
        $mail->template = $tpl->id;
        $mail->content = $tpl->text;
        // En el contenido:
        if($vars) {
            $mail->content = str_replace(array_keys($vars), array_values($vars), $mail->content);
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
        elseif (self::checkBlocked($this->to, $reason)) {
            $errors['email'] = "El destinatario esta bloqueado por demasiados rebotes o quejas [$reason]";
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
            $errors[] = 'Limite diario alcanzado.';
            return false;
        }

        if (empty($this->id)) {
            $this->saveEmailToDB();
        }

        if($this->validate($errors)) {
            try {

                $allowed = false;
                if (Config::get('env') === 'real') {
                    $allowed = true;
                }
                elseif (Config::get('env') === 'local') {
                    $this->subject = '[LOCAL] ' . $this->subject;
                }
                elseif (Config::get('env') === 'beta') {
                    $this->subject = '[BETA] ' . $this->subject;
                    if (Config::get('mail.beta_senders') && preg_match('/' . str_replace('/', '\/', Config::get('mail.beta_senders')) .'/i', $this->to)) {
                        $allowed = true;
                    }
                }

                $mail = $this->buildMessage();

                // exit if not allowed
                // TODO: log this?
                if (!$allowed) {
                    // add any debug here
                    Message::error('SKIPPING MAIL SENDING with subject [' . $this->subject . '] to [' . $this->to . '] from  [' . $this->from . '] using) template [' . $this->template . '] due configuration restrictions!');
                    // Log this email
                    $mail->preSend();
                    $path = GOTEO_LOG_PATH . 'mail-send/';
                    @mkdir($path, 0777, true);
                    $path .= $this->id . '.eml';
                    if(@file_put_contents($path, $mail->getSentMIMEMessage())) {
                        Message::error('Logged email content into: ' . $path);
                    }
                    else {
                        Message::error('ERROR while logging email content into: ' . $path);
                    }
                    // return true is ok, mail sent...
                    return true;
                }


                // Envía el mensaje
                if ($mail->send($errors)) {
                    return true;
                } else {
                    $errors[] = 'Internal mail server error!';
                    return false;
                }

        	} catch(\PDOException $e) {
                $errors[] = "Error sending message: " . $e->getMessage();
                return false;
			} catch(\phpmailerException $e) {
			    $errors[] = $e->getMessage();
			    return false;
			}
        }
        return false;
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

    public function getToken($encode = true) {
        // TODO: make this secure!
        $token = md5(Config::get('secret') . '-' . $this->to . '-' . $this->id) . '¬' . $this->to  . '¬' . $this->id;
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
            if($validate) {
                if(md5(Config::get('secret') . '-' . $decoded[1] . '-' . $decoded[2]) !== $decoded[0]) {
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
        return $this->render($plain, [
                    'alternate' => SITE_URL . '/mail/' . $this->getToken(),
                    'tracker' => SITE_URL . '/mail/track/' . $this->id . '.gif' ]
                );

    }

    /**
     * Renders the appropiated view for the mail
     * @param  boolean     $plain      [description]
     * @param  Array|array $extra_vars [description]
     * @return [type]                  [description]
     */
    public function render($plain = false, Array $extra_vars = []) {
        $viewData = $extra_vars;
        $viewData['content'] = $this->content;

        $viewData['unsubscribe'] = SITE_URL . '/user/leave?email=' . $this->to;

        if ($plain) {
            return strip_tags($this->content) . ($viewData['alternate'] ? "\n\n" . $viewData['alternate'] : '');
        }

        // para plantilla boletin
        if ($this->template === Template::NEWSLETTER) {
            $viewData['unsubscribe'] = SITE_URL . '/user/unsubscribe/' . $this->getToken(); // ????
            return View::render('email/newsletter', $viewData);
        }

        return View::render('email/default', $viewData);
    }

    /**
     * Save email metadata to DB
     * @param $email
     * @return int ID of the inserted email
     */
    public function saveEmailToDB() {

        $email = ($this->massive) ? 'any' : $this->to;

        $sql = "INSERT INTO mail (id, email, html, template, node, lang) VALUES ('', :email, :html, :template, :node, :lang)";
        $values = array (
            ':email' => $email,
            ':html' => $this->content,
            ':template' => $this->template,
            ':node' => $this->node,
            ':lang' => $this->lang
            );
        Model::query($sql, $values);

        $id = Model::insertId();
        $this->id = $id;

        return $id;

    }

    /**
     * Store HTML email body generating previously an unique ID for the filename
     * @param $sendId
     * @param $filename
     * @return
     */
    public function saveContentToFile() {

        // //do no need to repeat if already uploaded
        // $sql = "SELECT content FROM mail WHERE id = :id";
        // $query = Model::query($sql, array(':id' => $this->id));
        // $current = (int) $query->fetchColumn();
        // if(empty($current)) {
        //     return false;
        // }

        $email = ($this->massive) ? 'any' : $this->to;
        $path = ($this->massive) ? '/news/' : '/sys/';
        $contentId = md5("{$this->id}_{$email}_{$this->template}_" . Config::get('secret')) . ".html";

        // $sql = "UPDATE mail SET html='', content = :content WHERE id = :id";
        // $values = array (
        //     ':content' => $path . $contentId,
        //     ':id' => $this->id,
        //     );
        // Model::query($sql, $values);

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


    /**
     *
     * @param array $filters    user (nombre o email),  template
     * FIXME: No funciona cuando las fechas desde y hasta son iguales.
     */
    public static function getSentList($filters = array(), $node = null, $offset = 0, $limit = 10, $count = false) {

        $values = array();
        $sqlFilter = '';
        $and = " WHERE";

        if (!empty($filters['user'])) {
            $sqlFilter .= $and . " mail.email LIKE :user";
            $and = " AND";
            $values[':user'] = "%{$filters['user']}%";
        }

        if (!empty($filters['template'])) {
            $sqlFilter .= $and . " mail.template = :template";
            $and = " AND";
            $values[':template'] = $filters['template'];
        }

        /*
        if ($node != \GOTEO_NODE) {
            $sqlFilter .= $and . " mail.node = :node";
            $and = " AND";
            $values[':node'] = $node;
        } else
        */
        if (!empty($filters['node'])) {
            $sqlFilter .= $and . " mail.node = :node";
            $and = " AND";
            $values[':node'] = $filters['node'];
        }

        if (!empty($filters['date_from'])) {
            $sqlFilter .= $and . " mail.date >= :date_from";
            $and = " AND";
            $values[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_until'])) {
            $sqlFilter .= $and . " mail.date <= :date_until";
            $and = " AND";
            $values[':date_until'] = $filters['date_until'];
        }

        // Return total count for pagination
        if($count) {
            $sql = "SELECT COUNT(mail.id) FROM mail $sqlFilter";
            return (int) Model::query($sql, $values)->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT
                    mail.id as id,
                    mail.email as email,
                    mail.template as template,
                    DATE_FORMAT(mail.date, '%d/%m/%Y %H:%i') as date
                FROM mail
                $sqlFilter
                ORDER BY mail.date DESC
                LIMIT $offset,$limit";

        $query = Model::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_OBJ);

    }

    /**
     * Devuelve el enlace para Sinoves
     * @param $id
     * @return $url
     */
    public static function getSinovesLink($id, $filename = null) {

        $url = '';

        if (empty($filename)) {
            $sql = "SELECT content
            FROM mail
            WHERE id = :id";

            $query = Model::query($sql, array(':id' => $id));
            $content = $query->fetchColumn();

        } else {
            $content = $filename;
        }

        $url = SITE_URL . '/mail' . $content;
        if (FILE_HANDLER == 's3') {
            $url = 'http://' . AWS_S3_BUCKET_MAIL . $content;
        }

        return $url;
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
        $query = Model::query($sql, array(':modified' => $modified));
        $cuantos = (int) $query->fetchColumn();

        //añadir
        if (isset($add)) {
            $cuantos += $add;
            $sql = "SELECT num FROM mailer_limit WHERE `modified` > :modified AND `hora` = :hora";
            $query = Model::query($sql, array(':modified' => $modified, ':hora' => $hora));
            $current = (int) $query->fetchColumn();

            $values= array(':hora' => $hora, ':num' => ($current + $add), ':modified' => date('Y-m-d H:i:s'));
            Model::query("REPLACE INTO mailer_limit (`hora`, `num`, `modified`) VALUES (:hora, :num, :modified)", $values);
        }

        return ($ret) ? ($LIMIT - $cuantos) : ($cuantos < $LIMIT);
    }

    /**
     * Comprueba si un email esta bloqueado por bounces o complaints
     * @param  string $email  email a comprobar
     * @param  string $reason razon de bloqueo
     * @return boolean        true o false
     */
    static public function checkBlocked($email, &$reason) {
        $query = Model::query("SELECT * FROM mailer_control WHERE email=:email AND action='deny'", array(':email' => $email));
        if($ob = $query->fetchObject()) {
            $reason = $ob->last_reason;
            return ($ob->complaints > $ob->bounces ? $ob->complaints : $ob->bounces);
        }
        return false;
    }

    /**
     * Añade un email a la table de control (tipo bounce), con bloqueo de futuros envios si se especifica
     * @param string  $email  email a controlar
     * @param string  $reason razon de inclusion en la lista
     * @param boolean $block  true o false, si se bloquea para envios o solo se incluye informativamente
     */
    static public function addBounce($email, $reason = '', $block = false) {
        $query = Model::query("SELECT bounces FROM mailer_control WHERE email=:email", array(':email' => $email));
        $bounces = (int) $query->fetchColumn();
        $values = array(':email' => $email,
            ':bounces' => $bounces+1,
            ':reason' => $reason,
            ':action' => ($block ? 'deny' : 'allow')
            );
        Model::query("REPLACE INTO mailer_control (`email`, `bounces`, `last_reason`, `action`) VALUES (:email, :bounces, :reason, :action)", $values);
    }

    /**
     * Añade un email a la table de control (tipo complaint), con bloqueo de futuros envios
     * @param string  $email  email a controlar
     * @param string  $reason razon de inclusion en la lista
     */
    static public function addComplaint($email, $reason = '') {
        $query = Model::query("SELECT complaints FROM mailer_control WHERE email=:email", array(':email' => $email));
        $complaints = (int) $query->fetchColumn();
        $values = array(':email' => $email,
            ':complaints' => $complaints+1,
            ':reason' => $reason,
            ':action' => 'deny'
            );
        Model::query("REPLACE INTO mailer_control (`email`, `complaints`, `last_reason`, `action`) VALUES (:email, :complaints, :reason, :action)", $values);
    }

}

