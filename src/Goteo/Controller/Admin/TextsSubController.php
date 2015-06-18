<?php
/**
 * Gestion de textos
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
    Goteo\Application\Message,
    Goteo\Application\Config,
	Goteo\Library\Feed;

class TextsSubController extends AbstractSubController {

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
      'edit' => 'Editando Original',
      'translate' => 'Traduciendo Texto',
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


    static protected $label = 'Textos interficie';


    protected $filters = array (
      'group' => '',
      'text' => '',
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
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {


        // valores de filtro
        $groups    = Text::groups();

        // metemos el todos
        array_unshift($groups, 'Todas las agrupaciones');

        //@fixme temporal hasta pasar las agrupaciones a tabal o arreglar en el list.html.php
        $data = Text::getAll($filters, 'original');
        foreach ($data as $key=>$item) {
            $data[$key]->group = $groups[$item->group];
        }

        switch ($action) {
            case 'list':
                return array(
                        'folder' => 'texts',
                        'file' => 'list',
                        'data' => $data,
                        'columns' => array(
                            'edit' => '',
                            'text' => 'Texto',
                            'group' => 'Agrupación'
                        ),
                        'url' => '/admin/texts',
                        'filters' => array(
                            'filtered' => $filters['filtered'],
                            'group' => array(
                                    'label'   => 'Filtrar:',
                                    'type'    => 'select',
                                    'options' => $groups,
                                    'value'   => $filters['group']
                                ),
                            'text' => array(
                                    'label'   => 'Texto:',
                                    'type'    => 'input',
                                    'options' => null,
                                    'value'   => $filters['text']
                                )
                            /*,
                            'idfilter' => array(
                                    'label'   => 'Id:',
                                    'type'    => 'input',
                                    'options' => null,
                                    'value'   => $filters['idfilter']
                                )*/
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    $errors = array();

                    $id = $this->getPost('id');
                    $text = $this->getPost('text');

                    $data = array(
                        'id' => $id,
                        'text' => $this->getPost('text')
                    );

                    if (Text::update($data, $errors)) {
                        Message::info('El texto ha sido actualizado');

                        // tratar si han marcado pendiente de traducir
                        // no usamos Core\Model porque no es tabla _lang
                        if ($this->getPost('pending') == 1) {
                            $ok = Text::setPending($id, $errors);
                            if (!$ok) {
                                Message::error(implode('<br />', $errors));
                            }
                        }


                        return $this->redirect("/admin/texts");
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $text = Text::getPurpose($id);
                }

                return array(
                        'folder' => 'texts',
                        'file' => 'edit',
                        'data' => (object) array (
                            'id' => $id,
                            'text' => $text
                        ),
                        'form' => array(
                            'action' => '/admin/texts/edit/'.$id,
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Aplicar'
                            ),
                            'fields' => array (
                                'idtext' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden',
                                    'properties' => '',
                                ),
                                'newtext' => array(
                                    'label' => 'Texto',
                                    'name' => 'text',
                                    'type' => 'textarea',
                                    'properties' => 'cols="100" rows="6"',
                                )
                            )
                    )
                );

                break;
            default:
                return $this->redirect("/admin");
        }

    }

}
