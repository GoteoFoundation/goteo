<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Model\Template;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends \Goteo\Core\Controller {

	public function __construct() {
		// Cache & replica read activated in this controller
        \Goteo\Core\DB::cache(true);
		\Goteo\Core\DB::replica(true);
	}

	// última newsletter enviada
	public function indexAction($id = null, Request $request) {

		$lang = Lang::current('id');

		$sql = "SELECT content FROM mail WHERE
                    " . ($id ? ' id=' . (int) $id . ' AND' : '') . "
                    email = 'any'
                    AND template = " . Template::NEWSLETTER . "
                ORDER BY
                    lang = '$lang' DESC,
                    date DESC
                LIMIT 1";
		if (!($query = Model::query($sql)) || $query->rowCount() == 0) {
			$sql = "SELECT content FROM mail WHERE email = 'any' AND template = " . Template::NEWSLETTER . " ORDER BY date DESC LIMIT 1";
		}
		if (($query = Model::query($sql)) && $query->rowCount() > 0) {

			if ($content = $query->fetchColumn()) {
				return $this->viewResponse('email/newsletter', array('content' => $content));
			}
		}
		throw new ModelNotFoundException('Newsletter not found!');

	}

}
