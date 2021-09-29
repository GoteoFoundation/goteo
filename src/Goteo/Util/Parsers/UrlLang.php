<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Parsers;

use Goteo\Application\Config;
use Goteo\Application\Lang;
use Symfony\Component\HttpFoundation\Request;

class UrlLang {
    public $request;
    protected $path;
    protected $host;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->path = $request->getPathInfo();
        $this->host = $request->getHttpHost();
    }

    /**
     * Returns false if the requests is configured to skip any cookie setting
     */
    public function skipSessionManagement() {
        $skip = Config::get('session.skip');
        if ($this->matchPrefix($this->path, $skip)) {
            return true;
        }
        return false;
    }

    /**
     * Returns the proper URL after processing url_lang configurations (adds subdomain lang if needed)
     */
    public function getHost($lang) {
        $host = $this->host;
        if(empty($lang)) {
            return $host;
        }

        // Routes to leave as they are
        $skip = Config::get('url.redirect.skip');
        // Redirect to proper URL if url_lang is defined
        if (!Config::get('url.url_lang') || $this->matchPrefix($this->path, $skip)) {
            return $host;
        }

        // Routes to alway reditect to the main url
        $fixed = Config::get('url.redirect.fixed');
        $parts = explode('.', $host);
        $sub_lang = $parts[0];
        if($sub_lang == 'www') $sub_lang = Config::get('lang');
        if (Lang::exists($sub_lang)) {
            // reduce url: ca.goteo.org => goteo.org
            array_shift($parts);
            $host = implode('.', $parts);
        }
        // if reduced URL is the main domain, redirect to sub-level lang
        if($host === Config::get("url.url_lang")) {
            if($this->request->query->has('lang')) {
                $this->request->query->remove('lang');
            }
            // Login controller should mantaing always the same URL to help browser password management
            if($this->matchPrefix($this->path, $fixed)) {
                // $host = "$host";
                $this->request->query->set('lang', $lang);
            }
            else {
                $host = preg_replace('!https?://|/$!i', '', Lang::getUrl($lang));
            }

        }
        return $host;
    }

    /**
     * Chechsk if any of the elements in array $prefixes starts with the same chars as $full_str
     * @param  string $full_str  full string
     * @param  array $prefixes array of prexixes
     * @return boolean          found or not
     */
    protected function matchPrefix($full_str, $prefixes) {
        if(!is_array($prefixes)) {
            $prefixes = [$prefixes];
        }
        foreach($prefixes as $str) {
            if(strpos($full_str, $str) === 0) {
                return true;
            }
        }
        return false;
    }
}