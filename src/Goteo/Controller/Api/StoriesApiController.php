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

use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Library\Check;
use Goteo\Library\Text;
use Goteo\Model\Stories;
use Symfony\Component\HttpFoundation\Request;

class StoriesApiController extends AbstractApiController {

    protected function validateStory($id) {
        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $story = Stories::get($id);

        if(!$story)
            throw new ModelNotFoundException();

        $is_owner = $story->getUser() ? ($story->getUser()->id === $this->user->id) : false;
        if($this->user->hasPerm('admin-module-stories') || $story->active || $is_owner) {
            return $story;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-stories-not-active-yet'));
    }

    /**
     * AJAX upload image for the stories
     */
    public function uploadImagesAction(Request $request) {
        if(!$this->user || !$this->user->hasPerm('admin-module-stories'))
            throw new ControllerAccessDeniedException();

        $result = $this->genericFileUpload($request, 'file'); // 'file' is the expected form input name in the story object
        return $this->jsonResponse($result);
    }

    /**
     * Individual stories property checker/updater
     * To update a property, use the PUT method
     */
    public function storiesPropertyAction($id, $prop, Request $request) {
        $story = $this->validateStory($id);

        if(!$story) throw new ModelNotFoundException();

        $read_fields = ['id', 'node', 'project', 'lang', 'order', 'image', 'active', 'pool', 'title', 'description', 'review', 'url', 'pool_image', 'post', 'type', 'sphere', 'landing_match', 'landing_pitch'];
        $write_fields = ['title', 'description', 'review', 'url', 'active', 'pool'];
        $properties = [];
        foreach($read_fields as $f) {
            if(isset($story->{$f})) {
                $val = $story->{$f};
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
                if(in_array($f, ['active', 'pool'])) {
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

            if(!$this->user || !$this->user->hasPerm('admin-module-stories'))
                throw new ControllerAccessDeniedException();

            if(!in_array($prop, $write_fields)) {
                throw new ModelNotFoundException("Property [$prop] not writeable");
            }
            $story->{$prop} = $request->request->get('value');

            if(in_array($prop, ['active', 'pool'])) {
                if($story->{$prop} == 'false') $story->{$prop} = false;
                if($story->{$prop} == 'true') $story->{$prop} = true;
                $story->{$prop} = (bool) $story->{$prop};
            }

            $story->dbUpdate([$prop]);
            $result['value'] = $story->{$prop};

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

    /**
     * Individual stories property checker/updater
     * To update a property, use the PUT method
     */
    public function storiesSortAction($id, Request $request) {
        $story = $this->validateStory($id);

        if(!$story) throw new ModelNotFoundException();

        $result = ['value' => (int)$story->order, 'error' => false];

        if($request->isMethod('put') && $request->request->has('value')) {

            $res = Check::reorder($id, $request->request->get('value'), 'stories', 'id', 'order', ['node' => Config::get('node')]);

            if($res != $result['value']) {
                $result['value'] = $res;
            } else {
                $result['error'] = true;
                $result['message'] = 'Sorting failed';
            }

            if(!$this->user || !$this->user->hasPerm('admin-module-stories'))
                throw new ControllerAccessDeniedException();
        }

        return $this->jsonResponse($result);
    }

    /**
     * Simple listing of stories
     * TODO: according to permissions, filter this stories
     */
    public function storiesAction(Request $request) {
        if(!$this->user) throw new ControllerAccessDeniedException();

        $filters = [];
        $page = max((int) $request->query->get('pag'), 0);

        if($request->query->has('q')) {
            $filters['title'] = $request->query->get('q');
        }
        $limit = 25;
        $offset = $page * $limit;
        $list = [];

        foreach(Stories::getList($filters, $offset, $limit) as $story) {
            $ob = ['id' => $story->id,
                   'title' => $story->title,
                   'subtitle' => $story->subtitle,
                   'image' => $story->image ? $story->getImage()->getLink(64,64,true) : null,
                ];
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
