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

use Goteo\Model\User;

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

        $suggest = User::suggestUserId($email, $name, $userid);
        return $this->jsonResponse([
            'available' => $available,
            'suggest' => $suggest,
            'userid' => $userid,
            'email' => $email,
            'name' => $name
        ]);
    }

}
