<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Library\Mail,
		Goteo\Library\Template,
        Goteo\Model;

    class Translates {

        public static function process ($action = 'list', $id = null) {

            $filters = array();
            $fields = array('owner', 'translator');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $filter = "?owner={$filters['owner']}&translator={$filters['translator']}";

            $success = array();
            $errors  = array();

            switch ($action) {
                case 'add':
                    // proyectos que están más allá de edición y con traducción deshabilitada
                    $availables = Model\User\Translate::getAvailables();
                case 'edit':
                case 'assign':
                case 'unassign':
                case 'send':

                    // a ver si tenemos proyecto
                    if (empty($id) && !empty($_POST['project'])) {
                        $id = $_POST['project'];
                    }

                    if (!empty($id)) {
                        $project = Model\Project::getMini($id);
                    } elseif ($action != 'add') {
                        Message::Error('No hay proyecto sobre el que operar');
                        throw new Redirection('/admin/translates');
                    }

                    // asignar o desasignar
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $userData = Model\User::getMini($user);

                        $assignation = new Model\User\Translate(array(
                            'item' => $project->id,
                            'type' => 'project',
                            'user' => $user
                        ));

                        switch ($action) {
                            case 'assign': // se la ponemos
                                $assignation->save($errors);
                                $what = 'Asignado';
                                break;
                            case 'unassign': // se la quitamos
                                $assignation->remove($errors);
                                $what = 'Desasignado';
                                break;
                        }

                        if (empty($errors)) {
                            // Evento Feed
                            $log = new Feed();
                            $log->populate($what . ' traduccion (admin)', '/admin/translates',
                                \vsprintf('El admin %s ha %s a %s la traducción del proyecto %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', $what),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('project', $project->name, $project->id)
                            )));
                            $log->setTarget($userData->id, 'user');
                            $log->doAdmin('admin');
                            unset($log);
                        }

                        $action = 'edit';
                    }
                    // fin asignar o desasignar

                    // añadir o actualizar
                    // se guarda el idioma original y si la traducción está abierta o cerrada
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        echo \trace($_POST);

                        // ponemos los datos que llegan
                        $sql = "UPDATE project SET lang = :lang, translate = 1 WHERE id = :id";
                        if (Model\Project::query($sql, array(':lang'=>$_POST['lang'], ':id'=>$id))) {
                            $success[] = ($action == 'add') ? 'El proyecto '.$project->name.' se ha habilitado para traducir' : 'Datos de traducción actualizados';

                            if ($action == 'add') {
                                // Evento Feed
                                $log = new Feed();
                                $log->populate('proyecto habilitado para traducirse (admin)', '/admin/translates',
                                    \vsprintf('El admin %s ha %s la traducción del proyecto %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('relevant', 'Habilitado'),
                                        Feed::item('project', $project->name, $project->id)
                                )));
                                $log->doAdmin('admin');
                                unset($log);

                                $action = 'edit';
                            }
                        } else {
                            $errors[] = 'Ha fallado al habilitar la traducción del proyecto ' . $project->name;
                        }
                    }

                    if ($action == 'send') {
                        // Informar al autor de que la traduccion está habilitada
                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(26);
                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                        $search  = array('%OWNERNAME%', '%PROJECTNAME%', '%SITEURL%');
                        $replace = array($project->user->name, $project->name, SITE_URL);
                        $content = \str_replace($search, $replace, $template->text);
                        // iniciamos mail
                        $mailHandler = new Mail();
                        $mailHandler->to = $project->user->email;
                        $mailHandler->toName = $project->user->name;
                        // blind copy a goteo desactivado durante las verificaciones
            //              $mailHandler->bcc = 'comunicaciones@goteo.org';
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send()) {
                            $success[] = 'Se ha enviado un email a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>';
                        } else {
                            $errors[] = 'Ha fallado informar a <strong>'.$project->user->name.'</strong> de la posibilidad de traducción de su proyecto';
                        }
                        unset($mailHandler);
                        $action = 'edit';
                    }


                    $project->translators = Model\User\Translate::translators($id);
                    $translators = Model\User::getAll(array('role'=>'translator'));
                    // añadimos al dueño del proyecto en el array de traductores
                    array_unshift($translators, $project->user);


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'translates',
                            'file'   => 'edit',
                            'action' => $action,
                            'filters' => $filters,
                            'availables' => $availables,
                            'translators' => $translators,
                            'project'=> $project,
                            'success' => $success,
                            'errors' => $errors
                        )
                    );

                    break;
                case 'close':
                    // la sentencia aqui mismo
                    // el campo translate del proyecto $id a false
                    $sql = "UPDATE project SET translate = 0 WHERE id = :id";
                    if (Model\Project::query($sql, array(':id'=>$id))) {
                        $success[] = 'La traducción del proyecto '.$project->name.' se ha finalizado';

                        Model\Project::query("DELETE FROM user_translate WHERE type = 'project' AND item = :id", array(':id'=>$id));

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('traducción finalizada (admin)', '/admin/translates',
                            \vsprintf('El admin %s ha dado por %s la traducción del proyecto %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Finalizada'),
                                Feed::item('project', $project->name, $project->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    } else {
                        $errors[] = 'Falló al finalizar la traducción';
                    }
                    break;
            }

            $projects = Model\Project::getTranslates($filters);
            $owners = Model\User::getOwners();
            $translators = Model\User::getAll(array('role'=>'translator'));

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'translates',
                    'file' => 'list',
                    'projects' => $projects,
                    'filters' => $filters,
                    'owners' => $owners,
                    'translators' => $translators,
                    'success' => $success,
                    'errors' => $errors
                )
            );
            
        }

    }

}
