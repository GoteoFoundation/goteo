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

use Goteo\Application\Config\ConfigException;
use Goteo\Application\Config\YamlSettingsLoader;
use Goteo\Console\UsersSend;
use Goteo\Core\Model;
use Goteo\Application\Currency;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Route;

class Config {
    // Initial translation groups (groupped in yml files into Resources/translations/)
    static public $trans_groups = ['home', 'roles', 'public_profile', 'project', 'labels', 'form', 'profile', 'personal', 'overview', 'costs', 'rewards', 'supports', 'preview', 'dashboard', 'register', 'login', 'discover', 'community', 'general', 'blog', 'faq', 'contact', 'widget', 'invest', 'matcher', 'types', 'banners', 'footer', 'social', 'review', 'translate', 'menu', 'feed', 'mailer', 'bluead', 'error', 'wof', 'node_public', 'contract', 'donor', 'text_groups', 'template', 'admin', 'translator', 'metas', 'location', 'url', 'pool', 'dates'];
	static protected $loader;
	static protected $config;

	/**
	 * Loads all configurations
	 */
	static public function load($config_file) {
		try {
            if(!is_file($config_file)) $config_file = __DIR__ . '/../../../config/' . $config_file;
			// load the main config
			self::$config = self::loadFromYaml($config_file);

            // Load default permissions from yaml
            $permissions = self::loadFromYaml(__DIR__ . '/../../../Resources/permissions.yml');
            Role::addPermsFromArray($permissions);
            // Load default roles from yaml
            $roles = self::loadFromYaml(__DIR__ . '/../../../Resources/roles.yml');
            Role::addRolesFromArray($roles);


			//Timezone
			if (self::get('timezone')) {
				date_default_timezone_set(self::get('timezone'));
			}
            // Default system_lang to 'es' if not defined
            if(!array_key_exists('sql_lang', self::$config)) {
                self::$config['sql_lang'] = 'es';
            }


			// handles legacy config values
			self::setConstants();

			// Init database
			Model::factory();

			// load the language configuration
			$locales = self::loadFromYaml(__DIR__ . '/../../../Resources/locales.yml');
			if (is_array($locales) && $locales) {
				Lang::setLangsAvailable($locales);
			}
            // load the currency configuration
            $currencies = self::loadFromYaml(__DIR__ . '/../../../Resources/currencies.yml');
            if (is_array($currencies) && $currencies) {
                Currency::setCurrenciesAvailable($currencies);
            }
            if (self::get('currency')) {
                Currency::setDefault(self::get('currency'));
            }

			// load translations
			foreach (Lang::listAll('name', false) as $lang => $name) {
				Lang::addSqlTranslation($lang);
				foreach (self::$trans_groups as $group) {
					Lang::addYamlTranslation($lang, __DIR__ . '/../../../Resources/translations/' . $lang . '/' . $group . '.yml');
				}
			}

            // Add model zones for the translator
            \Goteo\Controller\TranslateController::addTranslateModel('criteria');
            \Goteo\Controller\TranslateController::addTranslateModel('sphere');

			// sets up the rest...
			self::setDirConfiguration();

		} catch (\Exception $e) {
			if (PHP_SAPI === 'cli') {
				throw $e;
			}
			$code = \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN;
			\Goteo\Application\View::addFolder(__DIR__ . '/../../../Resources/templates/responsive');
			// TODO: custom template
			$info = '';
			$trace = EventListener\ExceptionListener::jTraceEx($e);
			if (App::debug()) {
				$info = '<pre>' . $trace . '</pre>';
			}

            \Goteo\Application\View::setTheme('responsive');
			die(\Goteo\Application\View::render('errors/config', ['msg' => $e->getMessage(), 'info' => $info, 'file' => $config_file, 'code' => $code], false));
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
		foreach (YamlSettingsLoader::$cached_files as $file) {
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
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\UsersAdminController');
		\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\StatsAdminController');
        // \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\UsersSubController');

        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\AccountsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NodeSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\NodesSubController');
        // \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\TransnodesSubController');
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
		// \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\WordcountSubController');
		\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\WorthSubController');
		\Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\MilestonesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\OpenTagsSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\StoriesSubController');
        \Goteo\Controller\AdminController::addSubController('Goteo\Controller\Admin\SocialCommitmentSubController');


		// Adding Pool (internal credit) payment method
		\Goteo\Payment\Payment::addMethod('Goteo\Payment\Method\PoolPaymentMethod');
		// Adding Paypal payment method
		\Goteo\Payment\Payment::addMethod('Goteo\Payment\Method\PaypalPaymentMethod');
		// Adding Cash non-public payment method (manual admin investions)
		\Goteo\Payment\Payment::addMethod('Goteo\Payment\Method\CashPaymentMethod', true);

		// Plugins overwritting
		foreach (self::getPlugins() as $plugin => $vars) {
			// Calling start file from plugins
			if (is_file(__DIR__ . "/../../../extend/$plugin/start.php")) {
				include __DIR__ . "/../../../extend/$plugin/start.php";
			}
		}
		// TODO: fire event plugins loaded

        // If calls_enabled is not defined, figure it out from the database
        if(self::get('calls_enabled') === null) {
            // self::set('calls_enabled', (\Goteo\Model\Call::dbCount() > 0));
            $e = \Goteo\Controller\AdminController::existsSubController('Goteo\Controller\Admin\CallsSubController');
            self::set('calls_enabled', $e);
        }

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

        // TODO: add a generic matcher processor that uses Symfony Expression Language
        // http://symfony.com/doc/current/components/expression_language/syntax.html
        //
        // App::getService('app.matcher.finder')->addProcessor('Goteo\Util\MatcherProcessor\ExpressionLanguageProcessor');
        App::getService('app.matcher.finder')->addProcessor('Goteo\Util\MatcherProcessor\DuplicateInvestMatcherProcessor');

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

		// Default consultants to UsersSend
		if (is_array(Config::get('mail.consultants'))) {
			UsersSend::setConsultants(Config::get('mail.consultants'));
		}
		UsersSend::setLogger(App::getService('logger'));

		// Default theme in templates/default
		View::setTheme('default');

		// Some defaults
		View::getEngine()->useData([
			'title' => self::get('meta.title'),
			'meta_description' => self::get('meta.description'),
			'meta_keywords' => self::get('meta.keywords'),
			'meta_author' => self::get('meta.author'),
			'meta_copyright' => self::get('meta.copyright'),
			'image' => self::get('url.assets') . '/goteo_logo.png',
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

		$driver = self::get('db.driver', true);
		$host = self::get('db.host', true);
		$port = self::get('db.port', true);
		$charset = self::get('db.charset', true);
		if ($charset == 'UTF-8') {
			$charset = 'UTF8';
		}

		$database = self::get('db.database', true);
		$username = self::get('db.username', true);
		$password = self::get('db.password', true);
		self::set('dsn', "$driver:host=$host;dbname=$database;port=$port;charset=$charset");

		if ($replica = self::get('db.replica.host')) {
			self::set('dsn_replica', "$driver:host=$replica;dbname=$database;port=" . (self::get('db.replica.port') ? self::get('db.replica.port') : $port) . ";charset=$charset");

		}

		define('SQL_CACHE_DRIVER', self::get('db.cache.driver'));

		define('SRC_URL', self::get('url.assets'));

		if (self::get('url.data')) {
			define('GOTEO_DATA_URL', self::get('url.data'));
		}

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

        // analytics sanitize
        // TODO: add others like facebook pixel or piwik
        if($google = self::get('analytics.google')) {
            if(!is_array($google)) $google = [$google];
        } else {
            $google = [];
        }
        self::set('analytics.google', $google);

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
		if (array_key_exists($part, self::$config)) {
			$ret = self::$config[$part];
			while ($part = strtok('.')) {
				if (is_array($ret) && array_key_exists($part, $ret)) {
					$ret = $ret[$part];
					// echo "[$part]";
				} elseif ($strict) {
					throw new ConfigException("Config var [$name] not found!");
				} else {
					$ret = null;
				}
			}
			return $ret;
		} elseif ($strict) {
			throw new ConfigException("Config var [$name] not found!");
		}
		return null;
	}

	static public function set($name, $value) {
		$config = self::_set(self::$config, $name, $value);
	}

	static private function _set(&$config, $name, $value) {
		$pos = strpos($name, '.');
		if ($pos === false) {
			return $config[$name] = $value;
		}
		return self::_set($config[substr($name, 0, $pos)], substr($name, $pos + 1), $value);
	}

	/**
	 * Returns a mail (mail.mail, mail.contact, mail.manager) with fallback if not defined
	 * See config/settings-example.yml (mail part) for values
	 */
	static public function getMail($type = 'mail', $fallback = 'mail') {
		if (self::get("mail.$type")) {
			return self::get("mail.$type");
		}

		if (self::get("mail.$fallback")) {
			return self::get("mail.$fallback");
		}
		// throw a exception?
		throw new ConfigException("Config var mail.mail not found!");
	}

	/**
	 * Gets a suitable http(s) link for use
	 * @param  string $lang ca
	 * @return [type]       [description]
	 */
	static public function getUrl($lang = null) {
		$url = self::get('url.main');
		if (self::get('url.url_lang')) {
			if (is_null($lang)) {
				$lang = Lang::current();
			}
            if($lang == self::get('lang')) {
                $lang =  'www';
            }

			$url = "//$lang." . self::get('url.url_lang');
		}
		if (strpos($url, '//') === 0) {
			$url = 'http:' . $url;
		}
		if (self::get('ssl')) {
			$url = str_ireplace('http://', 'https://', $url);
		}
		return $url;
	}

    /**
     * Get sanitized Main URL
     * @return [type] [description]
     */
    static public function getMainUrl() {
        $url = self::get('url.main');
        if(strpos($url, '//') === 0) {
            $url = (self::get('ssl') ? 'https:' : 'http:') . $url;
        }
        return $url;
    }

	static public function getPlugins() {
		$all_plugins = self::get('plugins');
		if (!is_array($all_plugins)) {
			$all_plugins = [];
		}

		$plugins = [];
		foreach ($all_plugins as $plugin => $vars) {
			if ($vars['active']) {
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
		if ($node) {
			return self::get('node') === $node;
		}

		return self::isCurrentNode(self::get('node'));
	}
}
