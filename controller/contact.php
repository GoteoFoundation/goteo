<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template;

    class Contact extends \Goteo\Core\Controller {
        
        public function index () {

            $page = Page::get('contact');

                $errors = array();

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {

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
                        $msg_content = \strip_tags($_POST['message']);
                        $msg_content = nl2br($msg_content);
                    }

                    if (empty($errors)) {
                        $data = array(
                                'subject' => $_POST['subject'],
                                'email'   => $_POST['email'],
                                'message' => $_POST['message']
                        );

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(1);

                // Sustituimos los datos
                $subject = str_replace('%SUBJECT%', $subject, $template->title);

                // En el contenido:
                $search  = array('%TONAME%', '%MESSAGE%', '%USEREMAIL%');
                $replace = array('Goteo', $msg_content, $email);
                $content = \str_replace($search, $replace, $template->text);


                        $mailHandler = new Mail();

                        $mailHandler->to = 'info@platoniq.net';
                        $mailHandler->toName = 'Goteo';
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->fromName = '';
                        $mailHandler->from = $email;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send($errors)) {
                            $message = 'Mensaje de contacto enviado correctamente.';
                            $data = array();
                        } else {
                            $errors[] = 'Ha habido algún error al enviar el mensaje.';
                        }

                        unset($mailHandler);
                    }
                }

                return new View(
                    'view/about/contact.html.php',
                    array(
                        'data'    => $data,
                        'errors'  => $errors,
                        'message' => $message
                    )
                );
            
        }
        
    }
    
}