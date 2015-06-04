<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library,
        Goteo\Library\Text,
        Goteo\Library\Template;

    class Contact extends \Goteo\Core\Controller {

        public function index () {

            $tags = array();
            $rawTags = Text::get('contact-form-tags');
            $listTags = explode(';', $rawTags);
            foreach ($listTags as $pair) {
                $pair = trim($pair);
                if (empty($pair)) continue;
                $pairTag = explode(']', $pair);
                $keyTag = trim(str_replace(array('[', '<br />'), '', $pairTag[0]));
                $tags[$keyTag] = trim($pairTag[1]);
            }

            // $showCaptcha equivale a que estamos en entorno real
            $showCaptcha = (GOTEO_ENV == 'real');

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {

                // verificamos token
                if ($showCaptcha && ( !isset($_POST['msg_token']) || $_POST['msg_token']!=$_SESSION['msg_token']) ) {
                    header("HTTP/1.1 400 Bad request");
                    die('Token incorrect');
                }

                $name = $_POST['name'];

                // si falta mensaje, email o asunto, error
                if(empty($_POST['email'])) {
                    $errors['email'] = Text::get('error-contact-email-empty');
                } elseif(!\Goteo\Library\Check::mail($_POST['email'])) {
                    $errors['email'] = Text::get('error-contact-email-invalid');
                } else {
                    $email = $_POST['email'];
                }

                if(empty($_POST['subject'])) {
                    $errors['subject'] = Text::get('error-contact-subject-empty');
                } else {
                    $subject = $_POST['subject'];
                }

                if(empty($_POST['message'])) {
                    $errors['message'] = Text::get('error-contact-message-empty');
                } else {
                    $msg_content = nl2br(\strip_tags($_POST['message']));
                }

                if ($showCaptcha) {
                    // verificamos el captcha
                    // estamos en src/Goteo/Controller
                    require_once __DIR__ . '/../Library/recaptchalib/recaptchalib.php';
                    $resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response"]);
                    if (!$resp->is_valid) {
                        $errors['recaptcha'] = Text::get('error-contact-captcha');
                    }
                }

                $data = array(
                        'tag' => $_POST['tag'],
                        'subject' => $_POST['subject'],
                        'name'    => $_POST['name'],
                        'email'   => $_POST['email'],
                        'message' => $_POST['message']
                );

                if (empty($errors)) {

                    // Obtenemos la plantilla para asunto y contenido
                    $template = Template::get(1);

                    // Asunto, añadimos tag
                    $tag = (!empty($_POST['tag'])) ? "[{$_POST['tag']}] " : '';
                    $subject = $tag.$subject;

                    // destinatario
                    if (\defined('NODE_MAIL')) {
                        $to = \NODE_MAIL;
                        $toName = \NODE_NAME;
                    } else {
                        $to = \GOTEO_CONTACT_MAIL;
                        $toName = 'Goteo';
                    }

                    // En el contenido:
                    $search  = array('%TONAME%', '%MESSAGE%', '%USEREMAIL%');
                    $replace = array($toName, $msg_content, $name.' '.$email);
                    $content = \str_replace($search, $replace, $template->text);


                    $mailHandler = new Library\Mail();

                    $mailHandler->to = $to;
                    $mailHandler->toName = $toName;
                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->reply = $email;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    if ($mailHandler->send($errors)) {
                        Application\Message::info('Mensaje de contacto enviado correctamente.');
                        $data = array();
                    } else {
                        Application\Message::error('Ha fallado al enviar el mensaje.');
                    }

                    unset($mailHandler);
                }
            }

            return new View(
                'about/contact.html.php',
                array(
                    'data'    => $data,
                    'tags'    => $tags,
                    'showCaptcha' => $showCaptcha,
                    'errors'  => $errors
                )
            );

        }

    }

}
