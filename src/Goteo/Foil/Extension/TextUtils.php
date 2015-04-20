<?php

namespace Goteo\Foil\Extension;

use Foil\Contracts\ExtensionInterface;

use Goteo\Library\Text;

class TextUtils implements ExtensionInterface
{

    public function setup(array $args = [])
    {
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          't' => [$this, 'get'],
          'text' => [$this, 'get'],
          'text_html' => [$this, 'html'],
          'text_get' => [$this, 'get'],
          'text_widget' => [$this, 'widget'],
          'text_recorta' => [$this, 'recorta']
        ];
    }

    public function get($input, $part)
    {
        return Text::get($input);
    }

    public function html($var)
    {
        return Text::html($var);
    }

    public function widget($var)
    {
        return Text::widget($var);
    }
    public function recorta($var, $len = 10)
    {
        return Text::recorta($var, $len);
    }
}
