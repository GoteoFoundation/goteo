<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Worth;

    class Support extends \Goteo\Core\Controller {

        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */
        public function index ($project = null) {

            if (empty($_SESSION['user']))
                throw new Redirection ('/user/login?from=' . \rawurlencode('/support/' . $project), Redirection::TEMPORARY);

            if (empty($project))
                throw new Redirection('/project/explore', Redirection::TEMPORARY);

            $content = '';

            $projectData = Model\Project::get($project);

            $viewData = array(
                    'content' => $content,
                    'worthcracy' => Worth::getAll(),
                    'project' => $projectData
                );

            return new View (
                'view/supporters.html.php',
                $viewData
            );

        }

    }

}