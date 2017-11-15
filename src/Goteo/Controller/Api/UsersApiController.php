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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;

use Goteo\Model\User;
use Goteo\Model\Category;
use Goteo\Model\Image;
use Goteo\Library\Text;

class UsersApiController extends AbstractApiController {
    /**
     * Simple listing of users
     * TODO: according to permissions, filter this users
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function usersAction(Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        $filters = [];
        $node = null;
        $page = max((int) $request->query->get('pag'), 0);
        // General search
        if($request->query->has('q')) {
            $filters[$this->is_admin ? 'global' : 'name'] = $request->query->get('q');
        }
        $limit = 25;
        $offset = $page * $limit;
        $total = User::getList($filters, $node, 0, 0, true);
        $list = [];
        foreach(User::getList($filters, $node, $offset, $limit) as $user) {
            $ob = ['id' => $user->id,
                   'name' => $user->name,
                   'node' => $user->node,
                   'avatar' => $user->avatar->name ? $user->avatar->name : 'la_gota.png',
                   'created' => $user->created];
            if($this->is_admin) {
                $ob['email'] = $user->email;
                $ob['active'] = $user->active;
            }
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
            ]);
    }

    /**
     * Returns the availability of user id or email
     */
    public function userCheckAction(Request $request) {
        $seed = $request->query->get('seed');
        if(!is_array($seed)) $seed = [$seed];
        $userid = $request->query->get('userid');
        $email = $request->query->get('email');
        $name = $request->query->get('name');
        $available = false;

        $suggest = [];
        if($email) {
            if(!User::getByEmail($email)) {
                $available = true;
            }
        }
        elseif($userid) {
            if(!User::get($userid)) {
                $available = true;
            }
        }
        elseif($name) {
            $available = true; // names can be repeated
        }

        $suggest = User::suggestUserId(implode(" ", $seed), $email, $name, $userid);
        return $this->jsonResponse([
            'available' => $available,
            'suggest' => $suggest,
            'userid' => $userid,
            'email' => $email,
            'name' => $name
        ]);
    }

    /**
     * AJAX upload image to profile
     */
    public function userUploadAvatarAction($id, Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        if(!($user = User::get($id))) {
            throw new ModelNotFoundException();
        }
        if($user->id !== $user->id && !$this->user->hasRoleInNode($user->node, ['superadmin', 'root'])) {
            throw new ControllerAccessDeniedException();
        }

        $files = $request->files->get('file');
        if(!is_array($files)) $files = [$files];
        $global_msg = Text::get('all-files-uploaded');
        $result = [];

        $avatar = $user->avatar->id ? $user->avatar->id : null;
        $all_success = true;
        foreach($files as $file) {
            if(!$file instanceOf UploadedFile) continue;
            // Process image
            $msg = Text::get('uploaded-ok');
            $success = false;
            if($err = Image::getUploadErrorText($file->getError())) {
                $success = false;
                $msg = $err;
            } else {
                $user->user_avatar = new Image($file);
                $errors = [];
                if ($user->save($errors)) {
                    $success = true;
                } else {
                    $msg = implode(', ',$errors['image']);
                    // print_r($errors);
                }
            }

            $result[] = [
                'originalName' => $file->getClientOriginalName(),
                'name' => $user->avatar->id,
                'success' => $success,
                'msg' => $msg,
                'error' => $file->getError(),
                'size' => $file->getSize(),
                'maxSize' => $file->getMaxFileSize(),
                'errorMsg' => $file->getError() ? $file->getErrorMessage() : ''
            ];
            if(!$success) {
                $global_msg = Text::get('project-upload-images-some-ko');
                $all_success = false;
            }
        }

        return $this->jsonResponse(['files' => $result, 'avatar' => $avatar,  'msg' => $global_msg, 'success' => $all_success]);
    }

}
