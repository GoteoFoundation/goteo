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
        $this->dbReplica(true);
        $this->dbCache(true);
    }

    protected function validateCategories($tab, $id) {
        $this->dbReplica(false);
        $this->dbCache(false);

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $class = '\\Goteo\Model\\'.ucfirst($tab);
        $model = $class::get($id);

        if(!$model)
            throw new ModelNotFoundException();

        if($this->user->hasPerm('admin-module-categories')) {
            return $model;
        }

        throw new ControllerAccessDeniedException();
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
     */
    public function categoriesAction(string $tab = 'category') {
        $lang = Lang::current();
        if($tab === 'socialcommitment') {
            $list = SocialCommitment::getAll($lang);
            $fields = ['id', 'icon', 'name', 'description'];
        } elseif($tab === 'sphere') {
            $list = Sphere::getAll([], $lang);
            $fields = ['id', 'icon', 'name', 'landing_match'];
        } elseif($tab === 'sdg') {
            $list = Sdg::getList([],0,100, false, $lang);
            $fields = ['id', 'icon', 'name', 'description', 'link', 'footprints'];
        } elseif($tab === 'footprint') {
            $list = Footprint::getList([],0,100, false, $lang);
            $fields = ['id', /*'icon',*/ 'name', 'description'];
        } elseif($tab === 'category') {
            $list = Category::getAll($lang);
            $fields = ['id', 'name', 'description', 'social_commitment'];
        } else {
            throw new ModelNotFoundException("Not found type [$tab]");
        }

        return $this->jsonResponse(array_map(function($el) use ($fields){
            $ret = [];
            foreach($fields as $f) {
                $ret[$f] = $el->{$f};
                if(in_array($f, ['id', 'social_commitment'])) $ret[$f] = (int) $ret[$f];
                if ($f == 'footprints') $ret[$f] = $el->getFootprints();
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

    /**
     * Individual categories property checker/updater
     * To update a property, use the PUT method
     */
    public function categoriesPropertyAction($tab, $id, $prop, Request $request) {
        $model = $this->validateCategories($tab, $id);

        $read_fields = ['id', 'name', 'title', 'description', 'landing_match', 'link', 'order', 'icon'];
        $write_fields = ['title', 'description', 'landing_match', 'url'];
        $properties = [];
        foreach($read_fields as $f) {
            if(isset($model->{$f})) {
                $val = $model->{$f};
                if($val instanceOf Image) {
                    $val = $val->getName();
                }
                if(is_array($val)) {
                    foreach($val as $i => $ssub) {
                        if($sub instanceOf Image) {
                            $val[$i] = $sub->getName();
                        }
                    }
                }
                if(in_array($f, ['landing_match'])) {
                    $val = (bool) $val;
                }
                $properties[$f] = $val;
            }
        }
        if(!array_key_exists($prop, $properties)) {
            throw new ModelNotFoundException("Property [$prop] not found");
        }
        $result = ['value' => $properties[$prop], 'error' => false];

        if($request->isMethod('put') && $request->request->has('value')) {

            if(!$this->user || !$this->user->hasPerm('admin-module-categories'))
                throw new ControllerAccessDeniedException();

            if(!in_array($prop, $write_fields)) {
                throw new ModelNotFoundException("Property [$prop] not writeable");
            }
            $model->{$prop} = $request->request->get('value');

            if(in_array($prop, ['landing_match'])) {
                if($model->{$prop} == 'false') $model->{$prop} = false;
                if($model->{$prop} == 'true') $model->{$prop} = true;
                $model->{$prop} = (bool) $model->{$prop};
            }

            $model->dbUpdate([$prop]);
            $result['value'] = $model->{$prop};

            if($errors = Message::getErrors()) {
                $result['error'] = true;
                $result['message'] = implode("\n", $errors);
            }
            if($messages = Message::getMessages()) {
                $result['message'] = implode("\n", $messages);
            }
        }
        return $this->jsonResponse($result);
    }
}
