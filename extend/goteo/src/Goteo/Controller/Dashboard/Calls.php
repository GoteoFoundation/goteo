<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\ACL,
        Goteo\Core\Redirection,
		Goteo\Application\Message,
		Goteo\Library\Text;

    class Calls {

        /**
         * Verificación de convocatoria de trabajo
         *
         * @param object $user instancia Model\User del convocador
         * @param string $action por si es 'select'
         * @return array(call, calls)
         */
        public static function verifyCalls($user, $action) {

            $calls = Model\Call::ofmine($user->id);

            // si no tiene, no debería estar aquí
            if (empty($calls) || !is_array($calls)) {
                return array(null, null);
            }


            if ($action == 'select' && !empty($_POST['call'])) {
                // si se selecciona otra convocatoria
                $call = Model\call::getMini($_POST['call']);
            } elseif (!empty($_SESSION['call']->id)) {
                // mantener los datos de la convocatoria de trabajo
                $call = Model\Call::getMini($_SESSION['call']->id);
            }

            if (empty($call)) {
                $call = $calls[0];
            }

            // aqui necesito tener una convocatoria de trabajo,
            // si no hay ninguna ccoge la última
            if ($call instanceof \Goteo\Model\Call) {
                // y con todos los proyectos
                $call->projects = Model\Call\Project::getMini($_SESSION['call']->id, array('all'));
                $_SESSION['call'] = $call;
            } else {
                Message::error('No se puede trabajar con la convocatoria seleccionada, contacta con nosotros');
                $call = null;
            }

            return array($call, $calls);
        }


        /**
         * Procesa la gestión de patrocinadores
         *
         * @param int $id del registro de sponsor
         * @param string(50) $call Id de la convocatoria
         * @param string $action  (add, edit, deletye, up, down)
         * @param array $errors
         * @return object instancia de sponsor para las acciones add / edit
         * @throws Redirection para las acciones delete / up /down
         */
        public static function process_sponsors ($id, $call, $action, &$errors = array()) {

                switch ($action) {
                    case 'add':
                        return (object) array('order' => Model\Call\Sponsor::next($call));
                        break;
                    case 'edit':
                        // gestionar post
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                            // instancia
                            $sponsor = new Model\Call\Sponsor(array(
                                        'id' => $_POST['id'],
                                        'name' => $_POST['name'],
                                        'call' => $call,
                                        'image' => $_POST['prev_image'],
                                        'url' => $_POST['url'],
                                        'order' => $_POST['order']
                                    ));

                            // tratar si quitan la imagen
                            if (isset($_POST['image-' . md5($sponsor->image) . '-remove'])) {
                                $image = Model\Image::get($sponsor->image);
                                $image->remove($errors);
                                $sponsor->image = null;
                                $removed = true;
                            }

                            // tratar la imagen y ponerla en la propiedad image
                            if (!empty($_FILES['image']['name'])) {
                                $sponsor->image = $_FILES['image'];
                            }

                            if ($sponsor->save($errors)) {
                                Message::info('Datos grabados correctamente');
                                if (!$removed)
                                    throw new Redirection('/dashboard/calls/sponsors');
                            } else {
                                Message::error('No se han podido grabar los datos. ' . implode(', ', $errors));
                            }
                        } else {
                            $sponsor = Model\Call\Sponsor::get($id);
                        }

                        return $sponsor;
                        break;
                    case 'delete':
                        //si estamos quitando un patrocinador
                        if (!empty($id)) {

                            if (Model\Call\Sponsor::delete($id)) {
                                Message::error('El proyecto se ha quitado correctamente de la convocatoria');
                            } else {
                                Message::error('Falló al quitar el proyecto: ' . implode('<br />', $errors));
                            }
                        }
                        throw new Redirection('/dashboard/calls/sponsors');
                        break;
                    case 'up':
                        Model\Call\Sponsor::up($id, $call);
                        throw new Redirection('/dashboard/calls/sponsors');
                        break;
                    case 'down':
                        Model\Call\Sponsor::down($id, $call);
                        throw new Redirection('/dashboard/calls/sponsors');
                        break;
                }
        }

    }

}
