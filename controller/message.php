<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model;

    class Message extends \Goteo\Core\Controller {

        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */
        public function index ($project = null) {

            if (empty($_SESSION['user']))
                throw new Redirection ('/user/login?from=' . \rawurlencode('/message/' . $project), Redirection::TEMPORARY);

            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            $content = '';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();

                if (empty($_POST['message'])) {
                    $errors[] = 'Falta el texto';
                }

                if (empty($errors)) {

                    $message = new Model\Message(array(
                        'user' => $_SESSION['user']->id,
                        'project' => $project,
                        'thread' => $_POST['thread'],
                        'message' => $_POST['message']
                    ));

                    if ($message->save($errors)) {
                        $content .= 'Mensaje enviado';
                    }
                }

                if (!empty($errors)) {
                    $content .= 'Errores: ' . implode('.', $errors);
                }


			}

            $projectData = Model\Project::get($project);

            $viewData = array(
                    'content' => $content,
                    'project' => $projectData
                );

            return new View (
                'view/messages.html.php',
                $viewData
            );

        }

    }

}