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
    public function addCommentAction(Request $request) {
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
                'private' => $parent->private,
                'message' => $message,
                'date' => date('Y-m-d H:i:s')
            ]);
        }
        if(!$comment->save($errors)) {
            throw new ModelException('Update failed '. implode(", ", $errors));
        }
        if($recipients = $request->request->get('recipients')) {
            $comment->setRecipients($recipients);
        } else {
            // Set the parent as recipient
            $comment->setRecipients([$parent->getUser()]);
        }
        if(!$comment->getRecipients()) {
            throw new ModelException(Text::get('dashboard-message-donors-error'));
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
    public function deleteCommentAction($cid, Request $request) {
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
        $prj = Project::get($pid);

        // Security, first of all...
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $list = [];

        // TODO: filter type
        foreach(Comment::getAll($prj) as $msg) {
            $ob = ['id' => $msg->id,
                   'message' => $msg->getHtml(),
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
     * List of user messages for a project
     */
    public function userMessagesAction($pid, $uid, Request $request) {
        $prj = Project::get($pid);
        $user = User::get($uid);

        // Security, first of all...
        if(!$prj->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $list = [];

        // TODO: filter type
        foreach(Comment::getUserMessages($user, $prj) as $msg) {
            $mail = $msg->getMail();
            $stats = $msg->getStats();
            $opened = (bool) $stats && $stats->getEmailOpenedCollector()->getPercent();
            $ob = ['id' => $msg->id,
                   'message' => $msg->getHtml(),
                   'sent' => $mail ? true : false,
                   'opened' => $opened,
                   // 'date' => date_formater($msg->date, true),
                   'date' => $msg->date,
                   'project' => $msg->project,
                   'timeago' => $msg->timeago,
                   'recipient' => $msg->recipient,
                   'recipient_name' => $msg->recipient_name,
                   'user' => $msg->user,
                   'name' => $msg->getUser()->name,
                   'avatar' => $msg->getUser()->avatar->getLink(60,60,true),
                   'thread' => $msg->thread
               ];
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list
            ]);
    }

    /**
     * Add a comment over a project
     */
    public function addMessageAction(Request $request) {
        $subject = trim($request->request->get('subject'));
        $body = trim($request->request->get('body'));
        $project = $request->request->get('project');

        $prj = Project::get($project);
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        if(!$body && !$subject) {
            throw new ModelException(Text::get('validate-donor-mandatory'));
        }
        if($subject) {
            $body = "### $subject\n\n$body";
        }

        // Create the message
        $message = new Comment([
            'user' => $this->user,
            'project' => $project,
            'thread' => null, // Thread is assigned after creating the message
            'blocked' => false,
            'private' => true,
            'subject' => $subject,
            'message' => $body,
            'date' => date('Y-m-d H:i:s')
        ]);
        if(!$message->save($errors)) {
            throw new ModelException('Update failed '. implode(", ", $errors));
        }
        $users = $request->request->get('users');
        if(is_array($users)) $users = array_filter($users);

        $event = new FilterMessageEvent($message);
        if($users) {
            $message->setRecipients($users);
        } else {
            // TODO: find recipients from filters
            $filters = [
                'projects' => $project,
                // 'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID],
                'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED, Invest::STATUS_RELOCATED, Invest::STATUS_TO_POOL],
                'reward' => $request->request->get('reward'),
                'types' => $request->request->get('filter')
            ];
            $message->setRecipients(Invest::getUsersList($filters));
            $event->setDelayed(true);
        }

        if($recipients = $message->getRecipients()) {
            // assign a thread if the user is already in the coversation
            if(count($recipients) == 1) {
                $message->setThread('auto');
                if($message->thread) {
                    $message->save();
                }
            }
        } else {
            throw new ModelException(Text::get('dashboard-message-donors-error'));
        }

        // Send and event to create the Feed and send emails
        $this->dispatch(AppEvents::MESSAGE_CREATED, $event);

        return $this->jsonResponse([
            'id' => $message->id,
            'user' => $comment->user,
            'project' => $comment->project,
            'message' => $comment->message
        ]);
    }
}
