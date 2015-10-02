<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Dashboard {

    use Goteo\Model\User\UserLocation,
        Goteo\Application\Session,
        Goteo\Core\Redirection,
        Goteo\Core\Error;

    class Location {

        public static function process () {

            $user = Session::getUser();

            $errors = array();
            // quiere quitarse de los ilocalizables
            if (isset($_POST['locable'])) {
                UserLocation::setLocable($user->id, $errors);
            }

            // quiere desasignarse de la geolocalización
            if (isset($_POST['unlocate']) && $user->geoloc) {
                UserLocation::setUnLocable($user->id, $errors);
            }

            return $errors;
        }

    }

}
