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
use Goteo\Application\App;
use Goteo\Model\Image;

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
          'text_plain' => [$this, 'plain'],
          'text_url_link' => [$this, 'url_link'],
          'percent_span' => [$this, 'percent_span'],
          'percent_badge' => [$this, 'percent_badge'],
          'sanitize' => [$this, 'sanitize'],
          'markdown' => [$this, 'markdown'],
          'to_rgba' => [$this, 'to_rgba'],
          'image_src' => [$this, 'image_src']
        ];
    }

    /**
     * Percent colorized html <span>
     * @param  number  $percent  from 0 to 100
     * @param  integer $decimals number of decimals
     * @param  string  $text     text (otherwise just the n%)
     * @param  string  $class    class assigned to the span
     * @return string            html code
     */
    public function percent_span($percent, $decimals = 0, $text = '', $class = 'label label-percent') {
        $percent = number_format( (float) $percent, $decimals, ',', '');
        return '<span class="' . $class . '" style="background-color:hsl(' . (120 * $percent/100) . ',45%,50%);">' . ($text ? $text : "$percent %") .'</span>';
    }
    // handy method to put a simplified percent badge
    public function percent_badge($percent, $text = '') {
        return self::percent_span($percent, 0, $text, 'badge');
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

    public function widget($var = '')
    {
        return call_user_func_array ( 'Goteo\Library\Text::widget' , func_get_args() );
    }
    public function truncate($var = '', $len = 10)
    {
        return call_user_func_array ( 'Goteo\Library\Text::recorta' , func_get_args() );
    }
    public function plain($var = '')
    {
        return call_user_func_array ( 'Goteo\Library\Text::plain' , func_get_args() );
    }

    public function url_link($var = '')
    {
        return call_user_func_array ( 'Goteo\Library\Text::urlink' , func_get_args() );
    }

    public function sanitize($var = '')
    {
        return call_user_func_array ( 'Goteo\Core\Model::idealiza' , func_get_args() );
    }

    public function markdown($text = '')
    {
        return App::getService('app.md.parser')->text($text);
    }

    public function image_src($img, $w, $h, $ops = []) {
      if(!$img instanceof Image) {
        $img = new Image($img);
      }

      $src = $img->getLink($w, $h);

      return $src;
    }

    /**
     * converts a #xxyyzz HEX color to rgba(xx,yy,zz,opacity)
     * @param  string $color HEX color
     * @param  float $opacity level of opacity
     */
    public function to_rgba($hex, $opacity = 1) {
        if($hex{0} === '#') $hex = substr($hex, 1);
        if(strlen($hex) == 3) $hex = $hex{0}.$hex{0}.$hex{1}.$hex{1}.$hex{2}.$hex{2};

        // return "#$hex" . dechex(256 * $opacity);
        $d1 = hexdec($hex{0} . $hex{1});
        $d2 = hexdec($hex{2} . $hex{3});
        $d3 = hexdec($hex{4} . $hex{5});
        return "rgba($d1, $d2, $d3, $opacity)";
    }

}
