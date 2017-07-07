<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use UAParser\Parser as UAParser;
use Snowplow\RefererParser\Parser as RefererParser;

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Session;
use Goteo\Model\Origin;

/**
 * This listener tracks REFERER and User Agent (UA) to fill the origin stats
 * mysql table
 */
class OriginListener extends AbstractListener {
    public function onRequest(GetResponseEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Extracting UA elements
        // https://github.com/ua-parser/uap-php
        $parser = UAParser::create();
        $result = $parser->parse($request->headers->get('User-Agent'));
        $ua = array(
            'tag' => $result->ua->family,
            'category' => $result->os->family,
            'type' => 'ua'
            );

        // var_dump($ua);
        if(!Session::exists('origin.ua')) {
            Session::store('origin.ua', $referer);
            // echo "Saving ua";
        }

        // Extracting Referer elements
        // https://github.com/snowplow/referer-parser/tree/master/php
        $parser = new RefererParser();
        $ref = $request->headers->get('referer');
        $ref = 'https://t.co/mVJXgN1Wj0';
        $result = $parser->parse($ref, $request->getUri());
        // echo $ref .','. $request->getUri();
        if ($result->isKnown()) {
            $referer = array(
                'tag' => $result->getSource(),
                'category' => $result->getMedium(),
                'type' => 'referer'
                );

            // var_dump($referer);
            // Save if is visiting a project

            if(Session::get('origin.referer') !== $referer) {
                Session::store('origin.referer', $referer);
                // echo "Saving referer";
            }
        }
        // else echo "\nReferer unknown: ".$result->getMedium();
        //
        // Check if we are visiting a project
        $path = $request->getPathInfo();
        if(strpos($path, '/project/') === 0) {
            $parts = explode("/", $path);
            if(Session::get('origin.project_ua') !== $ua) {
                $origin = Origin::getFromArray($ua + ['project_id' => $parts[2]]);
                // echo "saving ua";
                $origin->save();
                Session::store('origin.project_ua', $ua);
            }
            if(Session::get('origin.project_referer') !== $referer) {
                $origin = Origin::getFromArray($referer + ['project_id' => $parts[2]]);
                // echo "saving referer";
                $origin->save();
                Session::store('origin.project_referer', $referer);
            }
        }
        if(strpos($path, '/call/') === 0) {
            $parts = explode("/", $path);
            if(Session::get('origin.call_ua') !== $ua) {
                $origin = Origin::getFromArray($ua + ['call_id' => $parts[2]]);
                // echo "saving ua";
                $origin->save();
                Session::store('origin.call_ua', $ua);
            }
            if(Session::get('origin.call_referer') !== $referer) {
                $origin = Origin::getFromArray($referer + ['call_id' => $parts[2]]);
                // echo "saving referer";
                $origin->save();
                Session::store('origin.call_referer', $referer);
            }
        }
    }

    /**
     * Register invest origin on create invest
     */
    public function onInvestInit(FilterInvestInitEvent $event) {
        $invest  = $event->getInvest();
        $method  = $event->getMethod();
        $request = $event->getRequest();
        $ua = Session::get('origin.ua');
        $referer = Session::get('origin.referer');
        if(Session::get('origin.invest_ua') !== $ua) {
            $origin = Origin::getFromArray($ua + ['invest_id' => $invest->id]);
            // echo "saving ua";
            $origin->save();
            Session::store('origin.invest_ua', $ua);
        }
        if(Session::get('origin.invest_referer') !== $referer) {
            $origin = Origin::getFromArray($referer + ['invest_id' => $invest->id]);
            // echo "saving referer";
            $origin->save();
            Session::store('origin.invest_referer', $referer);
        }
    }


    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => 'onRequest',
            AppEvents::INVEST_INIT => 'onInvestInit',
        );
    }

}
