<?php

namespace Goteo\Controller\Admin;

class WordcountSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Creando Usuario',
  'move' => 'Moviendo a otro Nodo el usuario ',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Usuario',
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
  'manage' => 'Gestionando Usuario',
  'impersonate' => 'Suplantando al Usuario',
);


static protected $label = 'Conteo de palabras';


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process ($action = 'list', $id = null) {

        $wordcount = array();

        return array(
                'folder' => 'base',
                'file' => 'wordcount',
                'wordcount' => $wordcount
        );

    }

}

