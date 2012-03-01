<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Model;

    class Projects {

        public static function process ($action = 'list', $id = null) {
            

            $filters = array();
            $fields = array('filtered', 'status', 'category', 'owner', 'name', 'order');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            if (!isset($filters['status'])) $filters['status'] = -1;

            $errors = array();


            if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])) {

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
                    $values = array(':id' => $_POST['id']);

                    foreach ($fields as $field) {
                        if ($set != '') $set .= ", ";
                        $set .= "`$field` = :$field ";
                        if (empty($_POST[$field]) || $_POST[$field] == '0000-00-00')
                            $_POST[$field] = null;

                        $values[":$field"] = $_POST[$field];
                    }

                    if ($set == '') {
                        break;
                    }

                    try {
                        $sql = "UPDATE project SET " . $set . " WHERE id = :id";
                        if (Model\Project::query($sql, $values)) {
                            $log_text = 'El admin %s ha <span class="red">tocado las fechas</span> del proyecto %s';
                        } else {
                            $log_text = 'Al admin %s le ha <span class="red">fallado al tocar las fechas</span> del proyecto %s';
                        }
                    } catch(\PDOException $e) {
                        $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                    }
                } elseif (isset($_POST['save-accounts'])) {

                    $accounts = Model\Project\Account::get($_POST['id']);
                    $accounts->bank = $_POST['bank'];
                    $accounts->paypal = $_POST['paypal'];
                    if ($accounts->save($errors)) {
                        $errors[] = 'Se han actualizado las cuentas del proyecto '.$_POST['id'];
                    }

                }

            }

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            if (isset($id)) {
                $project = Model\Project::get($id);
            }
            switch ($action) {
                case 'review':
                    // pasar un proyecto a revision
                    if ($project->ready($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Revisión</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Revisión</span>';
                    }
                    break;
                case 'publish':
                    // poner un proyecto en campaña
                    if ($project->publish($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">en Campaña</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">en Campaña</span>';
                    }
                    break;
                case 'cancel':
                    // descartar un proyecto por malo
                    if ($project->cancel($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Descartado</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Descartado</span>';
                    }
                    break;
                case 'enable':
                    // si no está en edición, recuperarlo
                    if ($project->enable($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Edición</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Edición</span>';
                    }
                    break;
                case 'complete':
                    // dar un proyecto por financiado manualmente
                    if ($project->succeed($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Financiado</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Financiado</span>';
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
            }

            if (isset($log_text)) {
                // Evento Feed
                $log = new Feed();
                $log->populate('Cambio estado/fechas de un proyecto desde el admin', '/admin/projects',
                    \vsprintf($log_text, array(
                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                    Feed::item('project', $project->name, $project->id)
                )));
                $log->doAdmin('admin');

                Message::Info($log->html);

                if ($action == 'publish') {
                    // si es publicado, hay un evento público
                    $log->populate($project->name, '/project/'.$project->id, Text::html('feed-new_project'), $project->gallery[0]->id);
                    $log->setTarget($project->id);
                    $log->doPublic('projects');
                }

                unset($log);

                throw new Redirection('/admin/projects/list');
            }

            if ($action == 'dates') {
                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'dates',
                        'project' => $project,
                        'filters' => $filters,
                        'errors' => $errors
                    )
                );
            }

            if ($action == 'accounts') {

                $accounts = Model\Project\Account::get($project->id);

                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'accounts',
                        'project' => $project,
                        'accounts' => $accounts,
                        'filters' => $filters,
                        'errors' => $errors
                    )
                );
            }


            $projects = Model\Project::getList($filters);
            $status = Model\Project::status();
            $categories = Model\Project\Category::getAll();
            $owners = Model\User::getOwners();
            $orders = array(
                'name' => 'Nombre',
                'updated' => 'Enviado a revision'
            );

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'projects',
                    'file' => 'list',
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
                    'categories' => $categories,
                    'owners' => $owners,
                    'orders' => $orders,
                    'errors' => $errors
                )
            );
            
        }

    }

}
