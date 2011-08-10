<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Mail,
        Goteo\Library\Text;

    class Message extends \Goteo\Core\Controller {

        public function index ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::PERMANENT);

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
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

        /*
         * Este metodo envia un mensaje interno
         */
        public function direct ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::PERMANENT);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // sacamos el mail del responsable del proyecto
                $project = Model\Project::get($project);

                if (!$project instanceof Model\Project) {
                    throw new Redirection('/', Redirection::TEMPORARY);
                }

                $msg_content = \nl2br(\strip_tags($_POST['message']));

                // sacamos el mail del usuario

                // el asunto
                $subject = 'Mensaje de un nuevo cofinanciador de tu proyecto en Goteo';

                // el mensaje que ha escrito el usuario
                $content = "Hola <strong>{$project->contract_name}</strong>, este es un mensaje enviado desde Goteo por {$_SESSION['user']->name}.
                <br/><br/>
                {$msg_content}
                <br/><br/>
                Puedes ver los cofinanciadores de '{$project->name}' en tu Dashboard ".SITE_URL."/dashboard";



                $mailHandler = new Mail();

                $mailHandler->to = $project->contract_email;
                $mailHandler->bcc = 'comunicaciones@goteo.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;

                $mailHandler->html = true;
                if ($mailHandler->send($errors)) {
                    // ok
                    \Goteo\Library\Message::Info(Text::get('regular-message_success'));
                } else {
                    \trace($mailHandler);
                    unset($mailHandler);
                    die;
                }

                unset($mailHandler);
			}

            throw new Redirection("/project/{$project->id}/messages", Redirection::TEMPORARY);
        }

        /*
         * Este metodo envia un mensaje personal
         */
        public function personal ($user = null) {
            if (empty($user))
                throw new Redirection('/community', Redirection::PERMANENT);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // sacamos el mail del responsable del proyecto
                $user = Model\User::get($user);

                if (!$user instanceof Model\User) {
                    throw new Redirection('/', Redirection::TEMPORARY);
                }

                $msg_content = \nl2br(\strip_tags($_POST['message']));


                // sacamos el mail del usuario

                // el asunto
                $subject = 'Mensaje personal de un nuevo usuario de Goteo';

                // el mensaje que ha escrito el usuario
                $content = "Hola <strong>{$user->name}</strong>, este es un mensaje enviado desde Goteo por {$_SESSION['user']->name}.
                <br/><br/>
                {$msg_content}
                <br/><br/>
                Puedes ver tu comunidad en tu perfil ".SITE_URL."/user/profile/{$user->id}/sharemates";



                $mailHandler = new Mail();

                $mailHandler->to = $user->email;
                $mailHandler->bcc = 'comunicaciones@goteo.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;

                $mailHandler->html = true;
                if ($mailHandler->send($errors)) {
                    // ok
                    \Goteo\Library\Message::Info(Text::get('regular-message_success'));
                } else {
                    \trace($mailHandler);
                    unset($mailHandler);
                    die;
                }

                unset($mailHandler);
			}

            throw new Redirection("/user/profile/{$user->id}", Redirection::TEMPORARY);
        }

        /*
         * Metodo para publicar una enttrada en un post
         */
        public function post ($post, $project = null) {

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
                $comment = new Model\Blog\Post\Comment(array(
                    'user' => $_SESSION['user']->id,
                    'post' => $post,
                    'date' => date('Y-m-d H:i:s'),
                    'text' => $_POST['message']
                ));

                if ($comment->save($errors)) {
                    // mensaje enviado con exito
                } else {
                    // error
                }
			}

            if (!empty($project)) {
                throw new Redirection("/project/{$project}/updates/{$post}", Redirection::TEMPORARY);
            } else {
                throw new Redirection("/blog/{$post}", Redirection::TEMPORARY);
            }
        }

    }

}