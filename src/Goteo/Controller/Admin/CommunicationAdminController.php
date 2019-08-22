<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Goteo\Model\Filter;
use Goteo\Model\Communication;
use Goteo\Application\Message;
use Goteo\Model\Mail\Sender;
use Goteo\Library\Text;
use Goteo\Application\Lang;
use Goteo\Library\Translator\ModelTranslator;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class CommunicationAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-envelope-o"></i>';

    public static function getGroup() {
        return 'communications';
    }

    public static function getRoutes()
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/add',
                ['_controller' => __CLASS__ . "::addAction"]
            ),
            new Route(
                '/edit/{id}',
                ['_controller' => __CLASS__ . "::addAction"]
            ),
            new Route(
                '/copy/{id}',
                ['_controller' => __CLASS__ . "::copyAction"]
            ),
            new Route(
                '/preview/{id}',
                ['_controller' => __CLASS__ . "::previewAction"]
            ),
            new Route(
                '/detail/{id}',
                ['_controller' => __CLASS__ . "::detailAction"]
            )
        ];
    }

    public function doSave($id = null, Request $request){

        if($request->isMethod('POST')) {
            // validate()
            $communication = ($id) ? Communication::get($id) : new Communication();
            
            $langs_ok = [];
            $all = $request->request->get('t');
            $form = $request->request->get('autoform');
            $communication->type = $form['data-editor-type']; 
            $communication->lang = Config::get('lang');
            $communication->filter = $form['filter'];
            $communication->template = $form['template'];
            $communication->subject = $all[$communication->lang]['subject'];
            $communication->content = $all[$communication->lang]['body'];
            $communication->header = $form['image'];
            $communication->save();
             
            $translator = new ModelTranslator();
            $fields = $translator::getFields('communication');
            $translator = $translator::get('communication', $communication->id);
    
            foreach($all as $lang => $texts) {
                // $values = [];

                if(trim($texts['subject']) === '') continue;
                if(trim($texts['body']) === '') continue;

                $fields['subject'] = $texts['subject'];
                $fields['content'] = $texts['body'];
                // Insert if not exists
                // Update if exists
                // Exception on failure
                try {
                    $translator->save($lang, $fields);
                } catch(\Exception $e) {
                    Message::error(Text::get('translator-saved-ko', ['%LANG%' => $lang, '%ERROR%' => $e->getMessage()]));
                }
            }
            return $communication;
        }
    }

    public function getReceivers($filter, $offset = 0, $limit = 10, $count = false, $sender_id = null){
        
        $receivers = $filter->getFiltered();

    }


    public function listAction(Request $request)
    {
        $filters = ['subject' => $request->query->get('q')];
        $limit = 25;
        $page = $request->query->get('pag') ?: 0;
        $list = Communication::getList($filters, $page * $limit, $limit, false, Config::get('lang'));
        $total = Communication::getList($filters, 0, $limit, true, Config::get('lang'));
        return $this->viewResponse('admin/communication/list', [
            'list' => $list,
            'total' => $total,
            'limit' => $limit,
            'filter' => [
                '_action' => '/communication',
                'q' => Text::get('admin-communication-global-search')
                ]
            ] 
        );
        
    }

    public function addAction($id = null, Request $request)
    {
        if ($request->isMethod('post') ) {
            $communication = $this->doSave($id, $request);
            return $this->redirect('/admin/communication/preview/'.$communication->id);
        }
        else {
            $filters = Filter ::getAll();
    
            $template = ['default' => 'General communication', 'newsletter' => Text::get('newsletter-lb')];
            $translates = [Config::get('lang') => Lang::getName(Config::get('lang'))];
            
            $langs = Lang::listAll('name', false);
            $editor_types = ['md' => Text::get('admin-text-type-md'), 'html' => Text::get('admin-text-type-html')];
    
            if ($id){
                try {
                    $communication = Communication::get($id);
                    if ($communication->sent){
                        throw new ControllerAccessDeniedException("Communication [$id] is already sent");
                    }
                }
                catch (Exception $exception){
                    Message::error($exception->getMessage());
                }

                $translator = new ModelTranslator();
                $translator = $translator::get('communication', $communication->id);
        
            }
    
            return $this->viewResponse('admin/communication/add',[
                'filters' => $filters,
                'templates' => $template,
                'languages' => $langs,
                'editor_types' => $editor_types,
                'translations' => $translates,
                'data' => $communication,
                'translator' => $translator,
                'variables' => Communication::variables()
            ]);
        }

    }


    public function copyAction($id, Request $request)
    {

        
        $communication = $id ? Communication::get($id) : new Communication();
        
		if (!$communication) {
            throw new ModelNotFoundException("Not found communication [$id]");
        }
        
        if ($request->isMethod('post') ) {
            $communication = $this->doSave($request);
            return $this->redirect('/admin/communication/preview/'.$communication->id);
        }
        
        $translator = new ModelTranslator();
        $translator = $translator::get('communication', $communication->id);

        $filters = Filter::getAll();
    
        $template = ['default' => 'General communication', 'newsletter' => Text::get('newsletter-lb')];
        $translates = [];

        foreach($communication->getLangsAvailable() as $lang) {
            $translates[$lang] = Lang::getName($lang);
        }
        // $translates = [Config::get('lang') => Lang::getName(Config::get('lang'))];
        
        $langs = Lang::listAll('name', false);
        $langs_available = $communication->getLangsAvailable();
        $editor_types = ['md' => Text::get('admin-text-type-md'), 'html' => Text::get('admin-text-type-html')];

        return $this->viewResponse('admin/communication/add',[
            'filters' => $filters,
            'templates' => $template,
            'languages' => $langs,
            'editor_types' => $editor_types,
            'translations' => $translates,
            'variables' => Communication::variables(),
            'data' => $communication,
            'translator' => $translator,
        ]);
    }

    public function previewAction($id, Request $request)
    {

        $communication = $id ? Communication::get($id) : new Communication();

		if (!$communication) {
			throw new ModelNotFoundException("Not found communication [$id]");
        }


        return $this->viewResponse('email/'.$communication->template, [
            'communication' => $communication
        ]);
    }

    public function detailAction(Request $request, $id)
    {
        $communication = Communication::get($id);
        $limit = 25;
        $page = $request->query->get('pag') ?: 0;


		if (!$communication) {
			throw new ModelNotFoundException("Not found communication [$id]");
        }

        $filter = Filter::get($communication->filter);
        $list = $filter->getFiltered(false, $limit, $page);
        // print_r($list); die;
        $total = $filter->getFiltered(true);

        return $this->viewResponse('admin/communication/detail', [
            'list' => $list,
            'total' => $total,
        ]);
    }

}
