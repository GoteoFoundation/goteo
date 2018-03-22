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
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;

use Goteo\Model\Call;
use Goteo\Library\Text;

class CallsApiController extends AbstractApiController {

    /**
     * Simple listing of calls
     * TODO: according to permissions, filter this calls
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function callsAction(Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        $filters = ['order' => 'IFNULL(published, opened) DESC'];
        $page = max((int) $request->query->get('pag'), 0);
        $status = [
            // Call::STATUS_EDITING,
            // Call::STATUS_REVIEWING,
            Call::STATUS_OPEN,
            Call::STATUS_ACTIVE,
            Call::STATUS_COMPLETED,
            Call::STATUS_EXPIRED
        ];

        // General search
        if($request->query->has('q')) {
            // $filters['global'] = $request->query->get('q');
            $filters['basic'] = $request->query->get('q');
        }
        if(!$this->is_admin) {
            $filters['status'] = $status;
        }

        if($request->query->has('status')) {
            $s = explode(",",preg_replace('/[^0-9,]/', '',$request->query->get('status')));
            if(!$this->is_admin) {
                $s = array_intersect($status, $s);
            }
            $filters['status'] = $s;
        }

        $limit = 25;
        $offset = $page * $limit;
        $total = Call::getList($filters, 0, 0, true);
        $list = [];
        foreach(Call::getList($filters, $offset, $limit) as $call) {
            foreach(['id', 'name', 'owner', 'call_location', 'subtitle', 'created', 'updated', 'opened', 'published', 'success', 'closed', 'lang'] as $k)
                $ob[$k] = $call->$k;
            foreach(['status', 'amount', 'maxdrop', 'maxproj', 'days'] as $k)
                $ob[$k] = (int)$call->$k;
            $ob['image'] = $call->image ? $call->getImage()->getLink(64,64,true) : null;
            $ob['backimage'] = $call->backimage ? $call->getBackImage()->getLink(64,64,true) : null;
            $ob['logo'] = $call->logo ? $call->getLogo()->getLink(64,64,true) : null;
            $ob['status_desc'] = $call->getTextStatus();
            $ob['url'] = '/call/' . $call->id;
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