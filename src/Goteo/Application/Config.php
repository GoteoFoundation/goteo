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

use Composer\Autoload\ClassLoader;
use Exception;
use Goteo\Application\Config\ConfigException;
use Goteo\Application\Config\YamlSettingsLoader;
use Goteo\Console\UsersSend;
use Goteo\Controller\Admin\AccountsSubController;
use Goteo\Controller\Admin\AnnouncementAdminController;
use Goteo\Controller\Admin\BannersSubController;
use Goteo\Controller\Admin\BlogAdminController;
use Goteo\Controller\Admin\CategoriesAdminController;
use Goteo\Controller\Admin\ChannelProjectsAdminController;
use Goteo\Controller\Admin\ChannelCriteriaAdminController;
use Goteo\Controller\Admin\ChannelPostsAdminController;
use Goteo\Controller\Admin\ChannelProgramAdminController;
use Goteo\Controller\Admin\ChannelResourceAdminController;
use Goteo\Controller\Admin\ChannelSectionAdminController;
use Goteo\Controller\Admin\ChannelStoryAdminController;
use Goteo\Controller\Admin\CommonsSubController;
use Goteo\Controller\Admin\CommunicationAdminController;
use Goteo\Controller\Admin\CriteriaSubController;
use Goteo\Controller\Admin\FaqSubController;
use Goteo\Controller\Admin\FilterAdminController;
use Goteo\Controller\Admin\GlossarySubController;
use Goteo\Controller\Admin\HomeSubController;
use Goteo\Controller\Admin\IconsSubController;
use Goteo\Controller\Admin\ImpactDataAdminController;
use Goteo\Controller\Admin\LicensesSubController;
use Goteo\Controller\Admin\MailingSubController;
use Goteo\Controller\Admin\MilestonesSubController;
use Goteo\Controller\Admin\NewsletterSubController;
use Goteo\Controller\Admin\NewsSubController;
use Goteo\Controller\Admin\NodesSubController;
use Goteo\Controller\Admin\NodeSubController;
use Goteo\Controller\Admin\OpenTagsSubController;
use Goteo\Controller\Admin\PagesSubController;
use Goteo\Controller\Admin\ProjectsSubController;
use Goteo\Controller\Admin\PromoteAdminController;
use Goteo\Controller\Admin\RecentSubController;
use Goteo\Controller\Admin\ReviewsSubController;
use Goteo\Controller\Admin\RewardsSubController;
use Goteo\Controller\Admin\SentSubController;
use Goteo\Controller\Admin\SponsorsSubController;
use Goteo\Controller\Admin\StatsAdminController;
use Goteo\Controller\Admin\StoriesAdminController;
use Goteo\Controller\Admin\SubscriptionsAdminController;
use Goteo\Controller\Admin\TagsSubController;
use Goteo\Controller\Admin\TemplatesSubController;
use Goteo\Controller\Admin\TextsSubController;
use Goteo\Controller\Admin\TranslatesSubController;
use Goteo\Controller\Admin\UsersAdminController;
use Goteo\Controller\Admin\WorkshopAdminController;
use Goteo\Controller\Admin\WorthSubController;
use Goteo\Controller\AdminController;
use Goteo\Controller\TranslateController;
use Goteo\Core\Model;
use Goteo\Library\Cacher;
use Goteo\Payment\Payment;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Route;

class Config {
    static public $trans_groups = [
        'home', 'roles', 'public_profile', 'project', 'labels', 'form', 'profile', 'personal', 'overview', 'costs',
        'rewards', 'subscriptions', 'supports', 'preview', 'dashboard', 'register', 'login', 'discover', 'community', 'general', 'blog',
        'faq', 'contact', 'widget', 'invest', 'matcher', 'types', 'banners', 'footer', 'social', 'review', 'translate',
        'menu', 'feed', 'mailer', 'bluead', 'error', 'wof', 'node_public', 'contract', 'donor', 'text_groups',
        'template', 'admin', 'translator', 'metas', 'location', 'url', 'pool', 'dates', 'stories', 'workshop', 'donate',
        'questionnaire', 'poster', 'channel_call', 'map', 'data_sets'
    ];

