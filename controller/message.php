<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Mail,
        Goteo\Library\Template,
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

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(3);

                // Sustituimos los datos
                // En el asunto: %PROJECTNAME% por $project->name
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);

                // En el contenido:  nombre del autor -> %OWNERNAME% por $project->contract_name
                // el mensaje que ha escrito el productor -> %MESSAGE% por $msg_content
                // nombre del usuario que ha aportado -> %USERNAME% por $_SESSION['user']->name
                // nombre del proyecto -> %PROJECTNAME% por $project->name
                // url de la plataforma -> %SITEURL% por SITE_URL
                $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%');
                $replace = array($msg_content, $project->contract_name, $_SESSION['user']->name, $project->name, SITE_URL);
                $content = \str_replace($search, $replace, nl2br($template->text));
                
                /*
                $content = "Hola <strong>{$project->contract_name}</strong>, este es un mensaje enviado desde Goteo por {$_SESSION['user']->name}.
                <br/><br/>
                {$msg_content}
                <br/><br/>
                Puedes ver los cofinanciadores de '{$project->name}' en tu Dashboard ".SITE_URL."/dashboard";
                */
// testing-------
                echo '<pre>'.print_r($template, 1).'</pre>';
                echo '<pre>'.print_r($subject, 1).'</pre>';
                echo '<pre>'.print_r($content, 1).'</pre>';
                die;
//-----
                $mailHandler = new Mail();

//                $mailHandler->to = $project->contract_email;
                $mailHandler->to = 'jcanaves_test_investmsg@doukeshi.org';
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


                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(4);

                // Sustituimos los datos
                // En el asunto: %USERNAME% por $_SESSION['user']->name
                $subject = str_replace('%USERNAME%', $_SESSION['user']->name, $template->title);

                // En el contenido:  nombre del destinatario -> %TONAME% por $user->name
                // el mensaje que ha escrito el usuario -> %MESSAGE% por $msg_content
                // nombre del usuario -> %USERNAME% por $_SESSION['user']->name
                // url del perfil -> %PROFILEURL% por ".SITE_URL."/user/profile/{$user->id}/sharemates"
                $search  = array('%MESSAGE%','%TONAME%',  '%USERNAME%', '%PROFILEURL%');
                $replace = array($msg_content, $user->name, $_SESSION['user']->name, SITE_URL."/user/profile/{$user->id}/sharemates");
                $content = \str_replace($search, $replace, nl2br($template->text));

    // testin ----------------
                echo '<pre>'.print_r($template, 1).'</pre>';
                echo '<pre>'.print_r($subject, 1).'</pre>';
                echo '<pre>'.print_r($content, 1).'</pre>';
                die;
                //--------------

                $mailHandler = new Mail();

//                $mailHandler->to = $user->email;
                $mailHandler->to = 'jcanaves_privatemsg@doukeshi.org';
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