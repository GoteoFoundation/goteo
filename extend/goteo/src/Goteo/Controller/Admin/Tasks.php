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

    class Tasks {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            // multiples usos
            $nodes = Model\Node::getList();

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            $errors = array();

            switch ($action)  {
                case 'add':

                    // si llega post: creamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        $task = new Model\Task();
                        $task->node = !empty($_POST['node']) ? $_POST['node'] : \GOTEO_NODE;
                        $task->text = $_POST['text'];
                        $task->url = $_POST['url'];
                        $task->done = null;
                        if($task->save($errors)) {
                          // mensaje de ok y volvemos a la lista de tareas
                          Message::Info('Nueva tarea pendiente creada correctamente');
                          throw new Redirection('/admin/tasks');
                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $task = (object) $_POST;
                            Message::Error(implode('<br />', $errors));
                        }
                    } else {
                        $task = (object) array('node'=>\GOTEO_NODE);
                    }

                    // vista de crear usuario
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'tasks',
                            'file'  => 'edit',
                            'task'  => $task,
                            'nodes' => $nodes,
                            'action' => 'add'
                        )
                    );

                    break;
                case 'edit':

                    $task = Model\Task::get($id);

                    // si llega post: actualizamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
                        $task->node = !empty($_POST['node']) ? $_POST['node'] : \GOTEO_NODE;
                        $task->text = $_POST['text'];
                        $task->url = $_POST['url'];
                        if (!empty($_POST['undone'])) {
                            $task->done = null;
                        }
                        if($task->save($errors)) {

                            // mensaje de ok y volvemos a la lista de tareas
                            Message::Info('Tarea actualizada');
                            throw new Redirection('/admin/tasks');

                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            Message::Error(implode('<br />', $errors));
                        }
                    }

                    // vista de editar usuario
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'tasks',
                            'file' => 'edit',
                            'task'=>$task,
                            'nodes'=>$nodes,
                            'action' => 'edit'
                        )
                    );

                    break;

                case 'remove':
                    $task = Model\Task::get($id);
                    if ($task instanceof  Model\Task) {
                        if ($task->remove())  {
                          // mensaje de ok y volvemos a la lista
                          Message::Info('Tarea eliminada correctamente');
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }
                        throw new Redirection('/admin/tasks');
                    }
                    break;

                case 'list':
                default:
                    $tasks = Model\Task::getAll($filters, $_SESSION['admin_node']);
                    $status = array(
                                'done' => 'Realizadas',
                                'undone' => 'Pendientes'
                            );
                    $admins = Model\User::getAdmins();

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'tasks',
                            'file' => 'list',
                            'tasks' => $tasks,
                            'filters' => $filters,
                            'status' => $status,
                            'admin' => $admins,
                            'nodes' => $nodes
                        )
                    );
                break;
            }

        }

    }

}
