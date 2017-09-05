<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
		Goteo\Library\Feed,
        Goteo\Application,
        Goteo\Application\Session,
        Goteo\Application\Config,
        Goteo\Model\Template,
        Goteo\Library\Text;

    class Message extends \Goteo\Core\Controller {

        // Deprecated
        public function index ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::PERMANENT);

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                $projectData = Model\Project::getMini($project);

                if ($projectData->status < 3) {
                    Application\Message::error(Text::get('project-messages-closed'));
                    throw new Redirection("/project/{$project}");
                }

                $message = new Model\Message(array(
                    'user' => Session::getUserId(),
                    'project' => $project,
                    'thread' => $_POST['thread'],
                    'message' => $_POST['message']
                ));

                if ($message->save($errors)) {
                    $support = Model\Message::isSupport($_POST['thread']);

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    if (empty($_POST['thread'])) {
                        // nuevo hilo
                        $log_html = \vsprintf('%s ha creado un tema en %s del proyecto %s', array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('message', Text::get('project-menu-messages'), $projectData->id.'/messages#message'.$message->id),
                            Feed::item('project', $projectData->name, $projectData->id)
                        ));
                    } else {
                        // respuesta
                        // si una respuesta a un mensaje de colaboraicón
                        if (!empty($support)) {
                            $log_html = \vsprintf('Nueva colaboración de %s con %s en el proyecto %s', array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('message', $support, $projectData->id.'/messages#message'.$_POST['thread']),
                                Feed::item('project', $projectData->name, $projectData->id)
                            ));
                        } else { // es una respuesta a un hilo normal
                            $log_html = \vsprintf('%s ha respondido en %s del proyecto %s', array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('message', Text::get('project-menu-messages'), $projectData->id.'/messages#message'.$message->id),
                                Feed::item('project', $projectData->name, $projectData->id)
                            ));
                        }
                    }
                    $log->populate('usuario escribe mensaje/respuesta en Mensajes del proyecto', '/admin/projects', $log_html);
                    $log->doAdmin('user');

                    // Evento público
                    if (empty($_POST['thread'])) {
                        $log_html = Text::html('feed-messages-new_thread',
                                            Feed::item('message', Text::get('project-menu-messages'), $projectData->id.'/messages#message'.$message->id),
                                            Feed::item('project', $projectData->name, $projectData->id)
                                            );
                    } else {
                        // si una respuesta a un mensaje de colaboraicón
                        if (!empty($support)) {
                            $log_html = Text::html('feed-message_support-response',
                                            Feed::item('message', $support, $projectData->id.'/messages#message'.$_POST['thread']),
                                            Feed::item('project', $projectData->name, $projectData->id)
                                        );
                        } else { // es una respuesta a un hilo normal
                            $log_html = Text::html('feed-messages-response',
                                            Feed::item('message', Text::get('project-menu-messages'), $projectData->id.'/messages#message'.$message->id),
                                            Feed::item('project', $projectData->name, $projectData->id)
                                        );
                        }
                    }
                    $log->populate(Session::getUser()->name, '/user/profile/'.Session::getUserId(), $log_html, Session::getUser()->avatar->id);
                    $log->doPublic('community');
                    unset($log);

                    if (!empty($_POST['thread'])) {
                        // aqui el owner es el autor del mensaje thread
                        $thread = Model\Message::get($_POST['thread']);

                        // Si no tiene estas notiicaciones bloqueadas en sus preferencias
                        $sql = "
                            SELECT
                              user_prefer.threads as spam,
                              user_prefer.comlang as lang
                            FROM user_prefer
                            WHERE user = :user
                            ";
                        $query = Model\Project::query($sql, array(':user' => $thread->user->id));
                        $prefer = $query->fetchObject();
                        if (!empty($thread->user->name) && !$prefer->spam) {
                            // Mail al autor del thread
                            $comlang = !empty($prefer->lang) ? $prefer->lang : $thread->user->lang;
                            // Obtenemos la plantilla para asunto y contenido
                            $template = Template::get(Template::THREAD_OWNER, $comlang);

                            // Sustituimos los datos
                            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

                            $response_url = SITE_URL . '/user/profile/' . Session::getUserId() . '/message';
                            $project_url = SITE_URL . '/project/' . $projectData->id . '/messages#message'.$message->id;

                            $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%RESPONSEURL%');
                            $replace = array($_POST['message'], $thread->user->name, Session::getUser()->name, $projectData->name, $project_url, $response_url);
                            $content = \str_replace($search, $replace, $template->parseText());

                            $mailHandler = new Model\Mail();
                            $mailHandler->lang = $comlang;
                            $mailHandler->to = $thread->user->email;
                            $mailHandler->toName = $thread->user->name;
                            $mailHandler->subject = $subject;
                            $mailHandler->content = $content;
                            $mailHandler->html = true;
                            $mailHandler->template = $template->id;
                            $mailHandler->send($errors);

                            unset($mailHandler);
                        }
                    } else {
                        // mensaje al autor del proyecto

                        //  idioma de preferencia
                        $comlang = Model\User::getPreferences($projectData->user)->comlang;

                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(Template::OWNER_NEW_THREAD, $comlang);

                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

                        $response_url = SITE_URL . '/user/profile/' . Session::getUserId() . '/message';
                        $project_url = SITE_URL . '/project/' . $projectData->id . '/messages#message'.$message->id;

                        $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%RESPONSEURL%');
                        $replace = array($_POST['message'], $projectData->user->name, Session::getUser()->name, $projectData->name, $project_url, $response_url);
                        $content = \str_replace($search, $replace, $template->parseText());

                        $mailHandler = new Model\Mail();

                        $mailHandler->lang = $comlang;
                        $mailHandler->to = $projectData->user->email;
                        $mailHandler->toName = $projectData->user->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        $mailHandler->send($errors);

                        unset($mailHandler);
                    }


                }
			}

            throw new Redirection("/project/{$project}/participate#child-msg-".$message->id, Redirection::TEMPORARY);
        }

        // DEPRECATED
        public function edit ($id, $project) {

            if (isset($_POST['message'])) {
                $message = Model\Message::get($id);
                $message->user = $message->user->id;
                $message->message = ($_POST['message']);

                $message->save();
            }

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

        // DEPRECATED
        public function delete ($id, $project) {

            $msg = Model\Message::get($id);
            if ($msg instanceof Model\Message && $msg->project == $project) {
                $msg->dbDelete();
            }

            throw new Redirection("/project/{$project}/messages", Redirection::TEMPORARY);
        }

        /*
         * Este metodo envia un mensaje interno
         */
        public function direct ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::PERMANENT);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // verificamos token
                if (!isset($_POST['msg_token']) || $_POST['msg_token']!=$_SESSION['msg_token']) {
                    // throw new Error(Error::BAD_REQUEST);
                    header("HTTP/1.1 418");
                    die('Temporalmente no disponible');
                }

                // sacamos el mail del responsable del proyecto
                $project = Model\Project::getMini($project);
                $ownerData = Model\User::getMini($project->owner);

                $msg_content = \nl2br(\strip_tags($_POST['message']));

                //  idioma de preferencia
                $comlang = Model\User::getPreferences($ownerData)->comlang;

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(Template::MESSAGE_OWNER, $comlang);

                // Sustituimos los datos
                // En el asunto: %PROJECTNAME% por $project->name
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);

                $response_url = SITE_URL . '/user/profile/' . Session::getUserId() . '/message';

                // En el contenido:  nombre del autor -> %OWNERNAME% por $project->contract_name
                // el mensaje que ha escrito el productor -> %MESSAGE% por $msg_content
                // nombre del usuario que ha aportado -> %USERNAME% por Session::getUser()->name
                // nombre del proyecto -> %PROJECTNAME% por $project->name
                // url de la plataforma -> %SITEURL% por SITE_URL
                $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%', '%RESPONSEURL%');
                $replace = array($msg_content, $ownerData->name, Session::getUser()->name, $project->name, SITE_URL, $response_url);
                $content = \str_replace($search, $replace, $template->parseText());

                $mailHandler = new Model\Mail();

                $mailHandler->lang = $comlang;
                $mailHandler->to = $ownerData->email;
                $mailHandler->toName = $ownerData->name;
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send($errors)) {
                    // ok
                    Application\Message::info(Text::get('regular-message_success'));
                } else {
                    Application\Message::info(Text::get('regular-message_fail') . '<br />' . implode(', ', $errors));
                }

                unset($mailHandler);
			}

            throw new Redirection("/project/{$project->id}/messages", Redirection::TEMPORARY);
        }

        /*
         * Este metodo envia un mensaje personal
         */
        public function personal ($user = null) {
            // verificacion de que esté autorizasdo a enviar mensaje
            if (!isset($_SESSION['message_autorized']) || $_SESSION['message_autorized'] !== true) {
                Application\Message::info('Temporalmente no disponible. Disculpen las molestias');
                throw new Redirection('/');
            } else {
                // y quitamos esta autorización
                unset($_SESSION['message_autorized']);
            }

            if (empty($user))
                throw new Redirection('/community', Redirection::PERMANENT);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // verificamos token
                if (!isset($_POST['msg_token']) || $_POST['msg_token']!=$_SESSION['msg_token']) {
                    header("HTTP/1.1 418");
                    die('Temporalmente no disponible');
                }

                // sacamos el mail del responsable del proyecto
                $user = Model\User::get($user);

                if (!$user instanceof Model\User) {
                    throw new Redirection('/', Redirection::TEMPORARY);
                }

                $msg_content = \nl2br(\strip_tags($_POST['message']));


                //  idioma de preferencia
                $comlang = Model\User::getPreferences($user)->comlang;

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(Template::MESSAGE_USERS, $comlang);

                // Sustituimos los datos
                if (isset($_POST['subject']) && !empty($_POST['subject'])) {
                    $subject = $_POST['subject'];
                } else {
                    // En el asunto por defecto: %USERNAME% por Session::getUser()->name
                    $subject = str_replace('%USERNAME%', Session::getUser()->name, $template->title);
                }

                $remite = Session::getUser()->name . ' ' . Text::get('regular-from') . ' ';
                $remite .= \Goteo\Application\Config::isMasterNode() ? Config::get('mail.transport.name') : NODE_NAME;

                $response_url = SITE_URL . '/user/profile/' . Session::getUserId() . '/message';
                $profile_url = SITE_URL."/user/profile/{$user->id}";
                // En el contenido:  nombre del destinatario -> %TONAME% por $user->name
                // el mensaje que ha escrito el usuario -> %MESSAGE% por $msg_content
                // nombre del usuario -> %USERNAME% por Session::getUser()->name
                // url del perfil -> %PROFILEURL% por ".SITE_URL."/user/profile/{$user->id}"
                $search  = array('%MESSAGE%','%TONAME%',  '%USERNAME%', '%PROFILEURL%', '%RESPONSEURL%');
                $replace = array($msg_content, $user->name, Session::getUser()->name, $profile_url, $response_url);
                $content = \str_replace($search, $replace, $template->parseText());

                $mailHandler = new Model\Mail();
                $mailHandler->lang = $comlang;
                $mailHandler->fromName = $remite;
                $mailHandler->to = $user->email;
                $mailHandler->toName = $user->name;
                // blind copy a goteo desactivado durante las verificaciones
//                $mailHandler->bcc = 'comunicaciones@goteo.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                $errors = [];
                if ($mailHandler->send($errors)) {
                    // ok
                    Application\Message::info(Text::get('regular-message_success'));
                } else {
                    Application\Message::info(Text::get('regular-message_fail') . '<br />' . implode(', ', $errors));
                }

                unset($mailHandler);
			}

            throw new Redirection("/user/profile/{$user->id}", Redirection::TEMPORARY);
        }

        /*
         * Metodo para publicar un comentario en un post
         */
        public function post ($post, $project = null) {
            if(!Session::getUser()->confirmed && Session::getUser()->active) {
                Application\Message::error('Please confirm your email before make any comment!');
            }
			elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
                // eliminamos etiquetas script, iframe, embed y form.

                $comment = new Model\Blog\Post\Comment(array(
                    'user' => Session::getUserId(),
                    'post' => $post,
                    'date' => date('Y-m-d H:i:s'),
                    'text' => $_POST['message']
                ));
                if ($comment->save($errors)) {
                    // a ver los datos del post
                    $postData = Model\Blog\Post::get($post);


                    // si es entrada de proyecto
                    if (!empty($project)) {

                        $projectData = Model\Project::getMini($project);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log_html = \vsprintf('%s ha escrito un %s en la entrada "%s" en las %s del proyecto %s', array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('message', 'Comentario'),
                            Feed::item('update-comment', $postData->title, $projectData->id.'/updates/'.$postData->id.'#comment'.$comment->id),
                            Feed::item('update-comment', 'Novedades', $projectData->id.'/updates/'),
                            Feed::item('project', $projectData->name, $projectData->id)
                        ));
                        $log->populate('usuario escribe comentario en blog/novedades', '/admin/projects', $log_html);
                        $log->doAdmin('user');

                        // Evento público
                        $log_html = Text::html('feed-updates-comment',
                            Feed::item('update-comment', $postData->title, $projectData->id.'/updates/'.$postData->id.'#comment'.$comment->id),
                            Feed::item('update-comment', 'Novedades', $projectData->id.'/updates/'),
                            Feed::item('project', $projectData->name, $projectData->id)
                        );
                        $log->populate(Session::getUser()->name, '/user/profile/'.Session::getUserId(), $log_html, Session::getUser()->avatar->id);
                        $log->doPublic('community');
                        unset($log);

                        //Notificación al autor del proyecto

                        //  idioma de preferencia
                        $comlang = Model\User::getPreferences($projectData->user)->comlang;

                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(Template::OWNER_NEW_COMMENT, $comlang);

                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

                        $response_url = SITE_URL . '/user/profile/' . Session::getUserId() . '/message';
                        $project_url = SITE_URL . '/project/' . $projectData->id . '/updates/'.$postData->id.'#comment'.$comment->id;

                        $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%RESPONSEURL%');
                        $replace = array($_POST['message'], $projectData->user->name, Session::getUser()->name, $projectData->name, $project_url, $response_url);
                        $content = \str_replace($search, $replace, $template->parseText());

                        // que no pete si no puede enviar el mail al autor
                        try {
                            $mailHandler = new Model\Mail();

                            $mailHandler->lang = $comlang;
                            $mailHandler->to = $projectData->user->email;
                            $mailHandler->toName = $projectData->user->name;
                            $mailHandler->subject = $subject;
                            $mailHandler->content = $content;
                            $mailHandler->html = true;
                            $mailHandler->template = $template->id;
                            $mailHandler->send($errors);

                            unset($mailHandler);
                        } catch (Exception $e) {
                            @mail(Config::getMail('fail'), 'FAIL '. __FUNCTION__ .' en ' . SITE_URL,
                                'Ha fallado a enviar mail a autor '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Objeto '. \trace($mailHandler));
                        }

                    } else {

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget('goteo', 'blog');
                        $log_html = \vsprintf('%s ha escrito un %s en la entrada "%s" del blog de %s', array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('message', 'Comentario'),
                            Feed::item('blog', $postData->title, $postData->id.'#comment'.$comment->id),
                            Feed::item('blog', 'Goteo', '/')
                        ));
                        $log->populate('usuario escribe comentario en blog/novedades', '/admin/projects', $log_html);
                        $log->doAdmin('user');

                        // Evento público
                        $log_html = Text::html('feed-blog-comment',
                            Feed::item('blog', $postData->title, $postData->id.'#comment'.$comment->id),
                            Feed::item('blog', 'Goteo', '/')
                        );
                        $log->populate(Session::getUser()->name, '/user/profile/'.Session::getUserId(), $log_html, Session::getUser()->avatar->id);
                        $log->doPublic('community');
                        unset($log);

                    }

                } else {
                    // error
                    @mail(Config::getMail('fail'), 'FAIL '. __FUNCTION__ .' en ' . SITE_URL,
                        'No ha grabado el comentario en post. '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. Session::getUserId() . ' Errores: '.implode('<br />', $errors));

                }
			}
                // print_r($comment);die("[$post]");

            if (!empty($project)) {
                throw new Redirection("/project/{$project}/updates/{$post}#comment".$comment->id, Redirection::TEMPORARY);
            } else {
                throw new Redirection("/blog/{$post}#comment".$comment->id, Redirection::TEMPORARY);
            }
        }

    }

}
