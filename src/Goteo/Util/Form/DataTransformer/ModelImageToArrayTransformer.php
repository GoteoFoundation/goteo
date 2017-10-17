<?php

namespace Goteo\Util\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Goteo\Model\Image;

class ModelImageToArrayTransformer implements DataTransformerInterface {

    public function transform($image) {
        return is_array($image) ? $image : [$image];
    }

    public function reverseTransform($image) {
        // var_dump($image);die;
        // Sum current + uploads
        $img = isset($image['current']) && is_array($image['current']) ? $image['current'] : [];
        if($image['uploads']) {
            if(is_array($image['uploads'])) {
                $img = array_merge($img, $image['uploads']);
            }
        }
        // var_dump($img);die;
        return $img;
        // return null;
    }
}
