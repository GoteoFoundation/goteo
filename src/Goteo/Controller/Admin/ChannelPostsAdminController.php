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
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Model\Blog\Post as GeneralPost;
use Goteo\Model\Node;
use Goteo\Model\Node\NodePost;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;


class ChannelPostsAdminController extends AbstractAdminController
{
	protected static $icon = '<i class="fa fa-2x fa-file-text-o"></i>';

    public static function getGroup(): string
    {
        return 'channels';
    }

    public static function getRoutes() {
		return [
			new Route(
				'/',
				['_controller' => function () {
                    return new RedirectResponse("/admin/channelposts/" . Config::get('node'));
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
				'/{id}/delete/{post_id}',
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
		$list = NodePost::getList(['node' => $channel->id], 0, $limit);
		$total = count($list);

		return $this->viewResponse('admin/channelposts/list', [
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
            $post = GeneralPost::get(intval($request->request->get('value')));
            $channel = Node::get($id);

            if ($channel->addPost($post->id, $errors)) {
                Message::info(Text::get('admin-channelpost-correct'));
            } else {
                Message::error(implode(', ', $errors));
            }
        }

        return $this->jsonResponse($post);
    }

    public function deleteAction($id, $post_id) {
        if(!$this->user && !$this->user->hasPerm('admin-module-channels') )
            throw new ControllerAccessDeniedException();

            $node_post = NodePost::getNodePost($id, $post_id);
            $node_post->dbDelete();

        return $this->redirect('/admin/channelposts/' . $id);
    }
}
