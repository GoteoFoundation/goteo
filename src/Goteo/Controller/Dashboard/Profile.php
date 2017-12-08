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
        Goteo\Application\Message,
        Goteo\Application\Config,
		Goteo\Library\Text;

    class Profile {

        /**
         * Tratamiento de formulario de datos de perfil
         *
         * @param object $user instancia de Model\User  (por referencia)
         * @param object $vip instancia de Model\User\Vip  (por referencia)
         * @param array $errors  (por referencia)
         * @param string $log_action  (por referencia)
         * @return boolean si se guarda bien
         */
        public static function process_profile (Model\User $user, $vip, &$errors, &$log_action) {

            $fields = array(
                'user_name' => 'name',
                'user_location' => 'location',
                'user_avatar' => 'avatar',
                'user_about' => 'about',
                'user_keywords' => 'keywords',
                'user_contribution' => 'contribution',
                'user_facebook' => 'facebook',
                'user_google' => 'google',
                'user_twitter' => 'twitter',
                'user_identica' => 'identica',
                'user_linkedin' => 'linkedin',
                'user_birthyear' => 'birthyear',
                'user_gender' => 'gender',
                'user_legal_entity' => 'legal_entity',
                'user_entity_type' => 'entity_type'
            );

            $project->help_cost = !empty($_POST['help_cost'])?1:0;

            foreach ($fields as $fieldPost => $fieldTable) {
                if (isset($_POST[$fieldPost])) {
                    $user->$fieldTable = $_POST[$fieldPost];
                }
            }

            $user->entity_type = !empty($_POST['user_entity_type'])?1:0;

            // Avatar
            if (isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                $user->user_avatar = $_FILES['avatar_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['avatar-' . $user->avatar->hash . '-remove'])) {
                $user->avatar->remove($errors);
                $user->user_avatar = null;
            }

            // Tratamiento de la imagen vip mediante el modelo User\Vip
            if ($vip instanceof Model\User\Vip) {
                if (isset($_FILES['vip_image_upload']) && $_FILES['vip_image_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                    $vip->image = $_FILES['vip_image_upload'];
                    $vip->save($errors);
                }

                // tratar si quitan la imagen vip
                if ($vip->image instanceof Image && !empty($_POST['vip_image-' . $vip->image->hash . '-remove'])) {
                    $vip->image->remove($errors);
                    $vip->remove();
                }
            }

            // ojo si es receptor de pruebas, no machacarlo
            if (in_array('15', $user->interests)) $_POST['user_interests'][] = '15';
            $user->interests = $_POST['user_interests'];

            //tratar webs existentes
            foreach ($user->webs as $i => $web) {
                // luego aplicar los cambios

                if (isset($_POST['web-' . $web->id . '-url'])) {
                    $web->url = $_POST['web-' . $web->id . '-url'];
                }

                //quitar las que quiten
                if (!empty($_POST['web-' . $web->id . '-remove'])) {
                    unset($user->webs[$i]);
                }
            }

            //tratar nueva web
            if (!empty($_POST['web-add'])) {
                $user->webs[] = new Model\User\Web(array(
                            'url' => 'http://'
                        ));
            }
            /// este es el único save que se lanza desde un metodo process_
            if ($user->save($errors)) {
                $log_action = 'Actualizado su perfil';
//                Message::info(Text::get('user-profile-saved'));

                // si no español, aplicamos auto traducción
                if ($user->lang != Config::get('lang')) {
                    // primero verificamos que no tenga traducido ya ese idioma
                    if (!Model\User::isTranslated($user->id, $user->lang)) {

                        $user->about_lang = $user->about;
                        $user->keywords_lang = $user->keywords;
                        $user->contribution_lang = $user->contribution;
                        $user->saveLang($errors);

                    }
                }
                $user = Model\User::flush();

                return true;
            } else {
                $log_action = '¡ERROR! al actualizar su perfil';
                Message::error(Text::get('user-save-fail'));
                return false;
            }
        }


        /**
         * Tratamiendo del formulario de datos personales
         *
         * @param string(59) $id del usuario logueado
         * @param array $errors  (por referencia)
         * @param string $log_action  (por referencia)
         * @return boolean si se guarda bien
         */
        public static function process_personal ($id, &$errors, &$log_action) {

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
                $personalData[$field] = $_POST[$field];
            }

            // actualizamos estos datos en los personales del usuario
            if (Model\User::setPersonal($id, $personalData, true, $errors)) {
                Message::info(Text::get('user-personal-saved'));
                $log_action = 'Modificado sus datos personales'; //feed admin
                return true;
            } else {
                Message::error(Text::get('user-save-fail'));
                $log_action = '¡ERROR! al modificar sus datos personales'; //feed admin
                return false;
            }
        }


        /**
         * Cambio de email / contraseña
         *
         * @param object $user instancia de Model\User  (por referencia)
         * @param array $errors  (por referencia)
         * @param string $log_action  (por referencia)
         * @return boolean si se guarda bien
         */
        public static function process_access (&$user, &$errors, &$log_action) {
            // E-mail
            if (!empty($_POST['user_nemail']) || !empty($_POST['user_remail'])) {
                if (empty($_POST['user_nemail'])) {
                    $errors['email'] = Text::get('error-user-email-empty');
                } elseif (!\Goteo\Library\Check::mail($_POST['user_nemail'])) {
                    $errors['email'] = Text::get('error-user-email-invalid');
                } elseif (empty($_POST['user_remail'])) {
                    $errors['email_retry'] = Text::get('error-user-email-empty');
                } elseif (strcmp($_POST['user_nemail'], $_POST['user_remail']) !== 0) {
                    $errors['email_retry'] = Text::get('error-user-email-confirm');
                } else {
                    $user->email = $_POST['user_nemail'];
                    unset($_POST['user_nemail']);
                    unset($_POST['user_remail']);
                    Message::info(Text::get('user-email-change-sent'));

                    $log_action = 'Cambiado su email'; //feed admin
                }
            }
            // Contraseña
            if (!empty($_POST['user_npassword']) || !empty($_POST['user_rpassword'])) {
                // No verificamos la contraseña actual (ni en recover ni en normal) porque los usuarios que acceden mediante servicio no tienen contraseña
                if (empty($_POST['user_npassword'])) {
                    $errors['password_new'] = Text::get('error-user-password-empty');
                } elseif (!\Goteo\Library\Check::password($_POST['user_npassword'])) {
                    $errors['password_new'] = Text::get('error-user-password-invalid');
                } elseif (empty($_POST['user_rpassword'])) {
                    $errors['password_retry'] = Text::get('error-user-password-empty');
                } elseif (strcmp($_POST['user_npassword'], $_POST['user_rpassword']) !== 0) {
                    $errors['password_retry'] = Text::get('error-user-password-confirm');
                } else {
                    $user->password = $_POST['user_npassword'];
                    unset($_POST['user_password']);
                    unset($_POST['user_npassword']);
                    unset($_POST['user_rpassword']);
                    Message::info(Text::get('user-password-changed'));

                    $log_action = 'Cambiado su contraseña'; //feed admin
                }
            }
            if (empty($errors) && $user->save($errors)) {
                // Refresca la sesión.
                $user = Model\User::flush();
                return true;
            } else {
                Message::error(Text::get('user-save-fail'));
                $log_action = '¡ERROR! al cambiar email/contraseña'; //feed admin
                return false;
            }
        }


        /**
         * Tratamiendo del formulario de preferencias de notificación
         *
         * @param string(59) $id del usuario logueado
         * @param array $errors  (por referencia)
         * @param string $log_action  (por referencia)
         * @return boolean si se guarda bien
         */
        public static function process_preferences ($id, &$errors, &$log_action) {

            $fields = array(
                'updates',
                'threads',
                'rounds',
                'mailing',
                'email',
                'tips' ,
                'comlang',
                'currency'
            );

            $preferences = array();

            foreach ($fields as $field) {
                $preferences[$field] = $_POST[$field];
            }

            // actualizamos estos datos en las preferencias del usuario
            if (Model\User::setPreferences($id, $preferences, $errors)) {
                Message::info(Text::get('user-prefer-saved'));
                $log_action = 'Modificado las preferencias de notificación'; //feed admin
                $_SESSION['currency']=$_POST['currency'];
                return true;
            } else {
                Message::error(Text::get('user-save-fail'));
                $log_action = '¡ERROR! al modificar las preferencias de notificación'; //feed admin
                return false;
            }
        }

    }

}
