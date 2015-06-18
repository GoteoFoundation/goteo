<?php
/**
 * Gestion de tareas (deprecated)
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Library\Template,
    Goteo\Application\Message,
    Goteo\Application\Config,
    Goteo\Model;

class TasksSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nueva Tarea',
      'move' => 'Moviendo a otro Nodo el proyecto',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Tarea',
      'translate' => 'Traduciendo Tag',
      'reorder' => 'Ordenando los padrinos en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Informe Impulsores',
      'admins' => 'Asignando administradores del Canal',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configuración de campaña del proyecto',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
      'keywords' => 'Palabras clave',
      'view' => 'Apadrinamientos',
      'info' => 'Información de contacto',
      'send' => 'Comunicación enviada',
      'init' => 'Iniciando un nuevo envío',
      'activate' => 'Iniciando envío',
      'detail' => 'Viendo destinatarios',
      'dates' => 'Fechas del proyecto',
      'accounts' => 'Cuentas del proyecto',
      'images' => 'Imágenes del proyecto',
      'assign' => 'Asignando a una Convocatoria el proyecto',
      'open_tags' => 'Asignando una agrupación al proyecto',
      'rebase' => 'Cambiando Id de proyecto',
      'consultants' => 'Cambiando asesor del proyecto',
      'paypal' => 'Informe PayPal',
      'geoloc' => 'Informe usuarios Localizados',
      'calls' => 'Informe Convocatorias',
      'donors' => 'Informe Donantes',
      'top' => 'Top Cofinanciadores',
      'currencies' => 'Actuales ratios de conversión',
      'preview' => 'Previsualizando Historia',
    );


    static protected $label = 'Tareas admin';


    protected $filters = array (
      'done' => '',
      'user' => '',
      'node' => '',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


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
