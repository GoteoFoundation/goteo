<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Model;

    class Document extends \Goteo\Core\Controller {

        public function index($id, $name = null) {
            try {
                $doc = Model\Contract\Document::get($id);
                
                if (!$doc instanceof Model\Contract\Document)
                    throw new Error('404', 'No tenemos el documento '.$name);
                
                // mime type en el header
                header("Content-type: " . $doc->type);
                // contenidos
                echo file_get_contents($doc->filedir . $doc->name);
            } catch(\PDOException $e) {
                die("No se ha podido recuperar el documento:<br />" . $e->getMessage());
            }
            
        }

    }

}
