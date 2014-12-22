<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Library\Cacher,
        Goteo\Model;

    class Image extends \Goteo\Core\Controller {
        public function __construct() {
            //activamos la cache para todo el controlador image
            \Goteo\Core\DB::cache(true);
        }

        public function index($id, $width = 200, $height = 200, $crop = false) {
            if ($image = Model\Image::get($id)) {
                $image->setCache(new Cacher());
                $image->display($width, $height, $crop);
            } else {
                throw new Error(Error::NOT_FOUND);
            }
        }

    }

}
