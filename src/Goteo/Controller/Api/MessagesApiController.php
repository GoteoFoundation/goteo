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
use Goteo\Application\Exception\InvalidDataException;

use Goteo\Application\View;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterMessageEvent;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Library\Text;
use Goteo\Model\Message as Comment;
use Goteo\Controller\Dashboard\ProjectDashboardController;

class MessagesApiController extends AbstractApiController {

    /**
     * Simple listing of messages for a project
     * TODO: according to permissions, filter this users
     */
    public function commentsAction($pid) {
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
        // Share with other user of the thread if required
        $shared = (bool) $request->request->get('shared');
        // Only project editors (currently) can create shared messages
        if($shared && !$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException('Only project editors can create shared messages');
        }
        // allowing only responses to other messages
        // (for the moment)
        if(! $parent = Comment::get($thread)) {
            throw new ModelNotFoundException("Thread [$thread] not found!");
        }
        if($parent->thread) {
            throw new ControllerAccessDeniedException('Parent is a child!');
        }
        $id = $request->request->get('id');
        $recipients = $request->request->get('recipients');
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
                'private' => $recipients ? true : $parent->private, // Set private if it has recipients or parent is private
                'shared' => $shared,
                'message' => $message,
                'date' => date('Y-m-d H:i:s')
            ]);
        }

        if(!$comment->save($errors)) {
            throw new InvalidDataException('Update failed '. implode(", ", $errors));
        }

        $event = new FilterMessageEvent($comment);

        if($recipients) {
            $comment->setRecipients($recipients);
        } else {
            if($shared || !$comment->private) {
                // Send to everyone in the thread except creator
                $recipients = array_filter($parent->getParticipants(), function($u) {
                    return $u->id !== $this->user->id;
                });
                $comment->setRecipients($recipients);
                if(count($recipients) > 1) $event->setDelayed($shared); // Send in background as a newsletter
            } else {
                // Set the parent as recipient
                $comment->setRecipients([$parent->getUser()]);
            }
        }
        if(!$comment->getRecipients()) {
            throw new ModelException(Text::get('dashboard-message-donors-error'));
        }

        $this->dispatch(AppEvents::MESSAGE_CREATED, $event);

        if($request->request->get('view') === 'dashboard') {
            $view = 'dashboard/project/partials/comments/item';
        } else {
            $view = 'project/partials/comment';
        }
        View::setTheme('responsive');
        return $this->jsonResponse([
            'id' => $comment->id,
            'user' => $comment->user,
            'project' => $comment->project,
            'shared' => $comment->shared,
            'message' => $comment->message,
            'recipients' => $comment->getRecipients(),
            'delayed' => $event->getDelayed(),
            'html' => View::render($view, [ 'comment' => $comment, 'project' => $prj, 'admin' => $request->request->get('admin') ])
        ]);
    }

    /**
     * Delete a comment
     */
    public function deleteCommentAction($cid) {
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

        // Send and event to create the Feed and/or update number of collaborations
        $this->dispatch(AppEvents::MESSAGE_DELETED, new FilterMessageEvent($message));

        return $this->jsonResponse(['id' => $message->id]);
    }

    /**
     * Simple listing of messages for a project
     * TODO: according to permissions, filter this users
     */
    public function messagesAction($pid) {
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
    public function userMessagesAction($pid, $uid) {
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
            $opened = (bool) $stats ? $stats->getEmailOpenedCollector()->getPercent() : false;
            $ob = [
                'id' => $msg->id,
                'message' => $msg->getHtml(),
                'sent' => $mail ? true : false,
                'opened' => $opened,
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
        if(is_array($users)) $users = array_filter($users); // Remove empty entries

        $event = new FilterMessageEvent($message);
        if(!$users) {
            // Try to extract recipients from filters if available
            list($filters, $filter_by) = ProjectDashboardController::getInvestFilters($prj, $request->request->get('filter'));
            $users = array_column(Invest::getUsersList($filter_by), 'id');
        }
        if($users) {
            $message->setRecipients($users);
            if(is_array($users) && count($users) > 1) $event->setDelayed(true); // Send in background as a newsletter
        }

        if($recipients = $message->getRecipients()) {
            // assign a thread if the user is already in the conversation
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
            'user' => $message->user instanceOf User ? $message->user->id : $message->user,
            'project' => $message->project,
            'users' => $users,
            'message' => $message->message
        ]);
    }

    /**
     * Project Mailing (generated from Messages to more than 2 users)
     */
    public function projectMailingAction($pid, Request $request) {
        $prj = Project::get($pid);

        // Security, first of all...
        if(!$prj->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $list = [];
        $filters = [
            'with_private' => true,
            'with_mailing' => true
        ];
        if($request->query->has('active')) $filters['active'] = (bool) $request->query->get('active');
        foreach(Comment::getAll($prj, null, $filters, 'date DESC') as $msg) {
            $stats = $msg->getStats();
            $percent = $stats ? $stats->getEmailOpenedCollector()->getPercent() : 0;
            $total = intval($stats ? $stats->getEmailOpenedCollector()->non_zero : 0);
            $sender = $msg->getMail() ? $msg->getMail()->getSender() : null;

            $list[] = [
                'id' => $msg->id,
                'date' => $msg->date,
                'timeago' => $msg->timeago,
                'private' => $msg->private,
                'subject' => $msg->getSubject(),
                'html' => $msg->getHtml(),
                'active' => $sender ? (bool)$sender->active : false,
                'status' => $msg->getStatusObject(),
                'opened' => ['percent' => $percent, 'total' => $total],
                'user_id' => $msg->user_id,
                'user_name' => $msg->user_name,
                'user_email' => $msg->user_email,
                'user_avatar' => $msg->user_avatar
            ];
        }

        return $this->jsonResponse([
            'list' => $list
        ]);
    }
}
