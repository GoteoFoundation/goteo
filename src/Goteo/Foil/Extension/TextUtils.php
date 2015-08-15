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
          'text_truncate' => [$this, 'truncate'],
          'percent_span' => [$this, 'percent_span']
        ];
    }

    public function percent_span($percent, $decimals = 0) {
        $percent = number_format( (float) $percent, $decimals, ',', '');
        return '<span class="label label-percent" style="background-color:hsl(' . (120 * $percent/100) . ',45%,50%);">' . $percent .' %</span>';
    }
    public function get()
    {
        return call_user_func_array ( 'Goteo\Library\Text::get' , func_get_args() );
    }

    public function html()
    {
        return call_user_func_array ( 'Goteo\Library\Text::html' , func_get_args() );
    }

    public function widget($var)
    {
        return call_user_func_array ( 'Goteo\Library\Text::widget' , func_get_args() );
    }
    public function truncate($var, $len = 10)
    {
        return call_user_func_array ( 'Goteo\Library\Text::recorta' , func_get_args() );
    }
}
