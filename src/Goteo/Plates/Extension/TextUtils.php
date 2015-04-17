<?php

namespace Goteo\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Goteo\Library\Text;

class TextUtils implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('text_html', [$this, 'html']);
        $engine->registerFunction('text', [$this, 'get']);
        $engine->registerFunction('text_get', [$this, 'get']);
        $engine->registerFunction('text_widget', [$this, 'widget']);
        $engine->registerFunction('text_recorta', [$this, 'recorta']);
    }

    public function html($var)
    {
        return Text::html($var);
    }

    public function get($var)
    {
        return Text::get($var);
    }

    public function widget($var)
    {
        return Text::widget($var);
    }
    public function recorta($var)
    {
        return Text::recorta($var);
    }
}
