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
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;

use Goteo\Application\View;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterMessageEvent;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Message as Comment;

class MessagesApiController extends AbstractApiController {

    /**
     * Simple listing of messages for a project
     * TODO: according to permissions, filter this users
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function commentsAction($pid, Request $request) {
        // if(!$this->user) {
        //     throw new ControllerAccessDeniedException();
        // }
        $prj = Project::get($pid);

        // Security, first of all...
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $list = [];
        foreach(Comment::getAll($prj) as $msg) {
            $ob = ['id' => $msg->id,
                   'message' => $msg->message,
                   'date' => $msg->date,
                   'project' => $msg->project,
                   'user' => $msg->getUser()->id
               ];
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list
            ]);
    }
    public function commentsEditAction(Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        $message = $request->request->get('message');
        $project = $request->request->get('project');
        $prj = Project::get($project);
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        $thread = $request->request->get('thread');
        // allowing only responses to other messages
        // (for the moment)
        if(! $parent = Comment::get($thread)) {
            throw new ModelNotFoundException("Thread [$thread] not found!");
        }
        if($parent->thread) {
            throw new ControllerAccessDeniedException('Parent is a child!');
        }
        $id = $request->request->get('id');
        // Create the Comment associated (not updated allowed for the moment)
        if($id) {
            if(!$comment = Comment::get($id)) {
                throw new ModelNotFoundException("Comment [$id] not found!");
            }
            $comment->message = $message;
        } else {
            $comment = new Comment([
                'id' => $id,
                'user' => $this->user,
                'thread' => $thread,
                'project' => $project,
                'blocked' => false,
                'message' => $message,
                'date' => date('Y-m-d H:i:s')
            ]);
        }
        if(!$comment->save($errors)) {
            throw new ModelException('Update failed '. implode(", ", $errors));
        }

        // Send and event to create the Feed and send emails
        $this->dispatch(AppEvents::MESSAGE_UPDATED, new FilterMessageEvent($comment));

        View::setTheme('responsive');
        return $this->jsonResponse([
            'id' => $comment->id,
            'user' => $comment->user,
            'project' => $comment->project,
            'message' => $comment->message,
            'html' => View::render('dashboard/project/partials/comments/item', [
                'name' => $comment->getUser()->name,
                'avatar' => $comment->getUser()->avatar->getLink(60, 60, true),
                'date' => date_formater($comment->date, true),
                'message' => $comment->message
            ])
        ]);
    }

}
