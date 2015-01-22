<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error;

    class Location {

        public static function process () {

            $user = Model\User::getUser();

            $errors = array();

            // quiere quitarse de los ilocalizables
            if (isset($_POST['locable'])) {
                Model\User\UserLocation::setLocable($user->id, $errors);
            }

            // quiere desasignarse de la geolocalización
            if (isset($_POST['unlocate']) && $user->geoloc) {
                Model\User\UserLocation::setUnLocable($user->id, $errors);
            }

            return $errors;
        }

    }

}
