<?php

namespace Goteo\Controller {

    use Goteo\Core\Redirection,
        Goteo\Core\Model,
        Goteo\Application\Config,
        Goteo\Library\Mail as Mailer;

    class Mail extends \Goteo\Core\Controller {

        /**
         * solo si recibe un token válido
         * Método antiguo que se mantiene por compatibilidad con la versión anterior que guardaba el contenido de los email en la BD
         */
        public function index ($token) {

            $link = '/';

            if (!empty($token)) {

                $token = \mybase64_decode($token);
                $parts = explode('¬', $token);

                /*
                    // este metodo no se puede usar si no se ha grabado el contenido
                    // y ahora mismo no se está grabando
                if (!empty($parts[2])) {

                    // $link = Mailer::getSinovesLink($parts[2]);
                }
                */

                // revert al antiguo sinoves que saca de la tabla mail
                if(count($parts) > 2 && $_GET['email'] == $parts[1] && !empty($parts[2])) {

                    // cogemos el md5 del archivo a cargar del campo 'content' de la tabla 'mail'
                    // montamos url (segun newsletter)  y hacemos get_content
                    // pasamos el contenido a la vista

                    // cogemos el contenido de la bbdd y lo pintamos aqui tal cual
                    if ($query = Model::query('SELECT html, template FROM mail WHERE id = ?', $parts[2])) {
                        $mail = $query->fetchObject();
                        $content = $mail->html;
                        $template = $mail->template;


                        if ($template == 33) {
                            return new View ('email/newsletter.html.php', array('content'=>$content, 'baja' => ''));
                        } else {
                            $baja = SEC_URL . '/user/leave/?email=' . $parts[1];

                            if (!\Goteo\Application\Config::isMasterNode() && \file_exists('nodesys/' . Config::get('current_node') . '/view/email/default.html.php')) {
                                return new View (Config::get('current_node') . '/view/email/default.html.php', array('content'=>$content, 'baja' => $baja));
                            } else {
                                return new View ('email/goteo.html.php', array('content'=>$content, 'baja' => $baja));
                            }
                        }
                    }
                }

            }

            throw new Redirection($link);
        }


        /**
         * Email
         */
        public function sys ($file) {

            if (FILE_HANDLER != 'file') {
                throw new Redirection('/');
            }

            // constante MAIL_PATH
            $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'sys' . DIRECTORY_SEPARATOR;
            $path .= $file;
            $contents = file_get_contents($path);

            if ($contents === false) {
                throw new Redirection('/');
            }

            die($contents);
        }

        /**
         * Newsletter email
         */
        public function news ($file) {

            if (FILE_HANDLER != 'file') {
                throw new Redirection('/');
            }

            // constante MAIL_PATH
            $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR;
            $path .= $file;
            $contents = file_get_contents($path);

            if ($contents === false) {
                throw new Redirection('/');
            }

            die($contents);
        }
    }

}
