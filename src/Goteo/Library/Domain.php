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

        $parse = parse_url($domain);
        return in_array($parse['host'], $domains);
    }
}
