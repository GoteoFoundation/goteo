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

use Goteo\Model\User;

interface AdminControllerInterface {

    /**
     * Returns a unique identificator for this controller
     */
    public static function getId();

    /**
     * Returns Symfony\Component\Routing\Route or Symfony\Component\Routing\RouteCollection routes for this submodule
     */
    public static function getRoutes();

    /**
     * Return HTMl definition of the admin module (for titles)
     */
    public static function getLabel($mode = 'html');

    /**
     * Returns an array suitable for Session::addToSidebarMenu
     * if no sidebar is returned, an automated link will be created to the index module page
     */
    public static function getSidebar();

    /**
     * Returns if this class can be administred by specified user
     * Overwrite this function for more specific permissions
     * @param $uri is relative to /admin/{uri} (starting with / optionally)
     */
    public static function isAllowed(User $user, $uri = '');
}
