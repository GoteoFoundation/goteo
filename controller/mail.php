<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Library\Mail as Mailer;

	class Mail extends \Goteo\Core\Controller {

	    /**
	     * solo si recibe un token válido
	     */
		public function index ($token) {

            if (!empty($token)) {
                $token = \mybase64_decode($token);
                $parts = explode('¬', $token);

                $link = Mailer::getSinovesLink($parts[2]);
                throw new Redirection($link);
            }

            throw new Redirection('/');
		}

    }

}
