<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Model;

    class Rewards {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            switch ($action)  {
                case 'fulfill':
                    $sql = "UPDATE reward SET fulsocial = 1 WHERE type= 'social' AND id = ?";
                    Model\Project\Reward::query($sql, array($id));
                    break;
                case 'unfill':
                    $sql = "UPDATE reward SET fulsocial = 0 WHERE id = ?";
                    Model\Project\Reward::query($sql, array($id));
                    break;
            }

            $projects = Model\Project::published('success');

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

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'rewards',
                    'projects'=>$projects,
                    'filters' => $filters,
                    'status' => $status,
                    'icons' => $icons,
                    'errors' => $errors
                )
            );

        }

    }

}
