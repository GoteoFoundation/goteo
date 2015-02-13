<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Core\Model,
        Goteo\Core\View;

	class Newsletter extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador newsletter
            \Goteo\Core\DB::cache(true);
        }

	    // última newsletter enviada
		public function index () {

            if ($query = Model::query("SELECT html FROM mail WHERE email = 'any' AND template = 33 ORDER BY id DESC LIMIT 1")) {
                $content = $query->fetchColumn();
                return new View ('email/newsletter.html.php', array('content'=>$content));
            }
		}

    }

}
