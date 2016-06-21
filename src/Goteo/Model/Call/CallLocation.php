<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Call;

use Goteo\Model\Call;

class CallLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'call_location';
    public $invest;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->invest = $this->id;
    }

    public static function get($invest) {
        $id = $invest;
        if($invest instanceOf Call) {
            $id = $invest->id;
        }

        return parent::get($id);
    }
}

