<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Goteo\Application\Session;
use Goteo\Application\View;

abstract class AbstractApiController extends \Goteo\Core\Controller {
    protected $is_admin = false;
    protected $user = null;

    public function __construct() {
        // changing to a json theme here (not really a theme)
        View::setTheme('JSON');
        $this->user = Session::getUser();
        $this->is_admin = Session::isAdmin();
        // cache active only on non-logged users
        if(!$this->user) $this->dbCache(true);
    }
}
