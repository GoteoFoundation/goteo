<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
        Goteo\Model,
        Goteo\Library\Mail,
        Goteo\Library\Message,
		Goteo\Library\Template,
        Goteo\Library\Newsletter as Boletin,
		Goteo\Library\Sender;

    class Newsletter {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            switch ($action) {
                case 'init':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        // plantilla
                        $template = $_POST['template'];

                        // destinatarios
                        if ($_POST['test']) {
                            $users = Boletin::getTesters();
                        } elseif ($template == 33) {
                            // los destinatarios de newsletter
                            $users = Boletin::getReceivers();
                        } elseif ($template == 35) {
                            // los destinatarios para testear a subscriptores
                            $users = Boletin::getReceivers();
                        } elseif ($template == 27 || $template == 38) {
                            // los cofinanciadores de este año
                            $users = Boletin::getDonors(Model\User\Donor::currYear());
                        }

                        // sin idiomas
                        $nolang = $_POST['nolang'];
                        if ($nolang) {
                            $receivers[LANG] = $users;
                        } else {
                            // separamos destinatarios en idiomas
                            $receivers = array();
                            foreach ($users as $usr) {
                                // idioma de preferencia
                                $prefer = Model\User::getPreferences($usr->user);
                                $comlang = !empty($prefer->comlang) ? $prefer->comlang : $usr->lang;
                                if (empty($comlang)) $comlang = LANG;
                                $receivers[$comlang][$usr->user] = $usr;
                            }
                        }

                        // idiomas que vamos a enviar
                        $langs = array_keys($receivers);


                        // para cada idioma
                        foreach ($langs as $lang) {

                            // destinatarios
                            $recipients = $receivers[$lang];

                            // datos de la plantilla
                            $tpl = Template::get($template, $lang);

                            // contenido de newsletter
                            $content = ($template == 33) ? Boletin::getContent($tpl->text, $lang) : $content = $tpl->text;

                            // asunto
                            $subject = $tpl->title;

                            $mailHandler = new Mail();
                            $mailHandler->template = $template;
                            $mailHandler->content = $content;
                            $mailHandler->node = $node;
                            $mailHandler->lang = $lang;
                            $mailId = $mailHandler->saveEmailToDB();

                            // inicializamos el envío
                            if (Sender::initiateSending($mailId, $subject, $recipients, true)) {
                                // ok...
                            } else {
                                Message::Error('No se ha podido iniciar el mailing con asunto "'.$subject.'"');
                            }
                        }

                    }

                    throw new Redirection('/admin/newsletter');

                    break;
                case 'activate':
                    if (Sender::activateSending($id)) {
                        Message::Info('Se ha activado un nuevo envío automático');
                    } else {
                        Message::Error('No se pudo activar el envío. Iniciar de nuevo');
                    }
                    throw new Redirection('/admin/newsletter');
                    break;
                case 'detail':

                    $mailing = Sender::getSending($id);
                    $list = Sender::getDetail($id, $filters['show']);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'detail',
                            'detail' => $filters['show'],
                            'mailing' => $mailing,
                            'list' => $list
                        )
                    );
                    break;
                default:
                    $list = Sender::getMailings();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'list',
                            'list' => $list
                        )
                    );
            }

        }
    }

}
