<?php

namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception,
        Goteo\Library\FileHandler\File,
        Goteo\Core\View;

    class Mail {

        public
            $id, // id registro en tabla mail
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

            // Inicializa la instancia PHPMailer.
            $mail = new \PHPMailer($exceptions);

            // Define  el idioma para los mensajes de error.
            $mail->setLanguage("es");

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
                    $mail->isSendmail(); // set mailer to use $Sendmail program.
                    break;
                case "qmail":
                    $mail->isQmail(); // set mailer to use qmail MTA.
                    break;
                case "smtp":
                    $mail->isSMTP(); // set mailer to use SMTP
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
            elseif(!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
		        $errors['email'] = 'Email destinatario inválido.';
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

            if (empty($this->id)) {
                $this->saveEmailToDB();
            }

            if($this->validate($errors)) {
                try {
                    $mail = $this->buildMessage();

                    // si estoy en entorno local ni lo intento
                    // if (GOTEO_ENV == 'local') {
                    //     // add any debug here
                    //     $errors[] = 'No envía porque está en local';
                    //     $errors[] = "Asunto: {$this->subject}";
                    //     $errors[] = "Destinatario: {$this->to}";
                    //     $errors[] = "Plantilla: {$this->template}";
                    //     $errors[] = "<hr />";

                    //     return true;
                    // }

                    // Envía el mensaje
                    if ($mail->send($errors)) {
                        $this->saveContentToFile();
                        return true;
                    } else {
                        $errors[] = 'Fallo del servidor de correo interno';
                        return false;
                    }

            	} catch(\PDOException $e) {
                    $errors[] = "No se ha podido enviar el mensaje: " . $e->getMessage();
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

            // Para pruebas en beta/local
            // TODO: arreglar esto para que sea mas portable
            if (GOTEO_ENV !== 'real') {
                if (!preg_match('/(.+)goteo\.org|(.+)platoniq\.net|(.+)microstudi.net|(.+)doukeshi\.org|julian\.canaves@gmail\.com|pablo@anche\.no|javicarrillo83@gmail\.com|esenabre@gmail\.com|mmtarres@gmail\.com|olivierschulbaum@gmail\.com/i', $address)) {
                    $address = str_replace('@', '_', $address).'_from_beta@doukeshi.org';
                }
            }

            $mail->addAddress($address, $this->toName);
            // copia a mail log si no es masivo
            if (GOTEO_ENV == 'real' && !$this->massive) {
                $mail->addBCC('goteomaillog@gmail.com', 'Verifier');
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

                // incrustar el logo de goteo o del nodo
                if (!empty($this->node) && $this->node != GOTEO_NODE) {
                    $mail->addEmbeddedImage(GOTEO_WEB_PATH . 'nodesys/'.$this->node.'/view/css/logo.png', 'logo', 'Goteo '.$this->node, 'base64', 'image/png');
                } else {
                    $mail->addEmbeddedImage(GOTEO_WEB_PATH . 'app/goteo_logo.png', 'logo', 'Goteo', 'base64', 'image/png');
                }
            }
            else {
                $mail->isHTML(false);
                $mail->Body    = $this->bodyHTML(true);
            }
            return $mail;
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

            $this->node = $_SESSION['admin_node'];

            // tokens
            $sinoves_token = md5(uniqid()) . '¬' . $this->to  . '¬' . $this->id;
            $leave_token = md5(uniqid()) . '¬' . $this->to  . '¬' . $this->id;

            $viewData['sinoves'] = SITE_URL . '/mail/' . \mybase64_encode($sinoves_token) . '?email=' . $this->to;
            // $viewData['sinoves'] = static::getSinovesLink($sendId);
            // no podemos usar este porque el content no se graba hasta despues de enviado
            // y el método no tiene esta prueba de fallo

            $viewData['baja'] = SITE_URL . '/user/leave?email=' . $this->to;

            if ($plain) {
                return strip_tags($this->content) . '

                ' . $viewData['sinoves'];
            } else {
                // para plantilla boletin
                if ($this->template == 33) {
                    $viewData['baja'] = SITE_URL . '/user/unsuscribe/' . \mybase64_encode($leave_token);
                    return View::get('email/newsletter.html.php', $viewData);

                } elseif (!empty($this->node) && $this->node != GOTEO_NODE) {
                    return View::get($this->node.'/view/email/default.html.php', $viewData);

                } else {
                    return View::get('email/goteo.html.php', $viewData);
                }
            }
        }

        /**
         * Save email metadata to DB
         * @param $email
         * @return int ID of the inserted email
         */
        public function saveEmailToDB() {

            $email = ($this->massive) ? "any" : $this->to;

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

            //do no need to repeat if already uploaded
            $sql = "SELECT content FROM mail WHERE id = :id";
            $query = Model::query($sql, array(':id' => $this->id));
            $current = (int) $query->fetchColumn();
            if(empty($current)) {
                return false;
            }

            $email = ($this->massive) ? "any" : $this->to;
            $path = ($this->massive) ? "/news/" : "/sys/";
            $contentId = md5("{$this->id}_{$email}_{$this->template}_" . GOTEO_MISC_SECRET) . ".html";

            $sql = "UPDATE mail SET html='', content = :content WHERE id = :id";
            $values = array (
                ':content' => $path . $contentId,
                ':id' => $this->id,
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
