<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Feed,
		Goteo\Library\Geoloc,
        Goteo\Library\Message,
        Goteo\Library\Template,
        Goteo\Library\Text,
        Goteo\Model;

    class Locations {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            switch ($action)  {
                
                // proceso automático para actualizar localidades de registros, auto-crear localizaciones y asignar
                case 'autocheck':
                    
                    // si llega post, grabo eso que llega y sigo ccheckeando
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
                        echo \trace($_POST);
                        echo 'No grabado aun.';
                        die;
                        // grabo esta geolocation
                        // la asigno a todos los usuarios que tengan esta localidad
                    }                    
                    
                    /*
                     * PROCESO
                     **********
                     * 
                     * Primero, busco la localización más compartida por usurios (más de un usuario)
                     * Cuando no quedan, busco localización específica de usuario no dado por ilocalizable
                     * 
                     * *
                     */
                    // Busco la localización que comparten más usuarios
                    // esta consulta no será así en el proceso automático
                    $sql = "SELECT 
                                location, count(id) as cuantos 
                            FROM user
                            WHERE user.id not IN (SELECT item FROM location_item WHERE type = 'user')
                            AND location IS NOT NULL
                            AND TRIM(location) != ''
                            GROUP BY location
                            HAVING cuantos > 1
                            ORDER BY cuantos DESC
                            ";
                    $query = Model\Location::query($sql);
                    $row = $query->fetchObject();
                    $user = $row->cuantos;
                    $location = $row->location;
                    /*
                     * Para cada localidad
                     * -------------------
                     * Reglas:
                     * * Una sola palabra (seguramente pais), miro si esta en el array de paises, geolocalizo el pais
                     * * Coordenadas
                     * * coordenadas geográficas (º'")
                     * * 
                     */
                    $geodata = Geoloc::searchLoc(array('address'=>$location));
                    
                    // vista de autocheck
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder'    => 'locations',
                            'file'      => 'autocheck',
                            'user'      => $user,
                            'location'  => $location,
                            'geodata'   => $geodata,
                            'action'    => 'list'
                        )
                    );

                    break;

                // crear nueva
                case 'add':


                    // si llega post: creamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        $location = new Model\Location();
                        $location->location = $_POST['location'];
                        $location->region = $_POST['region'];
                        $location->country = $_POST['country'];
                        $location->lon = $_POST['lon'];
                        $location->lat = $_POST['lat'];
                        $location->valid = $_POST['valid'];
                        if($location->save($errors)) {
                          // mensaje de ok y volvemos a la lista de tareas
                          Message::Info('Nueva localizacion creada correctamente');
                          throw new Redirection('/admin/locations');
                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $location = (object) $_POST;
                            Message::Error(implode('<br />', $errors));
                        }
                    } else {
                        $location = (object) array('country'=>'España');
                    }

                    // vista de crear
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'locations',
                            'file'  => 'edit',
                            'location'  => $location,
                            'action' => 'add'
                        )
                    );

                    break;

                // editar
                case 'edit':

                    $location = Model\Location::get($id);

                    // si llega post: actualizamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
                        $location->location = $_POST['location'];
                        $location->region = $_POST['region'];
                        $location->country = $_POST['country'];
                        $location->lon = $_POST['lon'];
                        $location->lat = $_POST['lat'];
                        $location->valid = $_POST['valid'];
                        if($location->save($errors)) {

                            // mensaje de ok y volvemos a la lista
                            Message::Info('Localización actualizada');
                            throw new Redirection('/admin/locations');

                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            Message::Error(implode('<br />', $errors));
                        }
                    }

                    // vista de editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'locations',
                            'file' => 'edit',
                            'location'=>$location,
                            'action' => 'edit'
                        )
                    );

                    break;

                // para revisar registros no asignados a localización
                case 'check':
                    Message::Info('Aun no esta listo');
                    throw new Redirection('/admin/locations');


                    // tipos de registro
                    $types = array(
                                'user' => 'Usuarios',
                                'project' => 'Proyectos',
                                'node' => 'Nodos',
                                'call' => 'Convocatorias'
                            );

                    $type = in_array($_GET['type'], $types) ? $_GET['type'] : 'user';
                    
                    // cargar la lista de registros a checkear  (segun tipo de registro)
                    // son los no asignados a una localizacion
                    $list = Model\Location::getCheck($type, 10);

                    // vista de editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'locations',
                            'file' => 'check',
                            'list' => $list,
                            'types' => $types,
                            'action' => 'edit'
                        )
                    );

                    break;

                // para encontrar registros por filtros de localización
                case 'find':

                    Message::Info('Aun no esta listo');
                    throw new Redirection('/admin/locations');

                    // tipos de registro
                    $types = Model\Location::$type;

                    $type = array_key_exists($_GET['type'], $types) ? $_GET['type'] : 'user';

                    // cargar la lista de registros a checkear  (segun tipo de registro)
                    // son los no asignados a una localizacion
                    $list = Model\Location::getSearch($type, $filters);

                    $countries = Model\Location::getList('country'); // distintos paises ya existentes

                    // filtro pais sobre regiones
                    if (empty($filters['region']) && !empty($filters['country'])) {
                        $regionFilter = array(
                            'type' => 'country',
                            'value' => md5($filters['country'])
                        );
                        $locationFilter = array(
                            'type' => 'country',
                            'value' => md5($filters['country'])
                        );
                    } else {
                        $regionFilter = null;
                    }

                    // regiones (si hay filtro de pais, filtramos estas por pais)
                    $regions = Model\Location::getList('region', $regionFilter); 

                    // filtro region sobre localizacion
                    if (empty($filters['location']) && !empty($filters['region'])) {
                        $locationFilter = array(
                            'type' => 'region',
                            'value' => md5($filters['region'])
                        );
                    } elseif (empty($filters['location']) && !empty($filters['country'])) {
                        $locationFilter = array(
                            'type' => 'country',
                            'value' => md5($filters['country'])
                        );
                    } else {
                        $locationFilter = null;
                    }

                    $locations = Model\Location::getList('location', $locationFilter); // distintas localizaciones ya existentes (si hay filtro de region, filtramos estas por region; si no hay filtro de region pero hay filtro de pais, filtramos  estas por pais)

                    // vista de editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'locations',
                            'file' => 'check',
                            'list' => $list,
                            'countries' => $countries,
                            'regions' => $regions,
                            'locations' => $locations,
                            'filters' => $filters,
                            'types' => $types,
                            'action' => 'edit'
                        )
                    );

                    break;

                case 'list':
                default:

                    // si hay filtro de localidad solo la region y pais de la localidad
                    // si hay de region, solo el pais de la region

                    $list = Model\Location::getAll($filters);

                    $countries = Model\Location::getList('country'); // distintos paises ya existentes

                    // filtro pais sobre regiones
                    if (empty($filters['region']) && !empty($filters['country'])) {
                        $regionFilter = array(
                            'type' => 'country',
                            'value' => $filters['country']
                        );
                        $locationFilter = array(
                            'type' => 'country',
                            'value' => $filters['country']
                        );
                    } else {
                        $regionFilter = null;
                    }

                    // regiones (si hay filtro de pais, filtramos estas por pais)
                    $regions = Model\Location::getList('region', $regionFilter);

                    // filtro region sobre localizacion
                    if (empty($filters['location']) && !empty($filters['region'])) {
                        $locationFilter = array(
                            'type' => 'region',
                            'value' => $filters['region']
                        );
                    } elseif (empty($filters['location']) && !empty($filters['country'])) {
                        $locationFilter = array(
                            'type' => 'country',
                            'value' => $filters['country']
                        );
                    } else {
                        $locationFilter = null;
                    }

                    $locations = Model\Location::getList('location', $locationFilter); // distintas localizaciones ya existentes (si hay filtro de region, filtramos estas por region; si no hay filtro de region pero hay filtro de pais, filtramos  estas por pais)

                    $valid = array(
                                'all' => 'Todas',
                                '0' => 'No revisadas',
                                '1' => 'Revisadas'
                            );
                    $used = array(
                                'all' => 'Todas',
                                '0' => 'No usadas',
                                '1' => 'En uso'
                            );

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'locations',
                            'file' => 'list',
                            'list' => $list,
                            'countries' => $countries,
                            'regions' => $regions,
                            'locations' => $locations,
                            'filters' => $filters,
                            'valid' => $valid,
                            'used' => $used
                        )
                    );
                break;
            }
            
        }

    }

}
