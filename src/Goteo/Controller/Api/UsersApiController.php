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
use Goteo\Application\Exception\ModelException;

use Goteo\Model\User;
use Goteo\Model\Category;
use Goteo\Model\Image;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

class UsersApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        // De-Activate cache & replica read for this controller by default
        $this->dbReplica(false);
        $this->dbCache(false);
    }

    /**
     * Simple listing of users
     * TODO: according to permissions, filter this users
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function usersAction(Request $request) {
        if(!$this->user instanceOf User) throw new ControllerAccessDeniedException();
        $this->dbReplica(true);
        $this->dbCache(true);

        $filters = [];
        $node = null;
        $page = max((int) $request->query->get('pag'), 0);
        // General search
        if($request->query->has('q')) {
            $filters[$this->is_admin ? 'global' : 'name'] = $request->query->get('q');
        }
        if($this->is_admin && $request->query->has('role')) {
            $role = $request->query->get('role');
            if($role === 'consultant') $filters['type'] = 'consultants';
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
                   'image' => $user->avatar->name ? $user->getImage()->getLink(64,64,true) : null,
                   'created' => $user->created];
            if($this->is_admin) {
                $ob['email'] = $user->email;
                $ob['active'] = (bool)$user->active;
                $ob['url'] = '/admin/users/manage/' . $user->id;
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

    protected function getSafeUser($user) {
        if(!$user instanceOf User) $user = User::get($user);
        if(!$user instanceOf User) throw new ModelNotFoundException();

        if(!$this->user instanceOf User) throw new ControllerAccessDeniedException();

        $is_admin = $this->user->canImpersonate($user) || $this->user->canRebase($user);

        $ob = [];
        $fields = ['id', 'name', 'about', 'keywords', 'twitter', 'facebook', 'google', 'instagram', 'identica', 'linkedin'];
        if($is_admin) {
            $fields = array_merge($fields, ['email', 'gender', 'birthyear', 'entity_type', 'legal_entity', 'hide', 'active', 'confirmed']);
        }
        foreach($fields as $k) {
            if(in_array($k, ['hide', 'active', 'confirmed'])) {
                $ob[$k] = (bool) $user->{$k};
            } else {
                $ob[$k] = $user->{$k};
            }
        }

        if($user->avatar instanceof Image) {
            $ob['avatar'] = $user->avatar->id;
        } else {
            $ob['avatar'] = $user->avatar;
        }

        if($is_admin) {
            $ob['password'] = '';
            $ob['roles'] = $user->getRoles()->getRoleNames();
        }
        return $ob;
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
        if(!$this->user instanceOf User) throw new ControllerAccessDeniedException();
        if(!($user = User::get($id))) {
            throw new ModelNotFoundException();
        }
        $is_admin = $this->user->canImpersonate($user);

        if($this->user->id !== $user->id && !$is_admin) {
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


    /**
     * Individual user property checker/updater
     * To update a property, use the PUT method
     */
    public function userPropertyAction($id, $prop, Request $request) {
        $user = User::get($id);
        $properties = $this->getSafeUser($user);

        $write_fields = ['name', 'gender', 'birthyear', 'entity_type', 'legal_entity', 'about', 'keywords', 'twitter', 'facebook', 'google', 'instagram', 'identica', 'linkedin'];

        $is_admin = $this->user->canRebase($user);

        if($is_admin) {
            $write_fields = array_merge($write_fields, ['id', 'roles', 'email', 'password', 'active', 'hide', 'confirmed']);
        }

        if(!isset($properties[$prop])) {
            throw new ModelNotFoundException("Property [$prop] not found");
        }

        $result = ['value' => $properties[$prop], 'error' => false];

        if($request->isMethod('put')) {
            if($this->user->id !== $user->id && !$is_admin) {
                throw new ControllerAccessDeniedException();
            }

            if(!in_array($prop, $write_fields)) {
                throw new ModelNotFoundException("Property [$prop] not writeable");
            }

            if($prop === 'roles') {
                $roles = $request->request->get('value');
                if(!is_array($roles)) $roles = [$roles];

                $ob = $user->getRoles();
                $current_roles = array_keys((array)$ob);
                if(!$this->user->canChangeRole(array_merge($roles, $current_roles), $fail)) {
                    throw new ModelException(Text::get('admin-role-change-forbidden', ['%ROLE%' => "'$fail'"]));
                }

                foreach($current_roles as $role) {

                    if(!in_array($role, $roles)) {
                        $ob->removeRole($role);
                    }
                }
                foreach($roles as $role) {
                    $ob->addRole($role);
                }

                $errors = [];
                $ob->save($errors);
                $new_roles = array_keys((array)$user->getRoles());
                if(count($new_roles) !== count($roles)) {
                    $errors[] = 'Role mismatch in' . implode(', ', $new_roles) .' against ' . implode(', ', $roles);
                }
                if($errors) throw new ModelException("Error assigning roles: " . implode(', ', $errors));
                $result['value'] = $new_roles;

            } else {
                $value = $request->request->get('value');
                if(in_array($prop, ['hide', 'active', 'confirmed'])) {
                    if($value === 'false') $value = false;
                    if($value === 'true') $value = true;
                    $value = (bool) $value;
                }


                if(in_array($prop, ['id', 'email', 'password'])) {
                    $errors = [];
                    $user->rebase($value, $prop);
                } else {
                    // do the SQL update
                    $user->{$prop} = $value;
                    $user->dbUpdate([$prop]);
                }
                if($is_admin) {
                    // Feed Event
                    // TODO: throw event instead
                    $log = new Feed();
                    $log->setTarget($admin->id, 'user')
                        ->populate(
                            Text::sys('feed-admin-user-modification'),
                            '/admin/users/manage/' . $user->id,
                        new FeedBody(null, null, 'feed-admin-has-modified', [
                                '%ADMIN%' => Feed::item('user', $this->user->name, $this->user->id),
                                '%USER%'    => Feed::item('user', $user->name, $user->id),
                                '%ACTION%'  => new FeedBody('relevant', null, 'feed-admin-modified-action'),
                                '%PROPERTY%'  => $prop
                            ])
                        )
                        ->doAdmin('user');
                }
                $result['value'] = $user->{$prop};
            }
        }
        return $this->jsonResponse($result);

    }

}
