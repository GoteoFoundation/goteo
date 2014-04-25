<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
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
            $statuses = Model\Project::status();
            $projStatus = array(4=>$statuses[4], 5=>$statuses[5]);


            // ver toda la información de contacto para un proyecto
            if ($action == 'info' && !empty($id)) {
                $project = Model\Project::getMedium($id);
                $contact = Model\Project::getContact($id);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'commons',
                        'file' => 'info',
                        'project'=>$project,
                        'contact' => $contact,
                        'status' => $statuses
                    )
                );
            }

            if ($action == 'view' && !empty($id)) {
                $project = Model\Project::getMini($id);
                $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', LANG);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'commons',
                        'file' => 'view',
                        'project'=>$project,
                        'filters' => $filters,
                        'statuses' => $statuses,
                        'status' => $status,
                        'icons' => $icons
                    )
                );
            }

            if ($action == 'fulfill' && !empty($id)) {
                $errors = array();
                $project = Model\Project::getMedium($id);
                // marcar que el proyecto ha cumplido con los retornos colectivos
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
                    Message::Error(implode('<br />', $errors));
                }

                throw new Redirection('/admin/commons');
            }



            if (!empty($filters['projStatus'])) {
                $projects = Model\Project::getMiniList(array('status'=>$filters['projStatus'], 'proj_id'=>$filters['project'], 'order'=>'success'), $_SESSION['admin_node']);
            } else {
                $projects = Model\Project::getMiniList(array('multistatus'=>"4,5", 'proj_id'=>$filters['project'], 'order'=>'success'), $_SESSION['admin_node']);
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
                'view/admin/index.html.php',
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
