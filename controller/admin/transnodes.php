<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Library\Mail,
		Goteo\Library\Template,
        Goteo\Model;

    class Transnodes {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors  = array();

            switch ($action) {
                case 'add':
                    // nodos que están más allá de edición y con traducción deshabilitada
                    $availables = Model\User\Translate::getAvailables('node');
                case 'edit':
                case 'assign':
                case 'unassign':
                case 'send':

                    // a ver si tenemos nodo
                    if (empty($id) && !empty($_POST['node'])) {
                        $id = $_POST['node'];
                    }

                    if (!empty($id)) {
                        $node = Model\Node::getMini($id);
                    } elseif ($action != 'add') {
                        Message::Error('No hay nodo sobre la que operar');
                        throw new Redirection('/admin/transnodes');
                    }

                    // asignar o desasignar
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $userData = Model\User::getMini($user);

                        $assignation = new Model\User\Translate(array(
                            'item' => $node->id,
                            'type' => 'node',
                            'user' => $user
                        ));

                        switch ($action) {
                            case 'assign': // se la ponemos
                                $assignation->save($errors);
                                $what = 'Asignado';
                                break;
                            case 'unassign': // se la quitamos
                                $assignation->remove($errors);
                                $what = 'Desasignado';
                                break;
                        }

                        if (empty($errors)) {
                            // Evento Feed
                            $log = new Feed();
                            $log->populate($what . ' traduccion de nodo (admin)', '/admin/transnodes',
                                \vsprintf('El admin %s ha %s a %s la traducción del nodo %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', $what),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('node', $node->name, $node->id)
                            )));
                            $log->setTarget($userData->id, 'user');
                            $log->doAdmin('admin');
                            unset($log);
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }

                        $action = 'edit';
                    }
                    // fin asignar o desasignar

                    // añadir o actualizar
                    // se guarda el idioma original y si la traducción está abierta o cerrada
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        echo \trace($_POST);

                        // ponemos los datos que llegan
                        $sql = "UPDATE `node` SET lang = :lang, translate = 1 WHERE id = :id";
                        if (Model\Project::query($sql, array(':lang'=>$_POST['lang'], ':id'=>$id))) {
                            if ($action == 'add') {
                                Message::Info('La nodo '.$node->name.' se ha habilitado para traducir');
                            } else {
                                Message::Info('Datos de traducción actualizados');
                            }

                            if ($action == 'add') {

                                // Evento Feed
                                $log = new Feed();
                                $log->populate('nodo habilitada para traducirse (admin)', '/admin/transnodes',
                                    \vsprintf('El admin %s ha %s la traducción del nodo %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('relevant', 'Habilitado'),
                                        Feed::item('node', $node->name, $node->id)
                                )));
                                $log->doAdmin('admin');
                                unset($log);

                                $action = 'edit';
                            }
                        } else {
                            Message::Error('Ha fallado al habilitar la traducción del nodo ' . $node->name);
                        }
                    }

                    if ($action == 'send') {
                        // Informar a los admines que la traduccion está habilitada
                        // Obtenemos la plantilla para asunto y contenido

                        $template = Template::get(32);
                        // Sustituimos los datos
                        $subject = str_replace('%NODENAME%', $node->name, $template->title);
                        $search  = array('%OWNERNAME%', '%NODENAME%', '%SITEURL%');
                        $replace = array($node->user->name, $node->name, SITE_URL);
                        $content = \str_replace($search, $replace, $template->text);
                        // iniciamos mail
                        $mailHandler = new Mail();
                        $mailHandler->to = $node->user->email;
                        $mailHandler->toName = $node->user->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send()) {
                            Message::Info('Se ha enviado un email a <strong>'.$node->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
                        } else {
                            Message::Error('Ha fallado informar a <strong>'.$node->user->name.'</strong> de la posibilidad de traducción de su nodo');
                        }
                        unset($mailHandler);

                        $action = 'edit';
                    }


                    $node->translators = Model\User\Translate::translators($id, 'node');
                    $translators = Model\User::getAll(array('role'=>'translator'));
                    // añadimos al dueño del proyecto en el array de traductores
                    array_unshift($translators, $node->user);


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'transnodes',
                            'file'   => 'edit',
                            'action' => $action,
                            'availables' => $availables,
                            'translators' => $translators,
                            'node'=> $node
                        )
                    );

                    break;
                case 'close':
                    // la sentencia aqui mismo
                    // el campo translate del nodo $id a false
                    $sql = "UPDATE `node` SET translate = 0 WHERE id = :id";
                    if (Model\Node::query($sql, array(':id'=>$id))) {
                        Message::Info('La traducción del nodo '.$node->name.' se ha finalizado');

                        Model\Node::query("DELETE FROM user_translate WHERE type = 'node' AND item = :id", array(':id'=>$id));

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('traducción nodo finalizada (admin)', '/admin/transnodes',
                            \vsprintf('El admin %s ha dado por %s la traducción del nodo %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Finalizada'),
                                Feed::item('node', $node->name, $node->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    } else {
                        Message::Error('Falló al finalizar la traducción del nodo ' . $node->name);
                    }
                    break;
            }

            $nodes = Model\Node::getTranslates($filters);
            $admins = Model\Node::getAdmins();
            $translators = Model\User::getAll(array('role'=>'translator'));

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'transnodes',
                    'file' => 'list',
                    'nodes' => $nodes,
                    'filters' => $filters,
                    'fields'  => array('admin', 'translator'),
                    'admins' => $admins,
                    'translators' => $translators
                )
            );
            
        }

    }

}
