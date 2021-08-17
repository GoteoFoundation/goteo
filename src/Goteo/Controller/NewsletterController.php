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
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Core\Model;
use Goteo\Model\Template;
use Goteo\Model\Communication;

class NewsletterController extends Controller {

	public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
	}

	// last newsletter public view
	public function indexAction($id = null) {
		$lang = Lang::current('id');
		$sql = "SELECT * FROM mail WHERE
                    " . ($id ? ' id=' . (int) $id . ' AND' : '') . "
                    email = 'any'
                    AND template = " . Template::NEWSLETTER . "
                ORDER BY
                    lang = '$lang' DESC,
                    date DESC
				LIMIT 1";

		if (!($query = Model::query($sql)) || $query->rowCount() == 0) {
			$sql = "SELECT * FROM mail WHERE email = 'any' AND template = " . Template::NEWSLETTER . " ORDER BY date DESC LIMIT 1";
		}
		if (($query = Model::query($sql)) && $query->rowCount() > 0) {
			if ($mail = $query->fetch()) {
				$extra_vars['content'] = $mail['content'];
				$extra_vars['subject'] = $mail['subject'];
				$extra_vars['unsubscribe'] = SITE_URL . '/user/leave?email=' . $mail['to'];
				$extra_vars['lang'] = $lang;

				if (isset($mail['communication_id'])) {
					$communication = Communication::get($mail['communication_id']);
					$extra_vars['type'] = $communication->type;
					if ($communication->header) $extra_vars['image'] = $communication->getImage()->getLink(1920,335,true, true);
					$extra_vars['promotes'] = $communication->getCommunicationProjects($communication->id);
				}

				return $this->viewResponse('email/newsletter', $extra_vars);
			}
		}
		throw new ModelNotFoundException('Newsletter not found!');
	}

}
