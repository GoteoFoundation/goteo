<?php

namespace Goteo\Core {

    use \Goteo\Core\Redirection;

    abstract class Controller {

        /**
         * Inicio de sesión.
         * Si no se le pasan parámetros carga el tpl de identificación.
         *
         * @param string $username Nombre de usuario
         * @param string $password Contraseña
         */
        public function login () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'];
                $password = $_POST['password'];
                if (false !== ($user = (\Goteo\Model\User::login($username, $password)))) {
                    $_SESSION['user'] = $user;
                    throw new Redirection('/dashboard');
                }
                else {
                    $error = true;
                }
            }
            include 'view/user/login.html.php';
        }

        /**
         * Cerrar sesión.
         */
        public function logout() {
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            session_destroy();
            throw new Redirection('/');
            die;
        }

    }
}