<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
        Goteo\Model\User\Donor,
        Goteo\Library\Message,
		Goteo\Library\Template,
        Goteo\Library\Newsletter as Boletin,
		Goteo\Library\Sender;

    class Newsletter {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            switch ($action) {
                case 'init':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        // asunto
                        $subject = \strip_tags($_POST['subject']);

                        // plantilla
                        $template = $_POST['template'];
                        $tpl = Template::get($template);
                        $content = $tpl->text;

                        // destinatarios
                        if ($_POST['test']) {
                            $receivers = Boletin::getTesters();
                        } elseif ($template == 33) {
                            // los destinatarios de newsletter
                            $receivers = Boletin::getReceivers();

                            // contenido de newsletter
                            $content = Boletin::getContent($tpl->text);

                        } elseif ($template == 27 || $template == 38) {
                            // los cofinanciadores de este año
                            $receivers = Boletin::getDonors(Donor::$currYear);
                        }

                        // creamos instancia
                        $sql = "INSERT INTO mail (id, email, html, template, node) VALUES ('', :email, :html, :template, :node)";
                        $values = array (
                            ':email' => 'any',
                            ':html' => $content,
                            ':template' => $template,
                            ':node' => $_SESSION['admin_node']
                        );
                        $query = \Goteo\Core\Model::query($sql, $values);
                        $mailId = \Goteo\Core\Model::insertId();


                        // inicializamos el envío
                        if (Sender::initiateSending($mailId, $subject, $receivers)) {

                            $mailing = Sender::getSending();

                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'newsletter',
                                    'file' => 'init',
                                    'mailing' => $mailing,
                                    'receivers' => $receivers
                                )
                            );
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
                    $list = Sender::getDetail($id, $filters['show']);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'detail',
                            'detail' => $id,
                            'list' => $list
                        )
                    );
                    break;
                default:
                    $mailing = Sender::getSending();
                    $list = Sender::getMailings();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'list',
                            'mailing' => $mailing,
                            'list' => $list
                        )
                    );
            }

        }
    }

}
