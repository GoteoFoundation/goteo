<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Message,
		Goteo\Library\Newsletter;

    class Newsletter {

        public static function process ($action = 'list', $id = null) {

            switch ($action) {
                case 'init':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $suject = \strip_tags($_POST['subject']);
                        if ($_POST['test']) {
                            $receivers = Newsletter::getTesters();
                        } else {
                            $receivers = Newsletter::getReceivers();
                        }
                        if (Newsletter::initiateSending($suject, $receivers)) {

                            $mailing = Newsletter::getSending();

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
                    if (Newsletter::activateSending()) {
                        Message::Info('Se ha activado el envío automático de newsletter');
                    } else {
                        Message::Error('No se pudo activar el envío. Iniciar de nuevo');
                    }
                    throw new Redirection('/admin/newsletter');
                    break;
                case 'detail':
                    $list = Newsletter::getDetail($id);

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
                    $mailing = Newsletter::getSending();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'list',
                            'mailing' => $mailing
                        )
                    );
            }

        }
    }

}
