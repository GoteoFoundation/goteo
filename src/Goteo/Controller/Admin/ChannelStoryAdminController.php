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
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeStories;
use Goteo\Model\Stories;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;


class ChannelStoryAdminController extends AbstractAdminController
{
	protected static $icon = '<i class="fa fa-2x fa-id-card-o"></i>';

    public static function getGroup(): string
    {
        return 'channels';
    }

    public static function getRoutes(): array
    {
		return [
			new Route(
				'/',
				['_controller' => function () {
                    return new RedirectResponse("/admin/channelstory/" . Config::get('node'));
                }]
			),
			new Route(
				'/{id}',
				['_controller' => __CLASS__ . "::listAction"]
			),
			new Route(
				'/{id}/add',
				['_controller' => __CLASS__ . "::addAction"]
			),
			new Route(
				'/{id}/delete/{stories_id}',
				['_controller' => __CLASS__ . "::deleteAction"]
			)
		];
    }

	public function listAction($id) {
		try {
			$channel = Node::get($id);
		} catch (ModelNotFoundException $e) {
			Message::error($e->getMessage());
			return $this->redirect('/admin');
		}

		$limit = 20;
		$list = $channel->getStories();
		$total = count($list);

		return $this->viewResponse('admin/channelstories/list', [
			'selectedNode' => $id,
			'nodes' => $this->user->getNodeNames(),
			'list' => $list,
			'total' => $total,
			'limit' => $limit,
		]);
    }

    public function addAction($id, Request $request) {
        if(!$this->user && !$this->user->hasPerm('admin-module-channels') )
            throw new ControllerAccessDeniedException();

        if($request->isMethod('post') && $request->request->has('value')) {
            $story = Stories::get($request->request->get('value'));
            $channel = Node::get($id);

            if ($channel->addStory($story)) {
                Message::info(Text::get('admin-channelstory-correct'));
            } else {
                Message::error(implode(', ', $errors));
            }
        }

        return $this->jsonResponse($story);
    }

    public function deleteAction($id, $stories_id) {
        if(!$this->user && !$this->user->hasPerm('admin-module-channels') )
            throw new ControllerAccessDeniedException();

        $node_story = NodeStories::getNodeStory($id, $stories_id);
        $node_story->dbDelete();

        return $this->redirect('/admin/channelstory/' . $id);
    }

}
