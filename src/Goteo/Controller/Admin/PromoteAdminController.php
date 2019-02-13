<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Promote;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class PromoteAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-cubes"></i>';

	// this modules is part of a specific group
	public static function getGroup() {
		return 'main';
	}

	public static function getRoutes() {
		return [
			new Route(
				'/',
				['_controller' => __CLASS__ . "::listAction"]
			)
		];
	}


	public function listAction(Request $request) {
    $promoted = Promote::getList(false);
		$fields = ['id','project','name','status','active','order','actions'];

		return $this->viewResponse('admin/promote/list', [
			'list' => $promoted,
			'fields' => $fields,
			'link_prefix' => '/promote/edit/',
			'total' => 20,
			'limit' => 20,
			// 'filter' => [
			// 	'_action' => '/promote',
			// 	'q' => Text::get('admin-stories-global-search'),
			// ],
		]);

	}

}
