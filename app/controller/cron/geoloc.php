<?php

namespace Goteo\Controller\Cron {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library;

    class Geoloc {

        public static function process ($debug = false) {

            // esto graba en un único archivo de log, solo las creaciones
            $log_file = GOTEO_LOG_PATH.'logs/cron/created_locations.log';
            \chmod($log_file, 0777);

            // geolocalizaciones existentes

            // eliminamos los registros que no nos sirven
            $sql = "DELETE FROM `geologin` WHERE msg LIKE '%unavailable%' OR msg LIKE '%not supported%'";
            if ($debug) echo $sql . '<br />';
            $query = Model\Location::query($sql);
            $count = $query->rowCount();
            if ($debug) echo "Eliminados $count registros de geologin que no nos sirven.<br />";

            // marca como ilocalizables los geologin no permitidos por el usuario
            $sql2 = "REPLACE INTO unlocable SELECT user FROM `geologin` WHERE msg LIKE '%denied%'";
            if ($debug) echo $sql2 . '<br />';
            if ($query2 = Model\Location::query($sql2)) {
                $sql21 = "DELETE FROM `geologin` WHERE msg LIKE '%denied%'";
                $query21 = Model\Location::query($sql21);
                $count2 = $query21->rowCount();
                if ($debug) echo "Eliminados $count2 registros de geologin añadidos a los usuarios ilocalizables.<br />";
            }

            if ($debug) echo "<hr /> Iniciamos tratamiento de geologins correctos<br/>";
            //Library\Geoloc
            $sql5 = "SELECT * FROM `geologin` WHERE msg LIKE 'OK' LIMIT 1000";
            if ($debug) echo $sql5 . '<br />';
            $query5 = Model\Location::query($sql5);
            foreach ($query5->fetchAll(\PDO::FETCH_OBJ) as $row) {
                if ($_SESSION['over-quota']) {
                    if ($debug) echo "Hemos excedido la quota<br />";
                    break;
                }
                if ($debug) echo "latlng: {$row->lat},{$row->lon}<br />";
                // para cada uno:
                $geoloc = null;
                $newloc = null;
                $issue = false;
                // peticion api gmaps
                $geodata = Library\Geoloc::searchLoc(array('latlng'=>"{$row->lat},{$row->lon}"));
                if ($debug) echo 'Obtenido por consulta API:<br />';
                if ($debug) echo \trace($geodata);

                // si no recupera nada
                if (!empty($geodata)) {
                    // proceso creación/recuperacion location

                    // si es una que ya tenemos, recuperamos la id
                    $locations = Model\Location::getAll(array(
                        'location'=> !empty($geodata['location']) ? md5($geodata['location']) : '',
                        'region'=> !empty($geodata['region']) ? md5($geodata['region']) : '',
                        'country'=> !empty($geodata['country']) ? md5($geodata['country']) : ''
                    ));

                    if (count($locations) > 0) {
                        if ($debug) echo 'existe:<br />';
                        if ($debug) echo \trace($locations[0]) . '<br />';
                        $geoloc = (count($locations) > 0) ? $locations[0]->id : '';
                        $locName = (count($locations) > 0) ? $locations[0]->name : '';
                        if ($debug) echo 'usamos: '.$geoloc.'<br />';
                    } else {
                        // si no la tenemeos (no tenemos id), la creamos con los datos obtenidos de la API gmaps
                        $errors = array();
                        $newloc = new Model\Location(array(
                            'location'=>$geodata['location'],
                            'region'=>$geodata['region'],
                            'country'=>$geodata['country'],
                            'lat'=>$row->lat,
                            'lon'=>$row->lon,
                            'valid'=>1
                        ));

                        if ($newloc->save($errors)) {
                            // OK
                            $locName = "{$geodata['location']}, {$geodata['region']}, {$geodata['country']}";
                            \file_put_contents($log_file, 'Localización creada: ['.$newloc->id.'] '.$locName.'<br />', FILE_APPEND);
                            if ($debug) echo 'Localización creada:<br />';
                            if ($debug) echo \trace($newloc);
                            if ($debug) echo '<hr />';
                        } else {
                            @mail(\GOTEO_FAIL_MAIL,
                                'Error al crear localidad automáticamente en cron/geoloc. En ' . SITE_URL,
                                'ERROR al crear, no se asignará. <br />'. implode('<br />', $errors).'<br />'.\trace($newloc).'<br />');
                            if ($debug) echo 'ERROR al crear, no se asignará. <br />'. implode('<br />', $errors).'<br />';
                            unset($geoloc);
                            unset($newloc);
                        }
                    }

                    // asignamos al usuario
                    $sql_insloc = "REPLACE INTO location_item (`location`, `item`, `type`) VALUES (:loc, :usr, 'user')";
                    if (isset($geoloc)) {
                        // a una localización existente
                        $values = array(':loc'=>$geoloc, ':usr'=>$row->user);
                    } elseif (!empty($newloc->id)) {
                        // a la nueva localización recien creada
                        $values = array(':loc'=>$newloc->id, ':usr'=>$row->user);
                    } else {
                        $sql_insloc = null;
                    }

                    if ($sql_insloc) {
                        if (Model\Location::query($sql_insloc, $values)) {
                            if ($debug) echo 'Se ha asignado:<br />'.\trace($values).'<hr />';
                        } else {
                            if ($debug) echo 'ERROR al asignar:<br />'. $sql_insloc.'<br />'.\trace($values).'<hr />';
                        }
                    } else {
                        // increible
                        $issue = true;
                    }

                } else {
                    if ($debug) echo 'No se ha recuperado ninguna localización<br />';
                    // increible
                    $issue = true;
                }

                // borrar entrada geologin (o marcar como increible)
                if ($issue) {
                    $sql7 = "UPDATE `geologin` SET msg = 'NOK' WHERE user = '{$row->user}'";
                    if ($debug) echo $sql7 . '<br />';
                    Model\Location::query($sql7);
                } else {
                    // si no ha rellenado el campo localidad, se lo rellenamos
                    $values = array(':locname'=>$locName, ':usr'=>$row->user);
                    $sql70 = "UPDATE `user` SET location = :locname WHERE id = :usr AND (location IS NULL OR TRIM(location) = '')";
                    if ($debug) echo $sql70 . '<br />';
                    if ($debug) echo \trace($values);
                    Model\Location::query($sql70, $values);


                    // borramos el geologin
                    $sql7 = "DELETE FROM `geologin` WHERE user = :usr";
                    if ($debug) echo $sql7 . '<br />';
                    Model\Location::query($sql7, array(':usr'=>$row->user));
                }

                if ($debug) echo "<hr />";
            }

            echo "Geoloc Listo!";

            return;
        }

    }

}
