<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Rewards {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            switch ($action)  {
                case 'fulfill':
                    $sql = "UPDATE reward SET fulsocial = 1 WHERE type= 'social' AND id = ?";
                    if (Model\Project\Reward::query($sql, array($id))) {
                        Message::Info('El retorno se ha marcado como cumplido');
                    } else {
                        Message::Error('Ha fallado al marcar el retorno');
                    }
                    break;
                case 'unfill':
                    $sql = "UPDATE reward SET fulsocial = 0 WHERE id = ?";
                    if (Model\Project\Reward::query($sql, array($id))) {
                        Message::Info('El retorno se ha desmarcado, ahora está pendiente');
                    } else {
                        message::Error('Ha fallado al desmarcar');
                    }
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
                    'icons' => $icons
                )
            );

        }

    }

}
