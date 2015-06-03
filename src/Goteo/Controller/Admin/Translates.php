<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Library\Mail,
		Goteo\Library\Template,
		Goteo\Application\Message,
        Goteo\Model;

    class Translates {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            $errors  = array();

            switch ($action) {
                case 'add':
                    // proyectos que están más allá de edición y con traducción deshabilitada
                    $current = (isset($_GET['project'])) ? $_GET['project'] : null;
                    $availables = Model\User\Translate::getAvailables('project', $_SESSION['admin_node'], $current);
                    if (empty($availables)) {
                        Message::Error('No hay más proyectos disponibles para traducir');
                        throw new Redirection('/admin/translates');
                    }

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
                                $what = 'Asignado';
                                if ($assignation->save($errors)) {
                                    Message::Info('Traducción asignada correctamente');
                                    throw new Redirection('/admin/translates/edit/'.$project->id);
                                } else {
                                    Message::Error('La traducción no se ha asignado correctamente<br />'.implode(', ', $errors));
                                }
                                break;
                            case 'unassign': // se la quitamos
                                $what = 'Desasignado';
                                if ($assignation->remove($errors)) {
                                    Message::Info('Traducción desasignada correctamente');
                                    throw new Redirection('/admin/translates/edit/'.$project->id);
                            } else {
                                    Message::Error('No se ha podido desasignar la traduccion.<br />'.implode(', ', $errors));
                                }
                                break;
                        }

                        if (empty($errors)) {
                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($userData->id, 'user');
                            $log->populate($what . ' traduccion (admin)', '/admin/translates',
                                \vsprintf('El admin %s ha %s a %s la traducción del proyecto %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', $what),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('project', $project->name, $project->id)
                            )));
                            $log->doAdmin('admin');
                            unset($log);
                        }

                        $action = 'edit';
                    }
                    // fin asignar o desasignar

                    // añadir o actualizar
                    // se guarda el idioma original y si la traducción está abierta o cerrada
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        if (empty($id)) {
                            Message::Error('Hemos perdido de vista el proyecto');
                            throw new Redirection('/admin/translates');
                        }

                        $values = array(':id'=>$id);

                        // si nos cambian el idioma del proyecto
                        if (isset($_POST['lang']) && $_POST['lang'] != $project->lang) {
                            $new_lang = $_POST['lang'];
                            $set = ', lang = :lang';
                            $values[':lang'] = $new_lang;
                        } else {
                            $new_lang = null;
                            $set = '';
                        }

                        // ponemos los datos que llegan
                        $sql = "UPDATE project SET translate = 1{$set} WHERE id = :id";
                        if (Model\Project::query($sql, $values)) {

                            // si no existe ya registro project_lang para ese idioma
                            if (!empty($new_lang)) {
                                static::autoTranslate($id, $new_lang, $errors);
                                if (!empty($errors)) {
                                    Message::Error(implode('<br />', $errors));
                                }
                            }

                            if ($action == 'add') {
                                Message::Info('El proyecto '.$project->name.' se ha habilitado para traducir');

                                // Evento Feed
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('proyecto habilitado para traducirse (admin)', '/admin/translates',
                                    \vsprintf('El admin %s ha %s la traducción del proyecto %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('relevant', 'Habilitado'),
                                        Feed::item('project', $project->name, $project->id)
                                    )));
                                $log->doAdmin('admin');
                                unset($log);

                                throw new Redirection('/admin/translates/edit/'.$project->id);

                            } else {
                                Message::Info('Datos de traducción actualizados');

                                throw new Redirection('/admin/translates');
                            }

                        } else {
                            Message::Error('Ha fallado al actualizar la traducción del proyecto ' . $project->name);
                        }
                    }

                    if ($action == 'send') {
                        // Informar al autor de que la traduccion está habilitada

                        //  idioma de preferencia
                        $prefer = Model\User::getPreferences($project->user->id);
                        $comlang = !empty($prefer->comlang) ? $prefer->comlang : $project->user->lang;

                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(26, $comlang);
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
                            Message::Info('Se ha enviado un email a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
                        } else {
                            Message::Error('Ha fallado al enviar el mail a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
                        }
                        unset($mailHandler);
                        $action = 'edit';
                    }


                    $project->translators = Model\User\Translate::translators($id);
                    $translators = Model\User::getAll(array('role'=>'translator'));
                    // añadimos al dueño del proyecto en el array de traductores
                    array_unshift($translators, $project->user);


                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'translates',
                            'file'   => 'edit',
                            'action' => $action,
                            'availables' => $availables,
                            'translators' => $translators,
                            'project'=> $project
                        )
                    );

                    break;
                case 'close':
                    // la sentencia aqui mismo
                    // el campo translate del proyecto $id a false
                    $sql = "UPDATE project SET translate = 0 WHERE id = :id";
                    if (Model\Project::query($sql, array(':id'=>$id))) {
                        Message::Info('La traducción del proyecto '.$project->name.' se ha finalizado');

                        Model\Project::query("DELETE FROM user_translate WHERE type = 'project' AND item = :id", array(':id'=>$id));

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('traducción finalizada (admin)', '/admin/translates',
                            \vsprintf('El admin %s ha dado por %s la traducción del proyecto %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Finalizada'),
                                Feed::item('project', $project->name, $project->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    } else {
                        Message::Error('Falló al finalizar la traducción');
                    }
                    break;
            }

            $projects = Model\Project::getTranslates($filters, $node);
            $owners = Model\User::getOwners();
            $translators = Model\User::getAll(array('role'=>'translator'));

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'translates',
                    'file' => 'list',
                    'projects' => $projects,
                    'filters' => $filters,
                    'fields'  => array('owner', 'translator'),
                    'owners' => $owners,
                    'translators' => $translators
                )
            );

        }

        /**
         *
         *  Este metodo graba registros de traducción con los datos actuales del proyecto para el idioma seleccionado
         * usamos  saveLang para cada entidad
         *
         * @param $id  -  Id del proyecto
         * @param $lang
         * @param array $errors
         */
        public static function autoTranslate($id, $lang, &$errors = array()) {

            // cogemos los datos del proyecto
            $project = Model\Project::get($id, null);

            // primero verificamos que no tenga traducido ya ese idioma
            if (Model\Project::isTranslated($id, $lang)) return null;


            // datos del proyecto
            $project->lang_lang = $lang;
            $project->description_lang = $project->description;
            $project->motivation_lang = $project->motivation;
            $project->video_lang = $project->video;
            $project->about_lang = $project->about;
            $project->goal_lang = $project->goal;
            $project->related_lang = $project->related;
            $project->reward_lang = $project->reward;
            $project->keywords_lang = $project->keywords;
            $project->media_lang = $project->media;
            $project->subtitle_lang = $project->subtitle;
            $project->saveLang($errors);

            // costes (cost)
            foreach ($project->costs as $key => $cost) {
                $cost->project = $project->id;
                $cost->lang = $lang;
                $cost->cost_lang = $cost->cost;
                $cost->description_lang = $cost->description;
                $cost->saveLang($errors);
            }

            // recompensas (reward)
            foreach ($project->social_rewards as $k => $reward) {
                $reward->project = $project->id;
                $reward->lang = $lang;
                $reward->reward_lang = $reward->reward;
                $reward->description_lang = $reward->description;
                $reward->other_lang = $reward->other;
                $reward->saveLang($errors);
            }
            foreach ($project->individual_rewards as $k => $reward) {
                $reward->project = $project->id;
                $reward->lang = $lang;
                $reward->reward_lang = $reward->reward;
                $reward->description_lang = $reward->description;
                $reward->other_lang = $reward->other;
                $reward->saveLang($errors);
            }

            // colaboraciones (support)
            foreach ($project->supports as $key => $support) {
                $support->project = $project->id;
                $support->lang = $lang;
                $support->support_lang = $support->support;
                $support->description_lang = $support->description;
                $support->saveLang($errors);

                // mensajes (mesajes) asociados a las colaboraciones
                $msg = Model\Message::get($support->thread);
                $msg->message_lang = "{$support->support_lang}: {$support->description_lang}";
                $msg->lang = $lang;
                $msg->saveLang($errors);
            }

            return (empty($errors));
        }

    }

}
