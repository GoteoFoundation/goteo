<?php

namespace Goteo\Application;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

use Goteo\Application\Config\YamlSettingsLoader;
use Goteo\Application\Config\ConfigException;
use Symfony\Component\Config\FileLocator;

use Goteo\Core\Model;
use Goteo\Application\View;

class Config {
    static protected $loader;
    static protected $routes;
    static protected $config;

    static public function factory(array $config) {
        self::$config = $config;
        self::setConstants();
        self::setDirConfiguration();
        // Init database
        Model::factory();

    }

    static public function loadFromYaml($file) {
        //
        //LOAD CONFIG
        //

        $configDirectories = array(__DIR__ . '/../../../config');

        $locator = new FileLocator($configDirectories);

        $loaderResolver = new LoaderResolver(array(new YamlSettingsLoader($locator)));
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        try {
            $config = $delegatingLoader->load(__DIR__ . '/../../../config/' . $file);
            // ... handle the config values
            self::factory($config);

        }
        catch(ConfigException $e) {
            $code = \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN;
            \Goteo\Application\View::addFolder(__DIR__ . '/../../../templates/default');
            // TODO: custom template
            die(\Goteo\Application\View::render('errors/config', ['msg' => $e->getMessage(), 'code' => $code], $code));
            return;
        }
    }


    static public function setLoader(\Composer\Autoload\ClassLoader $loader) {
        self::$loader = $loader;
    }

    static public function addAutoloadDir($dir) {
        self::$loader->add('', $dir);
    }

    static public function getRoutes() {
        if( ! self::$routes ) {
            self::$routes = include( __DIR__ . '/../../app.php' );
        }

        return self::$routes;
    }


