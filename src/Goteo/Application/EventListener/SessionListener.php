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

use Goteo\Application\Config;
use Goteo\Application\Cookie;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Model;
use Goteo\Model\Project;
use Goteo\Model\Node;
use Goteo\Model\Matcher;
use Goteo\Application\Currency;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Util\Parsers\UrlLang;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class SessionListener extends AbstractListener {

    public function onRequest(GetResponseEvent $event) {

        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $parser = new UrlLang($request);

        //non cookies for notifyAction on investController
        if($parser->skipSessionManagement()) {
            return;
        }

        // TODO: replace by some controller
        if ($request->query->has('cleancache')) {
            Model::cleanCache();
        }

        // if url_lang is defined set a common cookie for all domains
        if (Config::get('url.url_lang')) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            $sub_lang = array_shift($parts);
            if($sub_lang == 'www') $sub_lang = Config::get('lang');
            if (Lang::exists($sub_lang)) {
                // reduce host: ca.goteo.org => goteo.org
                $host = implode('.', $parts);
            }
            if(!Session::getSession()->isStarted()) {
                ini_set('session.cookie_domain', ".$host");
            }
        }
        Session::start('goteo-' . Config::get('env'), Config::get('session.time'));
        Session::onSessionExpires(function () {
            Message::info(Text::get('session-expired'));
        });

        $lang = Lang::setFromGlobals($request);
        $host = $parser->getHost($lang);
        // Maintain user in secure environment if logged and ssl config on
        if (Config::get('ssl') && Session::isLogged() && !$request->isSecure()) {
            $host = 'https://' . $host;
        } else {
            $host = $request->getScheme() . '://' . $host;
        }

        // Redirect if needed
        if ($host != $request->getScheme() . '://' . $request->getHttpHost()) {
            $query = http_build_query($request->query->all());
            $event->setResponse(new RedirectResponse($host . $request->getPathInfo() . ($query ? "?$query" : '')));
            return;
        }

        // the stupid cookie EU law
        if (!Cookie::exists('goteo_cookies')) {
            Cookie::store('goteo_cookies', 'ok');
            Message::info(Text::get('message-cookies'));
        }

        $currency = $request->query->get('currency');
        if ($amount = $request->query->get('amount')) {
            $currency = (string) substr($amount, strlen((int) $amount));
        }
        if (empty($currency)) {
            $currency = Currency::current('id');
        }

        //ensure is a valid currency
        $currency = Currency::get($currency, 'id');
        Session::store('currency', $currency); // depending on request

        $langs = [];
        foreach (Lang::listAll('name', true) as $id => $lang) {
            if (Lang::isActive($id)) continue;
            $langs[Lang::getUrl($id, $request)] = $lang;
        }
        Session::addToMainMenu('<i class="fa fa-globe"></i> ' . Lang::getName(), $langs, 'langs', 10, 'main');

        // Channels
        $nodes = [];
        foreach (Node::getAll(['available' => Session::getUserId()]) as $node) {
            if($node->id === Config::get('node')) continue;
            $nodes[Lang::getUrl() . 'channel/' . $node->id] = $node->name;
        }
        Session::addToMainMenu('<i class="icon icon-channel"></i> ' . Text::get('home-channels-header'), $nodes, 'channels', 20, 'main');
        // Default menus
        Session::addToMainMenu('<i class="fa fa-search"></i> ' . Text::get('regular-discover'), Lang::getUrl() . 'discover', 'discover', 30, null, 'global-search');
        Session::addToMainMenu('<i class="icon icon-drop"></i> ' . Text::get('regular-header-about'), Lang::getUrl() . 'blog', 'about', 40);
        Session::addToMainMenu('<i class="fa fa-question-circle"></i> ' . Text::get('regular-faq'), Lang::getUrl() . 'faq', 'faq', 100);

        // Currencies
        $currencies = [];
        foreach(Currency::$currencies as $id => $c) {
            if($id === $currency) continue;
            $currencies['?currency=' . $id] = $c['html'] . ' ' .$c['name'];
        }
        Session::addToMainMenu('<i>' . Currency::get($currency, 'html') . '</i> ' . Currency::get($currency, 'name'), $currencies, 'currencies', 50, 'main');

        // Minimal User menu
        Session::addToUserMenu('<i class="icon icon-activity"></i> ' . Text::get('dashboard-menu-activity'), Lang::getUrl() . 'dashboard/activity', 'dashboard-activity', 20);
        Session::addToUserMenu('<i class="icon icon-wallet"></i> ' . Text::get('dashboard-menu-pool'), Lang::getUrl() . 'dashboard/wallet', 'dashboard-wallet', 40);
        Session::addToUserMenu('<i class="icon icon-settings"></i> ' . Text::get('dashboard-menu-profile-preferences'), Lang::getUrl() . 'dashboard/settings', 'dashboard-setting', 50);

        if($user = Session::getUser()) {
            $matchers = Matcher::getList(['owner' => $user->id]);
            if ($matchers) {
                $submenu = [];
                foreach($matchers as $i => $matcher) {
                    $submenu[] = ['text' => '<img src="' . Image::get($matcher->logo)->getLink(30, 30, true) . '"> '.strip_tags($matcher->name), 'link' => Lang::getUrl() . 'dashboard/matcher/' . $matcher->id , 'id' => 'matcher-' . $matcher->id];
                }
                Session::addToUserMenu('<i class="icon icon-call"></i> ' . Text::get('dashboard-menu-matchers'), $submenu, 'dashboard-matchers', 60, 'main');
            }

            if ( isset($user->roles['translator']) ||  isset($user->roles['admin']) || isset($user->roles['superadmin']) ) {
                Session::addToUserMenu(Text::get('regular-translate_board'), Lang::getUrl() . 'translate', 'translate', 80);
            }

            if ( isset($user->roles['checker']) ) {
                Session::addToUserMenu(Text::get('regular-review_board'), Lang::getUrl() . 'review', 'review', 90);
            }

            if ( Session::isAdmin() ) {
                Session::addToUserMenu(Text::get('regular-admin_board'), Lang::getUrl() . 'admin', 'admin', 90);
            }

            // Add last 4 owned projects
            if($projects = Project::ofmine($user->id, false, 0, 4)) {
                $submenu = [];
                foreach($projects as $i => $prj) {
                    $submenu[] = ['text' => '<img src="' . $prj->image->getLink(30, 30, true) . '"> '.strip_tags($prj->name), 'link' => Lang::getUrl() . 'dashboard/project/' . $prj->id , 'id' => 'project-' . $prj->id];
                }
                Session::addToUserMenu('<i class="icon icon-projects"></i> ' . Text::get('dashboard-menu-projects'), $submenu, 'dashboard-projects', 30, 'main');
            } else {
                Session::addToUserMenu('<i class="icon icon-projects"></i> ' . Text::get('dashboard-menu-projects'), Lang::getUrl() . 'dashboard/projects', 'dashboard-projects', 30);
            }
        }

        Session::addToUserMenu('<i class="fa fa-sign-out"></i> ' . Text::get('regular-logout'), Lang::getUrl() . 'user/logout', 'logout', 100);

        Session::renew();
    }

    /**
     * Modifies the html to add some data
     */
    public function onResponse(FilterResponseEvent $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();
        //not need to do anything on sub-requests
        //Only in html content-type
        if (!$event->isMasterRequest() || false === stripos($response->headers->get('Content-Type'), 'text/html') || $request->isXmlHttpRequest()) {
            return;
        }

        $vars = [
            'code' => $response->getStatusCode(),
            'agent' => $request->headers->get('User-Agent'),
            'referer' => $request->headers->get('referer'),
            'path' => $request->getPathInfo(),
            'query' => $request->getQueryString(),
            'user' => Session::getUserId(),
            'time' => microtime(true) - Session::getStartTime(),
            'route' => $request->attributes->get('_route'),
        ];

        $controller = $request->attributes->get('_controller');
        if (is_string($controller) && strpos($controller, '::') !== false) {
            $c = explode('::', $controller);
            $vars['controller'] = $c[0];
            $vars['action'] = $c[1];

        } else {
            $vars['controller'] = $controller;
        }

        $this->info('Request', $vars);

        //Are we shadowing some user? let's add a nice bar to return to the original user
        if ($shadowed_by = Session::get('shadowed_by')) {
            $body = '<div class="user-shadowing-bar"><a href="/user/logout"><span class="badge"><i class="fa fa-user-md"></i> ' . Session::getUser()->name . '</span> &nbsp; <i class="fa fa-hand-o-right"></i> Back to ' . $shadowed_by[1] . '</a></div>';
            $content = $response->getContent();
            $search = '<div id="header"';

            if(View::getTheme() == 'responsive') {
                $search = '<body';
            }
            $pos = strpos($content, $search);
            if ($pos !== false) {
                $pos2 = $pos + strpos(substr($content, $pos), '>') + 1;
                $content = substr($content, 0, $pos2) . $body . substr($content, $pos2);
                $response->setContent($content);
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 50], // High priority
            KernelEvents::RESPONSE => ['onResponse', -50], // low priority: after headers are processed by symfony
        ];
    }
}
