<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model;

    class Dashboard extends \Goteo\Core\Controller {

        /*
         *  Muy guarro para poder moverse mientras desarrollamos
         */
        public function index ($section = null) {
            $user = $_SESSION['user']->id;

            // quitamos el stepped para que no nos lo coja para el siguiente proyecto que editemos
            if (isset($_SESSION['stepped'])) {
                unset($_SESSION['stepped']);
            }

            $message = "Hola {$user}<br />";

            if (ACL::check('/admin')) {
                $message .= '<a href="/admin">Ir al panel de administración</a><br />';
            }


            $projects = Model\Project::ofmine($user);

            $status = Model\Project::status();

            //mis cofinanciadores
            // array de usuarios con:
            //  foto, nombre, nivel, cantidad a mis proyectos, fecha ultimo aporte, nº proyectos que cofinancia
            $investors = array();
            foreach ($projects as $project) {

                // compruebo que puedo editar mis proyectos
                if (!ACL::check('/project/edit/'.$project->id)) {
                    ACL::allow('/project/edit/'.$project->id, '*', 'user', $user);
                }

                // y borrarlos
                if (!ACL::check('/project/delete/'.$project->id)) {
                    ACL::allow('/project/delete/'.$project->id, '*', 'user', $user);
                }

                foreach (Model\Invest::investors($project->id) as $key=>$investor) {
                    if (\array_key_exists($investor->user, $investors)) {
                        // ya está en el array, quiere decir que cofinancia este otro proyecto
                        // , añadir uno, sumar su aporte, actualizar la fecha
                        ++$investors[$investor->user]->projects;
                        $investors[$investor->user]->amount += $investor->amount;
                        $investors[$investor->user]->date = $investor->date;  // <-- @TODO la fecha mas actual
                    } else {
                        $investors[$investor->user] = (object) array(
                            'user' => $investor->user,
                            'name' => $investor->name,
                            'projects' => 1,
                            'avatar' => $investor->avatar,
                            'worth' => $investor->worth,
                            'amount' => $investor->amount,
                            'date' => $investor->date
                        );
                    }
                }
            }


            // comparten intereses
            $shares = Model\User\Interest::share($user);

            return new View (
                'view/dashboard.html.php',
                array(
                    'message' => $message,
                    'projects' => $projects,
                    'status' => $status,
                    'investors' => $investors,
                    'shares' => $shares
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
            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => "Estas en tu actividad: $option",
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
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
                        $user->save($errors);
                        $user = Model\User::flush();
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
                            Model\User::setPersonal($user->id, $personalData, true, $errors);
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
                            }
                        }
                        // Contraseña
                        if($_POST['change_password']) {
                            if(empty($_POST['user_password'])) {
                                $errors['password'] = Text::get('error-user-password-empty');
                            }
                            elseif(!Model\User::login($user->id, $_POST['user_password'])) {
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
                                $success[] = 'Te hemos enviado un email para que confirmes el cambio de contraseña';
                            }
                        }
                        if($user->save($errors)) {
                            // Refresca la sesión.
                            $user = Model\User::flush();
                        }
                    break;
                }
			}

            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => "Estas en tu perfil: $option",
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'errors'  => $errors,
                    'success' => $success,
                    'user'    => $user,
                    'personal'=> Model\User::getPersonal($user->id)
                )
            );
        }


        /*
         * Seccion, Mi proyecto (actualmente en campaña o financiado, solo uno)
         * Opciones:
         *      'actualizaciones' blog del proyecto (ahora son como mensajes),
         *      'editar colaboraciones' para modificar los mensajes de colaboraciones (no puede editar el proyecto y ya estan publicados)
         *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
         *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
         *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
         *      'pagina publica' enlace a la página pública del proyecto
         *
         */
        public function projects ($option = 'summary', $action = 'view') {
            
            $user = $_SESSION['user'];

            $projects = Model\Project::ofmine($id);


            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => "Estas en tus proyectos: $option",
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'projects'=> $projects,
                    'errors'  => $errors,
                    'success' => $success
                )
            );
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
                        'supports' => 'Editar colaboraciones',
                        'preview'  => 'Página pública',
                    )
                )
            );

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