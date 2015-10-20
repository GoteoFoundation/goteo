<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

use Goteo\Application\Config\YamlSettingsLoader;
use Goteo\Application\Config\ConfigException;
use Symfony\Component\Config\FileLocator;

use Goteo\Core\Model;

class Config {
    static protected $loader;
    static protected $config;

    /**
     * Loads all configurations
     */
    static public function load($config_file = 'settings.yml') {
        try {
            // load the main config
            self::$config = self::loadFromYaml(__DIR__ . '/../../../config/' . $config_file);
            //Timezone
            if(self::get('timezone')) date_default_timezone_set(self::get('timezone'));
            // handles legacy config values
            self::setConstants();
            // Init database
            Model::factory();
            // load the language configuration
            $locales = self::loadFromYaml(__DIR__ . '/../../../Resources/locales.yml');
            if(is_array($locales) && $locales) {
                Lang::setLangsAvailable($locales);
            }
            // load translations
            // Initial groups
            $groups = ['home', 'public_profile', 'project' , 'form'    , 'profile' , 'personal', 'overview', 'costs'   , 'rewards' , 'supports', 'preview' , 'dashboard', 'register', 'login'   , 'discover' , 'community' , 'general' , 'blog' , 'faq' , 'contact' , 'widget' , 'invest' , 'types', 'banners', 'footer', 'social', 'review', 'translate', 'menu', 'feed', 'mailer', 'bluead', 'error', 'wof', 'node_public', 'contract', 'donor', 'text_groups' ];
            foreach(Lang::listAll('name', false) as $lang => $name) {
                Lang::addSqlTranslation($lang);
                foreach($groups as $group) {
                    Lang::addYamlTranslation($lang, __DIR__ . '/../../../Resources/translations/' . $lang . '/' . $group . '.yml');
                }
            }
            // sets up the rest...
            self::setDirConfiguration();
        }
        catch(\Exception $e) {
            $code = \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN;
            \Goteo\Application\View::addFolder(__DIR__ . '/../../../Resources/templates/default');
            // TODO: custom template
            die(\Goteo\Application\View::render('errors/config', ['msg' => $e->getMessage(), 'file' => $file, 'code' => $code], $code));
            return;
        }
    }

    /**
     * Loads a configuration from a file
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    static public function loadFromYaml($file) {
        //
        //LOAD CONFIG
        //

        $locator = new FileLocator(array(dirname($file)));

        $loaderResolver = new LoaderResolver(array(new YamlSettingsLoader($locator)));
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        return $delegatingLoader->load($file);
    }

    /**
     * Purgues all cached setting files
     */
    static public function clearCache() {
        foreach(YamlSettingsLoader::$cached_files as $file) {
            unlink($file);
        }
    }

    /**
     * Registers a Autoload ClassLoader for composer
     * @param \Composer\Autoload\ClassLoader $loader the include(...) of composer
     */
    static public function setLoader(\Composer\Autoload\ClassLoader $loader) {
        self::$loader = $loader;
    }

    /**
     * Adds a directory to the composer autoload array
     * @param string $dir directory where to find classes
     */
    static public function addAutoloadDir($dir) {
        self::$loader->add('', $dir);
    }

