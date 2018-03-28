<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Exception\ModelException;
use Goteo\Model\Mail\MailStats;
use Goteo\Model\Mail\MailStatsLocation;
use Goteo\Model\Mail\Metric;
use Goteo\Model\Mail;
use Goteo\Model\User;

class MailController extends \Goteo\Core\Controller {

    /**
     * Expects a token and returns the email content
     */
    public function indexAction ($token, Request $request) {

        if (list($email, $mail_id, $tracker) = Mail::decodeToken($token)) {

            // die("$email | $mail_id | [$tracker]");
            $track = ($tracker == '1');
            // track this opening
            if ($track) {
                try {
                    // try to geolocate
                    try {
                        // email tracker
                        $stat = MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
                        // print_r($stat);die;
                        // geolocation if exists database
                        if (Config::get('geolocation.maxmind.cities')) {
                            $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                            if($loc) $loc->save();
                        }
                    } catch (ModelException $e) {
                        if (App::debug()) {
                            throw $e;
                        }
                    }

                } catch(\Exception $e) {
                    // TODO: log this
                    if (App::debug()) {
                        throw $e;
                    }
                }

            }
            // Content still in database?
            if ($mail = Mail::get($mail_id)) {
                if ($mail->massive) {
                    if ($user = User::getByEmail($email)) {
                        $mail->content = str_replace(
                            array('%USERID%', '%USEREMAIL%', '%USERNAME%', '%SITEURL%'),
                            array($user->id, $user->email, $user->name, SITE_URL),
                            $mail->content
                        );
                    }
                }
                $mail->to = $email;
                if(empty($mail->template)) {
                    $mail->content = '<pre>' . $mail->content . '</pre>';
                }
                return new Response($mail->render(false, [], $track));
            }

            // TODO, check if exists as file-archived
        }

        throw new ControllerException('Mail not available!');
    }

    /**
     * Redirects to the apropiate link
     * Using this method as default to avoid creating empty metrics in database
     */
    public function urlAction ($token, Request $request) {

        if (list($email, $mail_id, $url) = Mail::decodeToken($token)) {
            // die("$email $mail_id $url");
            // track this opening
            try {
                $stat = MailStats::incMetric($mail_id, $email, $url);
                // var_dump($stat);die;
                // try to geolocate
                try {
                    // set email opened metric if empty
                    $e = MailStats::incMetric($stat->mail_id, $stat->email, 'EMAIL_OPENED', true);
                    // geolocation if exists database
                    if (Config::get('geolocation.maxmind.cities')) {
                        $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                        if($loc) {
                            $loc->save();
                            $loc->id = $e->id; // add to EMAIL_OPENED
                            $loc->save();
                        }
                    }
                } catch (ModelException $e) {
                    if (App::debug()) {
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

        if ($stat = MailStats::get($id)) {
            // print_r($stat);die("$id");
            // track this opening
            try {
                try {
                    // set email opened metric if empty
                    $e = MailStats::incMetric($stat->mail_id, $stat->email, 'EMAIL_OPENED', true);
                    // try to geolocate
                    if (Config::get('geolocation.maxmind.cities')) {
                        // $loc = MailStatsLocation::createByIp($stat->id, $stat->id, '128.101.101.101');
                        $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                        $loc->save();
                        $loc->id = $e->id; // add to EMAIL_OPENED
                        $loc->save();
                    }
                } catch (ModelException $e) {
                    if (App::debug()) {
                        throw $e;
                    }
                }
                $stat->inc();
                $stat->save();
                // Mark as readed if mail exists
                $url = $stat->getMetric()->metric;
                // print_r($url);die;
                if ($url) {
                    return $this->redirect($url);
                }
            } catch(\Exception $e) {
                $this->warning($e->getMessage(), [$stat, 'link_id' => $id, 'url' => $url]);
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
        if (list($email, $mail_id) = Mail::decodeToken($token)) {
            // try to geolocate
            try {
                // email metric
                $stat = MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
                // Geolocation if exists
                if (Config::get('geolocation.maxmind.cities')) {
                    $loc = MailStatsLocation::createByIp($stat->id, $request->getClientIp());
                    if($loc) $loc->save();
                }
            } catch (ModelException $e) {
                // die($e->getMessage());
            }

        }
        // Return a transparent GIF
        return new Response(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='), Response::HTTP_OK, ['Content-Type' => 'image/gif']);
    }

}

