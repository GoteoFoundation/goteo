<?php

namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception,
        Goteo\Library\FileHandler\File,
        Goteo\Core\View;

    class Mail {

        public
            $from = GOTEO_MAIL_FROM,
            $fromName = GOTEO_MAIL_NAME,
            $to = GOTEO_MAIL_FROM,
            $toName = GOTEO_MAIL_NAME,
            $subject,
            $content,
            $node,
            $cc = false,
            $bcc = false,
            $reply = GOTEO_MAIL_FROM,
            $replyName = GOTEO_MAIL_NAME,
            $attachments = array(),
            $html = true,
            $massive = false,
            $template = null,
            $lang = null;

        /**
         * Constructor.
         */
        function __construct($exceptions = false) {
            require_once PHPMAILER_CLASS;
            require_once PHPMAILER_SMTP;
            require_once PHPMAILER_POP3;

            // Inicializa la instancia PHPMailer.
            $mail = new \PHPMailer($exceptions);

            // Define  el idioma para los mensajes de error.
            $mail->SetLanguage("es", PHPMAILER_LANGS);

            // Define la codificación de caracteres del mensaje.
            $mail->CharSet = "UTF-8";

            // Define el ajuste de texto a un número determinado de caracteres en el cuerpo del mensaje.
            $mail->WordWrap = 50;

            // Define el tipo de gestor de correo
            switch(GOTEO_MAIL_TYPE) {
                default:
                case "mail":
                    $mail->isMail(); // set mailer to use PHP mail() function.
                    break;
                case "sendmail":
                    $mail->IsSendmail(); // set mailer to use $Sendmail program.
                    break;
                case "qmail":
                    $mail->IsQmail(); // set mailer to use qmail MTA.
                    break;
                case "smtp":
                    $mail->IsSMTP(); // set mailer to use SMTP
                    $mail->SMTPAuth = GOTEO_MAIL_SMTP_AUTH; // enable SMTP authentication
                    $mail->SMTPSecure = GOTEO_MAIL_SMTP_SECURE; // sets the prefix to the servier
                    $mail->Host = GOTEO_MAIL_SMTP_HOST; // specify main and backup server
                    $mail->Port = GOTEO_MAIL_SMTP_PORT; // set the SMTP port
                    $mail->Username = GOTEO_MAIL_SMTP_USERNAME;  // SMTP username
                    $mail->Password = GOTEO_MAIL_SMTP_PASSWORD; // SMTP password
                    break;
            }
            $this->mail = $mail;
        }

        /**
         * Validar mensaje.
         * @param type array	$errors
         */
		public function validate(&$errors = array()) {
		    if(empty($this->to)) {
		        $errors['email'] = 'El mensaje no tiene destinatario.';
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
                $errors['subject'] = 'Limite diario alcanzado.';
                return false;
            }


            if($this->validate($errors)) {
                $mail = $this->mail;
                try {
                    // Construye el mensaje
                    $mail->From = $this->from;
                    $mail->FromName = $this->fromName;

                    $mail->AddAddress($this->to, $this->toName);
                    // copia a mail log si no es masivo
                    if (GOTEO_ENV == 'real' && !$this->massive) {
                        $mail->AddBCC('goteomaillog@gmail.com', 'Verifier');
                    }
                    if($this->cc) {
                        $mail->AddCC($this->cc);
                    }
                    if($this->bcc) {
                        if (is_array($this->bcc)) {
                            foreach ($this->bcc as $ml) {
                                $mail->AddBCC($ml);
                            }
                        } else {
                            $mail->AddBCC($this->bcc);
                        }
                    }
                    if($this->reply) {
                        $mail->AddReplyTo($this->reply, $this->replyName);
                    }
                    if (!empty($this->attachments)) {
                        foreach ($this->attachments as $attachment) {
                            if (!empty($attachment['filename'])) {
                                $mail->AddAttachment($attachment['filename'], $attachment['name'], $attachment['encoding'], $attachment['type']);
                            } else {
                                $mail->AddStringAttachment($attachment['string'], $attachment['name'], $attachment['encoding'], $attachment['type']);
                            }
                        }
                    }
                    $mail->Subject = $this->subject;
                    if($this->html) {
                        $mail->IsHTML(true);
                        $mail->Body    = $this->bodyHTML();
                        $mail->AltBody = $this->bodyText();

                        // incrustar el logo de goteo o del nodo
                        if (!empty($this->node) && $this->node != GOTEO_NODE) {
                            $mail->AddEmbeddedImage(GOTEO_PATH.'/nodesys/'.$this->node.'/view/css/logo.png', 'logo', 'Goteo '.$this->node, 'base64', 'image/png');
                        } else {
                            $mail->AddEmbeddedImage(GOTEO_PATH . '/goteo_logo.png', 'logo', 'Goteo', 'base64', 'image/png');
                        }
                    }
                    else {
                        $mail->IsHTML(false);
                        $mail->Body    = $this->bodyHTML(true);
                    }

                    // si estoy en entorno local ni lo intento
                    if (GOTEO_ENV == 'local') {
                        $errors[] = 'No envía porque está en local';
                        $errors[] = "Asunto: {$this->subject}";
                        $errors[] = "Destinatario: {$this->to}";
                        $errors[] = "Plantilla: {$this->template}";
                        $errors[] = "<hr />";

                        return true;
                    }

                    // Envía el mensaje
                    if ($mail->Send($errors)) {
                        return true;
                    } else {
                        $errors[] = 'Fallo del servidor de correo interno';
                        return false;
                    }

            	} catch(\PDOException $e) {
                    $errors[] = "No se ha podido enviar el mensaje: " . $e->getMessage();
                    return false;
    			} catch(phpmailerException $e) {
    			    $errors[] = $e->getMessage();
    			    return false;
    			}
            }
            return false;
		}

        /**
         * Cuerpo del mensaje en texto plano para los clientes de correo sin formato.
         */
        private function bodyText() {
            return strip_tags($this->content);
        }

        /**
         * Cuerpo del texto en HTML para los clientes de correo con formato habilitado.
         *
         * Se mete el contenido alrededor del diseño de email de Diego
         *
         */
        private function bodyHTML($plain = false) {

            $viewData = array('content' => $this->content);

            // grabamos el contenido en la tabla de envios
            // especial para masivos, solo grabamos un sinoves

            // 'mail-file'
            // el contenido se guarda en un bucket
            // para mails normales, se genera md5 (id.email.template.Secret)
            // para newsletter, se usa directamente id de registro tabla 'mail'
            // en el campo 'content' de la tabla grabamos el nombre del archivo
            // la dirección del bucket no se graba en la tabla (diferente para beta y real, desde settings)

            // Caducidad
            // se graba también en la tabla la fecha en la que caduca el contenido (un script auo. borra esos archivos del bucket y registros de la tabla)

            $email = ($this->massive) ? "any" : $this->to;
            $this->node = $_SESSION['admin_node'];

            if ($this->massive) {

                // @FIXME esto ya no sirve en cli mode
                if (!empty($_SESSION['NEWSLETTER_SENDID']) ) {
                    $sendId = $_SESSION['NEWSLETTER_SENDID'];
                } else {
                    $sendId = $this->saveEmailToDB($email);
                    $this->saveContentToFile($sendId);
                    $_SESSION['NEWSLETTER_SENDID'] = $sendId;
                }

            } else {
                $sendId = $this->saveEmailToDB($email);
                $this->saveContentToFile($sendId);
            }

            // tokens
            $leave_token = md5(uniqid()) . '¬' . $this->to  . '¬' . $sendId;

            $viewData['sinoves'] = static::getSinovesLink($sendId);
            $viewData['baja'] = SITE_URL . '/user/leave/?email=' . $this->to;

            if ($plain) {
                return strip_tags($this->content) . '

                ' . $viewData['sinoves'];
            } else {
                // para plantilla boletin
                if ($this->template == 33) {
                    $viewData['baja'] = SITE_URL . '/user/unsuscribe/' . \mybase64_encode($leave_token);
                    return new View (GOTEO_PATH.'view/email/newsletter.html.php', $viewData);
                } elseif (!empty($this->node) && $this->node != GOTEO_NODE) {
                    return new View (GOTEO_PATH.'nodesys/'.$this->node.'/view/email/default.html.php', $viewData);
                } else {
                    return new View (GOTEO_PATH.'view/email/goteo.html.php', $viewData);
                }
            }
        }

        /**
         * Save email metadata to DB
         * @param $email
         * @return int ID of the inserted email
         */
        public function saveEmailToDB($email) {

            $sql = "INSERT INTO mail (id, email, html, template, node, lang) VALUES ('', :email, :html, :template, :node, :lang)";
            $values = array (
                ':email' => $email,
                ':html' => $this->content,
                ':template' => $this->template,
                ':node' => $this->node,
                ':lang' => $this->lang
                );
            Model::query($sql, $values);

            return Model::insertId();
        }

        /**
         * Store HTML email body generating previously an unique ID for the filename
         * @param $sendId
         * @param $filename
         * @return
         */
        public function saveContentToFile($sendId) {
            $email = ($this->massive) ? "any" : $this->to;
            $path = ($this->massive) ? "/news/" : "/sys/";
            $contentId = md5("{$sendId}_{$email}_{$this->template}_" . GOTEO_MISC_SECRET) . ".html";

            $sql = "UPDATE mail SET content = :content WHERE id = :id";
            $values = array (
                ':content' => $path . $contentId,
                ':id' => $sendId,
                );
            Model::query($sql, $values);

            // Necesitamos constante de donde irán los mails: MAIL_PATH = /data/mail
            // MAIL_PATH + $path
            if (FILE_HANDLER == 'file') {
                $path = 'mail' . $path;
            }

            // Guardar al sistema de archivos
            $fpremote = File::factory(array('bucket' => AWS_S3_BUCKET_MAIL));
            $fpremote->setPath($path);

            $headers = array("Content-Type" => "text/html; charset=UTF-8");
            $fpremote->put_contents($contentId, $this->content, 0, 'public-read', array(), $headers);
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
        public static function getSended($filters = array(), $node = null, $limit = 9) {

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

            $sql = "SELECT
                        mail.id as id,
                        mail.email as email,
                        mail.template as template,
                        DATE_FORMAT(mail.date, '%d/%m/%Y %H:%i') as date
                    FROM mail
                    $sqlFilter
                    ORDER BY mail.date DESC
                    LIMIT {$limit}";

            $query = Model::query($sql, $values);
            return $query->fetchAll(\PDO::FETCH_OBJ);

        }

        /**
         * Devuelve el enlace para Sinoves
         * @param $id
         * @return $url
         */
        public static function getSinovesLink($id) {

            $url = '';

            $sql = "SELECT content
                FROM mail
                WHERE id = :id";

            $query = Model::query($sql, array(':id' => $id));
            $content = $query->fetchColumn();

            if (FILE_HANDLER == 's3') {
                $url = 'http://' . AWS_S3_BUCKET_MAIL . $content;
            } elseif (FILE_HANDLER == 'file') {
                $url = SITE_URL . '/mail' . $content;
            }

            return $url;
        }

        /**
         * Control de límite de mails que se pueden enviar al día
         * limite de 50000 al día, o lo que esté definido en la variable GOTEO_MAIL_QUOT
         * cli-sender.php sobreescribe GOTEO_MAIL_QUOTA para guardarnos un 20% para envios individuales
         */
        public static function checkLimit($add = null, $ret = false, $limit = null) {

            if((int)$limit) $LIMIT = (int) $limit;
            else            $LIMIT = (defined("GOTEO_MAIL_QUOTA") ? GOTEO_MAIL_QUOTA : 50000);

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

}
