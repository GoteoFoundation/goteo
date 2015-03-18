<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Library\Cacher,
        Goteo\Model;

    class Img extends \Goteo\Core\Controller {
        //some predefined sizes
        static private $sizes = array(
                'icon' => '16x16',
                'tiny' => '32x32',
                'thumb' => '56x56',
                'medium' => '192x192',
                'large' => '512x512',
                'big' => '1024x1024',
                'iconc' => '16x16c',
                'tinyc' => '32x32c',
                'thumbc' => '56x56c',
                'mediumc' => '192x192c',
                'largec' => '512x512c',
                'bigc' => '1024x1024c'
            );

        public function index($params = '200x200', $filename = null) {
            //check if predefined size
            if(self::$sizes[$params]) {
                $params = self::$sizes[$params];
            }
            //  $width = 200, $height = 200, $crop = false
            if (preg_match('/(\d+)x(\d+)([c]?)/', $params, $matches)) {

                $width = $matches[1];
                $height = $matches[2];
                $crop = ($matches[3] == 'c');

                // die("{$width}  {$height} {$crop} {$filename}");

                $image = new Model\Image;
                $image->setCache(new Cacher());
                $image->name = $filename;
                $image->display($width, $height, $crop);


            } else {
                throw new Error(Error::NOT_FOUND);
            }
        }

    }

}
