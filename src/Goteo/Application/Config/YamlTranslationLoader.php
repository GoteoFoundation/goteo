<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\Config;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Translation\Loader\YamlFileLoader;

use Goteo\Application\App;

class YamlTranslationLoader extends YamlFileLoader
{
    static public $cached_files = [];

    public function load($resource, $locale, $domain = 'messages')
    {
        // Cached resources ?
        $cachePath = GOTEO_CACHE_PATH . 'translations/'.md5($resource) . '.php';
        static::$cached_files[] = $cachePath;

        // the second argument indicates whether or not you want to use debug mode
        $cacheMatcher = new ConfigCache($cachePath, App::debug());

        if (!$cacheMatcher->isFresh()) {
            // Get config from yaml
            $catalogue = parent::load($resource, $locale, $domain);

            //Code in PHP
            $code = serialize($catalogue);

            $cacheMatcher->write($code);

        }

        // you may want to require the cached code:
        $catalogue = unserialize(file_get_contents($cachePath));

        return $catalogue;
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}
