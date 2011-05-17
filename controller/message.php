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

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $message = new Model\Message(array(
                    'user' => $_SESSION['user']->id,
                    'project' => $project,
                    'thread' => $_POST['thread'],
                    'message' => $_POST['message']
                ));

                $message->save($errors);
			}

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

    }

}