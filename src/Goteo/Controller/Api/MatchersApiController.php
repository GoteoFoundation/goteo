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
use Goteo\Model\Image;
use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class MatchersApiController extends AbstractApiController {

    /**
     * Simple listing of matchers
     */
    public function matchersAction(Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }

        $filters = [];
        $page = max((int) $request->query->get('pag'), 0);

        if($request->query->has('q')) {
            $filters[$this->is_admin ? 'global' : 'name'] = $request->query->get('q');
        }

        if(empty($filters) || !$this->is_admin) {
            $filters['active'] = true;
        }

        $limit = 25;
        $offset = $page * $limit;
        $total = Matcher::getList($filters, 0, 0, true);
        $list = [];
        foreach(Matcher::getList($filters, $offset, $limit) as $match) {
            foreach(['id', 'name', 'logo', 'matcher_location', 'lang', 'created'] as $k)
                $ob[$k] = $match->$k;
            foreach(['amount', 'used', 'projects', 'crowd'] as $k)
                $ob[$k] = (int)$match->$k;
            if($this->is_admin) {
                $ob['active'] = (bool)$user->active;
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

    static protected function getSafeProject(Project $prj) {
        $sob = [];
        foreach(['id', 'name', 'status', 'subtitle', 'owner', 'matcher_status'] as $k) {
            $sob[$k] = $prj->$k;
        }
        if($prj->image instanceof Image) {
            $sob['image'] = $prj->image->name;
        } else {
            $sob['image'] = $prj->image;
        }
        return $sob;
    }

    static protected function getSafeUser(User $usr) {
        $sob = [];
        foreach(['id', 'name', 'about'] as $k) {
            $sob[$k] = $usr->$k;
        }
        $sob['use_pool'] = (bool) $usr->use_pool;
        if($usr->avatar instanceof Image) {
            $sob['avatar'] = $usr->avatar->name;
        } else {
            $sob['avatar'] = $usr->avatar;
        }
        return $sob;
    }

    protected function getSafeMatcher($matcher) {
        if(!$matcher instanceOf Matcher) $matcher = Matcher::get($matcher, false);
        if(!$matcher instanceOf Matcher) {
            throw new ModelNotFoundException();
        }

        // Security, first of all...
        if(!$matcher->userCanView($this->user)) {
            throw new ControllerAccessDeniedException();
        }

        $ob = [];
        foreach(['id', 'name', 'logo', 'lang', 'processor'] as $k)
                $ob[$k] = $matcher->$k;
        foreach(['amount', 'used', 'projects', 'crowd'] as $k)
                $ob[$k] = (int)$matcher->$k;

        $ob['terms'] = $this->getService('app.md.parser')->text($matcher->terms);

        $ob['projects'] = array_map(['self', 'getSafeProject'], $matcher->getProjects('all'));
        $ob['users'] = array_map(['self', 'getSafeUser'], $matcher->getUsers(false));

        return $ob;
    }

    /**
     * Simple matcher info data
     */
    public function matcherAction($mid) {
        $properties = $this->getSafeMatcher($mid);

        return $this->jsonResponse($properties);
    }

    public function uploadImagesAction(Request $request) {
        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request); // 'file' is the expected form input name in the post object
        return $this->jsonResponse($result);
    }

}