    const ENV_PARAMETER_REG_EX = "/^%env\((.*)\)%$/";

	static protected $loader;
    static protected $config;

    static protected $f_defaults = __DIR__ . '/../../../Resources/defaults.yml';
    static protected $f_permissions = __DIR__ . '/../../../Resources/permissions.yml';
    static protected $f_roles = __DIR__ . '/../../../Resources/roles.yml';
	static protected $f_locales = __DIR__ . '/../../../Resources/locales.yml';
    static protected $f_currencies = __DIR__ . '/../../../Resources/currencies.yml';

    /**
     * Loads all configurations
     * @throws ConfigException
     */
	static public function load($config_file) {
		try {
            self::$config = self::loadFromYaml(static::$f_defaults);

            if(!is_file($config_file)) $config_file = __DIR__ . '/../../../config/' . $config_file;
			// load the main config
			if($config = self::loadFromYaml($config_file)) {
                self::$config = array_replace_recursive(self::$config , $config);
            }

            // Error traces
            if(self::get('debug')) {
                ini_set('display_errors', 1);
                App::debug(true);
            }
			//Timezone
			if (self::get('timezone')) {
				date_default_timezone_set(self::get('timezone'));
			}
            // Default system_lang to 'es' if not defined
            if(!array_key_exists('sql_lang', self::$config)) {
                self::set('sql_lang', 'es');
            }
            // assets url to main if not defined
            if(!self::get('url.assets')) self::set('url.assets', self::get('url.main'));

			// handles legacy config values
			self::setConstants();

			// Init database
			Model::factory();

            // Load default permissions from yaml
            $permissions = self::loadFromYaml(static::$f_permissions);
            Role::addPermsFromArray($permissions);

            // Load default roles from yaml
            $roles = self::loadFromYaml(static::$f_roles);
            Role::addRolesFromArray($roles);

			// load the language configuration
			$locales = self::loadFromYaml(static::$f_locales);
			if (is_array($locales) && $locales) {
				Lang::setLangsAvailable($locales);
			}
            // load the currency configuration
            $currencies = self::loadFromYaml(static::$f_currencies);
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
					Lang::addYamlTranslation($lang, __DIR__ . '/../../../translations/' . $lang . '/' . $group . '.yml');
				}
			}

            // Add model zones for the translator
            TranslateController::addTranslateModel('criteria');
            TranslateController::addTranslateModel('sphere');
            TranslateController::addTranslateModel('communication');
            TranslateController::addTranslateModel('call_to_action');
            TranslateController::addTranslateModel('node');
            TranslateController::addTranslateModel('node_program');
            TranslateController::addTranslateModel('node_faq');
            TranslateController::addTranslateModel('node_faq_question');
            TranslateController::addTranslateModel('node_faq_download');
            TranslateController::addTranslateModel('node_sponsor');
            TranslateController::addTranslateModel('node_team');
            TranslateController::addTranslateModel('node_resource');
            TranslateController::addTranslateModel('node_resource_category');
            TranslateController::addTranslateModel('image_credits');
            TranslateController::addTranslateModel('node_sections');
            TranslateController::addTranslateModel('question');
            TranslateController::addTranslateModel('question_options');
			TranslateController::addTranslateModel('impact_data');
            TranslateController::addTranslateModel('impact_item');

