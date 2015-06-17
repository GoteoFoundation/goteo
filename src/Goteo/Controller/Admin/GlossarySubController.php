<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

class GlossarySubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Nueva Pregunta',
  'move' => 'Reubicando el aporte',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe de proyecto',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Término',
  'translate' => 'Traduciendo Término',
  'reorder' => 'Ordenando las entradas en Portada',
  'footer' => 'Ordenando las entradas en el Footer',
  'projects' => 'Gestionando proyectos de la convocatoria',
  'admins' => 'Asignando administradores de la convocatoria',
  'posts' => 'Entradas de blog en la convocatoria',
  'conf' => 'Configurando la convocatoria',
  'dropconf' => 'Gestionando parte económica de la convocatoria',
  'keywords' => 'Palabras clave',
  'view' => 'Gestión de retornos',
  'info' => 'Información de contacto',
);


static protected $label = 'Glosario';


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


    public function process ($action = 'list', $id = null) {

        $errors = array();

        $url = '/admin/glossary';

		if ($this->isPost()) {

                $editing = false;

                if ($this->getPost('id')) {
                    $post = Model\Glossary::get($this->getPost('id'));
                } else {
                    $post = new Model\Glossary();
                }

                // campos que actualizamos
                $fields = array(
                    'id',
                    'title',
                    'text',
                    'media',
                    'legend'
                );

                foreach ($fields as $field) {
                    $post->$field = $this->getPost($field);
                }

                // tratar la imagen y ponerla en la propiedad image
                if(!empty($_FILES['image_upload']['name'])) {
                    $post->image = $_FILES['image_upload'];
                    $editing = true;
                }

                // tratar las imagenes que quitan
                foreach ($post->gallery as $key=>$image) {
                    if ($this->getPost("gallery-{$image->hash}-remove")) {
                        $image->remove($errors, 'glossary');
                        $editing = true;
                    }
                }

                if (!empty($post->media)) {
                    $post->media = new Model\Project\Media($post->media);
                }

                /// este es el único save que se lanza desde un metodo process_
                if ($post->save($errors)) {
                    if ($action == 'edit') {
                        Message::info('El término se ha actualizado correctamente');
                    } else {
                        Message::info('Se ha añadido un nuevo término');
                        $id = $post->id;
                    }
                    $action = $editing ? 'edit' : 'list';

                    // tratar si han marcado pendiente de traducir
                    if ($this->hasPost('pending') && $this->getPost('pending') == 1
                        && !Model\Glossary::setPending($post->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                } else {
                    Message::error(implode('<br />', $errors));
                    Message::error('Ha habido algun problema al guardar los datos');
                }
        }

        switch ($action)  {
            case 'remove':
                // eliminar un término
                if (Model\Glossary::delete($id)) {
                    Message::info('Término eliminado');
                } else {
                    Message::error('No se ha podido eliminar el término');
                }
                break;
            case 'add':
                // nueva entrada con wisiwig
                // obtenemos datos basicos
                $post = new Model\Glossary();

                $message = 'Añadiendo un nuevo término';

                return array(
                        'folder' => 'glossary',
                        'file' => 'edit',
                        'action' => 'add',
                        'post' => $post,
                        'message' => $message
                );
                break;
            case 'edit':
                if (empty($id)) {
                    throw new Redirection('/admin/glossary');
                    break;
                } else {
                    $post = Model\Glossary::get($id);

                    if (!$post instanceof Model\Glossary) {
                        Message::error('La entrada esta corrupta, contacte con nosotros.');
                        $action = 'list';
                        break;
                    }
                }

                $message = 'Editando un término existente';

                return array(
                        'folder' => 'glossary',
                        'file' => 'edit',
                        'action' => 'edit',
                        'post' => $post,
                        'message' => $message
                );
                break;
        }

        // lista de términos
        $posts = Model\Glossary::getAll();

        return array(
                'folder' => 'glossary',
                'file' => 'list',
                'posts' => $posts
        );

    }

}

