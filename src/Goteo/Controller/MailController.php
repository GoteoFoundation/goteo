<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Goteo\Application\Exception\ControllerException;
use Goteo\Model\MailStats;
use Goteo\Model\Mail;

class MailController extends \Goteo\Core\Controller {

    /**
     * Expects a token and returns the email content
     */
    public function indexAction ($token) {

        if(list($email, $mail_id) = Mail::decodeToken($token)) {
            // die("$email $mail_id");

            // A numeric email refers to a ID entry of the mailer_content table (pending sendings)
            // 'any' refers to any massive sending
            if(!is_numeric($email) && $email !== 'any') {
                // track this opening
                MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
            }
            // Content still in database?
            if ($mail = Mail::get($mail_id)) {
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

        if(list($email, $mail_id, $url) = Mail::decodeToken($token)) {
            // die("$email $mail_id $url");

            // track this opening
            MailStats::incMetric($mail_id, $email, $url);

            return $this->redirect($url);
        }

        throw new ControllerException('Link not available!');
    }

    /**
     * Returns an empty gif, to track the email
     */
    public function trackAction($token) {
        //decode token
        if(list($email, $mail_id) = Mail::decodeToken($token)) {
            MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
        }
        // Return a transparent GIF
        return new Response(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='), Response::HTTP_OK, ['Content-Type' => 'image/gif']);
    }

}

