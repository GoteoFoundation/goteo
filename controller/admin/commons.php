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

            switch ($action)  {
                case 'fulfill':
                    $sql = "UPDATE reward SET fulsocial = 1 WHERE type= 'social' AND id = ?";
                    if (Model\Project\Reward::query($sql, array($id))) {
                        Message::Info('El retorno se ha marcado como cumplido');
                    } else {
                        Message::Error('Ha fallado al marcar el retorno');
                    }
                    throw new Redirection('/admin/commons');
                    break;
                case 'unfill':
                    $sql = "UPDATE reward SET fulsocial = 0 WHERE id = ?";
                    if (Model\Project\Reward::query($sql, array($id))) {
                        Message::Info('El retorno se ha desmarcado, ahora está pendiente');
                    } else {
                        message::Error('Ha fallado al desmarcar');
                    }
                    throw new Redirection('/admin/commons');
                    break;
            }

            if (!empty($filters['projStatus'])) {
                $projects = Model\Project::getList(array('status'=>$filters['projStatus']));
            } else {
                $projects = Model\Project::published('fulfilled');
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
