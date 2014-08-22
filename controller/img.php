<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Model;

    class Img extends \Goteo\Core\Controller {

        public function index($params = '200x200', $filename) {

            //  $width = 200, $height = 200, $crop = false
            if (preg_match('/(\d+)x(\d+)([c]?)/', $params, $matches)) {

                $width = $matches[1];
                $height = $matches[2];
                $crop = ($matches[3] == 'c');

                // die("{$width}  {$height} {$crop} {$filename}");

                $image = new Model\Image;
                $image->name = $filename;
                $image->display($width, $height, $crop);

                die(\trace($image));

            } else {
                throw new Error(Error::NOT_FOUND);
            }
        }

    }

}