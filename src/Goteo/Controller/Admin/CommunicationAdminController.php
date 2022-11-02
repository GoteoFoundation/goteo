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

use Exception;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Library\Feed;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Library\Translator\ModelTranslator;
use Goteo\Model\Communication;
use Goteo\Model\Filter;
use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class CommunicationAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-envelope-o"></i>';

    public static function getGroup(): string
    {
        return 'communications';
    }

    public static function getRoutes(): array
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
            ),
            new Route(
                'send/{id}',
                ['_controller' => __CLASS__ . "::activateAction"]
            ),
            new Route(
                'cancel/{id}',
                ['_controller' => __CLASS__ . "::cancelAction"]
            )
        ];
    }

    public function doSave($id = null, Request $request){

        $communication = ($id) ? Communication::get($id) : new Communication();

        if($request->isMethod('POST')) {
            // validate()

            $errors = [];

            $all = $request->request->get('t');
            $form = $request->request->get('autoform');
            $communication->type = $form['data-editor-type'];
            $communication->lang = Config::get('lang');
            $communication->filter = $form['filter'];
            $communication->template = $form['template'];
            $communication->subject = $all[$communication->lang]['subject'];
            $communication->content = $all[$communication->lang]['body'];
            $communication->header = $form['image'];
            $communication->projects = $request->request->get('communication_add');
            $communication->save($errors);

            if ($errors) {
                throw new FormModelException(Text::get('form-sent-error', implode(',', $errors)));
                return;
            }

            $translator = new ModelTranslator();
            $fields = $translator::getFields('communication');
            $translator = $translator::get('communication', $communication->id);

            foreach($all as $lang => $texts) {

                if(trim($texts['subject']) === '') continue;
                if(trim($texts['body']) === '') continue;

                $fields['subject'] = $texts['subject'];
                $fields['content'] = $texts['body'];
                // Insert if not exists
                // Update if exists
                // Exception on failure
                try {
                    $translator->save($lang, $fields);
                } catch(Exception $e) {
                    Message::error(Text::get('translator-saved-ko', ['%LANG%' => $lang, '%ERROR%' => $e->getMessage()]));
                }
            }

            if ($id) {
                $mails = Mail::getFromCommunicationId($id);
                foreach($mails as $mail) {
                    $mailing = Sender::getFromMailId($mail->id);
                    if ($mailing)
                        $mailing->dbDelete();
                    $mail->dbDelete();
                }
            }

            foreach($communication->getAllLangs()  as $communication_lang) {
                $mailHandler = new Mail();
                $mailHandler->lang = $communication_lang->lang;
                $mailHandler->subject = $communication_lang->subject;
                $mailHandler->template = $communication->template;
                $mailHandler->communication_id = $communication->id;

                $mailHandler->content .= $communication_lang->content;
                $mailHandler->massive = true;

                $errors = [];

                $mailHandler->save($errors);

                $sender = new Sender(['mail' => $mailHandler->id]);
                $errors = [];
                if ( ! $sender->save($errors) ) { //persists in database
                    Message::error('Sender saving: ' . implode('<br>', $errors));
                    return $this->redirect('/admin/communication/detail/'.$communication->id);
                }

                $filter = Filter::get($communication->filter);
                $langs = array_keys(Lang::getDependantLanguages($communication_lang->lang));
                $langs = array_merge(array_diff($langs,$communication->getLangsAvailable()), [$communication_lang->lang]);
                list($sqlFilter, $values) = $filter->getFilteredSQL($langs, $sender->id);
                $sender->addSubscribersFromSQLValues($sqlFilter, $values);

                $log = new Feed();
                $log->populate(Text::sys('feed-admin-massive-subject'), '/admin/communication',
                    Text::sys('feed-admin-massive', [
                        '%USER%' =>  Feed::item('user', $this->user->name, $this->user->id),
                        '%TYPE%' =>  Feed::item('relevant', Text::sys('feed-admin-massive-communication')),
                    ]))
                    ->doAdmin('admin');
            }
        }
        return $communication;
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
            try {
                $communication = $this->doSave($id, $request);
            } catch(FormModelException $e) {
                Message::error($e->getMessage());
                return $this->redirect();
            }
            return $this->redirect('/admin/communication/detail/'.$communication->id);
        }
        else {
            $filters = Filter ::getAll();

            $template = [
                Template::COMMUNICATION => Text::get('admin-communications-communication'),
                Template::NEWSLETTER => Text::get('admin-communications-newsletter')
            ];

            $translates = [Config::get('lang') => Lang::getName(Config::get('lang'))];

            $langs = Lang::listAll('name', false);
            $editor_types = ['md' => Text::get('admin-text-type-md'), 'html' => Text::get('admin-text-type-html')];

            if ($id) {
                try {
                    $communication = Communication::get($id);
                    if ($communication->sent){
                        throw new ControllerAccessDeniedException("Communication [$id] is already sent");
                    }
                } catch (Exception $exception){
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
                'variables' => Communication::variables(),
                'copy' => false
            ]);
        }
    }


    public function copyAction($id, Request $request)
    {
        try {
            $communication = Communication::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/communication/');
        }

        if ($request->isMethod('post') ) {
            $communication = $this->doSave(null, $request);
            return $this->redirect('/admin/communication/detail/'.$communication->id);
        }

        $translator = new ModelTranslator();
        $translator = $translator::get('communication', $communication->id);

        $filters = Filter::getAll();

        $template = [
            Template::COMMUNICATION => Text::get('admin-communications-communication'),
            Template::NEWSLETTER => Text::get('admin-communications-newsletter')
        ];

        $translates = [];

        foreach($communication->getLangsAvailable() as $lang) {
            $translates[$lang] = Lang::getName($lang);
        }

        $langs = Lang::listAll('name', false);
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
            'copy' => true
        ]);
    }

    public function previewAction($id)
    {
        try {
            $communication = Communication::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/communication/');
        }

        $values['unsubscribe'] = SITE_URL . '/user/leave?email=' . $this->to;
        $values['content'] = $communication->content;
        $values['subject'] = $communication->subject;
        $values['promotes'] = $communication->getCommunicationProjects($communication->id);
        $values['type'] = $communication->type;
        $values['lang'] = $communication->lang;

        if ($communication->template == Template::NEWSLETTER) {
            $template = "newsletter";
            $values['image'] = $communication->getImage()->getLink(1920,335,true, true);
        }
        else $template = "default";

        return $this->viewResponse('email/'.$template, $values);
    }

    public function detailAction($id)
    {
        try {
        $communication = Communication::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin/communication/');
        }

        $mails = Mail::getFromCommunicationId($id);

        return $this->viewResponse('admin/communication/detail', [
            'communication' => $communication,
            'mails' => $mails,
        ]);
    }

    public function activateAction($id = null) {

        $communication = Communication::get($id);
        $mails = Mail::getFromCommunicationId($id);

        foreach($mails as $mail) {

            $mailing = Sender::getFromMailId($mail->id);
            if($mailing->getStatus() == 'inactive' && $mailing->setActive(true)) {
                Message::info("Communication [$mail->id] activated for immediate sending!");
                $log = new Feed();
                $log->populate(Text::sys('feed-admin-newsletter-activate-subject'), '/admin/communication',
                    Text::sys('feed-admin-newsletter-activate', [
                        '%USER%' =>  Feed::item('user', $this->user->name, $this->user->id),
                        '%SUBJECT%' =>  Feed::item('relevant', $mailing->subject),
                        '%ID%' => $mailing->id
                    ]))
                    ->doAdmin('admin');
            } else {
                Message::error("Communication [$mail->id] cannot be activated for immediate sending!");
                $log = new Feed();
                $log->populate(Text::sys('feed-admin-newsletter-activate-subject'), '/admin/communication',
                    Text::sys('feed-admin-newsletter-activate-failed', [
                        '%USER%' =>  Feed::item('user', $this->user->name, $this->user->id),
                        '%SUBJECT%' =>  Feed::item('relevant', $mailing->subject),
                        '%ID%' => $mailing->id
                    ]))
                    ->doAdmin('admin');
            }
        }
        $this->notice('Communication activated', [$mailing, $this->user]);
        return $this->redirect('/admin/communication/detail/' . $communication->id);
    }

    public function cancelAction($id = null) {

        $mails = Mail::getFromCommunicationId($id);
        foreach($mails as $mail) {
            if ($mailing = Sender::getFromMailId($mail->id)) {
                $mailing->setActive(false);
                $this->notice('Communication mails canceled', [$mailing, $this->user]);
                Message::info("Communication [$id] mails canceled!");
            }
        }

        return $this->redirect('/admin/communication/detail/' . $id);
    }
}
