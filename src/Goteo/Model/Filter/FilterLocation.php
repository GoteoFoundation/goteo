<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Filter;

use Goteo\Model\Filter;
use Goteo\Model\User;

class FilterLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'filter_location';
    protected static $Table_static = 'filter_location';
    public $filter;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->filter = $this->id;
    }

    public static function get($filter) {
        $id = $filter;
        if($filter instanceOf filter) {
            $id = $filter->id;
        }
        return parent::get($id);
    }

    /**
     * Same permissions as view filter
     * Onwer can view location
     * admins too
     * if filter is pubic too
     */
    public function userCanView(User $user) {
        return $this->getModel()->userCanView($user);
    }

    /**
     * same permissions as edit filter
     */
    public function userCanEdit(User $user) {
        return $this->getModel()->userCanEdit($user);
    }

}

