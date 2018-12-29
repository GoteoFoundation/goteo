<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\User;

use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\User;

class UserLocation extends \Goteo\Model\Location\LocationItem {
    protected $Table = 'user_location';
    protected static $Table_static = 'user_location';
    public $user;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->user = $this->id;
    }

    public static function get($user) {
        $id = $user;
        if($user instanceOf User) {
            $id = $user->id;
        }
        return parent::get($id);
    }

    /** Same user can view his location
     * admins too
     */
    public function userCanView(User $user) {
        if($user->id === $this->id) return true;
        return $user->canImpersonate($this->getModel());
    }

    /**
     * same privacy as view
     */
    public function userCanEdit(User $user) {
        return $this->userCanView($user);
    }

}
