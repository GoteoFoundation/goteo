<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Feed,
		Goteo\Library\Template,
        Goteo\Library\Message,
        Goteo\Model;

    class Users {

        public static function process ($action = 'list', $id = null, $subaction = '', $filters = array()) {

            $errors = array();

            switch ($action)  {
                case 'add':

                    // si llega post: creamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $errors = array();

                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        $user = new Model\User();
                        $user->userid = $_POST['userid'];
                        $user->name = $_POST['name'];
                        $user->email = $_POST['email'];
                        $user->password = $_POST['password'];
                        $user->save($errors);

                        if(empty($errors)) {
                          // mensaje de ok y volvemos a la lista de usuarios
                          Message::Info(Text::get('user-register-success'));
                          throw new Redirection('/admin/users/manage/'.$user->id);
                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $data = $_POST;
                        }
                    }

                    // vista de crear usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'add',
                            'data'=>$data,
                            'errors'=>$errors
                        )
                    );

                    break;
                case 'edit':

                    $user = Model\User::get($id);

                    // si llega post: actualizamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $errors = array();

                        $tocado = array();
                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        if (!empty($_POST['email'])) {
                            $user->email = $_POST['email'];
                            $tocado[] = 'el email';
                        }
                        if (!empty($_POST['password'])) {
                            $user->password = $_POST['password'];
                            $tocado[] = 'la contraseña';
                        }

                        if(!empty($tocado) && $user->update($errors)) {

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('Operación sobre usuario (admin)', '/admin/users', \vsprintf('El admin %s ha %s del usuario %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Tocado ' . implode (' y ', $tocado)),
                                Feed::item('user', $user->name, $user->id)
                            )));
                            //$log->setTarget($user->id, 'user');
                            $log->doAdmin('user');
                            unset($log);

                            // mensaje de ok y volvemos a la lista de usuarios
                            Message::Info('Datos actualizados');
                            throw new Redirection('/admin/users');

                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $data = $_POST;
                        }
                    }

                    // vista de editar usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'edit',
                            'user'=>$user,
                            'data'=>$data,
                            'errors'=>$errors
                        )
                    );

                    break;
                case 'manage':

                    // si llega post: ejecutamos + mensaje + seguimos editando

                    // operación y acción para el feed
                    $sql = '';
                    switch ($subaction)  {
                        case 'ban':
                            $sql = "UPDATE user SET active = 0 WHERE id = :user";
                            $log_action = 'Desactivado';
                            break;
                        case 'unban':
                            $sql = "UPDATE user SET active = 1 WHERE id = :user";
                            $log_action = 'Activado';
                            break;
                        case 'show':
                            $sql = "UPDATE user SET hide = 0 WHERE id = :user";
                            $log_action = 'Mostrado';
                            break;
                        case 'hide':
                            $sql = "UPDATE user SET hide = 1 WHERE id = :user";
                            $log_action = 'Ocultado';
                            break;
                        case 'checker':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'checker')";
                            $log_action = 'Hecho revisor';
                            break;
                        case 'nochecker':
                            $sql = "DELETE FROM user_role WHERE role_id = 'checker' AND user_id = :user";
                            $log_action = 'Quitado de revisor';
                            break;
                        case 'translator':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'translator')";
                            $log_action = 'Hecho traductor';
                            break;
                        case 'notranslator':
                            $sql = "DELETE FROM user_role WHERE role_id = 'translator' AND user_id = :user";
                            $log_action = 'Quitado de traductor';
                            break;
                        case 'caller':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'caller')";
                            $log_action = 'Hecho convocador';
                            $newcaller = true;
                            break;
                        case 'nocaller':
                            $sql = "DELETE FROM user_role WHERE role_id = 'caller' AND user_id = :user";
                            $log_action = 'Quitado de convocador';
                            break;
                        case 'admin':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'admin')";
                            $log_action = 'Hecho admin';
                            break;
                        case 'noadmin':
                            $sql = "DELETE FROM user_role WHERE role_id = 'admin' AND user_id = :user";
                            $log_action = 'Quitado de admin';
                            break;
                        case 'vip':
                            $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'vip')";
                            $log_action = 'Hecho VIP';
                            break;
                        case 'novip':
                            $sql = "DELETE FROM user_role WHERE role_id = 'vip' AND user_id = :user";
                            $log_action = 'Quitado el VIP';
                            break;
                    }


                    if (!empty($sql)) {

                        $user = Model\User::getMini($id);

                        if (Model\User::query($sql, array(':user'=>$id))) {

                            // mensaje de ok y volvemos a la gestion del usuario
                            Message::Info('Ha <strong>' . $log_action . '</strong> al usuario <strong>'.$user->name.'</strong> CORRECTAMENTE');
                            $log_text = 'El admin %s ha %s al usuario %s';

                        } else {

                            // mensaje de error y volvemos a la gestion del usuario
                            Message::Error('Ha FALLADO cuando ha <strong>' . $log_action . '</strong> al usuario <strong>'.$id.'</strong>');
                            $log_text = 'Al admin %s le ha <strong>FALLADO</strong> cuando ha %s al usuario %s';

                        }

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('Operación sobre usuario (admin)', '/admin/users',
                            \vsprintf($log_text, array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', $log_action),
                                Feed::item('user', $user->name, $user->id)
                        )));
                        $log->doAdmin('user');
                        unset($log);

                        throw new Redirection('/admin/users/manage/'.$id);
                    }

                    $user = Model\User::get($id);


                    // vista de gestión de usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'manage',
                            'user'=>$user,
                            'errors'=>$errors,
                            'success'=>$success
                        )
                    );


                    break;
                case 'impersonate':

                    $user = Model\User::get($id);

                    // vista de acceso a suplantación de usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file'   => 'impersonate',
                            'user'   => $user
                        )
                    );

                    break;
                /*
                case 'send':
                    // obtenemos los usuarios que siguen teniendo su email como contraseña
                    $workshoppers = Model\User::getWorkshoppers();

                    if (empty($workshoppers)) {
                        $errors[] = 'Ningún usuario tiene su email como contraseña, podemos cambiar la funcionalidad de este botón!';
                    } else {

                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(27);

                        foreach ($workshoppers as $fellow) {
                            $err = array();
                            // iniciamos mail
                            $mailHandler = new Mail();
                            $mailHandler->to = $fellow->email;
                            $mailHandler->toName = $fellow->name;
                            // blind copy a goteo desactivado durante las verificaciones
                //              $mailHandler->bcc = 'comunicaciones@goteo.org';
                            $mailHandler->subject = $template->title;
                            // substituimos los datos
                            $search  = array('%USERNAME%', '%USERID%', '%USEREMAIL%', '%SITEURL%');
                            $replace = array($fellow->name, $fellow->id, $fellow->email, SITE_URL);
                            $mailHandler->content = \str_replace($search, $replace, $template->text);
                            $mailHandler->html = true;
                            $mailHandler->template = $template->id;
                            if ($mailHandler->send($err)) {
                                $errors[] = 'Se ha enviado OK! a <strong>'.$fellow->name.'</strong> a la dirección <strong>'.$fellow->email.'</strong>';
                            } else {
                                $errors[] = 'Ha FALLADO! al enviar a <strong>'.$fellow->name.'</strong>. Ha dado este error: '. implode(',', $err);
                            }
                            unset($mailHandler);
                        }


                    }
*/

                case 'list':
                default:
                    $users = Model\User::getAll($filters);
                    $status = array(
                                'active' => 'Activo',
                                'inactive' => 'Inactivo'
                            );
                    $interests = Model\User\Interest::getAll();
                    $roles = array(
                        'admin' => 'Admin',
                        'checker' => 'Revisor',
                        'translator' => 'Traductor',
                        'caller' => 'Convocador',
                        'vip' => 'VIP'
                    );
                    $orders = array(
                        'created' => 'Fecha de alta',
                        'name' => 'Nombre'
                    );

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'list',
                            'users'=>$users,
                            'filters' => $filters,
                            'name' => $name,
                            'status' => $status,
                            'interests' => $interests,
                            'roles' => $roles,
                            'orders' => $orders,
                            'errors' => $errors
                        )
                    );
                break;
            }
            
        }

    }

}
