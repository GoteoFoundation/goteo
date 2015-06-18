<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
    Goteo\Model;

class NodeSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Datos actuales',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nueva Micronoticia',
      'move' => 'Reubicando el aporte',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe de proyecto',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando',
      'translate' => 'Traduciendo Micronoticia',
      'reorder' => 'Ordenando las entradas en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Gestionando proyectos de la convocatoria',
      'admins' => 'Viendo administradores',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configurando la convocatoria',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
      'keywords' => 'Palabras clave',
      'view' => 'Gestión de retornos',
      'info' => 'Información de contacto',
      'send' => 'Comunicación enviada',
      'init' => 'Iniciando un nuevo envío',
      'activate' => 'Iniciando envío',
      'detail' => 'Viendo destinatarios',
    );


    static protected $label = 'Datos del Canal';

    /**
     * Overwrite some permissions
     * @param  User    $user [description]
     * @param  [type]  $node [description]
     * @return boolean       [description]
     */
    static public function isAllowed(Model\User $user, $node) {
        // Central node not allowed here
        if(Config::isMasterNode($node)) return false;
        return parent::isAllowed($user, $node);
    }

    public function adminsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('admins', $id, $this->filters, $subaction));
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

        $node = Model\Node::get($this->node);
        if($this->isMasterNode()) {
            Message::info('No hay nada que gestionar aquí para Goteo Central');
            return $this->redirect('/admin');
        }

        $langs = \Goteo\Application\Lang::listAll('object', false);
        unset($langs['es']);

        $errors = array();

        switch ($action) {
            case 'edit':
                if ($this->isPost()) {

                    $fields = array(
                        'name',
                        'subtitle',
                        'email',
                        'location',
                        'description',
                        'twitter',
                        'facebook',
                        'google',
                        'linkedin',
                        'owner_background'
                    );

                    foreach ($fields as $field) {
                        if ($this->hasPost($field)) {
                            $node->$field = $this->getPost($field);
                        }
                    }

                    // tratar si quitan la imagen
                    if ($this->getPost('logo-' . $node->logo->hash .  '-remove')) {
                        if ($node->logo instanceof Model\Image) $node->logo->remove($errors);
                        $node->logo = null;
                    }

                    // tratar la imagen y ponerla en la propiedad logo
                    if(!empty($_FILES['logo_upload']['name'])) {
                        if ($node->logo instanceof Model\Image) $node->logo->remove($errors);
                        $node->logo = $_FILES['logo_upload'];
                    } else {
                        $node->logo = (isset($node->logo->id)) ? $node->logo->id : null;
                    }

                    // tratar si quitan el sello
                    if ($this->getPost('label-' . $node->label->hash .  '-remove')) {
                        if ($node->label instanceof Model\Image) $node->label->remove($errors);
                        $node->label = null;
                    }

                    // tratar la imagen y ponerla en la propiedad label
                    if(!empty($_FILES['label_upload']['name'])) {
                        if ($node->label instanceof Model\Image) $node->label->remove($errors);
                        $node->label = $_FILES['label_upload'];
                    } else {
                        $node->label = (isset($node->label->id)) ? $node->label->id : null;
                    }

                    /// este es el único save que se lanza desde un metodo process_
                    if ($node->update($errors)) {
                        Message::info('Datos del canal actualizados correctamente');
                        return $this->redirect('/admin/node');
                    } else {
                        Message::error('Falló al actualizar los datos del canal:<br />'.implode('<br />', $errors));
                    }

                }

                return array(
                        'folder' => 'node',
                        'file' => 'edit',
                        'node' => $node
                );
                break;

            case 'lang':
                if ($this->isPost() && $this->hasPost('lang')) {
                    $_SESSION['translate_lang'] = $this->getPost('lang');
                    Message::info('Ahora estás traduciendo al <strong>'.$langs[$_SESSION['translate_lang']]->name.'</strong>');
                    return $this->redirect('/admin/node/translate');
                }
                break;

            case 'translate':
                if (empty($_SESSION['translate_lang'])) {
                    $_SESSION['translate_lang'] = 'en';
                }

                if ($this->isPost() && $this->hasPost('savelang')) {

                    $node->lang_lang = $this->getPost('lang');
                    $node->subtitle_lang = $this->getPost('subtitle');
                    $node->description_lang = $this->getPost('description');

                    /// este es el único save que se lanza desde un metodo process_
                    if ($node->updateLang($errors)) {
                        Message::info('Traducción del canal al '.$langs[$_SESSION['translate_lang']].' actualizada correctamente');
                        return $this->redirect('/admin/node');
                    } else {
                        Message::error('Falló al actualizar la traducción al '.$langs[$_SESSION['translate_lang']]);
                    }

                }

                $nodeLang = Model\Node::get($node->id, $_SESSION['translate_lang']);

                return array(
                        'folder' => 'node',
                        'file' => 'translate',
                        'langs' => $langs,
                        'node' => $node,
                        'nodeLang' => $nodeLang
                );



                break;

            case 'admins':
                return array(
                        'folder' => 'node',
                        'file' => 'admins',
                        'node' => $node
                );
                break;

            default:
                return array(
                        'folder' => 'node',
                        'file' => 'list',
                        'node' => $node
                );
        }
    }

}


