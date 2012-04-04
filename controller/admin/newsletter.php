<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Message,
		Goteo\Library\Newsletter as Boletin;

    class Newsletter {

        public static function process ($action = 'list', $id = null) {

            switch ($action) {
                case 'init':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $suject = \strip_tags($_POST['subject']);
                        if ($_POST['test']) {
                            $receivers = Boletin::getTesters();
                        } else {
                            $receivers = Boletin::getReceivers();
                        }
                        if (Boletin::initiateSending($suject, $receivers)) {

                            $mailing = Boletin::getSending();

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
                    if (Boletin::activateSending()) {
                        Message::Info('Se ha activado el envío automático de newsletter');
                    } else {
                        Message::Error('No se pudo activar el envío. Iniciar de nuevo');
                    }
                    throw new Redirection('/admin/newsletter');
                    break;
                case 'detail':
                    $list = Boletin::getDetail($id);

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
                    $mailing = Boletin::getSending();

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
