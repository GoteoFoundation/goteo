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

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use UAParser\Parser as UAParser;
use Snowplow\RefererParser\Parser as RefererParser;

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Model\Origin;

/**
 * This listener tracks REFERER and User Agent (UA) to fill the origin stats
 * mysql table
 */
class OriginListener extends AbstractListener {
    /**
     * Saves default referer to session
     * @param  GetResponseEvent $event [description]
     * @return [type]                  [description]
     */
    public function onRequest(GetResponseEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if(!Session::exists('origin.ua')) {
            // Extracting UA elements
            // https://github.com/ua-parser/uap-php
            $parser = UAParser::create();
            $result = $parser->parse($request->headers->get('User-Agent'));
            $ua = [
                'tag' => $result->ua->family,
                'category' => $result->os->family,
                'type' => 'ua'
                ];

            // var_dump($ua);
            Session::store('origin.ua', $ua);
        }

        // Manual origin tracker
        if($ref = $request->query->get('ref')) {
            $referer = [
                'tag' => $ref,
                'category' => 'campaign',
                'type' => 'referer'
                ];
            // print_r($referer);die;
        } else {
            // Extracting Referer elements
            // https://github.com/snowplow/referer-parser/tree/master/php
            $parser = new RefererParser();
            $ref = $request->headers->get('referer');
            $result = $parser->parse($ref, $request->getUri());
            $parts = explode("/", $request->getPathInfo());

            // echo $ref .','. $request->getUri();
            $referer = [
                'tag' => $result->getSource(),
                'category' => $result->getMedium(),
                'type' => 'referer'
                ];
        }
        if($referer['category'] === 'internal') {
            $referer['tag'] = $parts[1];
        }

        // Tracked links form MailController as type "email"
        if($parts[1] === 'mail') {
            $referer['tag'] = 'Newsletter';
            $referer['category'] = 'email';
        }
        if($referer['category'] !== 'invalid' && Session::get('origin.referer') !== $referer) {
            Session::store('origin.referer', $referer);
        }
    }


    /**
     * Registers the origin of the visit in the response event so controllers can manipulate
     * the referer via $request->headers->set('referer', $request->getUri());
     * @param  FilterResponseEvent $event [description]
     * @return [type]                     [description]
     */
    public function onResponse(FilterResponseEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        list($_, $type, $id) = explode("/", $request->getPathInfo());

        $ua = Session::get('origin.ua');
        $referer = Session::get('origin.referer');
        // TODO: add channel, blog (post): requires db migration
        if(in_array($type, ['project', 'call'])) {
            // print_r($referer);print_r(Session::get("origin.{$type}_referer"));die("[$type] $id");
            $ua_id = $ua + ["{$type}_id" => $id];
            if(Session::get("origin.{$type}_ua") !== $ua_id) {
                $origin = Origin::getFromArray($ua_id);
                // echo "saving ua";
                $origin->save();
                Session::store("origin.{$type}_ua", $ua_id);
            }
            $referer_id = $referer + ["{$type}_id" => $id];
            if(Session::get("origin.{$type}_referer") !== $referer_id) {
                $origin = Origin::getFromArray($referer_id);
                // echo "saving referer";
                $origin->save();
                Session::store("origin.{$type}_referer", $referer_id);
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
        return [
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::RESPONSE => 'onResponse',
            AppEvents::INVEST_INIT => 'onInvestInit'
        ];
    }

}
