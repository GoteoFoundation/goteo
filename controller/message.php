<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\library\Mail;

    class Message extends \Goteo\Core\Controller {

        public function index ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

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
                throw new Redirection('/discover', Redirection::TEMPORARY);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // sacamos el mail del responsable del proyecto
                $project = Model\Project::get($project);

                if (!$project instanceof Model\Project) {
                    throw new Redirection('/', Redirection::TEMPORARY);
                }

                $msg_content = \strip_tags($_POST['message']);


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
                //@TODO blind copy a comunicaciones@goteo.org
                $mailHandler->bcc = 'bcc@doukeshi.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;

                $mailHandler->html = true;
                if ($mailHandler->send($errors)) {
                    // ok
                } else {
                    \trace($mailHandler);
                    unset($mailHandler);
                    die;
                }

                unset($mailHandler);
			}

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

        /*
         * Metodo para publicar una enttrada en un blog (de nodo o de proyecto)
         */
        public function blog ($blog, $post = null) {
            if (empty($blog))
                throw new Redirection('/', Redirection::TEMPORARY);

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
                $comment = new Model\Blog\Comment(array(
                    'user' => $_SESSION['user']->id,
                    'blog' => $blog,
                    'message' => $_POST['message']
                ));

                $comment->save($errors);
			}

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

    }

}