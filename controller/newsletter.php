<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Core\Model,
        Goteo\Core\View;

	class Newsletter extends \Goteo\Core\Controller {

	    // última newsletter enviada
		public function index () {

            if ($query = Model::query("SELECT html FROM mail WHERE email = 'any' ORDER BY id DESC")) {
                $content = $query->fetchColumn();
                return new View ('view/email/newsletter.html.php', array('content'=>$content));
            }
		}

    }

}