<?php

namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;

use Goteo\Application\Config\YamlSettingsLoader;
use Goteo\Application\Config\ConfigException;

use Goteo\Core\Model;
use Goteo\Core\NodeSys;

use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Currency;
use Goteo\Library\Text;
use Goteo\Application\View;
use Goteo\Foil\Extension;

//
class SessionListener implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event) {
        //
        //LOAD CONFIG
        //

        $loaderResolver = new LoaderResolver(array(new YamlSettingsLoader($locator)));
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $request = $event->getRequest();

        try {
            $delegatingLoader->load(GOTEO_PATH . '/config/settings.yml');
            // Init database
            Model::factory();
            // Init session
            Session::start('goteo-'.Config::get('env'), Config::get('session.time'));
        }
        catch(ConfigException $e) {
            $code = Response::HTTP_FORBIDDEN;
            // TODO: custom template
            $event->setResponse(new Response(View::render('errors/config', ['msg' => $e->getMessage(), 'code' => $code], $code)));
            return;
        }


        if(GOTEO_ENV !== 'real') {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
            ini_set("display_errors",1);
        }

        /*
         * Using the HTTP Foundation class
         * http://symfony.com/doc/current/book/http_fundamentals.html
         */

        //si el parametro GET vale:
        // 0 se muestra estadísticas de SQL, pero no los logs
        // 1 se hace un log con las queries no cacheadas
        // 2 se hace un log con las queries no cacheadas y también las cacheadas
        if ($request->query->has('sqldebug') && !defined('DEBUG_SQL_QUERIES')) {
            define('DEBUG_SQL_QUERIES', intval($request->query->get('sqldebug')));
        }

        // Quitar legacy
        if (!$request->query->has('no-legacy') && !defined('USE_LEGACY_DISPACHER')) {
            define('USE_LEGACY_DISPACHER', true);
        }

        // TODO: add node configuration
        /* Sistema nodos */
        // Get Node and check it
        $host = strtok($request->server->get('HTTP_HOST'), '.');

        if (NodeSys::isValid($host)) {
            define('NODE_ID', $host);

        } else {
            define('NODE_ID', GOTEO_NODE);
            // define('NODE_ID', 'barcelona');
        }
        //
        /* Fin inicializacion nodo */


        //clean all caches if requested
        if ($request->query->has('cleancache')) {
            Model::cleanCache();
        }

        /* Iniciación constantes *_URL */

        /**
         * Session.
         */
        Session::onSessionExpires(function () {
            Message::info(Text::get('session-expired'));
        });
        Session::onSessionDestroyed(function () {
            Message::info('That\'s all folks!');
        });

        // if ssl enabled
        $SSL = Config::get('ssl');
        $SITE_URL = Config::get('url.main');
        // segun sea nodo o central
        $raw_url = str_replace('http:', '', $SITE_URL);

        // SEC_URL (siempre https, si ssl activado)
        define('SEC_URL', ($SSL ? 'https:'.$raw_url : $SITE_URL));

        // si estamos en entorno seguro
        define('HTTPS_ON', ($request->server->get('HTTPS') === 'on' || $request->server->get('HTTP_X_FORWARDED_PROTO') === 'https' ));

        // SITE_URL, según si estamos en entorno seguro o si el usuario esta autenticado
        if ($SSL && (HTTPS_ON || Session::isLogged())) {
            define('SITE_URL', SEC_URL);
        } else {
            define('SITE_URL', $SITE_URL);
        }

        // si el usuario ya está validado debemos mantenerlo en entorno seguro
        // usamos la funcionalidad de salto entre nodos para mantener la sesión
        if ($SSL && Session::isLogged() && !HTTPS_ON) {
            $this->setResponse(new RedirectResponse(SEC_URL . $request->server->get('REQUEST_URI')));
            return;
        }
        /* Fin inicializacion constantes *_URL */

        /*
         * Pagina de en mantenimiento
         */
        if (Config::get('maintenance') && $request->server->get('REQUEST_URI') != '/about/maintenance'
             && !$request->request->has('Num_operacion')
            ) {
            $this->setResponse(new RedirectResponse('/about/maintenance'));
            return;
        }


        // set currency
        Session::store('currency', Currency::set()); // depending on request

        // Set lang
        Lang::setDefault(GOTEO_DEFAULT_LANG);
        Lang::setFromGlobals($request);

        /**********************************/
        // LEGACY VIEWS
        \Goteo\Core\View::addViewPath(GOTEO_WEB_PATH . 'view');
        //NormalForm views
        \Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/NormalForm/view');
        //SuperForm views
        \Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/SuperForm/view');
        //TODO: PROVISIONAL
        //add view
        \Goteo\Core\View::addViewPath(GOTEO_WEB_PATH . 'nodesys');
        /**********************************/


        //Compiled views by grunt
        View::addFolder(GOTEO_WEB_PATH . 'templates/grunt', 'compiled');

        //If node, Node templates first
        //Node/call theme
        if(Config::isNode()) {
            //Custom templates first (PROVISIONAL: should be configurable in settings)
            View::addFolder(GOTEO_PATH . 'extend/goteo/templates/node', 'node-goteo');
            //Nodes views
            View::addFolder(GOTEO_PATH . 'templates/node', 'node');
        }

        //Custom templates first (PROVISIONAL: should be configurable in settings)
        View::addFolder(GOTEO_PATH . 'extend/goteo/templates/default', 'goteo');

        //Default templates
        View::addFolder(GOTEO_PATH . 'templates/default', 'default');

        // print_r(View::getEngine());

        // views function registering
        View::getEngine()->loadExtension(new Extension\GoteoCore(), [], true);
        View::getEngine()->loadExtension(new Extension\TextUtils(), [], true);
        View::getEngine()->loadExtension(new Extension\Pages(), [], true);


        // Some defaults
        View::getEngine()->useData([
            'title' => Config::get('meta.title'),
            'meta_description' => Config::get('meta.description'),
            'meta_keywords' => Config::get('meta.keywords'),
            'meta_author' => Config::get('meta.author'),
            'meta_copyright' => Config::get('meta.copyright'),
            'URL' => SITE_URL,
            'SRC_URL' => SRC_URL,
            'image' => SRC_URL . '/goteo_logo.png'
            // 'og_title' => 'Goteo.org',
            // 'og_description' => GOTEO_META_DESCRIPTION,
            ]);
    }

    public function onResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        // Cookie
        // the stupid law cookie
        if (!Cookie::exists('goteo_cookies')) {
            Cookie::store('goteo_cookies', '1');
            Message::info(Text::get('message-cookies'));
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::RESPONSE => 'onResponse'
        );
    }
}

