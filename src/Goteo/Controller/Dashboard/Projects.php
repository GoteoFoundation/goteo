<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
		Goteo\Library\Text,
		Goteo\Library\Feed,
        Goteo\Model\Mail,
		Goteo\Model\Page,
        Goteo\Model\Template,
        Goteo\Model\Project\ProjectMilestone,
		Goteo\Model\Exception\ModelException,
        Goteo\Application\Session,
        Goteo\Application\Message,
        Goteo\Application\Config,
		Goteo\Application\App;
    use Goteo\Core\Traits\StaticLoggerTrait;

/*
 * las opciones para /dashboard/projects:
 *
 *      'updates' actualizaciones
 *      'supports' editar colaboraciones
 *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
 *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
 *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
 *      'messengers' gestionar colaboradores
 *      'contract' contrato
 *      'account'  cuentas
 */
    class Projects {

        use StaticLoggerTrait;

        /**
         * Verificación de proyecto de trabajo
         *
         * @param object $user instancia Model\User del convocador
         * @param string $action por si es 'select' y para el redirect
         * @param string $option para el redirect
         * @return array(project, projects)
         */
        public static function verifyProject($user, $action = 'list', $option = 'summary') {

            $projects = Model\Project::ofmine($user->id); // sus proyectos

            // si no tiene, no debería estar aquí
            if (empty($projects) || !is_array($projects)) {
                return array(null, null);
            }


            try {
                // si está seleccionando otro proyecto
                if ( $action == 'select' && !empty($_REQUEST['project']) ) {
                    $_SESSION['project'] = $_REQUEST['project'];
                    throw new Redirection('/dashboard/projects/'.$option);

                } elseif ( !empty($_SESSION['project']) ) {
                    // mantener los datos del proyecto de trabajo
                    $project = Model\Project::get($_SESSION['project']);

                } else {
                    // si no hay proyecto de trabajo, coger el primero
                    $project = Model\Project::get($projects[0]->id);

                }

            } catch(\Goteo\Application\Exception\ModelNotFoundException $e) {

                // Capturar el posible 404 por cambio de id
                $_SESSION['project'] = null;
                throw new Redirection('/dashboard/projects/'.$option);

            }


            // tiene que volver con un proyecto de trabajo
            if ($project instanceof \Goteo\Model\Project) {
                $_SESSION['project'] = $project->id; // lo guardamos en sesión para la próxima verificación
            } else {
                Message::error('No se puede trabajar con el proyecto seleccionado');
                throw new Redirection('/dashboard/projects/');
                $project = null;
            }

            // devolvemos lista de proyectos y proyecto de trabajo
            return array($project, $projects);
        }

        /**
         * Verifica que todo está correcto para publicar novedades
         *
         * @param type $project Instancia de proyecto de trabajo
         * @return \Goteo\Controller\Dashboard\Blog
         * @throws Redirection a Mis Proyectos si hay algo mal
         */
        public static function verifyBlog($project) {

            $errors = array();

            // tenemos proyecto de trabajo, comprobar si el proyecto esta en estado de tener blog
            if ($project->status < 3) {
                Message::error(Text::get('dashboard-project-blog-wrongstatus'));
                throw new Redirection('/dashboard/projects/summary');
            }

            // si no tiene registro de blog se lo creamos
            $blog = Model\Blog::get($project->id);
            if (!$blog instanceof Model\Blog) {
                $blog = new Model\Blog(
                                array(
                                    'id' => '',
                                    'type' => 'project',
                                    'owner' => $project->id,
                                    'active' => true,
                                    'project' => $project->id,
                                    'posts' => array()
                                )
                );
                if (!$blog->save($errors)) {
                    Message::error(Text::get('dashboard-project-blog-fail'));
                    Message::error(implode('<br />', $errors));
                    throw new Redirection('/dashboard/projects/summary');
                }
            } elseif (!$blog->active) {
                Message::error(Text::get('dashboard-project-blog-inactive'));
                throw new Redirection('/dashboard/projects/summary');
            }

            return $blog;
        }



        /**
         * Gestiona las acciones de gestión de updates
         *
         * @param type $action (por referencia)
         * @param type $id del post a gestionar
         * @param type $blog id del blog del proyecto
         * @param type $errors (por referencia)
         * @return instancia de Post para las acciones add y edit , array de Posts para las acciones delete y list
         */
        public static function prepare_updates (&$action, $id, $blog) {
            // segun la accion
            switch ($action) {
                case 'none' :
                    $posts = array();
                    break;
                case 'add':
                    $post = new Model\Blog\Post(
                                    array(
                                        'blog' => $blog,
                                        'date' => date('Y-m-d'),
                                        'publish' => false,
                                        'allow' => true
                                    )
                    );
                    return array($post, null);

                    break;
                case 'edit':
                    if (empty($id)) {
                        Message::error(Text::get('dashboard-project-updates-nopost'));
                        $action = 'list';
                        break;
                    } else {
                        $post = Model\Blog\Post::get($id);

                        if (!$post instanceof Model\Blog\Post) {
                            Message::error(Text::get('dashboard-project-updates-postcorrupt'));
                            $action = 'list';
                            break;
                        }
                        return array($post, null);
                    }

                    break;
                case 'delete':
                    if (Model\Blog\Post::delete($id)) {
                        Message::info(Text::get('dashboard-project-updates-deleted'));
                    } else {
                        Message::error(Text::get('dashboard-project-updates-delete_fail'));
                    }
                    $posts = Model\Blog\Post::getAll($blog, null, false);
                    $action = 'list';
                    return array(null, $posts);

                    break;
                default:
                    $posts = Model\Blog\Post::getAll($blog, null, false);
                    $action = 'list';
                    return array(null, $posts);

                    break;
            }
        }

        /**
        * Prepara el contenido a mostrar segun estado de contrato
        *
        * @param boolean $show Segmento segun estado
        **/
        public static function prepare_content ($show = 'off') {

            $shwCnt = array(
                'off' => 0,
                'campaign' => 1,
                'edit' => 2,
                'closed' => 3,
                'review' => 4,
                'ready' => 5,
                'recieved' => 6,
                'payed' => 7,
                'fulfilled' => 8,
                'separador' => 9,
                'siempre' => 10
            );

            $base = Page::get('dashboard_contract');
            // explodear el contenido y mostrar segun el show
            $content = explode('<hr />', $base->content);

            return $content[$shwCnt[$show]];
        }


        /**
         * Realiza el envio masivo a participantees o cofinanciadores
         *
         * @param type $option 'messengers' || 'rewards'
         * @param type $project Instancia del proyecto de trabajo
         * @return boolean
         */
        public static function process_mailing ($option, $project) {
            $who = array();

            // verificar que hay mensaje
            if (empty($_POST['message'])) {
                Message::error(Text::get('dashboard-investors-mail-text-required'));
                return false;
            } else {
                $msg_content = nl2br(\strip_tags($_POST['message']));
            }

            // si a todos los participantes
            if ($option == 'messengers' && !empty($_POST['msg_all'])) {
                // a todos los participantes
                foreach (Model\Message::getMessengers($project->id) as $messeger => $msgData) {
                    if ($messeger == $project->owner)
                        continue;
                    $who[$messeger] = $messeger;
                   // unset($msgData); // los datos del mensaje del participante no se usan
                }
            } elseif ($option == 'rewards' && !empty($_POST['msg_all'])) {
                // a todos los cofinanciadores
                foreach (Model\Invest::investors($project->id, false, true, 0, null) as $user => $investor) {
                    // no duplicar
                    $who[$investor->user] = $investor->user;
                }
            } elseif (!empty($_POST['msg_user'])) {
                // a usuario individual
                $who[$_POST['msg_user']] = $_POST['msg_user'];
            } elseif ($option == 'rewards') {
                $msg_rewards = array();
                // estos son msg_reward-[rewardId], a un grupo de recompensa
                foreach ($_POST as $key => $value) {
                    $parts = explode('-', $key);
                    if ($parts[0] == 'msg_reward' && $value == 1) {
                        $msg_rewards[] = $parts[1];
                    }
                }

                // para cada recompensa
                foreach ($msg_rewards as $reward) {
                    foreach (Model\Invest::choosed($reward) as $user) {
                        $who[$user] = $user;
                    }
                }
            }
            // print_r($_POST);
            // print_r($who);die;

            // no hay destinatarios
            if (count($who) == 0) {
                Message::error(Text::get('dashboard-investors-mail-nowho'));
                return false;
            }

            // obtener contenido
            // segun destinatarios
            $allsome = explode('/', Text::get('regular-allsome'));
            $enviandoa = !empty($_POST['msg_all']) ? $allsome[0] : $allsome[1];
            if ($option == 'messengers') {
                Message::info(Text::get('dashboard-messegers-mail-sendto', $enviandoa));
            } else {
                Message::info(Text::get('dashboard-investors-mail-sendto', $enviandoa));
            }

            // idioma de preferencia
            $comlang = Model\User::getPreferences($project->user->id)->comlang;

            // Obtenemos la plantilla para asunto y contenido
            $template = Template::get(Template::MESSAGE_DONORS, $comlang);

            // Sustituimos los datos
            if (!empty($_POST['subject'])) {
                $subject = $_POST['subject'];
            } else {
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
            }

            $remite = $project->name . ' ' . Text::get('regular-from') . ' ';
            $remite .= \Goteo\Application\Config::isMasterNode() ? Config::get('mail.transport.name') : NODE_NAME;

            $search = array('%MESSAGE%', '%PROJECTNAME%', '%PROJECTURL%', '%OWNERURL%', '%OWNERNAME%');
            $replace = array($msg_content, $project->name, SITE_URL . "/project/" . $project->id,
                SITE_URL . "/user/profile/" . $project->owner, $project->user->name);
            $content = \str_replace($search, $replace, $template->parseText());

            // para usar el proceso Sender:


            // - $who debe ser compatible con el formato $receivers
            // (falta nombre e email), sacarlo con getMini
            $receivers = array();
            foreach ($who as $userId) {
                $user = Model\User::getMini($userId);
                $user->user = $user->id;
                $receivers[] = $user;
            }

            // - en la plantilla hay que cambiar %NAME% por %USERNAME% para que sender reemplace

            // -

            // - se crea un registro de tabla mail
            $mailHandler = new Mail();
            $mailHandler->template = $template->id;
            $mailHandler->node = \GOTEO_NODE;
            $mailHandler->lang = $comlang;
            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->massive = true;

            if( !$mailHandler->save($errors) ) throw new ModelException(implode('<br>', $errors));

            // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
            // , también metemos el reply y repplyName (remitente) en la instancia de envío
            static::setLogger(App::getService('logger'));

            if (\Goteo\Model\Mail\Sender::initiateSending($mailHandler->id, $receivers, 1, $project->user->email, $remite)) {
                static::notice('Promoter massive sending activated', [$project, $mailHandler]);
                Message::info(Text::get('dashboard-investors-mail-sent')); // cambiar este texto
            } else {
                static::critical("ERROR initiating promoter massive sending", [$project, $mailHandler]);
                Message::error(Text::get('dashboard-investors-mail-fail', 'la cola de envíos')); // cambiar este texto
            }


            return true;
        }


        /**
         * procesar algo respecto al contrato....
         *
         * @param object $project Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return boolean
         */
        public static function process_contract ($project, &$errors = array()) {

        }


        /**
         * Graba las colaboraciones con lo recibido por POST
         *
         * @param object $project Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return object $project Instancia de proyecto modificada
         */
        public static function process_supports ($project, &$errors = array()) {
            // tratar colaboraciones existentes
            foreach ($project->supports as $key => $support) {

                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["support-{$support->id}-remove"])) {
                    unset($project->supports[$key]);
                    continue;
                }

                if (isset($_POST['support-' . $support->id . '-support'])) {
                    $support->support = $_POST['support-' . $support->id . '-support'];
                    $support->description = $_POST['support-' . $support->id . '-description'];
                    $support->type = $_POST['support-' . $support->id . '-type'];

                    if (!empty($support->thread)) {
                        // actualizar ese mensaje
                        $msg = Model\Message::get($support->thread);

                        // ojo si tenemos id pero no hay mensaje
                        if (!$msg instanceof Model\Message) {
                            $msg = new Model\Message(array(
                                'user' => $project->owner,
                                'project' => $project->id
                            ));
                        }

                        $msg->date = date('Y-m-d');
                        $msg->message = "{$support->support}: {$support->description}";
                        $msg->blocked = true;
                        $msg->save();
                    } else {
                        // grabar nuevo mensaje
                        $msg = new Model\Message(array(
                                    'user' => $project->owner,
                                    'project' => $project->id,
                                    'date' => date('Y-m-d'),
                                    'message' => "{$support->support}: {$support->description}",
                                    'blocked' => true
                                ));
                        if ($msg->save()) {
                            // asignado a la colaboracion como thread inicial
                            $support->thread = $msg->id;

                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('usuario pone una nueva colaboracion en su proyecto (dashboard)', '/admin/projects', \vsprintf('%s ha publicado una nueva %s en el proyecto %s, con el título "%s"', array(
                                        Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                        Feed::item('message', 'Colaboración'),
                                        Feed::item('project', $project->name, $project->id),
                                        Feed::item('update', $support->support, $project->id . '/messages#message' . $msg->id)
                                    )));
                            $log->doAdmin('user');

                            // evento público, si el proyecto es público
                            if ($project->status > 2) {
                                $log->populate(Session::getUser()->name, '/user/profile/' . Session::getUserId(), Text::html('feed-new_support', Feed::item('project', $project->name, $project->id), Feed::item('update', $support->support, $project->id . '/messages#message' . $msg->id)
                                        ), Session::getUser()->avatar->id);
                                $log->doPublic('community');
                            }
                            unset($log);
                        }
                    }
                }
            }

            // añadir nueva colaboracion (no hacemos lo del mensaje porque esta sin texto)
            if (!empty($_POST['support-add'])) {

                $new_support = new Model\Project\Support(array(
                            'project' => $project->id,
                            'support' => 'Nueva colaboración',
                            'type' => 'task',
                            'description' => ''
                        ));
                if ($new_support->save($errors)) {
                    $project->supports[$new_support->id] = $new_support;
                    $_POST['support-' . $new_support->id . '-edit'] = true;
                } else {
                    $project->supports[] = new Model\Project\Support(array(
                                'project' => $project->id,
                                'support' => 'Nueva colaboración',
                                'type' => 'task',
                                'description' => ''
                            ));
                }
            }

            // guardamos los datos que hemos tratado y los errores de los datos
            $project->save($errors);

            return $project;
        }


        /**
         * Graba un registro de novedad con lo recibido por POST
         *
         * @param array  $action (add o edit) y $id del post
         * @param object $project Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return array $action por si se queda editando o sale a la lista y $id por si es un add y se queda editando
         */
        public static function process_updates ($action, $project, &$errors = array()) {


            $editing = false;

            if (!empty($_POST['id'])) {
                $post = Model\Blog\Post::get($_POST['id']);
            } else {
                $post = new Model\Blog\Post();
            }

            $post->owner_type = 'project';
            $post->owner_id = $project->id;

            // campos que actualizamos
            $fields = array(
                'id',
                'blog',
                'title',
                'text',
                'image',
                'media',
                'legend',
                'date',
                'publish',
                'allow'
            );

            foreach ($fields as $field) {
                $post->$field = $_POST[$field];
            }

            // tratar la imagen y ponerla en la propiedad image
            if (!empty($_FILES['image_upload']['name'])) {
                $post->image = $_FILES['image_upload'];
                $editing = true;
            }

            // tratar las imagenes que quitan
            foreach ($post->gallery as $key => $image) {
                if (!empty($_POST["gallery-{$image->hash}-remove"])) {
                    $image->remove($errors, 'post');
                    unset($post->gallery[$key]);
                    if ($post->image == $image->id) {
                        $post->image = '';
                    }
                    $editing = true;
                }
            }

            if (!empty($post->media)) {
                $post->media = new Model\Project\Media($post->media);
            }

            // el blog de proyecto no tiene tags?Â¿?
            // $post->tags = $_POST['tags'];
            /// este es el único save que se lanza desde un metodo process_
            if ($post->save($errors)) {

                // si el idioma del proyecto no es español, actualizar registro de post_lang para idioma del proyecto
                if ($project->lang != 'es') {
                    $post->title_lang = $_POST['title'];
                    $post->text_lang = $_POST['text'];
                    $post->media_lang = $_POST['media'];
                    $post->legend_lang = $_POST['legend'];
                    $post->lang = $project->lang;
                    $post->saveLang($errors);
                }

                $id = $post->id;
                if ($action == 'edit') {
                    Message::info(Text::get('dashboard-project-updates-saved'));
                } else {
                    Message::info(Text::get('dashboard-project-updates-inserted'));
                }
                $action = $editing ? 'edit' : 'list';

                // si ha marcado publish, grabamos evento de nueva novedad en proyecto
                if ((bool) $post->publish) {
                    // imagen de proyecto si la entrada no tiene imagen

                    if (is_object($post->image)) {
                        $image = $post->image->id;
                    } elseif (!empty($post->image)) {
                        $image = $post->image;
                    }  else {
                        $image = (!empty($project->image->id)) ? $project->image->id : 'empty';
                    }

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($project->id);
                    $log->setPost($post->id);
                    $log->populate('usuario publica una novedad en su proyecto (dashboard)', '/project/' . $project->id . '/updates/' . $post->id,
                            \vsprintf('%s ha publicado un nuevo post en %s sobre el proyecto %s, con el título "%s"', array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('blog', Text::get('project-menu-updates')),
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('update', $post->title, $project->id . '/updates/' . $post->id)
                                )
                            ),
                        $image
                    );
                    $log->unique = true;
                    $log->doAdmin('user');

                    // evento público
                    $log->populate($post->title,
                            '/project/' . $project->id . '/updates/' . $post->id,
                            Text::html('feed-new_update', Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('blog', Text::get('project-menu-updates')),
                            Feed::item('project', $project->name, $project->id)
                        ), $image);
                    $log->doPublic('projects');

                    // si no ha encontrado otro, lanzamos la notificación a cofinanciadores
                    // y el post no es demasiado viejo

                    if (!$log->unique_issue && $project->num_investors && (new \DateTime('-1 week')) <  (new \DateTime($post->date))) {
                        \Goteo\Console\UsersSend::toInvestors('update', $project, null, $post);
                    }

                    unset($log);
                }
            } else {
                $errors[] = Text::get('dashboard-project-updates-fail');
            }

            // Get de post Milestone
            $post_milestone=ProjectMilestone::get($project->id, $post->id);

            if($post->publish && !$post_milestone)
            {
                //Insert milestone
                $project_milestone= new ProjectMilestone;
                $project_milestone->project=$project->id;
                $project_milestone->post=$post->id;
                $project_milestone->date=$post->date;
                $project_milestone->save($errors);
            }
            elseif(!$post->publish)
            {
                //Delete milestone
                $project_milestone= new ProjectMilestone;
                $project_milestone->project=$project->id;
                $project_milestone->post=$post->id;
                $project_milestone->removePostMilestone($errors);

            }

            return array($action, $id);

        }

        /**
         * Método de datos para la vista del gráfico goteo-analytics
         * @param object $project Instancia del proyecto a visualizar
         * @return mixed
         */
        public static function graph ($id) {

            // aportes
            $invests = array();
            $sql = "SELECT amount, user, invested FROM invest WHERE project = ? AND status IN ('0', '1', '3', '4')"; // solo aportes que aparecen públicamente
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $invests[] = $row;
            }

            // fechas
            $dates = array();
            $sql = 'SELECT published, closed, success, passed FROM project WHERE id = ?';
            $result = Model\Invest::query($sql, array($id));
            $dates = $result->fetchObject();
            if(empty($dates)) $dates = new \stdClass;
            $project_conf = Model\Project\Conf::get($id);
            $dates->days_round1 = (int) $project_conf->days_round1;
            $dates->days_round2 = (int) $project_conf->days_round2;
            $dates->days_total = (int) $dates->days_round1 + $dates->days_round2;

            // importes objetivo
            $optimum = $minimum = 0;
            $sql = 'SELECT sum(amount) as amount, required FROM cost WHERE project = ? GROUP BY required';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                if ($row['required'] == 1){
                    $minimum = $row['amount'];
                } else {
                    $optimum = $row['amount'];
                }
            }

            $data = array('invests' => $invests,
                        'dates' => $dates,
                        'minimum' => $minimum,
                        'optimum' => $optimum
                    );

            return $data;
        }

    }

}
