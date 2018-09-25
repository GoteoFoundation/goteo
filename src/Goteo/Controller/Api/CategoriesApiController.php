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
use Goteo\Application\Exception\ModelNotFoundException;

use Goteo\Application\Lang;
use Goteo\Model\Category;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Sphere;
use Goteo\Model\Sdg;
use Goteo\Model\Footprint;

class CategoriesApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        // Activate cache & replica read for this controller
        $this->dbReplica(true);
        $this->dbCache(true);
    }


    /**
     * Returns a list of keywords for suggestions
     */
    public function keywordsAction(Request $request) {
        $keywords = array_map(function($el) {
            return ['tag' => $el];
        }, Category::getKeywords($request->query->get('q')));

        return $this->jsonResponse($keywords);
    }

    /**
     * Return a list of categories where:
     * @param  string $tab which kind of category to be returned
     * @param  Request $request [description]
     */
    public function categoriesAction($tab = 'category', Request $request) {
        $lang = Lang::current();
        if($tab === 'socialcommitment') {
            $list = SocialCommitment::getAll($lang);
            $fields = ['id', 'icon', 'name', 'description', /*'order',*/];
        } elseif($tab === 'sphere') {
            $list = Sphere::getAll([], $lang);
            $fields = ['id', 'icon', 'name', 'landing_match', /*'order',*/];
        } elseif($tab === 'sdg') {
            $list = Sdg::getList([],0,100, false, $lang);
            $fields = ['id', 'icon', 'name', 'description', 'link'];
        } elseif($tab === 'footprint') {
            $list = Footprint::getList([],0,100, false, $lang);
            $fields = ['id', /*'icon',*/ 'name', 'description'];
        } elseif($tab === 'category') {
            $list = Category::getAll($lang);
            $fields = ['id', 'name', 'description', 'social_commitment', /*'order',*/];
        } else {
            throw new ModelNotFoundException("Not found type [$tab]");
        }

        return $this->jsonResponse(array_map(function($el) use ($fields){
            $ret = [];
            foreach($fields as $f) {
                $ret[$f] = $el->{$f};
                if(in_array($f, ['id', 'social_commitment'])) $ret[$f] = (int) $ret[$f];
                if($f === 'icon') $ret[$f] = $el->getIcon()->getLink(0,0,false,true);
            }
            return $ret;
        }, $list));

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
