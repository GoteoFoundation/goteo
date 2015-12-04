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
use Symfony\Component\Translation\Loader\ArrayLoader;

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Core\Model;

class SqlTranslationLoader extends ArrayLoader
{
    static public $cached_files = [];

    public function load($resource, $locale, $domain = 'messages')
    {
        // Cached resources ?
        $cachePath = GOTEO_CACHE_PATH . 'translations/sql-texts-' . $locale . '.php';
        self::$cached_files[] = $cachePath;

        // the second argument indicates whether or not you want to use debug mode
        $cacheMatcher = new ConfigCache($cachePath, App::debug());
        $expired = !empty(Config::get('db.cache.driver')) || ((time() - @filemtime($cacheMatcher->getPath())) > (int)Config::get('db.cache.long_time'));

        if (!$cacheMatcher->isFresh() || $expired) {

            $sql="SELECT * FROM text WHERE text.lang = :lang";
            $values = array(':lang' => $locale);
            $query = Model::query($sql, $values);
            $messages = [];
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $text) {
                $messages[$text->id] = $text->text;
            }

            $catalogue = parent::load($messages, $locale, $domain);

            //Code in PHP
            $code = serialize($catalogue);

            $cacheMatcher->write($code);
        }

        // you may want to require the cached code:
        $catalogue = unserialize(file_get_contents($cachePath));
        return $catalogue;
    }

}
