<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerException;
use Goteo\Model\Mail\MailStats;
use Goteo\Model\Mail\Metric;
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
            $track = false;
            if(!is_numeric($email) && $email !== 'any') {
                // track this opening
                $track = true;
                try {
                    MailStats::incMetric($mail_id, $email, 'EMAIL_OPENED');
                    // TODO: create location

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
     * Redirects to the apropiate link
     * Not really used, only as a fallback if mailstats fails
     */
    public function urlAction ($token) {

        if(list($email, $mail_id, $url) = Mail::decodeToken($token)) {
            // track this opening
            try {
                MailStats::incMetric($mail_id, $email, $url);
                // TODO: create location

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
                if($cities = Config::get('geolocation.maxmind.cities')) {
                    try {
                        // This creates the Reader object, which should be reused across lookups.
                        $reader = new \GeoIp2\Database\Reader($cities);
                        $record = $reader->city($request->getClientIp());
                        // $record = $reader->city('128.101.101.101');
                        //Handles user localization
                        $loc = new \Goteo\Model\Mail\MailStatsLocation(array(
                                'id'           => $stat->id,
                                'city'         => $record->city->name,
                                'region'       => $record->mostSpecificSubdivision->name,
                                'country'      => $record->country->name,
                                'country_code' => $record->country->isoCode,
                                'longitude'    => $record->location->longitude,
                                'latitude'     => $record->location->latitude,
                                'method'       => 'ip'
                            ));
                        $loc->save($errors);
                    }catch(\Exception $e){
                        // die($e->getMessage());
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
            }

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

