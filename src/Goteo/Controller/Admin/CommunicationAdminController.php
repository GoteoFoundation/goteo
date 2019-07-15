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
use Goteo\Controller\Message;
use Goteo\Library\Text;
use Goteo\Application\Lang;
use Goteo\Library\Translator\ModelTranslator;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;

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
                '/preview/id/{id}',
                ['_controller' => __CLASS__ . "::previewAction"]
            ),
            new Route(
                '/detail/id/{id}',
                ['_controller' => __CLASS__ . "::detailAction"]
            )
        ];
    }

    public function doSave(Request $request){

        if($request->isMethod('POST')) {
            // validate()
            $communication = new Communication();
            $langs_ok = [];
            $all = $request->request->get('t');
            $form = $request->request->get('autoform');
            $communication->type = $form['data-editor-type']; 
            $communication->original_lang = Config::get('lang');
            $communication->filter = $form['filter'];
            $communication->template = $form['template'];
            $communication->subject = $all[$communication->original_lang]['subject'];
            $communication->content = $all[$communication->original_lang]['body'];
            $communication->save();
             
            $translator = new ModelTranslator();
            $translator = $translator::get('communication', $communication->id);
            $fields = $translator::getFields('communication');
    
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
        
        switch($filter->typeofdonor) {
            case 0: // donor
                break; 
            case 1: // promoter
                break;
            case 2: // matcher
                break;
            case 3: //test
                break;
        }

    }



    public function listAction(Request $request)
    {

        if ($request->isMethod('post') ) {
            $communication = $this->doSave($request);
            return $this->redirect('/admin/communication/preview/id/'.$communication->id);
        }
        else {
            $filter = new Filter();
            $processor = $this->getModelForm('ProjectFilter', $filter , Array(), Array(), $request);
            $processor->createForm();
            $form_filter = $processor->getForm();
            $form_filter->handleRequest($request);
    
            $filters = $filter->getAll();
    
            $template = ['0' => 'General communication', '1' => Text::get('newsletter-lb')];
            $translates = [Config::get('lang') => Lang::getName(Config::get('lang'))];
            
            $langs = Lang::listAll('name', false);
            $editor_types = ['md' => Text::get('admin-text-type-md'), 'html' => Text::get('admin-text-type-html')];
    
    
            return $this->viewResponse('admin/communication/list',[
                'filters' => $filter->getAll(),
                'form_filter' => $form_filter->createView(),
                'templates' => $template,
                'languages' => $langs,
                'editor_types' => $editor_types,
                'translations' => $translates,
                'variables' => Communication::variables()
            ]);
        }

    }


    public function previewAction($id, Request $request)
    {

        $communication = $id ? Communication::get($id) : new Communication();

		if (!$communication) {
			throw new ModelNotFoundException("Not found communication [$id]");
        }

        return $this->viewResponse('email/newsletter', [
            'content' => $communication->content
        ]);
    }

    public function detailAction(Request $request, $id)
    {
        $communication = $id ? Communication::get($id) : new Communication();

		if (!$communication) {
			throw new ModelNotFoundException("Not found communication [$id]");
        }
        
        // $filter = Filter::get($communication->filter);

        // if (!$filter) {
        //     throw new ModelNotFoundException("Not found filter [$communication->filter]");
        // }

        // $this->getReceivers();


        return $this->viewResponse('admin/communication/detail');
    }
}
