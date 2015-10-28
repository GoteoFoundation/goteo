<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Foil\Extension;

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
          'text_sys' => [$this, 'sys'],
          'text_html' => [$this, 'html'],
          'text_get' => [$this, 'get'],
          'text_widget' => [$this, 'widget'],
          'text_truncate' => [$this, 'truncate'],
          'percent_span' => [$this, 'percent_span'],
          'sanitize' => [$this, 'sanitize']
        ];
    }

    public function percent_span($percent, $decimals = 0, $text = '') {
        $percent = number_format( (float) $percent, $decimals, ',', '');
        return '<span class="label label-percent" style="background-color:hsl(' . (120 * $percent/100) . ',45%,50%);">' . ($text ? $text : "$percent %") .'</span>';
    }
    public function get()
    {
        return call_user_func_array ( 'Goteo\Library\Text::get' , func_get_args() );
    }

    public function sys()
    {
        return call_user_func_array ( 'Goteo\Library\Text::sys' , func_get_args() );
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
    public function sanitize($var)
    {
        return call_user_func_array ( 'Goteo\Core\Model::idealiza' , func_get_args() );
    }
}
