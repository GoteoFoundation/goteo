<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Feed,
        Goteo\Application\Message,
        Goteo\Library\Mail,
		Goteo\Library\Template,
        Goteo\Model,
        Goteo\Controller\Cron\Send;

    class Projects {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $log_text = null;
            $errors = array();

            // multiples usos
            $nodes = Model\Node::getList();

            if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])) {

                $projData = Model\Project::get($_POST['id']);
                if (empty($projData->id)) {
                    Message::Error('El proyecto '.$_POST['id'].' no existe');
                    throw new Redirection('/admin/projects');
                }

                if (isset($_POST['save-dates'])) {
                    $fields = array(
                        'created',
                        'updated',
                        'published',
                        'success',
                        'closed',
                        'passed'
                        );

                    $set = '';
                    $values = array(':id' => $projData->id);

                    foreach ($fields as $field) {
                        if ($set != '') $set .= ", ";
                        $set .= "`$field` = :$field ";
                        if (empty($_POST[$field]) || $_POST[$field] == '0000-00-00')
                            $_POST[$field] = null;

                        $values[":$field"] = $_POST[$field];
                    }

                    try {
                        $sql = "UPDATE project SET " . $set . " WHERE id = :id";
                        if (Model\Project::query($sql, $values)) {
                            $log_text = 'El admin %s ha <span class="red">tocado las fechas</span> del proyecto '.$projData->name.' %s';
                        } else {
                            $log_text = 'Al admin %s le ha <span class="red">fallado al tocar las fechas</span> del proyecto '.$projData->name.' %s';
                        }
                    } catch(\PDOException $e) {
                        Message::Error("Ha fallado! " . $e->getMessage());
                    }
                } elseif (isset($_POST['save-accounts'])) {

                    $accounts = Model\Project\Account::get($projData->id);
                    $accounts->bank = $_POST['bank'];
                    $accounts->bank_owner = $_POST['bank_owner'];
                    $accounts->paypal = $_POST['paypal'];
                    $accounts->paypal_owner = $_POST['paypal_owner'];
                    if ($accounts->save($errors)) {
                        Message::Info('Se han actualizado las cuentas del proyecto '.$projData->name);
                    } else {
                        Message::Error(implode('<br />', $errors));
                    }

                } elseif (isset($_POST['save-rounds'])) {

                    $project_conf = Model\Project\Conf::get($projData->id);
                    $project_conf->days_round1 = (!empty($_POST['round1'])) ? $_POST['round1'] : 40;
                    $project_conf->days_round2 = (!empty($_POST['round2'])) ? $_POST['round2'] : 40;
                    $project_conf->one_round = isset($_POST['oneround']);
                    // si es ronda única, los días de segunda deben grabarse a cero (para que el getActive no lo cuente para segunda)
                    if ($project_conf->one_round) $project_conf->days_round2 = 0;
                    if ($project_conf->save($errors)) {
                        Message::Info('Se han actualizado los días de campaña del proyecto ' . $projData->name);
                    } else {
                        Message::Error(implode('<br />', $errors));
                    }
                } elseif (isset($_POST['save-node'])) {

                    if (!isset($nodes[$_POST['node']])) {
                        Message::Error('El nodo '.$_POST['node'].' no existe! ');
                    } else {

                        $values = array(':id' => $projData->id, ':node' => $_POST['node']);
                        $values2 = array(':id' => $projData->owner, ':node' => $_POST['node']);
                        try {
                            $sql = "UPDATE project SET node = :node WHERE id = :id";
                            $sql2 = "UPDATE user SET node = :node WHERE id = :id";
                            if (Model\Project::query($sql, $values)) {
                                $log_text = 'El admin %s ha <span class="red">movido al nodo '.$nodes[$_POST['node']].'</span> el proyecto '.$projData->name.' %s';
                                if (Model\User::query($sql2, $values2)) {
                                    $log_text .= ', tambien se ha movido al impulsor';
                                } else {
                                    $log_text .= ', pero no se ha movido al impulsor';
                                }
                            } else {
                                $log_text = 'Al admin %s le ha <span class="red">fallado al mover al nodo '.$nodes[$_POST['node']].'</span> el proyecto '.$projData->name.' %s';
                            }
                        } catch(\PDOException $e) {
                            Message::Error("Ha fallado! " . $e->getMessage());
                        }

                    }

                } elseif ($action == 'images') {

                    $todook = true;

                    /*
                     *  Ya no movemos con flechas, cambiamos directamente el número de orden
                    if (!empty($_POST['move'])) {
                        $direction = $_POST['action'];
                        Model\Project\Image::$direction($id, $_POST['move'], $_POST['section']);
                    }
                    */

                    foreach ($_POST as $key=>$value) {
                        $parts = explode('_', $key);

                        if ($parts[1] == 'image' && in_array($parts[0], array('section', 'url', 'order'))) {
                            if (Model\Project\Image::updateImage($id, $parts[2], $parts[0], $value)) {
                                // OK
                            } else {
                                $todook = false;
                                Message::Error("No se ha podido actualizar campo {$parts[0]} al valor {$value}");
                            }
                        }
                    }

                    if ($todook) {
                        Message::Info('Se han actualizado los datos');
                        // recalculamos las galerias e imagen

                        // getGalleries en Project\Image  procesa todas las secciones
                        $galleries = Model\Project\Image::getGalleries($id);
                        Model\Project\Image::setImage($id, $galleries['']);
                    }

                    throw new Redirection('/admin/projects/images/'.$id);

                } elseif ($action == 'rebase') {

                    $todook = true;

                    if ($_POST['proceed'] == 'rebase' && !empty($_POST['newid'])) {

                        // verificamos que el nuevo id sea
                        $newid = Model\Project::idealiza($_POST['newid']);

                        // pimero miramos que no hay otro proyecto con esa id
                        $test = Model\Project::getMini($newid);
                        if ($test->id == $newid) {
                            Message::Error('Ya hay un proyecto con ese Id.');
                            throw new Redirection('/admin/projects/rebase/'.$id);
                        }

                        if ($projData->status >= 3 && $_POST['force'] != 1) {
                            Message::Error('El proyecto no está ni en Edición ni en Revisión, no se modifica nada.');
                            throw new Redirection('/admin/projects/rebase/'.$id);
                        }

                        if ($projData->rebase($newid)) {
                            Message::Info('Verificar el proyecto -> <a href="'.SITE_URL.'/project/'.$newid.'" target="_blank">'.$projData->name.'</a>');
                            throw new Redirection('/admin/projects');
                        } else {
                            Message::Info('Ha fallado algo en el rebase, verificar el proyecto -> <a href="'.SITE_URL.'/project/'.$projData->id.'" target="_blank">'.$projData->name.' ('.$id.')</a>');
                            throw new Redirection('/admin/projects/rebase/'.$id);
                        }


                    }

                } elseif (isset($_POST['assign-to-call'])) {

                    $values = array(':project' => $projData->id, ':call' => $_POST['call']);
                    try {
                        $sql = "REPLACE INTO call_project (`call`, `project`) VALUES (:call, :project)";
                        if (Model\Project::query($sql, $values)) {
                            $log_text = 'El admin %s ha <span class="red">asignado a la convocatoria call/'.$_POST['call'].'</span> el proyecto '.$projData->name.' %s';
                        } else {
                            $log_text = 'Al admin %s le ha <span class="red">fallado al asignar a la convocatoria call/'.$_POST['call'].'</span> el proyecto '.$projData->name.' %s';
                        }
                        Model\Call\Project::addOneApplied($_POST['call']);

                    } catch(\PDOException $e) {
                        Message::Error("Ha fallado! " . $e->getMessage());
                    }

                }
            }

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            $admins = Model\User::getAdmins();

            if (isset($id)) {
                $project = Model\Project::get($id);
                $project->consultants = Model\Project::getConsultants($project->id);
            }
            switch ($action) {
                case 'review':
                    // pasar un proyecto a revision
                    if ($project->ready($errors)) {
                        $redir = '/admin/reviews/add/'.$project->id;
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Revision</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Revision</span>';
                    }
                    break;
                case 'publish':
                    // poner un proyecto en campaña
                    if ($project->publish($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">en Campaña</span>';
                        Send::toOwner('tip_0', $project);
                        Send::toConsultants('tip_0', $project);
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">en Campaña</span>';
                    }
                    break;
                case 'cancel':
                    // descartar un proyecto por malo

                    // Asignar como asesor al admin que lo ha descartado
                    if ($_SESSION['user']->id != 'root') {
                        if ((!isset($project->consultants[$_SESSION['user']->id])) && ($project->assignConsultant($_SESSION['user']->id, $errors))) {
                            $msg = 'Se ha asignado tu usuario (' . $_SESSION['user']->id . ') como asesor del proyecto "' . $project->id . '"';
                            Message::Info($msg);
                        }
                    }

                    if ($project->cancel($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Descartado</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Descartado</span>';
                    }
                    break;
                case 'enable':
                    // si no esta en edicion, recuperarlo

                    // Si el proyecto no tiene asesor, asignar al admin que lo ha pasado a negociación
                    // No funciona con el usuario root
                    if ((empty($project->consultants)) && $_SESSION['user']->id != 'root') {
                        if ($project->assignConsultant($_SESSION['user']->id, $errors)) {
                            $msg = 'Se ha asignado tu usuario (' . $_SESSION['user']->id . ') como asesor del proyecto "' . $project->id . '"';
                            Message::Info($msg);
                        }
                    }

                    if ($project->enable($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Edicion</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Edicion</span>';
                    }
                    break;
                case 'fulfill':
                    // marcar que el proyecto ha cumplido con los retornos colectivos
                    if ($project->satisfied($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Retorno cumplido</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Retorno cumplido</span>';
                    }
                    break;
                case 'unfulfill':
                    // dar un proyecto por financiado manualmente
                    if ($project->rollback($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Financiado</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Financiado</span>';
                    }
                    break;
            }

            if ($action == 'report') {
                // informe financiero
                // Datos para el informe de transacciones correctas
                $Data = Model\Invest::getReportData($project->id, $project->status, $project->round, $project->passed);
                $account = Model\Project\Account::get($project->id);

                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'report',
                        'project' => $project,
                        'account' => $account,
                        'Data' => $Data
                    )
                );
            }

            if ($action == 'dates') {
                // cambiar fechas
                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'dates',
                        'project' => $project
                    )
                );
            }

            if ($action == 'accounts') {

                $accounts = Model\Project\Account::get($project->id);

                // cambiar fechas
                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'accounts',
                        'project' => $project,
                        'accounts' => $accounts
                    )
                );
            }

            if ($action == 'conf') {

                $conf = Model\Project\Conf::get($project->id);

                // cambiar fechas
                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'conf',
                        'project' => $project,
                        'conf' => $conf
                    )
                );
            }

            if ($action == 'images') {

                // imagenes
                $images = array();

                // secciones
                $sections = Model\Project\Image::sections();
                foreach ($sections as $sec=>$secName) {
                    $secImages = Model\Project\Image::get($project->id, $sec);
                    foreach ($secImages as $img) {
                        $images[$sec][] = $img;
                    }
                }

                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'images',
                        'project' => $project,
                        'images' => $images,
                        'sections' => $sections
                    )
                );
            }

            if ($action == 'move') {
                // cambiar el nodo
                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'move',
                        'project' => $project,
                        'nodes' => $nodes
                    )
                );
            }

            if ($action == 'open_tags') {
                // cambiar la agrupacion

                if (isset($_GET['op']) && isset($_GET['open_tag']) &&
                    (($_GET['op'] == 'assignOpen_tag') || ($_GET['op'] == 'unassignOpen_tag'))) {
                    if ($project->$_GET['op']($_GET['open_tag'])) {
                        // ok
                    } else {
                        Message::Error(implode('<br />', $errors));
                    }
                }

                $project->open_tags = Model\Project::getOpen_tags($project->id);
                // disponibles
                $open_all_tags = Model\Project\OpenTag::getAll();
                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'open_tags',
                        'project' => $project,
                        'open_tags' =>$open_all_tags
                    )
                );
            }


            if ($action == 'rebase') {
                // cambiar la id
                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'rebase',
                        'project' => $project
                    )
                );
            }

            if ($action == 'consultants') {
                // cambiar el asesor
                if (isset($_GET['op']) && isset($_GET['user']) &&
                    (($_GET['op'] == 'assignConsultant' && Model\User::isAdmin($_GET['user'])) || ($_GET['op'] == 'unassignConsultant'))) {
                    if ($project->$_GET['op']($_GET['user'])) {
                        // ok
                    } else {
                        Message::Error(implode('<br />', $errors));
                    }
                }

                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'consultants',
                        'project' => $project,
                        'admins' => $admins
                    )
                );
            }

            if ($action == 'assign') {
                // asignar a una convocatoria solo si
                //   está en edición a campaña
                //   y no está asignado
                if (!in_array($project->status, array('1', '2', '3')) || $project->called) {
                    Message::Error("No se puede asignar en este estado o ya esta asignado a una convocatoria");
                    throw new Redirection('/admin/projects/list');
                }
                // disponibles
                $available = Model\Call::getAvailable();

                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'assign',
                        'project' => $project,
                        'available' => $available
                    )
                );
            }


            // Rechazo express
            if ($action == 'reject') {
                if (empty($project)) {
                    Message::Error('No hay proyecto sobre el que operar');
                } else {

                    //  idioma de preferencia
                    $prefer = Model\User::getPreferences($project->user->id);
                    $comlang = !empty($prefer->comlang) ? $prefer->comlang : $project->user->lang;

                    // Obtenemos la plantilla para asunto y contenido
                    $template = Template::get(40, $comlang);
                    // Sustituimos los datos
                    $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                    $search  = array('%USERNAME%', '%PROJECTNAME%');
                    $replace = array($project->user->name, $project->name);
                    $content = \str_replace($search, $replace, $template->text);
                    // iniciamos mail
                    $mailHandler = new Mail();
                    $mailHandler->to = $project->user->email;
                    $mailHandler->toName = $project->user->name;
                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    if ($mailHandler->send()) {
                        Message::Info('Se ha enviado un email a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
                    } else {
                        Message::Error('Ha fallado al enviar el mail a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
                    }
                    unset($mailHandler);

                    // Asignar como asesor al admin que lo ha rechazado
                    if ($_SESSION['user']->id != 'root') {
                        if ((!isset($project->consultants[$_SESSION['user']->id])) && ($project->assignConsultant($_SESSION['user']->id, $errors))) {
                            $msg = 'Se ha asignado tu usuario (' . $_SESSION['user']->id . ') como asesor del proyecto "' . $project->id . '"';
                            Message::Info($msg);
                        }
                    }

                    $project->cancel();
                }

                throw new Redirection('/admin/projects/list');
            }


            // cortar el grifo
            if ($action == 'noinvest') {
                if (Model\Project\Conf::closeInvest($project->id)) {
                    $log_text = 'El admin %s ha <span class="red">cerrado el grifo</span> al proyecto %s';
                } else {
                    Message::Error('Ha fallado <strong>cerrar el grifo</strong>');
                    $log_text = 'Al admin %s le ha <span class="red">fallado al cerrar el grifo</span> al proyecto %s';
                }
            }

            // abrir el grifo
            if ($action == 'openinvest') {
                if (Model\Project\Conf::openInvest($project->id)) {
                    $log_text = 'El admin %s ha <span class="red">abierto el grifo</span> al proyecto %s';
                } else {
                    Message::Error('Ha fallado <strong>abrir el grifo</strong>');
                    $log_text = 'Al admin %s le ha <span class="red">fallado al abrir el grifo</span> al proyecto %s';
                }
            }

            // Vigilar
            if ($action == 'watch') {
                if (Model\Project\Conf::watch($project->id)) {
                    $log_text = 'El admin %s ha empezado a <span class="red">vigilar</span> el proyecto %s';
                } else {
                    Message::Error('Ha fallado <strong>empezar a vigilar</strong>');
                    $log_text = 'Al admin %s le ha <span class="red">fallado la vigilancia</span> el proyecto %s';
                }
            }

            // Dejar de vigilar
            if ($action == 'unwatch') {
                if (Model\Project\Conf::unwatch($project->id)) {
                    $log_text = 'El admin %s ha <span class="red">dejado de vigilar</span> el proyecto %s';
                } else {
                    Message::Error('Ha fallado <strong>dejar de vigilar</strong>');
                    $log_text = 'Al admin %s le ha <span class="red">fallado dejar de vigilar</span> el proyecto %s';
                }
            }

            // Finalizar campaña
            if ($action == 'finish') {

                if (Model\Project\Conf::finish($project)) {
                    $log_text = 'El admin %s ha <span class="red">finalizado la campaña</span> del proyecto %s';
                } else {
                    Message::Error('Ha fallado <strong>finalizar campaña</strong>');
                    $log_text = 'Al admin %s le ha <span class="red">fallado finalizar campaña</span> del proyecto %s';
                }
            }


            // Feed
            if (isset($log_text)) {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($project->id);
                $log->populate('Acción sobre un proyecto desde el admin', '/admin/projects',
                    \vsprintf($log_text, array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('project', $project->name, $project->id)
                    )));
                $log->doAdmin('admin');

                Message::Info($log->html);
                if (!empty($errors)) {
                    Message::Error(implode('<br />', $errors));
                }

                if ($action == 'publish') {
                    // si es publicado, hay un evento publico
                    $log->populate($project->name, '/project/'.$project->id, Text::html('feed-new_project'), $project->image);
                    $log->doPublic('projects');
                }

                unset($log);

                if (empty($redir)) {
                    throw new Redirection('/admin/projects/list');
                } else {
                    throw new Redirection($redir);
                }
            }

            if (!empty($filters['filtered'])) {
                $page = (is_numeric($_GET['page'])) ? $_GET['page'] : 1;
                $items_per_page = 10;

                $projects = Model\Project::getList($filters,
                                                    $_SESSION['admin_node'],
                                                    $items_per_page,
                                                    $pages,
                                                    $page
                            );
            } else {
                $projects = array();
            }

            $status = Model\Project::status();
            $categories = Model\Project\Category::getAll();
            $contracts = Model\Contract::getProjects();
            $calls = Model\Call::getAvailable(true);
            $open_tags = Model\Project\OpenTag::getAll();
            // la lista de nodos la hemos cargado arriba
            $orders = array(
                'name' => 'Nombre',
                'updated' => 'Enviado a revision'
            );

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'projects',
                    'file' => 'list',
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
                    'categories' => $categories,
                    'contracts' => $contracts,
                    'admins' => $admins,
                    'calls' => $calls,
                    'nodes' => $nodes,
                    'open_tags' => $open_tags,
                    'orders' => $orders,
                    'pages' => $pages,
                    'currentPage' => $page
                )
            );

        }

    }

}