    /**
     * sets directory configuration
     */
    static private function setDirConfiguration() {

        //Admin subcontrollers added manually for legacy compatibility
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\AccountsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NodeSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NodesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TransnodesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\BannersSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\BlogSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CategoriesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CommonsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\CriteriaSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\FaqSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\HomeSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\GlossarySubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\IconsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\LicensesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\MailingSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NewsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NewsletterSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\PagesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\ProjectsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\PromoteSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\RecentSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\ReviewsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\RewardsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\SentSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\SponsorsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TagsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TemplatesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TextsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TranslatesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\UsersSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\WordcountSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\WorthSubController');

        // Adding Pool (internal credit) payment method
        \Goteo\Payment\Payment::addMethod('Goteo\Payment\Method\PoolPaymentMethod');
        // Adding Paypal payment method
        \Goteo\Payment\Payment::addMethod('Goteo\Payment\Method\PaypalPaymentMethod');

        // Plugins overwritting
        foreach(self::getPlugins() as $plugin => $vars) {
            // Calling start file from plugins
            if(is_file(__DIR__ . "/../../../extend/$plugin/start.php")) {
                include(__DIR__ . "/../../../extend/$plugin/start.php");
            }
        }
        // TODO: fire event plugins loaded

        // A catch-all Legacy routes controller (LEGACY DISPATCHER)
        App::getRoutes()->add('legacy-dispacher', new Route(
                '/{url}',
                array(
                    '_controller' => 'Goteo\Controller\ErrorController::legacyControllerAction',
                ),
                array(
                    'url' => '.*',
                )
        ));

        // Set routes into service container
        App::getServiceContainer()->setParameter('routes', App::getRoutes());

        //Cache dir in libs
        \Goteo\Library\Cacher::setCacheDir(GOTEO_CACHE_PATH);

        /**********************************/
        // LEGACY VIEWS
        \Goteo\Core\View::addViewPath(GOTEO_PATH . 'Resources/templates/legacy');

        //NormalForm views
        \Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/NormalForm/view');
        //SuperForm views
        \Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/SuperForm/view');
        /**********************************/

        // Default theme in templates/default
        View::setTheme('default');

        // views function registering
        View::getEngine()->loadExtension(new \Goteo\Util\Foil\Extension\GoteoCore(), [], true);
        View::getEngine()->loadExtension(new \Goteo\Util\Foil\Extension\TextUtils(), [], true);
        View::getEngine()->loadExtension(new \Goteo\Util\Foil\Extension\Pages(), [], true);
        View::getEngine()->loadExtension(new \Goteo\Util\Foil\Extension\LangUtils(), [], true);


        // Some defaults
        View::getEngine()->useData([
            'title' => self::get('meta.title'),
            'meta_description' => self::get('meta.description'),
            'meta_keywords' => self::get('meta.keywords'),
            'meta_author' => self::get('meta.author'),
            'meta_copyright' => self::get('meta.copyright'),
            'image' => self::get('url.assets') . '/goteo_logo.png'
            ]);

        // TODO: fire event here
    }

    /**
     * Compatibility constants
     */
    static public function setConstants() {
        // foreach(self::$config as $name => $value) {
        //     echo "$name => " . print_r($value, 1)."\n";
        // };die;
        define('GOTEO_MAINTENANCE', self::get('maintenance'));
        define('GOTEO_SESSION_TIME', self::get('session.time', true));
        define('GOTEO_MISC_SECRET', self::get('secret', true));
        define('GOTEO_ENV', self::get('env', true));
        define('GOTEO_NODE', self::get('node', true));
        self::set('current_node', self::get('node', true));
        define('GOTEO_FEE', self::get('fee', true));

        define('GOTEO_META_TITLE', self::get('meta.title', true));
        define('GOTEO_META_DESCRIPTION', self::get('meta.description', true));
        define('GOTEO_META_KEYWORDS', self::get('meta.keywords', true));
        define('GOTEO_META_AUTHOR', self::get('meta.author', true));
        define('GOTEO_META_COPYRIGHT', self::get('meta.copyright', true));

        define('AWS_KEY', self::get('filesystem.aws.key'));
        define('AWS_SECRET', self::get('filesystem.aws.secret'));
        define('AWS_REGION', self::get('filesystem.aws.region'));

        define('GOTEO_DB_DRIVER', self::get('db.driver', true));
        define('GOTEO_DB_HOST', self::get('db.host', true));
        define('GOTEO_DB_PORT', self::get('db.port', true));
        define('GOTEO_DB_CHARSET', self::get('db.charset', true));
        define('GOTEO_DB_SCHEMA', self::get('db.database', true));
        define('GOTEO_DB_USERNAME', self::get('db.username', true));
        define('GOTEO_DB_PASSWORD', self::get('db.password', true));

        if(self::get('db.replica.host'))     define('GOTEO_DB_READ_REPLICA_HOST', self::get('db.replica.host'));
        if(('db.replica.port'))              define('GOTEO_DB_READ_REPLICA_PORT', self::get('db.replica.port'));
        if(self::get('db.replica.username')) define('GOTEO_DB_READ_REPLICA_USERNAME', self::get('db.replica.username'));
        if(self::get('db.replica.password')) define('GOTEO_DB_READ_REPLICA_PASSWORD', self::get('db.replica.password'));

        define('SQL_CACHE_DRIVER', self::get('db.cache.driver'));
        define('SQL_CACHE_TIME', self::get('db.cache.time'));
        define('SQL_CACHE_LONG_TIME', self::get('db.cache.long_time'));

        define('SRC_URL', self::get('url.assets'));

        if(self::get('url.data')) define('GOTEO_DATA_URL', self::get('url.data'));

        define('GOTEO_SSL', self::get('ssl'));
        define('FILE_HANDLER', self::get('filesystem.handler'));
        define('AWS_S3_BUCKET_STATIC', self::get('filesystem.bucket.static'));
        define('AWS_S3_BUCKET_MAIL', self::get('filesystem.bucket.mail'));
        define('AWS_S3_BUCKET_DOCUMENT', self::get('filesystem.bucket.document'));
        define('AWS_S3_BUCKET_PRESS', self::get('filesystem.bucket.press'));
        define('OAUTH_FACEBOOK_ID', self::get('oauth.facebook.id'));
        define('OAUTH_FACEBOOK_SECRET', self::get('oauth.facebook.secret'));
        define('OAUTH_GOOGLE_ID', self::get('oauth.google.id'));
        define('OAUTH_GOOGLE_SECRET', self::get('oauth.google.secret'));
        define('OAUTH_TWITTER_ID', self::get('oauth.twitter.id'));
        define('OAUTH_TWITTER_SECRET', self::get('oauth.twitter.secret'));
        define('OAUTH_LINKEDIN_ID', self::get('oauth.linkedin.id'));
        define('OAUTH_LINKEDIN_SECRET', self::get('oauth.linkedin.secret'));

    }

