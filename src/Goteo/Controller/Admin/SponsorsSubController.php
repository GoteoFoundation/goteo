<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Application\Message,
    Goteo\Model\Sponsor;

class SponsorsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nuevo Patrocinador',
      'move' => 'Moviendo a otro Nodo el proyecto',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Patrocinador',
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


    static protected $label = 'Apoyos institucionales';


    public function editAction($id = null, $subaction = null) {

        // gestionar post
        if ($this->isPost()) {
            $id = $this->getPost('id');
            // instancia
            $item = new Sponsor(array(
                'id' => $id,
                'name' => $this->getPost('name'),
                'node' => $this->getPost('node'),
                'image' => $this->getPost('image'),
                'url' => $this->getPost('url'),
                'order' => $this->getPost('order')
            ));
            // tratar si quitan la imagen
            if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                $image = Model\Image::get($item->image);
                $image->remove($errors);
                $item->image = null;
                $removed = true;
            }

            // tratar la imagen y ponerla en la propiedad image
            if(!empty($_FILES['image']['name'])) {
                $item->image = $_FILES['image'];
            }

            if ($item->save($errors)) {
                Message::info('Datos grabados correctamente');
                return $this->redirect($this->url);
            } else {
                Message::error('No se han podido grabar los datos. ' . implode(', ', $errors));
            }
        } else {
            $item = Sponsor::get($id);
        }

        return array(
            'template' => 'admin/generic_edit',
            'data' => $item,
            'form' => array(
                'action' => $this->url . "/edit/$id",
                'submit' => array(
                    'name' => 'update',
                    'label' => Text::get('regular-save')
                ),
                'fields' => array (
                    'id' => array(
                        'label' => '',
                        'name' => 'id',
                        'type' => 'hidden'

                    ),
                    'node' => array(
                        'label' => '',
                        'name' => 'node',
                        'type' => 'hidden'

                    ),
                    'name' => array(
                        'label' => 'Patrocinador',
                        'name' => 'name',
                        'type' => 'text'
                    ),
                    'url' => array(
                        'label' => 'Enlace',
                        'name' => 'url',
                        'type' => 'text',
                        'properties' => 'size=100'
                    ),
                    'image' => array(
                        'label' => 'Logo',
                        'name' => 'image',
                        'type' => 'image'
                    ),
                    'order' => array(
                        'label' => 'Posición',
                        'name' => 'order',
                        'type' => 'text'
                    )
                )
            )
        );
    }


    /**
    * Just the form
    */
    public function addAction($id = null, $subaction = null) {
        return array(
            'template' => 'admin/generic_edit',
            'data' => (object) array('order' => Sponsor::next($this->node), 'node' => $this->node ),
            'form' => array(
                'action' => $this->url . '/edit',
                'submit' => array(
                    'name' => 'update',
                    'label' => 'Añadir'
                ),
                'fields' => array (
                    'id' => array(
                        'label' => '',
                        'name' => 'id',
                        'type' => 'hidden'

                    ),
                    'node' => array(
                        'label' => '',
                        'name' => 'node',
                        'type' => 'hidden'

                    ),
                    'name' => array(
                        'label' => 'Patrocinador',
                        'name' => 'name',
                        'type' => 'text'
                    ),
                    'url' => array(
                        'label' => 'Enlace',
                        'name' => 'url',
                        'type' => 'text',
                        'properties' => 'size=100'
                    ),
                    'image' => array(
                        'label' => 'Logo',
                        'name' => 'image',
                        'type' => 'image'
                    ),
                    'order' => array(
                        'label' => 'Posición',
                        'name' => 'order',
                        'type' => 'text'
                    )
                )
            )
        );
    }


    public function listAction($id = null, $subaction = null) {
        $data = Sponsor::getAll($this->node);
        return array(
            'template' => 'admin/generic_list',
            'addbutton' => 'Nuevo patrocinador',
            'data' => $data,
            'columns' => array(
                'edit' => '',
                'name' => 'Patrocinador',
                'url' => 'Enlace',
                'image' => 'Imagen',
                'order' => 'Posición',
                'up' => '',
                'down' => '',
                'remove' => ''
            ),
            'url' => $this->url
        );

    }

    public function upAction($id = null, $subaction = null) {
        Sponsor::up($id, $this->node);
        return $this->redirect($this->url);
    }

    public function downAction($id = null, $subaction = null) {
        Sponsor::down($id, $this->node);
        return $this->redirect($this->url);
    }

    public function removeAction($id = null, $subaction = null) {
        if (Sponsor::delete($id)) {
            Message::info('Se ha eliminado el registro');
        } else {
            Message::info('No se ha podido eliminar el registro');
        }
        return $this->redirect($this->url);
    }

}
