<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
		Goteo\Library\WallFriends,
        Goteo\Model;

    class Wof extends \Goteo\Core\Controller {

        public function index($id, $width = 200, $height = 200, $mode = 0) {
			if($wof = new WallFriends($id)) {
				echo $wof->html($width,$height,$mode);
			}
			else {
				throw new Error(Error::NOT_FOUND);
			}
        }

    }

}