    /**
     * Return a value
     * @param  string $name ex: filesystem.handler
     *                          filesystem.bucket      => array
     *                          filesystem.bucket.mail => string
     * @param  string $strick throws a Exception on fail
     * @return [type]       [description]
     */
    static public function get($name, $strict = false) {
        $part = strtok($name, '.');
        if(array_key_exists($part, self::$config)) {
            $ret = self::$config[$part];
            while($part = strtok('.')) {
                if(is_array($ret) && array_key_exists($part, $ret)) {
                    $ret = $ret[$part];
                    // echo "[$part]";
                }
                elseif($strict) {
                    throw new ConfigException("Config var [$name] not found!");
                }
                else {
                    $ret = null;
                }
            }
            return $ret;
        }
        elseif($strict) {
            throw new ConfigException("Config var [$name] not found!");
        }
        return null;
    }

    static public function set($name, $value) {
        $config = self::_set(self::$config, $name, $value);
    }

    static private function _set(&$config, $name, $value) {
        $pos = strpos($name, '.');
        if($pos === false) {
            return $config[$name] = $value;
        }
        return self::_set($config[substr($name, 0, $pos)], substr($name, $pos + 1), $value);
    }

    /**
     * Returns a mail (mail.mail, mail.contact, mail.manager) with fallback if not defined
     * See config/settings-example.yml (mail part) for values
     */
    static public function getMail($type = 'mail', $fallback = 'mail') {
        if(self::get("mail.$type")) {
            return self::get("mail.$type");
        }

        if(self::get("mail.$fallback")) {
            return self::get("mail.$fallback");
        }
        // throw a exception?
        throw new ConfigException("Config var mail.mail not found!");
    }

    static public function getPlugins() {
        $all_plugins = self::get('plugins');
        if(!is_array($all_plugins)) $all_plugins = [];
        $plugins = [];
        foreach($all_plugins as $plugin => $vars) {
            if($vars['active']) {
                $plugins[$plugin] = $vars;
            }
        }
        return $plugins;
    }

    /**
     * If a node is active
     * @param  [type]  $node [description]
     * @return boolean       [description]
     */
    static public function isCurrentNode($node) {
        return self::get('current_node') === $node;
    }

    /**
     * If is the main node
     * @return boolean [description]
     */
    static public function isMasterNode($node = null) {
        if($node) return self::get('node') === $node;
        return self::isCurrentNode(self::get('node'));
    }


}
