<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Library\File,
        Goteo\Model;

    class Document extends \Goteo\Core\Controller {

        public function index($id, $name = null) {
            try {
                $doc = Model\Contract\Document::get($id);

                if (!$doc instanceof Model\Contract\Document)
                    throw new Error('404', 'No tenemos el documento '.$name);

                // pero ojo porque al ser el archivo privado quizás habrá que coger los contenidos
                // mime type en el header
                $fp = new File();
                $fp->setBucket(AWS_S3_BUCKET_DOCUMENT, $doc->filedir);

                header("Content-type: " . $doc->type);
                // contenidos
                echo $fp->get_contents($doc->name);

            } catch(\PDOException $e) {
                die("No se ha podido recuperar el documento:<br />" . $e->getMessage());
            }

        }

        public function cert($user, $year) {
            try {
                $pdf = Model\User\Donor::getPdf($user, $year);

                if (empty($pdf))
                    throw new Error('404', 'No se ha generado el certificado de '.$user.' para '.$year);

                $fp = new File();
                $fp->setBucket(AWS_S3_BUCKET_DOCUMENT, 'certs/');

                header("Content-type: application/pdf");
                // archivo
                echo $fp->get_contents($pdf);

            } catch(\PDOException $e) {
                die("No se ha podido recuperar el certificado:<br />" . $e->getMessage());
            }

        }

    }

}
