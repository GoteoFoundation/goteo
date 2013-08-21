<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Model;

    class Document extends \Goteo\Core\Controller {

        public function index($id, $name) {
            try {
                $doc = Model\Contract\Document::get($id);
                // mime type en el header
                header("Content-type: " . $doc->type);
                // ruta absoluta a contract_docs
                $path = Model\Contract\Document::$dir_docs . $doc->name;
                // coger contenidos y ponerlos
                echo file_get_contents($path);
            } catch(\PDOException $e) {
                die("No se ha podido recuperar el documento:<br />" . $e->getMessage());
            }
            
        }

    }

}