<?php

namespace Goteo\Util\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Goteo\Model\Contract\Document;
use Symfony\Component\HttpFoundation\File\File;

class ModelContractDocumentTransformer implements DataTransformerInterface {

    public function transform($doc) {
        return $doc;
    }

    public function reverseTransform($doc) {
        if(is_array($doc)) {
            // var_dump($doc);
            foreach($doc as $i => $f) {
                if(!$f) continue;
                if(!$f instanceOf Document) {
                    $doc[$i] = new Document($f);
                }
            }
        } elseif($doc instanceOf File) {
            $doc = new Document($doc);
        }

        return $doc;

    }
}
