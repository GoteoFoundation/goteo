<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Invest;

use Goteo\Model\Invest;

class InvestLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'invest_location';
    protected static $Table_static = 'invest_location';
    public $invest;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->invest = $this->id;
    }

    public static function get($invest) {
        $id = $invest;
        if($invest instanceOf Invest) {
            $id = $invest->id;
        }

        return parent::get($id);
    }
}

