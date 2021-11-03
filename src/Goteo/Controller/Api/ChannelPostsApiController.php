<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Check;
use Goteo\Library\Text;
use Goteo\Model\Node\NodePost;
use Symfony\Component\HttpFoundation\Request;

class ChannelPostsApiController extends AbstractApiController {

    protected function validateNodeStory($node_id, $post_id) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $node_post = NodePost::getNodePost($node_id, $post_id);

        if(!$node_post)
            throw new ModelNotFoundException();

        if($this->user->hasPerm('admin-module-channels')) {
            return $node_post;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-Nodestories-not-active-yet'));
    }

    /**
     * Individual Nodestories property checker/updater
     * To update a property, use the PUT method
     */
    public function channelpostsSortAction($node_id, $post_id, Request $request) {
        $node_post = $this->validateNodeStory($node_id, $post_id);

        if(!$node_post) throw new ModelNotFoundException();

        $result = ['value' => (int)$node_post->order, 'error' => false];

        if($request->isMethod('put') && $request->request->has('value')) {

            $res = Check::reorder($post_id, $request->request->get('value'), 'node_post', 'post_id', 'order', [ 'node_id' => $node_id]);
            if($res != $result['value']) {
                $result['value'] = $res;
            } else {
                $result['error'] = true;
                $result['message'] = 'Sorting failed';
            }

            if(!$this->user || !$this->user->hasPerm('admin-module-channels'))
                throw new ControllerAccessDeniedException();
        }

        return $this->jsonResponse($result);
    }
}
