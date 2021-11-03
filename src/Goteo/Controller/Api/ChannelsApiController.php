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
use Goteo\Model\Node;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;

class ChannelsApiController extends AbstractApiController {

    /**
     * Simple listing of channels
     * TODO: according to permissions, filter this channels
     */
    public function channelsAction(Request $request) {
        if(!$this->user instanceOf User) throw new ControllerAccessDeniedException();

        $filters = [];
        $node = null;
        $page = max((int) $request->query->get('pag'), 0);

        if($request->query->has('q')) {
            $filters[$this->is_admin ? 'global' : 'name'] = $request->query->get('q');
        } else {
            $filters['available'] = true;
        }
        $limit = 25;
        $offset = $page * $limit;
        $list = [];
        foreach(Node::getAll($filters, $node, $offset, $limit) as $node) {
            $ob = ['id' => $node->id,
                   'name' => $node->name,
                   'subtitle' => $node->subtitle,
                   'owner_background' => $node->owner_background,
                   'logo' => $node->logo ? $node->getLogo()->getLink(64,64,true) : null,
                   'label' => $node->label ? $node->getLabel()->getLink(64,64,true) : null,
                   'home_img' => $node->home_img ? $node->getHomeImage()->getLink(64,64,true) : null,
                   'created' => $node->created];
            if($this->is_admin) {
                $ob['email'] = $node->email;
                $ob['active'] = (bool)$node->active;
            }
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list,
            'total' => count($list),
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * AJAX upload image for the categories
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || !$this->user->hasPerm('admin-module-categories'))
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request, 'file'); // 'file' is the expected form input name in the story object
        return $this->jsonResponse($result);
    }

}
