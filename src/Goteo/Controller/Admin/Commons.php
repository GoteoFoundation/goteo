<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Commons {

        /*
         * Usa 'ultra-secret-ws' para procesar las operaciones de cumplir/descumplir y cambiar url
         * ya no usa acciones de fulfill y unfulfill
         */
        public static function process ($action = 'list', $id = null, $filters = array()) {

            // variables comunes
            $status = array(
                'nok' => 'Pendiente',
                'ok'  => 'Cumplido'

            );
            $icons = Model\Icon::getAll('social');
            foreach ($icons as $key => $icon) {
                $icons[$key] = $icon->name;
            }
            $licenses = Model\License::getList();
            $statuses = Model\Project::status();
            $projStatus = array(4=>$statuses[4], 5=>$statuses[5]);

            // Acciones sobre proyecto
            if (!empty($id)) {

                // datos del proyecto
                $project = Model\Project::getMini($id);

                switch ($action) {
                    case 'info':
                        // ver toda la información de contacto para un proyecto
                        $contact = Model\Project::getContact($id);

                        return new View(
                            'admin/index.html.php',
                            array(
                                'folder' => 'commons',
                                'file' => 'info',
                                'project'=>$project,
                                'contact' => $contact,
                                'status' => $statuses
                            )
                        );

                        break;

                    case 'fulfill':
                        // marcar que el proyecto ha cumplido con los retornos colectivos
                        $errors = array();
                        if ($project->satisfied($errors)) {
                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('Cambio estado de un proyecto desde retornos colectivos', '/admin/projects',
                                \vsprintf('El admin/revisor %s ha pasado el proyecto %s al estado <span class="red">Retorno cumplido</span>', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('project', $project->name, $project->id)
                                )));
                            $log->doAdmin('admin');

                        } else {
                            Message::error(implode('<br />', $errors));
                        }

                        throw new Redirection('/admin/commons');
                        break;

                    case 'view':
                        // ver los retornos de un proyecto
                        $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', LANG);

                        return new View(
                            'admin/index.html.php',
                            array(
                                'folder' => 'commons',
                                'file' => 'view',
                                'project'=>$project,
                                'statuses' => $statuses,
                                'status' => $status,
                                'icons' => $icons,
                                'licenses' => $licenses
                            )
                        );
                        break;

                    // acciones sobre retorno
                    case 'add':
                    case 'edit':
                        // editar un retorno colectivo
                        if (empty($_GET['reward_id'])) {
                            $reward = new Model\Project\Reward;
                            $reward->id = '';
                            $reward->project = $id;
                            $reward->bonus = 1;
                        } else {
                            $reward = Model\Project\Reward::get($_GET['reward_id']);
                        }

                        $stypes = Model\Project\Reward::icons('social');

                        // si llega post -> procesamos el formulario
                        if (isset($_POST['social_reward-' . $reward->id . '-reward'])) {
                            $errors = array();

                            $reward->reward = $_POST['social_reward-' . $reward->id . '-reward'];
                            $reward->description = $_POST['social_reward-' . $reward->id . '-description'];
                            $reward->icon = $_POST['social_reward-' . $reward->id . '-icon'];
                            if ($reward->icon == 'other') {
                                $reward->other = $_POST['social_reward-' . $reward->id . '-other'];
                            }
                            $reward->license = $_POST['social_reward-' . $reward->id . '-' . $reward->icon . '-license'];
                            $reward->icon_name = $icons[$reward->icon];

                            if ($reward->save($errors)) {
                                throw new Redirection('/admin/commons/view/'.$id);
                            } else {
                                Message::error(implode('<br />', $errors));
                            }
                        }



                        return new View(
                            'admin/index.html.php',
                            array(
                                'folder' => 'commons',
                                'file' => 'edit',
                                'action' => 'edit',
                                'project' => $project,
                                'reward' => $reward,
                                'statuses' => $statuses,
                                'status' => $status,
                                'stypes' => $stypes,
                                'icons' => $icons,
                                'licenses' => $licenses,
                                'path' => '/admin/commons'
                            )
                        );
                        break;

                    case 'delete':
                        // eliminar retorno
                        if (isset($_GET['reward_id'])) {
                            $errors = array();
                            $reward = Model\Project\Reward::get($_GET['reward_id']);

                            if(!$reward->remove($errors)) {
                                Message::error(implode('<br />', $errors));
                            }
                        }
                        throw new Redirection('/admin/commons/view/'.$id);
                        break;
                }

            }

            if (!empty($filters['projStatus'])) {
                $projects = Model\Project::getMiniList(array('status'=>$filters['projStatus'], 'proj_name'=>$filters['project'], 'order'=>'success'), $_SESSION['admin_node']);
            } else {
                $projects = Model\Project::getMiniList(array('multistatus'=>"4,5", 'proj_name'=>$filters['project'], 'order'=>'success'), $_SESSION['admin_node']);
            }

            foreach ($projects as $kay=>&$project) {
                $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', LANG);
                $cumplidos = 0;
                foreach ($project->social_rewards as $ret) {
                    if ($ret->fulsocial) {
                        $cumplidos++;
                    }
                }
                $project->cumplidos = $cumplidos;
            }

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'commons',
                    'file' => 'list',
                    'projects'=>$projects,
                    'filters' => $filters,
                    'statuses' => $statuses,
                    'projStatus' => $projStatus,
                    'status' => $status,
                    'icons' => $icons,
                    'autocomplete' => true
                )
            );

        }

    }

}
