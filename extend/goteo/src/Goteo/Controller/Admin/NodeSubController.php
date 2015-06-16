<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

class NodeSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $node = Model\Node::get($this->node);
        if($this->isDefaultNode()) {
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


