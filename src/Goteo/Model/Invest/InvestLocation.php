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
use Goteo\Model\User;

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

    /** Same user can view his location
     * admins too
     */
    public function userCanView(User $user) {
        $invest_user = $this->getModel()->getUser();
        if($user->id === $invest_user->id) return true;
        return $user->canImpersonate($invest_user);
    }

    /**
     * same privacy as view
     */
    public function userCanEdit(User $user) {
        return $this->userCanView($user);
    }

}

