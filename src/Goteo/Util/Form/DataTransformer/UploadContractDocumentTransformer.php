<?php

namespace Goteo\Util\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Goteo\Model\Contract\Document;

class UploadContractDocumentTransformer implements DataTransformerInterface {

    public function transform($doc) {
        return null;
    }

    public function reverseTransform($doc) {
        if(is_array($doc)) {
            foreach($doc as $i => $f) {
                if(!$f) continue;

                // Convert File to Document
                if(!$f instanceOf Document) {
                    $doc[$i] = new Document($f);
                }
            }
        }
        return $doc;
    }
}

