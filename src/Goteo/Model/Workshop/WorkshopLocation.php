<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Workshop;

use Goteo\Model\Workshop;

class WorkshopLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'workshop_location';
    protected static $Table_static = 'workshop_location';
    public $workshop;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->workshop = $this->id;
    }

    public static function get($workshop) {
        $id = $workshop;
        if($workshop instanceOf Workshop) {
            $id = $workshop->id;
        }

        return parent::get($id);
    }
}

