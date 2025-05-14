<?php

namespace Goteo\Library;

use Goteo\Application\Config;
use Goteo\Application\Config\ConfigException;

class Domain
{
    public static function isAllowedDomain(string $domain): bool
    {
        try {
            $domains = Config::get('url.allowed_domains');
        } catch (ConfigException $e) {
            return false;
        }

        if (empty($domains))
            return false;

        $parse = parse_url(rawurldecode($domain), PHP_URL_HOST);
        if (!$parse)
            return false;

        $validDomains = array_filter($domains, function ($domain) use ($parse) {
            $parsedDomain = parse_url($domain);
            if (!$parsedDomain['scheme'])
                return $parsedDomain['path'] == $parse;

            return $parsedDomain['host'] == $parse;
        });

        return !empty($validDomains);
    }
}
