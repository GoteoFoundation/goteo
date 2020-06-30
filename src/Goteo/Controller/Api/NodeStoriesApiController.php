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

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;

use Goteo\Model\Node\NodeStories;
use Goteo\Library\Text;
use Goteo\Library\Check;

class NodeStoriesApiController extends AbstractApiController {

    protected function validateNodeStory($node_id, $stories_id) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $node_story = NodeStories::getNodeStory($node_id, $stories_id);

        if(!$node_story)
            throw new ModelNotFoundException();

        $is_owner = $node_story->getUser() ? ($node_story->getUser()->id === $this->user->id) : false;
        if($this->user->hasPerm('admin-module-channelstory')) {
            return $node_story;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-Nodestories-not-active-yet'));
    }

    /**
     * Individual Nodestories property checker/updater
     * To update a property, use the PUT method
     */
    public function nodestoriesSortAction($node_id, $stories_id, Request $request) {
        $node_story = $this->validateNodeStory($node_id, $stories_id);

        if(!$node_story) throw new ModelNotFoundException();

        $result = ['value' => (int)$node_story->order, 'error' => false];

        if($request->isMethod('put') && $request->request->has('value')) {

            $res = Check::reorder($id, $request->request->get('value'), 'node_stories', 'node_id', 'order');

            if($res != $result['value']) {
                $result['value'] = $res;
            } else {
                $result['error'] = true;
                $result['message'] = 'Sorting failed';
            }

            if(!$this->user || !$this->user->hasPerm('admin-module-channelstory'))
                throw new ControllerAccessDeniedException();
        }

        return $this->jsonResponse($result);

    }
}

