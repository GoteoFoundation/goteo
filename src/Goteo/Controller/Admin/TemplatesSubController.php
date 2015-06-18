<?php
/**
 * Gestion de plantillas de emails
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Library\Template;

class TemplatesSubController extends AbstractSubController {

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
      'edit' => 'Editando Plantilla',
      'translate' => 'Traduciendo Plantilla',
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


    static protected $label = 'Plantillas de email';


    protected $filters = array (
      'id' => '',
      'group' => '',
      'name' => '',
    );


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->filters, $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {

        $errors = array();

        // valores de filtro
        $groups    = Template::groups();

        switch ($action) {
            case 'edit':
                // si estamos editando una plantilla
                $template = Template::get($id);

                // si llega post, vamos a guardar los cambios
                if ($this->isPost()) {
                    $template->title = $this->getPost('title');
                    $template->text  = $this->getPost('text');
                    if ($template->save($errors)) {
                        Message::info('La plantilla se ha actualizado correctamente');

                        // tratar si han marcado pendiente de traducir
                        if ($this->getPost('pending') == 1 && !\Goteo\Core\Model::setPending($id, 'template')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        return $this->redirect("/admin/templates");
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                }


                // sino, mostramos para editar
                return array(
                        'folder' => 'templates',
                        'file' => 'edit',
                        'template' => $template
                 );
                break;
            case 'list':
                // si estamos en la lista de páginas
                $templates = Template::getAll($filters);

                return array(
                        'folder' => 'templates',
                        'file' => 'list',
                        'templates' => $templates,
                        'groups' => $groups,
                        'filters' => $filters
                );
                break;
            default:
                Message::error('No se ha especificado una acción válida para plantillas en la URL');
                return $this->redirect('/admin/templates');
        }

    }

}

