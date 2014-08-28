<?php

namespace Goteo\Controller {

    use Goteo\Core\Redirection,
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

                if (!empty($parts[2])) {
                    if (FILE_HANDLER == 's3') {
                        $link = Mailer::getSinovesLink($parts[2]);
                    } elseif (FILE_HANDLER == 'file') {
                        $link = Mailer::getSinovesLink($parts[2]);
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

            $path = 'data' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'sys' . DIRECTORY_SEPARATOR;
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
