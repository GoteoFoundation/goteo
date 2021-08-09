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
use Goteo\Library\Check;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Promote;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;


class PromoteAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-star"></i>';

	public static function getGroup(): string
    {
		return 'main';
	}

	public static function getRoutes(): array
    {
		return [
			new Route(
				'/',
				['_controller' => __CLASS__ . "::showNodeAction"]
			),
			new Route(
				'/channel/{channel}',
				['_controller' => __CLASS__ . "::listAction"]
			),
			new Route(
				'/delete/channel/{channel}/id/{id}',
				['_controller' => __CLASS__ . "::deleteAction"]
			)
		];
	}

	protected function validatePromote($id) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $promote = $id ? Promote::get($id) : new Promote();

        if(!$promote)
            throw new ModelNotFoundException();

        if($this->user->hasPerm('admin-module-promote') ) {
            return $promote;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-promote-not-active-yet'));
	}

	public function showNodeAction() {
		// $nodes = array_keys(Node::getList());
		$node = Config::get('node');
		return $this->redirect('/admin/promote/channel/' . $node);
	}

	public function listAction($channel, Request $request) {
		try {
			Node::get($channel);
		} catch (ModelNotFoundException $e) {
			Message::error($e->getMessage());
			return $this->redirect('/admin');
		}

		$filters['channel'] = $channel;

		$limit = 20;
		$page = $request->query->get('pag') ?: 0;
		$list = Promote::getList($filters, $page * $limit, $limit, false);
		$total = Promote::getList($filters, 0, 0, true);

		return $this->viewResponse('admin/promote/list', [
			'selectedNode' => $channel,
			'nodes' => $this->user->getNodeNames(),
			'list' => $list,
			'total' => $total,
			'limit' => $limit,
		]);
	}

	public function deleteAction($channel, $id) {
        $promote = $this->validatePromote($id);

		Check::reorderDecrease($id,'promote', 'id', 'order', ['node' => $channel]);
		$promote->dbDelete();

        return $this->redirect('/admin/promote/channel/' . $channel);
	}
}
