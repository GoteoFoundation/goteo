<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
		Goteo\Library\WallFriends,
        Goteo\Model;

    class Wof extends \Goteo\Core\Controller {

        public function index($id, $type = 1, $all_avatars = 1, $width = 200) {
			if($wof = new WallFriends($id,$all_avatars)) {
				echo $wof->html($width,$type);
			}
			else {
				throw new Error(Error::NOT_FOUND);
			}
        }

    }

}
