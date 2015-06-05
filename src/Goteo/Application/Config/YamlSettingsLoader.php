<?php

namespace Goteo\Application\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

use Goteo\Application\Config;

class YamlSettingsLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        $config = Yaml::parse(file_get_contents($resource));
        // ... handle the config values
        Config::factory($config);

    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}
