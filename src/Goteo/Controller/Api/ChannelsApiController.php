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
use Goteo\Model\Node;
use Goteo\Model\Category;
use Goteo\Model\Image;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

class ChannelsApiController extends AbstractApiController {

    /**
     * Simple listing of channels
     * TODO: according to permissions, filter this channels
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function channelsAction(Request $request) {
        if(!$this->user instanceOf User) throw new ControllerAccessDeniedException();

        $filters = [];
        $node = null;
        $page = max((int) $request->query->get('pag'), 0);
        // General search
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
}
