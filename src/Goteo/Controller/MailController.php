<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Goteo\Core\Redirection;
use Goteo\Core\Model;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Model\Template;
use Goteo\Model\MailStats;
use Goteo\Library\Mail as Mailer;

class MailController extends \Goteo\Core\Controller {

    /**
     * Expects a token and returns the email content
     */
    public function indexAction ($token) {

        if(list($md5, $email, $id) = Mailer::decodeToken($token)) {

            // track this opening
            MailStats::markRead($id);
            // Content still in database?
            if ($mail = Mailer::get($id)) {
                return new Response($mail->render());
            }

            // TODO, check if exists as file-archived
        }

        throw new ControllerException('Mail not available!');
    }

    /**
     * Redirects to the apropiate link
     */

    /**
     * Returns an empty gif, to track the email
     */
    public function trackAction($id) {
        MailStats::markRead($id);
        // Return a transparent GIF
        return new Response(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='), Response::HTTP_OK, ['Content-Type' => 'image/gif']);
    }

}

