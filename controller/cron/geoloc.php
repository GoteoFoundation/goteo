<?php

namespace Goteo\Controller\Cron {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library;

    class Geoloc {

        public static function process () {

            // geolocalizaciones existentes
            
            // eliminamos los registros que no nos sirven
            $sql = "DELETE FROM `geologin` WHERE msg LIKE '%unavailable%' OR msg LIKE '%not supported%'";
            
            echo $sql . '<br />';
            $query = Model\Location::query($sql);
            $count = $query->rowCount();
            echo "Eliminados $count registros de geologin que no nos sirven.<br />";
            
            // marca como ilocalizables los geologin no permitidos por el usuario
            $sql2 = "REPLACE INTO unlocable SELECT user FROM `geologin` WHERE msg LIKE '%denied%'";
            echo $sql2 . '<br />';
            if ($query2 = Model\Location::query($sql2)) {
                $sql21 = "DELETE FROM `geologin` WHERE msg LIKE '%denied%'";
                $query21 = Model\Location::query($sql21);
                $count2 = $query21->rowCount();
                echo "Eliminados $count2 registros de geologin añadidos a los usuarios ilocalizables.<br />";
            }
            
            echo "<hr /> Iniciamos tratamiento de geologins correctos<br/>";
            //Library\Geoloc
            $sql5 = "SELECT * FROM `geologin` WHERE msg LIKE 'OK' LIMIT 2000";
            echo $sql5 . '<br />';
            $query5 = Model\Location::query($sql5);
            foreach ($query5->fetchAll(\PDO::FETCH_OBJ) as $row) {
                // para cada uno: 
                $geoloc = null;
                $newloc = null;
                $issue = false;
                // peticion api gmaps
                $geodata = Library\Geoloc::searchLoc(array('latlng'=>"{$row->lat},{$row->lon}"));
                echo 'Obtenido por consulta API:<br />';
                echo \trace($geodata);
                
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
                        echo 'existe:<br />';
                        echo \trace($locations[0]) . '<br />';
                        $geoloc = (count($locations) > 0) ? $locations[0]->id : '';
                        $locName = (count($locations) > 0) ? $locations[0]->name : '';
                        echo 'usamos: '.$geoloc.'<br />';
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
                            echo 'Localización creada:<br />';
                            echo \trace($newloc);
                            echo '<hr />';
                        } else {
                            echo 'ERROR al crear, no se asignará. <br />'. implode('<br />', $errors).'<br />';
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
                            echo 'Se ha asignado:<br />';
                            echo \trace($values);
                            echo '<hr />';
                        } else {
                            echo 'ERROR al asignar:<br />'. $sql_insloc.'<br />';
                            echo \trace($values);
                            echo '<hr />';
                        }
                    } else {
                        // increible
                        $issue = true;
                    }
                    
                } else {
                    echo 'No se ha recuperado ninguna localización<br />';
                    // increible
                    $issue = true;
                }
                    
                // borrar entrada geologin (o marcar como increible)
                if ($issue) {
                    $sql7 = "UPDATE `geologin` SET msg = 'NOK' WHERE user = '{$row->user}'";
                    echo $sql7 . '<br />';
                    Model\Location::query($sql7);
                } else {
                    // si no ha rellenado el campo localidad, se lo rellenamos
                    $sql70 = "UPDATE `user` SET location = :locname WHERE user = :usr AND (location IS NULL OR TRIM(location) = '')";
                    echo $sql70 . '<br />';
                    Model\Location::query($sql70, array(':locname'=>$locName, ':usr'=>$row->user));
                    
                    
                    // borramos el geologin
                    $sql7 = "DELETE FROM `geologin` WHERE user = :usr";
                    echo $sql7 . '<br />';
                    Model\Location::query($sql7, array(':usr'=>$row->user));
                }

                echo "<hr />";
                echo "<br />Hasta aqui por el mometno";
                return;
            }
            
            echo "<br />";
            echo 'Listo!';

            return;
        }

    }

}
