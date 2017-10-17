<?php

namespace Goteo\Util\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Goteo\Model\Image;
use Symfony\Component\HttpFoundation\File\File;

class ModelImageTransformer implements DataTransformerInterface {

    public function transform($image) {
        return $image;
    }

    public function reverseTransform($image) {
        if(is_array($image)) {
            // var_dump($image);
            foreach($image as $i => $img) {
                if(!$img) continue;
                if(!$img instanceOf Image) {
                    $image[$i] = Image::get($img);
                }
            }
        } elseif($image instanceOf File) {
            $image = new Image($image);
        }

        return $image;

    }
}

