<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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
