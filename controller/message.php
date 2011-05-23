<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model;

    class Message extends \Goteo\Core\Controller {

        public function index ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $message = new Model\Message(array(
                    'user' => $_SESSION['user']->id,
                    'project' => $project,
                    'thread' => $_POST['thread'],
                    'message' => $_POST['message']
                ));

                if ($message->save($errors)) {
                    // permiso para editarlo y borrarlo
                    ACL::allow("/message/edit/{$message->id}/{$project}", '*', 'user', $_SESSION['user']->id);
                    ACL::allow("/message/delete/{$message->id}/{$project}", '*', 'user', $_SESSION['user']->id);
                }
			}

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

        public function edit ($id, $project) {

            if (isset($_POST['message'])) {
                $message = Model\Message::get($id);
                $message->user = $message->user->id;
                $message->message = ($_POST['message']);

                $message->save();
            }

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

        public function delete ($id, $project) {

            Model\Message::get($id)->delete();

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

    }

}