			// sets up the rest...
			self::setDirConfiguration();
		} catch (Exception $e) {
			if (PHP_SAPI === 'cli') {
				throw $e;
			}
			View::addFolder(__DIR__ . '/../../../Resources/templates/responsive');
            View::setTheme('responsive');

			return;
		}
	}

    /**
     * Performs some saving operations to database if required
     */
    static public function autosave(): bool
    {
        if(!Config::get('autosave')) return false;

        $not_cached = !YamlSettingsLoader::getConfigCache(YamlSettingsLoader::getCacheFilename(static::$f_roles))->isFresh();
        if($not_cached) {
            Role::saveRoles();
        }
        return true;
    }

	static public function loadFromYaml($file) {
		$locator = new FileLocator(array(dirname($file)));
		$loaderResolver = new LoaderResolver(array(new YamlSettingsLoader($locator)));
		$delegatingLoader = new DelegatingLoader($loaderResolver);

		return $delegatingLoader->load($file);
	}

	static public function clearCache() {
		foreach (YamlSettingsLoader::$cached_files as $file) {
			unlink($file);
		}
	}

	/**
	 * Registers a Autoload ClassLoader for composer
	 * @param ClassLoader $loader the include(...) of composer
	 */
	static public function setLoader(ClassLoader $loader) {
		self::$loader = $loader;
	}

    static public function getLoader() {
        return self::$loader;
    }

	/**
	 * Adds a directory to the composer autoload array
	 */
	static public function addAutoloadDir(string $dir) {
		self::$loader->add('', $dir);
	}

    /**
     * Adds an external autoload.php file (ie from a composer vendor plugin)
     */
    static public function addComposerAutoload($autoload) {
        $loader = require ( $autoload );
        self::$loader->addClassMap($loader->getClassMap());
    }

	static private function setDirConfiguration() {
        self::addAdminControllers();
        self::addLegacyAdminControllers();

		// Adding Pool (internal credit) payment method
		Payment::addMethod('Goteo\Payment\Method\PoolPaymentMethod');
		Payment::addMethod('Goteo\Payment\Method\PaypalPaymentMethod');
		// Adding Cash non-public payment method (manual admin investions)
		Payment::addMethod('Goteo\Payment\Method\CashPaymentMethod', true);
		// Adding Stripe (subscriptions) payment method
		Payment::addMethod('Goteo\Payment\Method\StripeSubscriptionPaymentMethod', true);

		// Plugins overwriting
		foreach (self::getPlugins() as $plugin => $vars) {
			// Calling start file from plugins
			if (is_file(__DIR__ . "/../../../extend/$plugin/start.php")) {
				include __DIR__ . "/../../../extend/$plugin/start.php";
			}
		}
		// TODO: fire event plugins loaded

        // If calls_enabled is not defined, figure it out from the database
        if(self::get('calls_enabled') === null) {
            $e = AdminController::existsSubController('Goteo\Controller\Admin\CallsSubController');
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
        App::getService('app.matcher.finder')->addProcessor('Goteo\Util\MatcherProcessor\DuplicateInvestMatcherProcessor');
        App::getService('app.matcher.finder')->addProcessor('Goteo\Util\MatcherProcessor\CriteriaInvestMatcherProcessor');

		//Cache dir in libs
		Cacher::setCacheDir(GOTEO_CACHE_PATH);

		// LEGACY VIEWS
		// One day, this will be removed, and happiness will spread along the galaxy
		\Goteo\Core\View::addViewPath(GOTEO_PATH . 'Resources/templates/legacy');
		//NormalForm views
		\Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/NormalForm/view');
		//SuperForm views
		\Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/SuperForm/view');

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

    static public function addAdminControllers()
    {
        AdminController::addSubController(BlogAdminController::class);
        AdminController::addSubController(CategoriesAdminController::class);
        AdminController::addSubController(CommunicationAdminController::class);
        AdminController::addSubController(FilterAdminController::class);
        AdminController::addSubController(PromoteAdminController::class);
        AdminController::addSubController(StatsAdminController::class);
        AdminController::addSubController(StoriesAdminController::class);
        AdminController::addSubController(UsersAdminController::class);
        AdminController::addSubController(WorkshopAdminController::class);
        AdminController::addSubController(ChannelCriteriaAdminController::class);
        AdminController::addSubController(ChannelResourceAdminController::class);
        AdminController::addSubController(ChannelProgramAdminController::class);
        AdminController::addSubController(ChannelPostsAdminController::class);
        AdminController::addSubController(ChannelSectionAdminController::class);
        AdminController::addSubController(ChannelStoryAdminController::class);
        AdminController::addSubController(ChannelProjectsAdminController::class);
        AdminController::addSubController(ImpactDataAdminController::class);
		AdminController::addSubController(SubscriptionsAdminController::class);
		AdminController::addSubController(AnnouncementAdminController::class);
    }

    static public function addLegacyAdminControllers()
    {
        AdminController::addSubController(AccountsSubController::class);
        AdminController::addSubController(NodeSubController::class);
        AdminController::addSubController(NodesSubController::class);
        AdminController::addSubController(BannersSubController::class);
        AdminController::addSubController(CommonsSubController::class);
        AdminController::addSubController(CriteriaSubController::class);
        AdminController::addSubController(FaqSubController::class);
        AdminController::addSubController(HomeSubController::class);
        AdminController::addSubController(GlossarySubController::class);
        AdminController::addSubController(IconsSubController::class);
        AdminController::addSubController(LicensesSubController::class);
        AdminController::addSubController(MailingSubController::class);
        AdminController::addSubController(NewsSubController::class);
        AdminController::addSubController(NewsletterSubController::class);
        AdminController::addSubController(PagesSubController::class);
        AdminController::addSubController(ProjectsSubController::class);
        AdminController::addSubController(RecentSubController::class);
        AdminController::addSubController(ReviewsSubController::class);
        AdminController::addSubController(RewardsSubController::class);
        AdminController::addSubController(SentSubController::class);
        AdminController::addSubController(SponsorsSubController::class);
        AdminController::addSubController(TagsSubController::class);
        AdminController::addSubController(TemplatesSubController::class);
        AdminController::addSubController(TextsSubController::class);
        AdminController::addSubController(TranslatesSubController::class);
        AdminController::addSubController(WorthSubController::class);
        AdminController::addSubController(MilestonesSubController::class);
        AdminController::addSubController(OpenTagsSubController::class);
    }

	static public function setConstants() {
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
     * @param string $name ex: filesystem.handler
     *                          filesystem.bucket      => array
     *                          filesystem.bucket.mail => string
     * @param bool $strict
     * @return array|false|mixed|string|null
     * @throws ConfigException
     */
	static public function get(string $name, bool $strict = false) {
        $part = strtok($name, '.');
        if (self::$config && array_key_exists($part, self::$config)) {
            $paramValue = self::$config[$part];
            while ($part = strtok('.')) {
                if (is_array($paramValue) && array_key_exists($part, $paramValue)) {
                    $paramValue = $paramValue[$part];
                } elseif ($strict) {
                    throw new ConfigException("Config var [$name] not found!");
                } else {
                    $paramValue = null;
                }
            }

            return self::replaceEnvVars($paramValue);
        } elseif ($strict) {
            throw new ConfigException("Config var [$name] not found!");
        }

        return null;
	}

	static protected function replaceEnvVars($paramValue) {
		 if(is_array($paramValue)) {
		    foreach($paramValue as $key => $val) {
		        $paramValue[$key] = self::replaceEnvVars($val);
		    }
		} elseif (preg_match(self::ENV_PARAMETER_REG_EX, $paramValue, $matches)) {
		    if (sizeof($matches) >= 1) {
		        $paramValue = getenv($matches[1]);
		    }
		}
		return $paramValue;
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
     * @throws ConfigException
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
     */
    static public function getMainUrl(bool $schema = true) {
        $url = self::get('url.main');
        if(strpos($url, '//') === 0) {
            $url = (self::get('ssl') ? 'https:' : 'http:') . $url;
        }
        if(!$schema) {
            $url = preg_replace('!^[a-z]*://!', '', $url);
        }
        return $url;
    }

	static public function getPlugins(): array
    {
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
	 */
	static public function isCurrentNode($node): bool
    {
		return self::get('current_node') === $node;
	}

	static public function isMasterNode($node = null): bool
    {
		if ($node) {
			return self::get('node') === $node;
		}

		return self::isCurrentNode(self::get('node'));
	}
}
