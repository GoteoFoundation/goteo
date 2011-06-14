<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Page,
        Goteo\Library\Mail,
        Goteo\Library\Text;

    class Dashboard extends \Goteo\Core\Controller {

        /*
         *  Muy guarro para poder moverse mientras desarrollamos
         */
        public function index ($section = null) {

            /*
            if (mail('jcanaves@gmail.com', 'test', 'prueba de la funcion mail en local')) {
                echo 'dice que vale ';
            } else{
                echo 'dice que fail';
            }
            */

            $page = Page::get('dashboard');

            $message = \str_replace('%USER_NAME%', $_SESSION['user']->name, $page->content);

            return new View (
                'view/dashboard/index.html.php',
                array(
                    'message' => $message,
                    'menu'    => self::menu()
                )
            );

        }

        /*
         * Sección, Mi actividad
         * Opciones:
         *      'projects' los proyectos del usuario y a los que ha aportado,
         *      'comunity' relacion con la comunidad
         * 
         */
        public function activity ($option = 'summary', $action = 'view') {

            // quitamos el stepped para que no nos lo coja para el siguiente proyecto que editemos
            if (isset($_SESSION['stepped'])) {
                unset($_SESSION['stepped']);
            }
            
            $user = $_SESSION['user'];

            $projects = Model\Project::ofmine($user->id);

            $status = Model\Project::status();

            foreach ($projects as $project) {

                // compruebo que puedo editar mis proyectos
                if (!ACL::check('/project/edit/'.$project->id)) {
                    ACL::allow('/project/edit/'.$project->id, '*', 'user', $user);
                }

                // y borrarlos
                if (!ACL::check('/project/delete/'.$project->id)) {
                    ACL::allow('/project/delete/'.$project->id, '*', 'user', $user);
                }
            }


            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => $message,
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'projects'=> $projects,
                    'status'  => $status,
                    'errors'  => $errors,
                    'success' => $success
                )
            );

        }

        /*
         * Seccion, Mi perfil
         * Opciones:
         *      'public' perfil público (paso 1), 
         *      'personal' datos personales (paso 2),
         *      'access' configuracion (cambio de email y contraseña)
         *
         */
        public function profile ($option = 'profile', $action = 'edit') {

            // tratamos el post segun la opcion y la acion
            $user = $_SESSION['user'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			    $errors = array();
                switch ($option) {
                    // perfil publico
                    case 'profile':
                        // tratar la imagen y ponerla en la propiedad avatar
                        // __FILES__

                        $fields = array(
                            'user_name'=>'name',
                            'user_location'=>'location',
                            'user_avatar'=>'avatar',
                            'user_about'=>'about',
                            'user_keywords'=>'keywords',
                            'user_contribution'=>'contribution',
                            'user_twitter'=>'twitter',
                            'user_facebook'=>'facebook',
                            'user_linkedin'=>'linkedin'
                        );

                        foreach ($fields as $fieldPost=>$fieldTable) {
                            if (isset($_POST[$fieldPost])) {
                                $user->$fieldTable = $_POST[$fieldPost];
                            }
                        }

                        // Avatar
                        if(!empty($_FILES['avatar_upload']['name'])) {
                            $user->avatar = $_FILES['avatar_upload'];
                        }

                        // tratar si quitan la imagen
                        if (!empty($_POST['avatar-' . $user->avatar->id .  '-remove'])) {
                            $user->avatar->remove('user');
                            $user->avatar = '';
                        }

                        $user->interests = $_POST['user_interests'];

                        //tratar webs existentes
                        foreach ($user->webs as $i => &$web) {
                            // luego aplicar los cambios

                            if (isset($_POST['web-'. $web->id . '-url'])) {
                                $web->url = $_POST['web-'. $web->id . '-url'];
                            }

                            //quitar las que quiten
                            if (!empty($_POST['web-' . $web->id .  '-remove'])) {
                                unset($user->webs[$i]);
                            }

                        }

                        //tratar nueva web
                        if (!empty($_POST['web-add'])) {
                            $user->webs[] = new Model\User\Web(array(
                                'url'   => 'http://'
                            ));
                        }

                        /// este es el único save que se lanza desde un metodo process_
                        if ($user->save($errors)) {
                            $message = 'Informacion de perfil actualizada'; //Text::get('user-profile-saved');
                            $user = Model\User::flush();
                        }
                    break;
                    
                    // datos personales
                    case 'personal':
                        // campos que guarda este paso
                        $fields = array(
                            'contract_name',
                            'contract_nif',
                            'phone',
                            'address',
                            'zipcode',
                            'location',
                            'country'
                        );

                        $personalData = array();

                        foreach ($fields as $field) {
                            if (isset($_POST[$field])) {
                                $personalData[$field] = $_POST[$field];
                            }
                        }

                        // actualizamos estos datos en los personales del usuario
                        if (!empty ($personalData)) {
                            if (Model\User::setPersonal($user->id, $personalData, true, $errors)) {
                                $message = 'Datos personales actualizados'; //Text::get('user-personal-saved');
                            }
                        }
                    break;

                    //cambio de email y contraseña
                    case 'access':
                        // E-mail
                        if($_POST['change_email']) {
                            if(empty($_POST['user_nemail'])) {
                                $errors['email'] = Text::get('error-user-email-empty');
                            }
                            elseif(!\Goteo\Library\Check::mail($_POST['user_nemail'])) {
                                $errors['email'] = Text::get('error-user-email-invalid');
                            }
                            elseif(empty($_POST['user_remail'])) {
                                $errors['email']['retry'] = Text::get('error-user-email-empty');
                            }
                            elseif (strcmp($_POST['user_nemail'], $_POST['user_remail']) !== 0) {
                                $errors['email']['retry'] = Text::get('error-user-email-confirm');
                            }
                            else {
                                $user->email = $_POST['user_nemail'];
                                unset($_POST['user_nemail']);
                                unset($_POST['user_remail']);
                                $success[] = 'Te hemos enviado un email para que confirmes el cambio de email';
                                //Text::get('user-email-change-sended');
                            }
                        }
                        // Contraseña
                        if($_POST['change_password']) {
                            // la recuperacion de contraseña se hace con esta funcionalidad
                            // no chequearemos la contraseña anterior
                            $recover = false;
                            if ($_POST['action'] == 'recover') {
                                $recover = true;
                            }

                            if(empty($_POST['user_password'])) {
                                $errors['password'] = Text::get('error-user-password-empty');
                            }
                            elseif(!$recover && !Model\User::login($user->id, $_POST['user_password'])) {
                                $errors['password'] = Text::get('error-user-wrong-password');
                            }
                            elseif(empty($_POST['user_npassword'])) {
                                $errors['password']['new'] = Text::get('error-user-password-empty');
                            }
                            elseif(!\Goteo\Library\Check::password($_POST['user_npassword'])) {
                                $errors['password']['new'] = Text::get('error-user-password-invalid');
                            }
                            elseif(empty($_POST['user_rpassword'])) {
                                $errors['password']['retry'] = Text::get('error-user-password-empty');
                            }
                            elseif(strcmp($_POST['user_npassword'], $_POST['user_rpassword']) !== 0) {
                                $errors['password']['retry'] = Text::get('error-user-password-confirm');
                            }
                            else {
                                $user->password = $_POST['user_npassword'];
                                unset($_POST['user_password']);
                                unset($_POST['user_npassword']);
                                unset($_POST['user_rpassword']);
                                $success[] = 'Has cambiado tu contraseña';
                                //Text::get('user-password-changed');
                            }
                        }
                        if($user->save($errors)) {
                            // Refresca la sesión.
                            $user = Model\User::flush();
                        } else {
                            $errors[] = 'Ha habido algun problema al guardar los datos';
                            //Text::get('user-save-fail');
                        }
                    break;
                }
			}

            $viewData = array(
                    'menu'    => self::menu(),
                    'message' => $message,
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'errors'  => $errors,
                    'success' => $success,
                    'user'    => $user
                );

                switch ($option) {
                    case 'profile':
                        $viewData['interests'] = Model\User\Interest::getAll();
                        break;
                    case 'personal':
                        $viewData['personal'] = Model\User::getPersonal($user->id);
                        break;
                    case 'access':
                        // si es recover, en contraseña actual tendran que poner el username
                        if ($action == 'recover') {
                            $viewData['message'] = "Esta recuperando su contraseña, recuerde poner el nombre de usuario en el campo 'contraseña actual' para cambiarla";
                            //Text::get('dashboard-password-recover-advice');
                        }
                        break;
                }


            return new View (
                'view/dashboard/index.html.php',
                $viewData
            );
        }


        /*
         * Seccion, Mis proyectos
         * Opciones:
         *      'actualizaciones' blog del proyecto (ahora son como mensajes),
         *      'editar colaboraciones' para modificar los mensajes de colaboraciones (no puede editar el proyecto y ya estan publicados)
         *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
         *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
         *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
         *      'pagina publica' enlace a la página pública del proyecto
         *
         */
        public function projects ($option = 'summary', $action = 'list', $id = null) {
            
            $user    = $_SESSION['user'];

            $errors = array();

            if ($action == 'select' && !empty($_POST['project'])) {
                // otro proyecto de trabajo
                $project = Model\Project::get($_POST['project']);
            } else {
                // si tenemos ya proyecto, mantener los datos actualizados
                if (!empty($_SESSION['project']->id)) {
                    $project = Model\Project::get($_SESSION['project']->id);
                }
            }

            $projects = Model\Project::ofmine($user->id);

            // si no hay proyectos no tendria que estar aqui
            if (count($projects) == 0) {
                throw new Redirection('/project/create', Redirection::TEMPORARY);
            } else {
                // compruebo permisos
                //@FIXME! buscar otro modo
                /*
                foreach ($projects as $proj) {

                    // compruebo que puedo editar mis proyectos
                    if (!ACL::check('/project/edit/'.$proj->id)) {
                        ACL::allow('/project/edit/'.$proj->id, '*', 'user', $user);
                    }

                    // y borrarlos
                    if (!ACL::check('/project/delete/'.$proj->id)) {
                        ACL::allow('/project/delete/'.$proj->id, '*', 'user', $user);
                    }
                }
                 *
                 */
            }
            
            if (empty($project)) {
                $project = $projects[0];
            }

            // aqui necesito tener un proyecto de trabajo,
            // si no hay ninguno ccoge el último
            if ($project instanceof  \Goteo\Model\Project) {
                $_SESSION['project'] = $project;
            } else {
                // si no es que hay un problema
                throw new Redirection('/dashboard', Redirection::TEMPORARY);
            }

            // tenemos proyecto de trabajo, comprobar si el proyecto esta en estado de tener blog
            if ($option == 'updates' && in_array($project->status, array(1,2,6))) {
                $errors[] = 'Lo sentimos, aun no se pueden publicar actualizaciones en este proyecto';
                //Text::get('dashboard-project-blog-wrongstatus');
                $action = 'list';
            } elseif ($option == 'updates') {
                // solo cargamos el blog en la gestion de updates
                $blog = Model\Blog::get($project->id);
                if (!$blog instanceof \Goteo\Model\Blog) {
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
                        $errors[] = 'Contacte con nosotros';
                        //Text::get('dashboard-project-blog-fail');
                        $option = 'summary';
                        $action = 'view';
                    }
                } else {
                    if (!$blog->active) {
                        $errors[] = 'Lo sentimos, las actualizaciones para este proyecto estan desactivadas';
                        //Text::get('dashboard-project-blog-inactive');
                        $action = 'list';
                    }
                }

                // primero comprobar que tenemos blog
                if (!$blog instanceof Model\Blog) {
                    $errors[] = 'No se ha encontrado ningún blog para este proyecto';
                    //Text::get('dashboard-project-updates-noblog');
                    $option = 'summary';
                    $action = 'list';
                    break;
                }

            }



			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                switch ($option) {
                    // gestionar retornos
                    case 'rewards':
                        // segun action
                        switch ($action) {
                            // filtro
                            case 'filter':
                                $filter = $_POST['filter'];
                            break;
                        
                            // procesar marcas
                            case 'process':
                                $filter = $_POST['filter'];
                                // todos los checkboxes
                                $fulfill = array();
                                // se marcan con Model/Invest con el id del aporte y el id de la recompensa
                                // estos son ful_reward-[investid]-[rewardId]
                                // no se pueden descumplir porque viene sin value (un admin en todo caso?)
                                // o cuando sea con ajax @FIXME
                                foreach ($_POST as $key=>$value) {
                                    $parts = explode('-', $key);
                                    if ($parts[0] == 'ful_reward') {
                                        Model\Invest::setFulfilled($parts[1], $parts[2]);
                                    }
                                }
                            break;

                            // enviar mensaje
                            case 'message':
                                $filter = $_POST['filter'];

                                if (empty($_POST['message'])) {
                                    $errors[] = 'Escribe el mensaje';
                                    //Text::get('dashboard-investors-mail-text-required');
                                    break;
                                } else {
                                    $msg_content = \strip_tags($_POST['message']);
                                }

                                if (!empty($_POST['msg_all'])) {
                                    // si a todos
                                    $who = array();
                                    foreach (Model\Invest::investors($project->id) as $investor) {
                                        if (!in_array($investor->user, $who)) {
                                            $who[] = $investor->user;
                                        }
                                    }
                                } else {
                                    $msg_rewards = array();
                                    // estos son msg_reward-[rewardId]
                                    foreach ($_POST as $key=>$value) {
                                        $parts = explode('-', $key);
                                        if ($parts[0] == 'msg_reward' && $value == 1) {
                                            $msg_rewards[] = $parts[1];
                                        }
                                    }

                                    $who = array();
                                    // para cada recompensa
                                    foreach ($msg_rewards as $reward) {
                                        foreach (Model\Invest::choosed($reward) as $user) {
                                            if (!in_array($user, $who)) {
                                                $who[] = $user;
                                            }
                                        }
                                    }
                                }

                                if (count($who) == 0) {
                                    $errors[] = 'No se han encontrado destinatarios';
                                    //Text::get('dashboard-investors-mail-nowho');
                                    break;
                                }

                                // obtener contenido
                                // segun destinatarios
                                $enviandoa = !empty($msg_all) ? 'todos' : 'algunos';
                                $message .= 'enviar a ' . $enviandoa  . '<br />';
                                $message .= implode(',', $who);

                                //asunto
                                $subject = 'Mensaje del proyecto que cofinancias: ' . $project->name;
                                //Text::get('dashboard-investors-mail-subject');
                                // el mensaje que ha escrito el productor
                                $content = "Hola <strong>%NAME%</strong>, este es un mensaje enviado desde Goteo por el productor del proyecto {$project->name}.
                                <br/><br/>
                                {$msg_content}
                                <br/><br/>
                                Puedes ver el proyecto en ".SITE_URL."/project/{$project->id}";
                                //Text::get('dashboard-investors-mail-template'); // lleva parametros

                                foreach ($who as $key=>$userId) {

                                    //me cojo su email y lo meto en un array para enviar solo con una instancia de Mail
                                    $data = Model\User::getMini($userId);
                                    $data->email = $userId.'-goteo@doukeshi.org';

                                    // reusamos el objeto mail
                                    $mailHandler = new Mail();

                                    $mailHandler->to = $data->email;
                                    //@TODO blind copy a comunicaciones@goteo.org
                               //     $mailHandler->bcc = 'bcc@doukeshi.org';
                                    $mailHandler->subject = $subject;
                                    $mailHandler->content = str_replace('%NAME%', $data->name, $content);

                                    $mailHandler->html = true;
                                    if ($mailHandler->send($errors)) {
                                        $success[] = 'Mensaje enviado correctamente a ' . $data->name . ' : ' . $data->email;
                                       //Text::get('dashboard-investors-mail-sended'); //lleva parametros
                                    } else {
                                        $errors[] = 'Falló al enviar el mensaje a ' . $data->name . ' : ' . $data->email;
                                        //Text::get('dashboard-investors-mail-fail'); //lleva parametros
                                    }

                                    unset($mailHandler);
                                }
                                

                            break;
                        }
                        // fin segun action
                    break;
                    case 'supports':
                        if ($action == 'save') {
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
                                }

                            }

                            // añadir nueva colaboracion
                            if (!empty($_POST['support-add'])) {
                                $project->supports[] = new Model\Project\Support(array(
                                    'project'       => $project->id,
                                    'support'       => 'Nueva colaboración',
                                    'type'          => 'task',
                                    'description'   => ''
                                ));
                            }

                            // guardamos los datos que hemos tratado y los errores de los datos
                            $project->save($errors);
                        }

                    break;

                    case 'updates':
                        if (!in_array($action, array('add','edit')))
                            break;

                        $post = new Model\Blog\Post();
                        // campos que actualizamos
                        $fields = array(
                            'id',
                            'title',
                            'text',
                            'image',
                            'media',
                            'date',
                            'allow'
                        );

                        foreach ($fields as $field) {
                            $post->$field = $_POST[$field];
                        }

                        // tratar la imagen y ponerla en la propiedad image
                        if(!empty($_FILES['image_upload']['name'])) {
                            $post->image = $_FILES['image_upload'];
                        }

                        // tratar si quitan la imagen
                        if (!empty($_POST['image-' . $post->image->id .  '-remove'])) {
                            $post->image->remove('post');
                            $post->image = '';
                        }

                        if (!empty($post->media)) {
                            $post->media = new Model\Project\Media($post->media);
                        }

                        // el blog de proyecto no tiene tags?¿?
                        // $post->tags = $_POST['tags'];

                        /// este es el único save que se lanza desde un metodo process_
                        if ($post->save($errors)) {
                            if ($action == 'edit') {
                                $success[] = 'La entrada se ha actualizado correctamente'; 
                                ////Text::get('dashboard-project-updates-saved');
                            } else {
                                $success[] = 'Se ha añadido una nueva entrada'; 
                                ////Text::get('dashboard-project-updates-inserted');
                            }
                            $action = 'list';
                        } else {
                            $errors[] = 'Ha habido algun problema al guardar los datos';
                            ////Text::get('dashboard-project-updates-fail');
                        }
                        break;
                }
            }

            if ($option == 'updates') {
                // segun la accion
                switch ($action) {
                    case 'add':
                        $post = new Model\Blog\Post(
                                array(
                                    'blog' => $blog->id,
                                    'date' => date('Y-m-d'),
                                    'allow' => true
                                )
                            );

                        $message = 'Añadiendo una nueva entrada';
                        //Text::get('dashboard-project-updates-add_title');
                        break;
                    case 'edit':
                        if (empty($id)) {
                            $errors[] = 'No se ha encontrado la entrada';
                            //Text::get('dashboard-project-updates-nopost');
                            $action = 'list';
                            break;
                        } else {
                            $post = Model\Blog\Post::get($id);

                            if (!$post instanceof Model\Blog\Post) {
                                $errors[] = 'La entrada esta corrupta, contacte con nosotros.';
                                //Text::get('dashboard-project-updates-postcorrupt');
                                $action = 'list';
                                break;
                            }
                        }

                        $message = 'Editando una entrada existente';
                        //Text::get('dashboard-project-updates-edit_title');
                        break;
                    default:
                        $posts = Model\Blog\Post::getAll($blog->id);
                        $message = 'Lista de actualizaciones';
                        //Text::get('dashboard-project-updates-list_title');
                        $action = 'list';
                        break;
                }

            }



            // view data basico para esta seccion
            $viewData = array(
                    'menu'    => self::menu(),
                    'message' => $message,
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'projects'=> $projects,
                    'errors'  => $errors,
                    'success' => $success
                );


            switch ($option) {
                // gestionar retornos
                case 'rewards':
                    // recompensas ofrecidas
                    $viewData['rewards'] = Model\Project\Reward::getAll($_SESSION['project']->id, 'individual');
                    // aportes para este proyecto
                    $viewData['invests'] = Model\Invest::getAll($_SESSION['project']->id);
                    // ver por (esto son orden y filtros)
                    $viewData['filter'] = $filter;
                break;

                // editar colaboraciones
                case 'supports':
                    $viewData['types'] = Model\Project\Support::types();
                    $project->supports = Model\Project\Support::getAll($_SESSION['project']->id);
                break;

                // publicar actualizaciones
                case 'updates':
                    $viewData['blog'] = $blog;
                    $viewData['post'] = $post;
                    break;
            }

            $viewData['project'] = $project;

            return new View ('view/dashboard/index.html.php', $viewData);
        }

        /*
         * Salto al admin
         *
         */
        public function admin ($option = 'board') {
            if (ACL::check('/admin')) {
                throw new Redirection('/admin', Redirection::TEMPORARY);
            } else {
                throw new Redirection('/dashboard', Redirection::TEMPORARY);
            }
        }

        private static function menu() {
            // todos los textos del menu dashboard
            $menu = array(
                'activity' => array(
                    'label'   => 'Mi actividad',
                    'options' => array (
                        'summary' => 'Resumen',
                        'wall'    => 'Mi muro'
                    )
                ),
                'profile' => array(
                    'label'   => 'Mi perfil',
                    'options' => array (
                        'profile'  => 'Editar perfil',
                        'personal' => 'Datos personales',
                        'access'   => 'Datos de acceso',
                    )
                ),
                'projects' => array(
                    'label' => 'Mis proyectos',
                    'options' => array (
                        'summary'  => 'Resumen',
                        'updates'  => 'Actualizaciones',
                        'widgets'  => 'Widgets',
                        'contract' => 'Contrato',
                        'rewards'  => 'Gestionar retornos',
                        'supports' => 'Colaboraciones',
                        'preview'  => 'Página pública',
                    )
                )
            );

            // si tiene permiso para ir al admin
            if (ACL::check('/admin')) {
                $menu['admin'] = array(
                    'label'   => 'Administración',
                    'options' => array(
                        'board' => 'Ir al panel'
                    )
                );
            }

            return $menu;

        }



        }

}