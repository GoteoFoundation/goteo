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
use Goteo\Model\Invest;
use Goteo\Library\Text;
use Goteo\Model\Message as Comment;

class MessagesApiController extends AbstractApiController {

    /**
     * Simple listing of messages for a project
     * TODO: according to permissions, filter this users
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

    /**
     * Add a comment over a support message
     */
    public function commentsAddAction(Request $request) {
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
        if($recipients = $request->request->get('recipients')) {
            $comment->setRecipients($recipients);
        }

        // Send and event to create the Feed and send emails
        $this->dispatch(AppEvents::MESSAGE_CREATED, new FilterMessageEvent($comment));

        if($request->request->get('view') === 'dashboard') {
            $view = 'dashboard/project/partials/comments/item';
        }
        else {
            $view = 'project/partials/comment';
        }
        View::setTheme('responsive');
        return $this->jsonResponse([
            'id' => $comment->id,
            'user' => $comment->user,
            'project' => $comment->project,
            'message' => $comment->message,
            'html' => View::render($view, [ 'comment' => $comment, 'project' => $prj, 'admin' => $request->request->get('admin') ])
        ]);
    }


    /**
     * Delete a comment
     */
    public function commentsDeleteAction($cid, Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        if( !$message = Comment::get($cid) ) {
            throw new ModelNotFoundException("Message [$cid] not found");
        }
        if(!$prj = Project::get($message->project)) {
            throw new ModelNotFoundException("Project for message [$cid] not found");
        }
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }
        $message->dbDelete();
        return $this->jsonResponse(['id' => $message->id]);
    }

    /**
     * Simple listing of messages for a project
     * TODO: according to permissions, filter this users
     */
    public function messagesAction($pid, Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        $prj = Project::get($pid);

        // Security, first of all...
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $list = [];

        // TODO: filter type
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

    /**
     * Add a comment over a support message
     */
    public function messagesAddAction(Request $request) {
        $subject = trim($request->request->get('subject'));
        $body = trim($request->request->get('body'));
        $project = $request->request->get('project');
        $prj = Project::get($project);
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        if($subject && $body) {
            $message = "<strong>$subject</strong><br>\n<br>\n$body";
        } else {
            throw new ModelException(Text::get('validate-donor-mandatory'));
        }

        // Create the message
        $message = new Comment([
            'user' => $this->user,
            'project' => $project,
            'blocked' => false,
            'private' => true,
            'subject' => $subject,
            'message' => $message,
            'date' => date('Y-m-d H:i:s')
        ]);
        if(!$message->save($errors)) {
            throw new ModelException('Update failed '. implode(", ", $errors));
        }
        $users = $request->request->get('users');
        if(is_array($users)) $users = array_filter($users);

        $evt = new FilterMessageEvent($message);
        if($users) {
            $message->setRecipients($users);
        } else {
            // TODO: find recipients from filters
            $filters = [
                'projects' => $project,
                'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID],
                'reward' => $request->request->get('reward'),
                'types' => $request->request->get('filter')
            ];
            $message->setRecipients(Invest::getUsersList($filters));
            $evt->setDelayed(true);
        }

        if(!$message->getRecipients()) {
            throw new ModelException(Text::get('dashboard-message-donors-error'));
        }

        // Send and event to create the Feed and send emails
        $this->dispatch(AppEvents::MESSAGE_CREATED, $evt);

        // if($request->request->get('view') === 'dashboard') {
        //     $view = 'dashboard/project/partials/comments/item';
        // }
        // else {
        //     $view = 'project/partials/comment';
        // }
        View::setTheme('responsive');
        return $this->jsonResponse([
            'id' => $message->id,
            'user' => $comment->user,
            'project' => $comment->project,
            'message' => $comment->message,
            // 'html' => View::render($view, [ 'comment' => $comment, 'project' => $prj, 'admin' => $request->request->get('admin') ])
        ]);
    }
}
