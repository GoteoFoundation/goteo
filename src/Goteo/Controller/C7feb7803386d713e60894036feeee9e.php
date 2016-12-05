<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/*
 * Webservice super secreto para realizar operaciones de admin
 * todos los datos se reciben por POST
 * 'ultra-secret-ws'
 */
namespace Goteo\Controller {

    use Goteo\Model;
    use Goteo\Application\Session;
    use Goteo\Console\UsersSend;
    use Goteo\Library\Check;
    use Goteo\Library\Feed;
    use Goteo\Core\ACL;

    class C7feb7803386d713e60894036feeee9e extends \Goteo\Core\Controller {

        /*
         * Marcar retorno colectivo como cumplido o pendiente
         * 'set-fulsocial'
         */
        public function ce8c56139d45ec05e0aa2261c0a48af9() {

            $user = Session::getUser();
            $project = $_POST['project'];
            $reward = $_POST['reward'];
            $value = $_POST['value'];
            // cogemos roles del usuario en sesion
            // si es admin, ok
            // si es usuario, mirar si es el dueño del proyecto
            if (isset($user->roles['admin']) || isset($user->roles['superadmin'])) {
                $rol = (isset($user->roles['superadmin'])) ? 'superadmin' : 'admin';
                $log_txt = "El {$rol} {$user->id} ";
            } elseif (Model\Project::isMine($project, $user->id)) {
                $log_txt = "El usuario impulsor {$user->id} ";
            } elseif (ACL::check('/'.md5('ultra-secret-ws'))) {
                $log_txt = "El usuario permitido {$user->name} ";
            } else {
                header ('HTTP/1.1 403 Forbidden');
                die;
            }

            if (empty($project) || empty($reward)) {
                header ('HTTP/1.1 400 Bad request');
                echo 'Not enough params';
                die;
            }

            $log_txt .= "ha marcado el retorno colectivo {$reward} del proyecto {$project} como '{$value}'";

            // TODO: Comprobar los parámetros de usuario por seguridad

            $sql = "UPDATE reward SET fulsocial = :val WHERE project = :proj AND type= 'social' AND id = :id";
            if (Model\Project\Reward::query($sql, array(':proj' => $project, ':val' => $value, ':id' => $reward))) {
                header ('HTTP/1.1 200 Ok');
                echo 'OK';
            } else {
                header ('HTTP/1.1 400 Bad request');
                echo 'SQL FAIL';
                die;
            }

            // Evento Feed
            $log = new Feed();
            $log->populate('Gestion retorno cumplido', '/ultra-secret-ws/set-fulsocial', $log_txt);
            $log->doAdmin('usws');
            unset($log);

            die;
        }

        /*
         * Poner la Url de localización del retorno colectivo
         * 'set-rewardurl'
         */
        public function d82318a7bec39ac2b78be96b8ec2b76e() {

            $user = Session::getUser();
            $project = $_POST['project'];
            $reward = $_POST['reward'];
            $value = $_POST['value'];
            // cogemos roles del usuario en sesion
            // si es admin, ok
            // si es usuario, mirar si es el dueño del proyecto
            if (isset($user->roles['admin']) || isset($user->roles['superadmin'])) {
                $rol = "el ";
                $rol .= (isset($user->roles['superadmin'])) ? 'superadmin' : 'admin';
                $who = $user->id;
            } elseif (Model\Project::isMine($project, $user->id)) {
                $rol = "el usuario impulsor";
                $who = $user->id;
            } elseif (ACL::check('/'.md5('ultra-secret-ws'))) {
                $rol = "el usuario permitido";
                $who = $user->name;
            } else {
                header ('HTTP/1.1 403 Forbidden');
                die;
            }

            if (empty($project) || empty($reward)) {
                header ('HTTP/1.1 400 Bad request');
                echo 'Not enough params';
                die;
            }

            if (strpos($value, 'http') !== 0) {
                $value = 'http://' . $value;
            }

            $isValidUrl = filter_var($value, FILTER_VALIDATE_URL);
            if (empty($value) || !$isValidUrl) {
                header ('HTTP/1.1 400 Bad request');
                echo 'Invalid url';
                die;
            }

            // TODO: Comprobar los parámetros de usuario por seguridad
            $sql = "UPDATE reward SET url = :val WHERE project = :proj AND type= 'social' AND id = :id";
            if (Model\Project\Reward::query($sql, array(':proj' => $project, ':val' => $value, ':id' => $reward))) {
                header ('HTTP/1.1 200 Ok');
                echo 'OK';
            } else {
                header ('HTTP/1.1 400 Bad request');
                echo 'SQL FAIL';
                die;
            }

            $log_txt = $rol . " " . $who . " ha puesto la url de localización del retorno colectivo {$reward} del proyecto {$project} a '{$value}'";

            // Evento Feed
            $log = new Feed();
            $log->populate('Gestion url retorno', '/ultra-secret-ws/set-rewardurl', $log_txt);
            $log->doAdmin('usws');
            unset($log);

            // Enviar correo informativo a los asesores del proyecto.
            $project_obj = Model\Project::getMini($project);

            //Añadir siempre a Olivier.
            if (!in_array('olivier', array_keys($project_obj->getConsultants()))) {
                $project_obj->consultants['olivier'] = 'Olivier Schulbaum';
            }

            //Añadir siempre a Manuela.
            if (!in_array('lamanuf', array_keys($project_obj->getConsultants()))) {
                $project_obj->consultants['lamanuf'] = 'Manuela Frudà';
            }

            $project_obj->whodidit = $who;
            $project_obj->whorole = $rol;
            UsersSend::toConsultants('rewardfulfilled', $project_obj);

            die;
        }

    }

}
