<?php
/**
 * Historial de envios en el nodo
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Model\Node,
	Goteo\Library\Feed,
	Goteo\Library\Template,
	Goteo\Library\Mail;

class SentSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Emails enviados',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Iniciando briefing',
      'move' => 'Moviendo a otro Nodo el proyecto',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe',
      'viewer' => 'Viendo logs',
      'edit' => 'Gestionando recompensa',
      'translate' => 'Traduciendo Destacado',
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
    );


    static protected $label = 'Historial envíos';


    protected $filters = array (
      'user' => '',
      'template' => '',
      'node' => '',
      'date_from' => '',
      'date_until' => '',
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

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {
        $templates = Template::getAllMini();
        $nodes = Node::getList();
        $node = $this->node;

        if ($filters['filtered'] == 'yes'){
            $sended = Mail::getSended($filters, $node);
        } else {
            $sended = array();
        }

        return array(
                'folder' => 'sended',
                'file' => 'list',
                'filters' => $filters,
                'templates' => $templates,
                'nodes' => $nodes,
                'sended' => $sended
        );

    }

}
