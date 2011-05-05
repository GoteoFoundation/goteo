<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Library;

    class Image extends \Goteo\Core\Controller {

        public function index($id, $width = 200, $height = 200) {
            if ($image = Library\Image::get($id)) {
                $image->display($width, $height);
            } else {
                throw new Error(Error::NOT_FOUND);
            }
        }
        
        public function upload ($width = 200, $height = 200) {
            
            if (!empty($_FILES) && count($_FILES) === 1) {
                // Do upload
                $image = new Library\Image(current($_FILES));
                
                if ($image->save()) {                    
                    return $image->getLink($width, $height);
                }
                                
            }
            
        }

    }

}