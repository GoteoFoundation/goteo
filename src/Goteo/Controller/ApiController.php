<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\View;

use Goteo\Model\User;


class ApiController extends \Goteo\Core\Controller {
    private $is_admin = false;
    private $user = null;

    public function __construct() {
        // changing to a json theme here (not really a theme)
        View::setTheme('JSON');
        $this->user = Session::getUser();
        $this->is_admin = Session::isAdmin();
    }

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
        $nodes = null;
        $page = max((int) $request->query->get('pag'), 0);
        // General search
        if($request->query->has('q')) {
            $filters[$this->is_admin ? 'global' : 'name'] = $request->query->get('q');
        }
        $limit = 25;
        $offset = $page * $limit;
        $total = User::getList($filters, $nodes, 0, 0, true);
        $list = [];
        foreach(User::getList($filters, $nodes, $offset, $limit) as $user) {
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
}
