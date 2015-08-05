<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Goteo\Core\Redirection;
use Goteo\Core\Model;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Model\Template;
use Goteo\Library\MailStats;
use Goteo\Library\Mail as Mailer;

class MailController extends \Goteo\Core\Controller {

    /**
     * Expects a token and returns the email content
     */
    public function indexAction ($token) {

        if(list($email, $mail_id) = Mailer::decodeToken($token)) {
            // die("$email $mail_id");

            // A numeric email refers to a ID entry of the mailer_content table (pending sendings)
            // 'any' refers to any massive sending
            if(!is_numeric($email) && $email !== 'any') {
                // track this opening
                MailStats::incrMetric($mail_id, $email, 'read');
            }
            // Content still in database?
            if ($mail = Mailer::get($mail_id)) {
                $mail->to = $email;
                return new Response($mail->render());
            }

            // TODO, check if exists as file-archived
        }

        throw new ControllerException('Mail not available!');
    }

    /**
     * Redirects to the apropiate link
     */
    public function urlAction ($token) {

        if(list($email, $mail_id, $url) = Mailer::decodeToken($token)) {
            // die("$email $mail_id $url");

            // track this opening
            MailStats::incrMetric($mail_id, $email, $url);

            return $this->redirect($url);
        }

        throw new ControllerException('Link not available!');
    }

    /**
     * Returns an empty gif, to track the email
     */
    public function trackAction($token) {
        //decode token
        if(list($email, $mail_id) = Mailer::decodeToken($token)) {
            MailStats::incrMetric($mail_id, $email, 'read');
        }
        // Return a transparent GIF
        return new Response(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='), Response::HTTP_OK, ['Content-Type' => 'image/gif']);
    }

}

