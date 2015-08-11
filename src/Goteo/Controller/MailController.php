<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Exception\ModelException;
use Goteo\Model\Mail\MailStats;
use Goteo\Model\Mail\MailStatsLocation;
use Goteo\Model\Mail\Metric;
use Goteo\Model\Mail;

class MailController extends \Goteo\Core\Controller {

    /**
     * Expects a token and returns the email content
     */
    public function indexAction ($token, Request $request) {

        if(list($email, $mail_id) = Mail::decodeToken($token)) {
            // die("$email $mail_id");

            // A numeric email refers to a ID entry of the mailer_content table (pending sendings)
            // 'any' refers to any massive sending
            $track = false;
            if(!is_numeric($email) && $email !== 'any') {
                // track this opening
                $track = true;
                try {
                    $stat = MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
                    // try to geolocate
                    try {
                        $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                        $loc->save();
                    } catch (ModelException $e) {
                        if(App::debug()) {
                            throw $e;
                        }
                    }

                } catch(\Exception $e) {
                    // TODO: log this
                }

            }
            // Content still in database?
            if ($mail = Mail::get($mail_id)) {
                $mail->to = $email;
                return new Response($mail->render(false, [], false));
            }

            // TODO, check if exists as file-archived
        }

        throw new ControllerException('Mail not available!');
    }

    /**
     * @deprecated
     * Redirects to the apropiate link
     * Not really used, only as a fallback if mailstats fails
     */
    public function urlAction ($token, Request $request) {

        if(list($email, $mail_id, $url) = Mail::decodeToken($token)) {
            // track this opening
            try {
                $stat = MailStats::incMetric($mail_id, $email, $url);
                // try to geolocate
                try {
                    $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                    $loc->save();
                } catch (ModelException $e) {
                    if(App::debug()) {
                        throw $e;
                    }
                }
            } catch(\Exception $e) {
                //TODO: log this
            }

            return $this->redirect($url);
        }

        throw new ControllerException('Link not available!');
    }

    /**
     * Redirects to the apropiate from a mailStats id
     */
    public function linkAction ($id, Request $request) {

        if($stat = MailStats::get($id)) {
            // track this opening
            try {
                // try to geolocate
                try {
                    // $loc = MailStatsLocation::createByIp($stat->id, $stat->id, '128.101.101.101');
                    $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                    $loc->save();
                } catch (ModelException $e) {
                    if(App::debug()) {
                        throw $e;
                    }
                }
                $stat->inc();
                $stat->save();
                $url = $stat->getMetric()->metric;

                if($url) {
                    return $this->redirect($url);
                }
            } catch(\Exception $e) {
                //TODO: log this
                Message::error($e->getMessage());
            }

        }

        throw new ControllerException('Link not available!');
    }

    /**
     * Returns an empty gif, to track the email
     */
    public function trackAction($token, Request $request) {
        //decode token
        if(list($email, $mail_id) = Mail::decodeToken($token)) {
            $stat = MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
            // try to geolocate
            try {
                $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                $loc->save();
            } catch (ModelException $e) {
                // die($e->getMessage());
            }

        }
        // Return a transparent GIF
        return new Response(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='), Response::HTTP_OK, ['Content-Type' => 'image/gif']);
    }

}

