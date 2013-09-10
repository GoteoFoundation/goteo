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

            /*
             * Usa 'ultra-secret-ws' para procesar las operaciones de cumplir/descumplir y cambiar url
             * ya no usa acciones de fulfill y unfulfill
             */
            
            if (!empty($filters['projStatus'])) {
                $projects = Model\Project::getList(array('status'=>$filters['projStatus']));
            } else {
                $projects = Model\Project::published('almost-fulfilled');
            }

            foreach ($projects as $kay=>&$project) {
                $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', LANG, $filters['status'], $filters['icon']);
            }

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