    static public function setDirConfiguration() {
        $extend = self::get('extend.autoload');
        if(is_array($extend)) {
            foreach($extend as $plugin) {
                //Autoload classes
                self::addAutoloadDir(__DIR__ . '/../../../extend/goteo/src');
            }
        }
        // Route app
        if(is_file(__DIR__ . '/../../../extend/' .  self::get('extend.routes'))) {
            self::$routes = include(__DIR__ . '/../../../extend/' . self::get('extend.routes'));
        }

        //Cache dir in libs
        \Goteo\Library\Cacher::setCacheDir(GOTEO_CACHE_PATH);

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
        View::addFolder(__DIR__ . '/../../../templates/grunt', 'compiled');

        // //If node, Node templates first
        // //Node/call theme
        // if(self::isNode()) {
        //     //Custom templates first (PROVISIONAL: should be configurable in settings)
        //     View::addFolder(GOTEO_PATH . 'extend/goteo/templates/node', 'node-goteo');
        //     //Nodes views
        //     View::addFolder(GOTEO_PATH . 'templates/node', 'node');
        // }
        $extend = self::get('extend.templates');
        if(is_array($extend)) {
            foreach($extend as $path) {
                //Custom templates first
                View::addFolder(__DIR__ . '/../../../extend/' . $path, str_replace('/', '-', $path));
            }
        }
        //Default templates
        View::addFolder(__DIR__ . '/../../../templates/default', 'default');


        // print_r(View::getEngine());

        // views function registering
        View::getEngine()->loadExtension(new \Goteo\Foil\Extension\GoteoCore(), [], true);
        View::getEngine()->loadExtension(new \Goteo\Foil\Extension\TextUtils(), [], true);
        View::getEngine()->loadExtension(new \Goteo\Foil\Extension\Pages(), [], true);


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

        define('GOTEO_MAIL_FROM', self::get('mail.transport.from'));
        define('GOTEO_MAIL_NAME', self::get('mail.transport.name'));
        define('GOTEO_MAIL_TYPE', self::get('mail.transport.type'));
        define('GOTEO_MAIL_SMTP_AUTH', self::get('mail.transport.smtp.auth'));
        define('GOTEO_MAIL_SMTP_SECURE', self::get('mail.transport.smtp.secure'));
        define('GOTEO_MAIL_SMTP_HOST', self::get('mail.transport.smtp.host'));
        define('GOTEO_MAIL_SMTP_PORT', self::get('mail.transport.smtp.port'));
        define('GOTEO_MAIL_SMTP_USERNAME', self::get('mail.transport.smtp.username'));
        define('GOTEO_MAIL_SMTP_PASSWORD', self::get('mail.transport.smtp.password'));
        define('GOTEO_MAIL', self::get('mail.mail'));
        define('GOTEO_CONTACT_MAIL', self::get('mail.contact'));
        define('GOTEO_MANAGER_MAIL', self::get('mail.manager'));
        define('GOTEO_FAIL_MAIL', self::get('mail.fail'));
        define('GOTEO_LOG_MAIL', self::get('mail.log'));
        define('GOTEO_MAIL_QUOTA', self::get('mail.quota.total'));
        define('GOTEO_MAIL_SENDER_QUOTA', self::get('mail.quota.sender'));
        define('AWS_SNS_CLIENT_ID', self::get('mail.sns.client_id'));
        define('AWS_SNS_REGION', self::get('mail.sns.region'));
        define('AWS_SNS_BOUNCES_TOPIC', self::get('mail.sns.bounces_topic'));
        define('AWS_SNS_COMPLAINTS_TOPIC', self::get('mail.sns.complaints_topic'));
        define('GOTEO_DEFAULT_LANG', self::get('lang'));

        define('GOTEO_URL', self::get('url.main'));
        define('SRC_URL', self::get('url.assets'));

        if(self::get('url.data')) define('GOTEO_DATA_URL', self::get('url.data'));

        define('GOTEO_SSL', self::get('ssl'));
        define('FILE_HANDLER', self::get('filesystem.handler'));
        define('AWS_S3_BUCKET_STATIC', self::get('filesystem.bucket.static'));
        define('AWS_S3_BUCKET_MAIL', self::get('filesystem.bucket.mail'));
        define('AWS_S3_BUCKET_DOCUMENT', self::get('filesystem.bucket.document'));
        define('AWS_S3_BUCKET_PRESS', self::get('filesystem.bucket.press'));
        define('CRON_PARAM', self::get('cron.param'));
        define('CRON_VALUE', self::get('cron.value'));
        define('PP_CONFIG_PATH', GOTEO_PATH . 'config/');
        define('PAYPAL_REDIRECT_URL', self::get('paypal.redirect_url'));
        define('TPV_MERCHANT_CODE', self::get('tpv.merchant_code'));
        define('TPV_REDIRECT_URL', self::get('tpv.redirect_url'));
        define('TPV_ENCRYPT_KEY', self::get('tpv.encrypt_key'));
        define('OAUTH_FACEBOOK_ID', self::get('oauth.facebook.id'));
        define('OAUTH_FACEBOOK_SECRET', self::get('oauth.facebook.secret'));
        define('OAUTH_TWITTER_ID', self::get('oauth.twitter.id'));
        define('OAUTH_TWITTER_SECRET', self::get('oauth.twitter.secret'));
        define('OAUTH_LINKEDIN_ID', self::get('oauth.linkedin.id'));
        define('OAUTH_LINKEDIN_SECRET', self::get('oauth.linkedin.secret'));
        define('RECAPTCHA_PUBLIC_KEY', self::get('recaptcha.public'));
        define('RECAPTCHA_PRIVATE_KEY', self::get('recaptcha.private'));

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
                    throw new Config\ConfigException("Config var [$name] not found!", 1);
                }
                else {
                    $ret = null;
                }
            }
            return $ret;
        }
        elseif($strict) {
            throw new Config\ConfigException("Config var [$name] not found!", 1);
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
     * If a node is active
     * @param  [type]  $node [description]
     * @return boolean       [description]
     */
    static public function isCurrentNode($node) {
        return self::get('current_node') === $node;
    }

    /**
     * If is not the main node
     * @return boolean [description]
     */
    static public function isNode() {
        return !self::isCurrentNode(self::get('node'));
    }

}
