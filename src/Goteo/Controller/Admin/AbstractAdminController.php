<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;
use Goteo\Core\Traits\LoggerTrait;

abstract class AbstractAdminController extends \Goteo\Core\Controller implements AdminControllerInterface {
    use LoggerTrait;

    /**
     * Returns the identificator for this controller
     * @return MyControllerSubController becames mycontroller
     */
    public static function getId() {
        $class = get_called_class();
        $a = explode('\\', $class);
        return strtolower(str_replace('AdminController', '', end($a)));
    }

}
