<?php

namespace Goteo\Util\Foil\Extension;

use Symfony\Component\HttpFoundation\Request;
use Foil\Contracts\ExtensionInterface;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class LangUtils implements ExtensionInterface
{

    private $args;
    private static $request;

    public static function setRequest(Request $request) {
        self::$request = $request;
    }

    public static function getRequest() {
        if(!self::$request)
            self::$request = App::getRequest();
        return self::$request;
    }

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'lang_current' => [$this, 'lang_current'],
          'lang_short' => [$this, 'lang_short'],
          'lang_name' => [$this, 'lang_name'],
          'lang_locale' => [$this, 'lang_locale'],
          'lang_active' => [$this, 'lang_active'],
          'lang_list' => [$this, 'lang_list'],
          'lang_url' => [$this, 'lang_url'],
          'lang_url_query' => [$this, 'lang_url_query'],
          'lang_host' => [$this, 'lang_host'],
          'list_countries' => [$this, 'list_countries'],
          'is_rtl' => [$this, 'is_rtl'],
          'lang_dir' => [$this, 'lang_dir'],
        ];
    }
    public function lang_current($public_only = false)
    {
        return Lang::current($public_only);
    }

    public function lang_short($lang = null, $public_only = false)
    {
        return Lang::getShort($lang ? $lang : Lang::current($public_only));
    }

    public function lang_name($lang = null, $public_only = false)
    {
        return Lang::getName($lang ? $lang : Lang::current($public_only));
    }

    public function list_countries($lang = null)
    {
        return Lang::listCountries($lang);
    }

    public function lang_list($type = 'short', $public_only = true)
    {
        return Lang::listAll($type, $public_only);
    }

    public function lang_locale()
    {
        return Lang::getLocale();
    }

    public function lang_active($lang)
    {
        return Lang::isActive($lang);
    }

    public function lang_url($lang = null)
    {
        return Lang::getUrl($lang, self::getRequest());
    }

    public function lang_url_query($lang = null)
    {
        return Lang::getUrlQuery($lang, self::getRequest());
    }

    public function lang_host($lang = null)
    {
        return Lang::getUrl($lang);
    }

    /**
     * Check if current language is RTL (Right-to-Left)
     *
     * @param string|null $lang Language code (defaults to current)
     * @return bool
     */
    public function is_rtl($lang = null)
    {
        $lang = $lang ?: Lang::current();
        $rtl_languages = ['fa', 'ar', 'he'];
        return in_array($lang, $rtl_languages);
    }

    /**
     * Get text direction for current language
     *
     * @param string|null $lang Language code (defaults to current)
     * @return string 'rtl' or 'ltr'
     */
    public function lang_dir($lang = null)
    {
        return $this->is_rtl($lang) ? 'rtl' : 'ltr';
    }

}
