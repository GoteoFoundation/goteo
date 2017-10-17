<?php

namespace Goteo\Util\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Goteo\Model\Image;

class UploadImageTransformer implements DataTransformerInterface {

    public function transform($image) {
        return null;
    }

    public function reverseTransform($image) {
        if(is_array($image)) {
            foreach($image as $i => $img) {
                if(!$img) continue;

                // Convert File to Image
                if(!$img instanceOf Image) {
                    $image[$i] = new Image($img);
                }
            }
        }
        return $image;
    }
}

