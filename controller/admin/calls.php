<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Calls {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            $log_text = null;

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            if (isset($id)) {
                $call = Model\Call::get($id);
            }
            // si es admin (no superadmin) si no la tiene asignada no puede hacer otra cosa que no sea listar.
            if ($action != 'list'
                && isset($_SESSION['user']->roles['admin'])
                && !isset($_SESSION['user']->roles['superadmin']) // no es superadmin
                ) {
                    // Si no la tiene asignada no puede gestionar cosas
                    if (isset($id) && !$call->isAdmin($_SESSION['user']->id)) {
                        Message::Error('No tienes permiso para gestionar esta convocatoria');
                        throw new Redirection("/admin/calls");
                    } elseif (!isset($id)) {
                        // si no hay id es crear y tampoco puede
                        Message::Error('No tienes permiso para gestionar convocatorias');
                        throw new Redirection("/admin/calls");
                    }
            }
            
            switch ($action) {
                case 'review': // listo para aplicar proyectos (se publica, sino que siga en edicion)
                    if ($call->ready($errors)) {
                        $log_text = 'El admin %s ha pasado la convocatoria %s a <span class="red">Revisión</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar la convocatoria %s a <span class="red">Revisión</span>';
                    }
                    break;
                case 'open': // comienza la campaña de postulacion
                    if ($call->open($errors)) {
                        $log_text = 'El admin %s ha pasado la convocatoria %s al estado <span class="red">Recepción de proyectos</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar la convocatoria %s al estado <span class="red">Recepción de proyectos</span>';
                    }
                    break;
                case 'publish': // comienza la campaña de pasta
                    if ($call->publish($errors)) {
                        $log_text = 'El admin %s ha pasado la convocatoria %s al estado <span class="red">en Campaña</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar la convocatoria %s al estado <span class="red">en Campaña</span>';
                    }
                    break;
                case 'cancel': // caducar una campaña o aplicacion antes de hora
                    if ($call->fail($errors)) {
                        $log_text = 'El admin %s ha pasado la convocatoria %s al estado <span class="red">Caducado</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar la convocatoria %s al estado <span class="red">Caducado</span>';
                    }
                    break;
                case 'enable': // reabrir la edición Ojo que se quita de campña! Se puede editar mientras está en campaña?
                    if ($call->enable($errors)) {
                        $log_text = 'El admin %s ha pasado la convocatoria %s al estado <span class="red">Edición</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar la convocatoria %s al estado <span class="red">Edición</span>';
                    }
                    break;
                case 'complete': // dar por finalizada la convocatoria
                    if ($call->succeed($errors)) {
                        $log_text = 'El admin %s ha marcado la convocatoria %s como <span class="red">Finalizada</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al marcar la convocatoria %s como <span class="red">Finalizada</span>';
                    }
                    break;
                case 'delete': // eliminar completamente la convocatoria
                    if ($call->delete($errors)) {
                        $log_text = 'El admin %s ha eliminado la convocatoria %s <span class="red">Completamente</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al eliminar la convocatoria %s <span class="red">Completamente</span>';
                    }
                    break;
            }

            if (!empty($errors)) {
                Message::Error(implode('<br />', $errors));
            }

            //si llega post, verificamos los datos y hacemos lo que se tenga que hacer
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['operation']) && !empty($call)) {
                switch ($_POST['operation']) {
                    case 'assign':
                        if (!empty($_POST['project'])) {
                            $registry = new Model\Call\Project;
                            $registry->id = $_POST['project'];
                            $registry->call = $call->id;
                            if ($registry->save($errors)) {
                                Message::Info('Proyecto seleccionado correctamente');

                                $projectData = Model\Project::get($_POST['project']);

                                // Evento feed
                                $log = new Feed();
                                $log->setTarget($projectData->id);
                                $log->populate('proyecto asignado a convocatoria desde admin', 'admin/calls/'.$call->id.'/projects',
                                    \vsprintf('El admin %s ha asignado el proyecto %s a la convocatoria %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('project', $projectData->name, $projectData->id),
                                        Feed::item('call', $call->name, $call->id))
                                    ));
                                $log->doAdmin('call');

                                // si la convocatoria está en campaña ( y el proyecto también), feed público
                                if ($call->status == 4 && $projectData->status == 3) {
                                    $log->populate($projectData->name, '/project/'.$projectData->id,
                                        \vsprintf('Ha sido seleccionado en la convocatoria %s', array(
                                            Feed::item('call', $call->name, $call->id))
                                        ), $projectData->gallery[0]->id);
                                    $log->doPublic('projects');
                                }
                                unset($log);

                            } else {
                                Message::Error('Fallo al seleccionar proyecto');
                            }
                        } else {
                            Message::Error('No has seleccionado ningun proyecto para asignar a la convocatoria, no?');
                        }
                        break;
                    case 'unassign':
                        if (!empty($_POST['project'])) {
                            $registry = new Model\Call\Project;
                            $registry->id = $_POST['project'];
                            $registry->call = $call->id;
                            if ($registry->remove($errors)) {
                                Message::Info('Proyecto desasignado correctamente');
                            } else {
                                Message::Error('Fallo al desasignar proyecto');
                            }
                        } else {
                            Message::Error('No has clickado ningun proyecto para desasignar, no?');
                        }
                        break;
                }
            }


            if (isset($log_text)) {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($call->id, 'call');
                $log_html = \vsprintf($log_text, array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('call', $call->name, $call->id))
                    );
                // Mensaje como el log
                Message::Info($log_html);
                $log->populate('Gestion de una convocatoria desde el admin', '/admin/calls', $log_html);
                $log->doAdmin('admin');

                // publicos
                switch ($action) {
                    case 'open': // se ha abierto para recibir proyectos
                        $log->populate($call->name, '/call/'.$call->id, Text::html('feed-new_call-opened'), $call->logo);
                        $log->doPublic();
                    break;
                    case 'publish': // ha iniciado la campaña
                        $log->populate($call->name, '/call/'.$call->id, Text::html('feed-new_call-published'), $call->logo);
                        $log->doPublic();
                    break;
                }
                unset($log);

                throw new Redirection('/admin/calls/list');
            }

            if ($action == 'add') {
                $callers = Model\User::getCallers();

                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'calls',
                        'file' => 'add',
                        'callers' => $callers
                    )
                );
            }

            if ($action == 'projects') {
                if (empty($call)) {
                    throw new Redirection('/admin/calls/list');
                }
                $projects   = Model\Call\Project::get($call->id, null, true);
                $status     = Model\Project::status();

                // los available son los que aparecen en el discover/call pero tambien los que estan en esdicion
                $available  = Model\Call\Project::getAvailable($call->id);


                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'calls',
                        'file' => 'projects',
                        'call' => $call,
                        'projects' => $projects,
                        'available' => $available,
                        'status' => $status
                    )
                );
            }

            if ($action == 'admins') {
                if (isset($_GET['op']) && isset($_GET['user']) && in_array($_GET['op'], array('assign', 'unassign'))) {
                    if ($call->$_GET['op']($_GET['user'])) {
                        // ok
                    } else {
                        Message::Error(implode('<br />', $errors));
                    }
                }

                $call->admins = Model\Call::getAdmins($call->id);
                $admins = Model\User::getAdmins();

                return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'calls',
                                    'file' => 'admins',
                                    'action' => 'admins',
                                    'call' => $call,
                                    'admins' => $admins
                                )
                );
            }            

            // si es admin, solo las suyas
            if (isset($_SESSION['user']->roles['admin'])) {
                $filters['admin'] = $_SESSION['user']->id;
            }
            
            $calls = Model\Call::getList($filters);
            $status = Model\Call::status();
            $categories = Model\Call\Category::getAll();
            $callers = Model\User::getCallers();
            $admins = Model\Call::getAdmins();
            $orders = array(
                'name' => 'Nombre',
                'updated' => 'Apertura postulacion'
            );

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'calls',
                    'file' => 'list',
                    'calls' => $calls,
                    'filters' => $filters,
                    'status' => $status,
                    'categories' => $categories,
                    'callers' => $callers,
                    'admins' => $admins,
                    'orders' => $orders
                )
            );
            
        }

    }

}
