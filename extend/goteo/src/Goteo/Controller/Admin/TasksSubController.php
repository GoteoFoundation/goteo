<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Library\Template,
    Goteo\Application\Message,
    Goteo\Model;

class TasksSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array()) {

        // multiples usos
        $nodes = Model\Node::getList();

        $node = $this->node;

        $errors = array();

        switch ($action)  {
            case 'add':

                // si llega post: creamos
                if ($this->isPost() && $this->hasPost('save')) {

                    // para crear se usa el mismo método save del modelo, hay que montar el objeto
                    $task = new Model\Task();
                    $task->node = !empty($this->getPost('node')) ? $this->getPost('node') : \GOTEO_NODE;
                    $task->text = $this->getPost('text');
                    $task->url = $this->getPost('url');
                    $task->done = null;
                    if($task->save($errors)) {
                      // mensaje de ok y volvemos a la lista de tareas
                      Message::info('Nueva tarea pendiente creada correctamente');
                      throw new Redirection('/admin/tasks');
                    } else {
                        // si hay algun error volvemos a poner los datos en el formulario
                        $task = (object) $_POST;
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $task = (object) array('node'=>\GOTEO_NODE);
                }

                // vista de crear usuario
                return array(
                        'folder' => 'tasks',
                        'file'  => 'edit',
                        'task'  => $task,
                        'nodes' => $nodes,
                        'action' => 'add'
                );

                break;
            case 'edit':

                $task = Model\Task::get($id);

                // si llega post: actualizamos
                if ($this->isPost() && $this->hasPost('save')) {
                    $task->node = !empty($this->getPost('node')) ? $this->getPost('node') : \GOTEO_NODE;
                    $task->text = $this->getPost('text');
                    $task->url = $this->getPost('url');
                    if (!empty($this->getPost('undone'))) {
                        $task->done = null;
                    }
                    if($task->save($errors)) {

                        // mensaje de ok y volvemos a la lista de tareas
                        Message::info('Tarea actualizada');
                        throw new Redirection('/admin/tasks');

                    } else {
                        // si hay algun error volvemos a poner los datos en el formulario
                        Message::error(implode('<br />', $errors));
                    }
                }

                // vista de editar usuario
                return array(
                        'folder' => 'tasks',
                        'file' => 'edit',
                        'task'=>$task,
                        'nodes'=>$nodes,
                        'action' => 'edit'
                );

                break;

            case 'remove':
                $task = Model\Task::get($id);
                if ($task instanceof  Model\Task) {
                    if ($task->remove())  {
                      // mensaje de ok y volvemos a la lista
                      Message::info('Tarea eliminada correctamente');
                    } else {
                        Message::error(implode('<br />', $errors));
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

                return array(
                        'folder' => 'tasks',
                        'file' => 'list',
                        'tasks' => $tasks,
                        'filters' => $filters,
                        'status' => $status,
                        'admin' => $admins,
                        'nodes' => $nodes
                );
            break;
        }

    }

}
