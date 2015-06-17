<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class RecentSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Nuevo Destacado',
  'move' => 'Moviendo a otro Nodo el proyecto',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe Financiero del proyecto',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Destacado',
  'translate' => 'Traduciendo Destacado',
  'reorder' => 'Ordenando los padrinos en Portada',
  'footer' => 'Ordenando las entradas en el Footer',
  'projects' => 'Gestionando proyectos de la convocatoria',
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
);


static protected $label = 'Actividad reciente';


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process ($action = 'list', $id = null) {

        $node = $this->node;

        $feed = $this->getGet('feed') ? $this->getGet('feed') : 'all';

        $items = Feed::getAll($feed, 'admin', 50, $node);

        return array(
                'folder' => 'recent',
                'file' => $action,
                'feed' => $feed,
                'items' => $items
        );

    }

}
