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

use Goteo\Util\Origins\Parser as OriginParser;

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
        $subdomains = Config::get('plugins.custom-domains.active') ? Config::get('plugins.custom-domains.domains') : [];
        $parser = new OriginParser($event->getRequest(), Config::getMainUrl(false), $subdomains);

        if(!Session::exists('origin.ua')) {
            // var_dump($ua);
            Session::store('origin.ua', $parser->getUA());
        }

        $referer = $parser->getReferer();

        if(Session::get('origin.referer') === $referer)
            return;

        if($referer['category'] === 'invalid')
            return;

        // Internal only if none previous registered
        if($referer['category'] === 'internal' && Session::exists('origin.referer'))
            return;

        Session::store('origin.referer', $referer);
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
        if(in_array($type, ['project', 'call', 'channel'])) {
            // print_r($referer);print_r(Session::get("origin.{$type}_referer"));die("[$type] $id");
            if($ua && is_array($ua)) {
                $ua_id = $ua + ["{$type}_id" => $id];
                if(Session::get("origin.{$type}_ua") !== $ua_id) {
                    $origin = Origin::getFromArray($ua_id);
                    // echo "saving ua";
                    $origin->save();
                    Session::store("origin.{$type}_ua", $ua_id);
                }
            }
            if($referer && is_array($referer)) {
                $referer_id = $referer + ["{$type}_id" => $id];
                if(Session::get("origin.{$type}_referer") !== $referer_id) {
                    $origin = Origin::getFromArray($referer_id);
                    // echo "saving referer";
                    $origin->save();
                    Session::store("origin.{$type}_referer", $referer_id);
                }
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
