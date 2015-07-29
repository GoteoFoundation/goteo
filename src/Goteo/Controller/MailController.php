<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Core\Redirection;
use Goteo\Core\Model;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Model\Template;
use Goteo\Library\Mail as Mailer;

class MailController extends \Goteo\Core\Controller {

    /**
     * Expects a token and returns the email content
     */
    public function indexAction ($token, Request $request) {

        if(list($md5, $email, $id) = Mailer::decodeToken($token)) {

            // Content still in database?
            if ($query = Model::query('SELECT html, template FROM mail WHERE id = ?', $id)) {
                $mail = $query->fetchObject();
                $content = $mail->html;
                $template = $mail->template;

                if ($template == Template::NEWSLETTER) {
                    return $this->viewResponse('email/newsletter', array('content' => $content, 'baja' => ''));
                } else {
                    $baja = SEC_URL . '/user/leave?email=' . $email;
                    return $this->viewResponse('email/default', array('content' => $content, 'baja' => $baja));
                }
            }
            // TODO, check if exists as file-archived
        }

        throw new ControllerException('Mail not available!');
    }

}

