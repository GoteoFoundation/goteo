<?php

namespace Goteo\Controller {

	use Goteo\Library;

	class Image extends \Goteo\Core\Controller {

	    public function index ($id, $width = 200, $height = 200) {
		    if($image = Library\Image::get($id)) {
		        $image->display($width, $height);
		    }
		}

    }

}