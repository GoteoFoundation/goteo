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

use Goteo\Application\Role;

/**
 * Class UserRole
 * @package Goteo\Model\User
 *
 * Class for handeling user permissions
 *
 */
class UserRole extends \Goteo\Core\Model
{
    protected $permissions = array();
    protected $role_id;

    public function __construct() {
    }

    public function get($role_id) {

    }

    // check if a permission is set
    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }
}
