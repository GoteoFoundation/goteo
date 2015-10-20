<?php

namespace Goteo\Util\Foil\Extension;

use Symfony\Component\HttpFoundation\Request;
use Foil\Contracts\ExtensionInterface;
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
            self::$request = Request::create();
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
          'lang_locale' => [$this, 'lang_locale'],
          'lang_active' => [$this, 'lang_active'],
          'lang_list' => [$this, 'lang_list'],
          'lang_url' => [$this, 'lang_url'],
          'list_countries' => [$this, 'list_countries'],
        ];
    }
    public function lang_current()
    {
        return Lang::current();
    }

    public function list_countries($lang = null)
    {
        return Lang::listCountries($lang);
    }

    public function lang_list($type = 'short')
    {
        return Lang::listAll($type);
    }

    public function lang_locale()
    {
        return Lang::getLocale();
    }

    public function lang_active($lang)
    {
        return Lang::isActive($lang);
    }

    public function lang_url($lang)
    {
        $url = Config::get('url.main');
        $url_lang = Config::get('url.url_lang');
        $path = '/';
        if($request = GoteoCore::getRequest()) {
            $path = $request->getPathInfo();
        }
        if($url_lang) {
            $url = (Config::get('ssl') ? 'https://' : 'http://') . $lang . '.' . $url_lang . $path;
        }
        else {
            $url .= $path . '?lang=' . $lang;
        }
        return $url;
    }

}
