<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Core\Redirection;
use Goteo\Core\Model;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Library\Mail as Mailer;

class MailController extends \Goteo\Core\Controller {

    /**
     * solo si recibe un token válido
     * Método antiguo que se mantiene por compatibilidad con la versión anterior que guardaba el contenido de los email en la BD
     */
    public function indexAction ($token, Request $request) {

        $link = '/';
        // die(mybase64_encode('asd¬19mikel95@gmail.com¬1234587'));
        $token = \mybase64_decode($token);
        list($md5, $email, $id) = explode('¬', $token);
        // die("[$md5] [$email] [$id]");

        // revert al antiguo sinoves que saca de la tabla mail
        if($request->query->get('email') === $email && $id) {

            // cogemos el md5 del archivo a cargar del campo 'content' de la tabla 'mail'
            // montamos url (segun newsletter)  y hacemos get_content
            // pasamos el contenido a la vista

            // cogemos el contenido de la bbdd y lo pintamos aqui tal cual
            if ($query = Model::query('SELECT html, template FROM mail WHERE id = ?', $id)) {
                $mail = $query->fetchObject();
                $content = $mail->html;
                $template = $mail->template;


                if ($template == 33) {
                    return $this->viewResponse('email/newsletter', array('content' => $content, 'baja' => ''));
                } else {
                    $baja = SEC_URL . '/user/leave?email=' . $email;
                    return $this->viewResponse('email/default', array('content' => $content, 'baja' => $baja));
                }
            }
        }

        throw new ControllerException('Mail not available!');
    }


    /**
     * Email
     */
    public function sysAction ($file) {

        if (FILE_HANDLER != 'file') {
            throw new ControllerException('Mail not available!');
        }

        // constante MAIL_PATH
        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'sys' . DIRECTORY_SEPARATOR;
        $path .= $file;
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new ControllerException('Mail not available!');
        }

        die($contents);
    }

    /**
     * Newsletter email
     */
    public function newsAction ($file) {

        if (FILE_HANDLER != 'file') {
            throw new ControllerException('Mail not available!');
        }

        // constante MAIL_PATH
        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR;
        $path .= $file;
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new ControllerException('Mail not available!');
        }

        die($contents);
    }
}

