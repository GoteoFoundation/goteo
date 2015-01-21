<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Geoloc;

    class Location {

        public static function process () {

            $user = Model\User::getUser();
            $user_location = $user->getLocation();

            $errors = array();

            // quiere quitarse de los ilocalizables
            if (isset($_POST['locable'])) {
                Model\User\UserLocation::setLocable($user->id, $errors);
            }

            // quiere desasignarse de la geolocalización
            if (isset($_POST['unlocate']) && $user_location) {
                Model\User\UserLocation::setUnLocable($user->id, $errors);
            }

            // si cambian la localización
            if (isset($_POST['relocate'])) {

                $location = $_POST['location'];
                print_r($location);die;

                // ponemos lo que han escrito
                Model\Location::query('UPDATE user SET location = :location WHERE id = :id', array(':location'=>$location, ':id'=>$user->id));

                if (!empty($location)) {
                    // veamos si es una existente

                    $sql = "SELECT id FROM location WHERE CONCAT(location, ', ', region, ', ', country) LIKE ?";
                    $query = Model\Location::query($sql, array($location));
                    $exist = $query->fetchColumn();

                    // si han escogido una existente se asigna y listos
                    $assign = null;
                    if (!empty($exist)) {
                        $assign = $exist;
                    } else {
                        // si han escrito una no existente, se consulta a gmaps y si encuentra la creamos
                        $geodata = Geoloc::searchLoc(array('address'=>$location));
                        // grabo esta geolocation en la tabla maestra
                        if (!empty($geodata)) {
                            // miro que no haya encontrado una existente
                            $sql = "SELECT id FROM location WHERE CONCAT(location, ', ', region, ', ', country) LIKE ?";
                            $query = Model\Location::query($sql, array("{$geodata['location']}, {$geodata['region']}, {$geodata['country']}"));
                            $exists = $query->fetchColumn();

                            // si han escogido una existente se asigna y listos
                            if (!empty($exists)) {
                                $assign = $exists;
                            } else {

                                // con los datos obtenidos de la API gmaps
                                $newloc = new Model\Location(array(
                                    'location'=>$geodata['location'],
                                    'region'=>$geodata['region'],
                                    'country'=>$geodata['country'],
                                    'lat'=>$geodata['lat'],
                                    'lon'=>$geodata['lon'],
                                    'valid'=>0
                                ));

                                if ($newloc->save($errors)) {
                                    $assign = $newloc->id;
                                }
                            }
                        } else {
                            $errors[] = 'No se ha encontrado esa localización, intentalo de nuevo escribiéndola de manera general en fomato Localidad, Provincia, País';
                        }

                    }

                    // geolocaliza al usuario
                    if (!empty($assign)) {
                        $setloc = new Model\User\Location(array('user'=>$user->id, 'location'=>$assign));
                        $setloc->save($errors);
                    }

                }
            }

            return $errors;
        }

    }

}
