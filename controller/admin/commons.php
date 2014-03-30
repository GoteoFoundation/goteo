<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Commons {

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


            /*
             * Usa 'ultra-secret-ws' para procesar las operaciones de cumplir/descumplir y cambiar url
             * ya no usa acciones de fulfill y unfulfill
             */
            if ($action == 'view') {
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
            
            if (!empty($filters['projStatus'])) {
                $projects = Model\Project::getList(array('status'=>$filters['projStatus'], 'proj_id'=>$filters['project']), $_SESSION['admin_node']);
            } else {
                $projects = Model\Project::getList(array('multistatus'=>"4,5", 'proj_id'=>$filters['project']), $_SESSION['admin_node']);
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
                    'icons' => $icons
                )
            );

        }

    }

}
