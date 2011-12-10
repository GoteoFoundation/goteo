<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Model;

    class Image extends \Goteo\Core\Controller {

        public function index($id, $width = 200, $height = 200, $crop = false) {
            if ($image = Model\Image::get($id)) {
/*
                // cabeceras de cache
                $expires = 60*60*24*60;
                header("Pragma: public");
//                header('Cache-Control: public, max-age='.$expires);
                header('Cache-Control: public');
                
                header('Expires: '. gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
//                header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()-$expires) . ' GMT');
                header('ETag: ' . 'img' . $id . 'w'. $width . 'h'. $height . 'c'. (int) $crop);
  */
                $image->display($width, $height, $crop);
            } else {
                throw new Error(Error::NOT_FOUND);
            }
        }

        public function upload ($width = 200, $height = 200) {

            if (!empty($_FILES) && count($_FILES) === 1) {
                // Do upload
                $image = new Model\Image(current($_FILES));

                if ($image->save()) {
                    return $image->getLink($width, $height);
                }

            }

        }

    }

}