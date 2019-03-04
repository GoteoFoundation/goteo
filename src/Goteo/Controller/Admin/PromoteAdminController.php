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
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Message;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Promote;
use Goteo\Model\Project;
use Goteo\Model\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Goteo\Library\Check;


class PromoteAdminController extends AbstractAdminController {
	protected static $icon = '<i class="fa fa-2x fa-star"></i>';

	// this modules is part of a specific group
	public static function getGroup() {
		return 'main';
	}

	public static function getRoutes() {
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

		$nodes = array_keys(Node::getList());
		return $this->redirect('/admin/promote/channel/' . $nodes[0]);
	}

	public function listAction($channel, Request $request) {

		try {
			Node::get($channel);
			$selectedNode = $channel;
			$promoted = Promote::getList(false, $channel); // This method has to be changed for a new Promote::getList that does paging. Similar to Stories::getList
			$total = count($promoted);
		}

		catch (ModelNotFoundException $exception) {
			$promoted = [];
			$total = 0;
			$selectedNode = false;
			Message::error(Text::get('fatal-error-channel'));
		}

		return $this->viewResponse('admin/promote/list', [
			'selectedNode' => $selectedNode,
			'nodes' => $this->user->getNodeNames(),
			'list' => $promoted,
			'total' => $total,
			'limit' => 20,
		]);

	}

	public function deleteAction($channel, $id, Request $request) {
        $promote = $this->validatePromote($id);

		Check::reorderDecrease($id,'promote', 'id', 'order', ['node' => $channel]);
		$promote->dbDelete();

        return $this->redirect('/admin/promote/channel/' . $channel);
	}
}