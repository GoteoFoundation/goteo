<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Model\User\Translate;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;


class TranslateController extends \Goteo\Core\Controller {

    protected static $zones = [
        'home' => [
            'news',
            'promote',
            'stories',
            'banner',
        ],
        'system' => [
            'template',
            'texts',
            'category',
            'opentag',
            'worthcracy',
            'icon',
            'license',
            'milestone',
        ],
        'tables' => [
            'faq',
            'page',
            'post',
            'tag',
            'glossary',
            'info',
        ]
    ];

    protected $langs = [];

    public function __construct() {
        //Set the responsive theme
        View::setTheme('responsive');

        // all languages for admins
        if (Session::getUser()->roles['admin']) {
            $this->langs = Lang::listAll('name', false);
        } else {
            $this->langs = Translate::getLangs(Session::getUserId());
        }
        if ( !$this->langs ) {
            throw new ControllerAccessDeniedException(Text::get('translator-no-langs'));
        }

        // Common vars for all views
        $this->contextVars([
            'zones' => self::$zones,
            'languages' => $this->langs
            ]);
    }

    /**
     * Adds a model to the translator
     * @param [type] $table  [description]
     * @param string $parent [description]
     */
    public static function addTranslateModel($table, $parent = 'tables') {
        if(!is_array(self::$zones[$parent])) self::$zones[$parent] = [];
        self::$zones[$parent][] = $table;
    }

    public function createSidebar($zone = 'activity') {
        Session::addToSidebarMenu('<i class="icon icon-2x icon-activity"></i> ' . Text::get('dashboard-menu-activity'), '/translate', 'activity');
        $icons = ['home' => 'home', 'system' => 'cogs', 'tables' => 'table'];
        foreach(self::$zones as $k => $parts) {
            $submenu = [];
            foreach($parts as $v) {
                $submenu[$v] = ['text' => Text::get("translator-$v"), 'id' => $v, 'link' => "/translate/$v", 'class' => 'nopadding'];
            }
            Session::addToSidebarMenu('<i class="fa fa-2x fa-' . $icons[$k] . '"></i> ' . Text::get("translator-$k"), $submenu, $k, null, 'sidebar');
        }

        $this->contextVars([
            'zone' => $zone,
        ]);
    }

    public function indexAction (Request $request) {
        $this->createSidebar('activity');

        return $this->viewResponse('translate/index', [
            'feed' => Feed::getAll('translate', 'admin', 50)
        ]);
    }

    protected function doList($zone, $translator, Request $request) {
        $this->createSidebar($zone);

        // Get list texts
        $filters = [];
        $limit = 20;
        $offset = intval($request->query->get('pag')) * $limit;
        try {
            $fields = $translator::getFields($zone);
            if($request->query->has('q')) {
                $filters['id'] = $request->query->get('q');
                foreach($fields as $f => $t) {
                    $filters[$f] = $request->query->get('q');
                }
            }
            if($request->query->has('p')) {
                $filters['pending'] = $request->query->get('p');
            }
            $total = $translator::getList($zone, $filters, 0, 0, true);
            $list = $translator::getList($zone, $filters, $offset, $limit);
            // print_r($list);die;
        } catch(ModelException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/translate');
        }

        return $this->viewResponse('translate/list', [
            'list' => $list,
            'limit' => $limit,
            'total' => $total,
            'fields' => $fields
            ]);

    }

    protected function doEdit ($zone, $id, $translator, Request $request) {

        $redirect = "/translate/$zone" . ($request->getQueryString() ? '?' . $request->getQueryString() : '');

        try {

            $translator = $translator::get($zone, $id);
            $fields = $translator::getFields($zone);

            if($request->isMethod('POST')) {
                // Delete language if requested
                if($request->request->has('d')) {
                    $translator->delete($request->request->get('d'));
                    Message::info(Text::get('translator-deleted-ok', $this->langs[$request->request->get('d')]));
                    return $this->redirect($redirect);
                }

                $previous_values = Session::get("previous_values", []);
                // print_R($previous_values);die;
                $langs_ok = [];
                $all = $request->request->get('t');
                foreach($all as $lang => $texts) {
                    // Don't skip original lang anymore, Translator is capable of editing original tables too
                    // if($lang === $translator->original) continue;
                    $all_empty = true;
                    $values = [];
                    // $values = [];
                    foreach($texts as $key => $text) {
                        // Skip keys not in fields
                        if(!array_key_exists($key, $fields)) continue;
                        if(trim($text) === '' && $key !== 'pending') {
                            continue;
                        }

                        if(array_key_exists("trans-$zone-$id-$key-$lang", $previous_values)) {
                            $previous = $previous_values["trans-$zone-$id-$key-$lang"];
                        } else {
                            $previous = $translator->getTranslation($lang, $key);
                        }
                        // if($lang == 'ca' && $key == 'pending') {print_r($texts);die("$lang.$previous");}
                        if($previous != $text) {
                            if($key === 'pending') {
                                if($translator->isTranslated($lang)) {
                                    $values[$key] = $text;
                                    $all_empty = false;
                                }
                                // print_R($previous);die("$lang.$key");

                            } else {
                                $all_empty = false;
                                $values[$key] = $text;
                            }
                        }
                    }
                    // Do not save empty langs
                    if($all_empty) {
                        continue;
                    }
                    // Insert if not exists
                    // Update if exists
                    // Exception on failure
                    try {
                        // if($lang != 'ca') {print_r($values);print_r($lang);die;}
                        $translator->save($lang, $values);
                        $langs_ok[$lang] = $this->langs[$lang];
                    } catch(\Exception $e) {
                        Message::error(Text::get('translator-saved-ko', ['%LANG%' => $lang, '%ERROR%' => $e->getMessage()]));
                    }
                }

                if($langs_ok) {
                    $log  = new Feed();
                    $log->setTarget(Session::getUserId(), 'user')
                        ->populate(
                        Text::sys('translator-feed-translated-target', ucfirst($zone)),
                        "/translate/$zone/$id",
                        new FeedBody(null, null, 'translator-feed-translated-desc', [
                                '%USER%'    => Feed::item('user', Session::getUser()->name, Session::getUser()->id),
                                '%LINK%'  => Feed::item('translate', ucfirst($zone) . '/' . $id, $zone . '/' . $id ),
                                '%LANGS%'  => '<strong>'.implode('</strong>, <strong>', $langs_ok) .'</strong>',
                            ])
                    )
                        ->doAdmin('translate');

                    Message::info(Text::get('translator-saved-ok', implode(', ', $langs_ok)));
                } else {
                    Message::error(Text::get('translator-not-saved'));
                }

                Session::del("previous_values");
                return $this->redirect($redirect);

            }
        } catch(ModelException $e) {
            Message::error($e->getMessage());
            return $this->redirect($redirect);
        }

        // Store values in session to allow multi-user editing
        $previous_values = [];
        foreach($this->langs as $lang => $name) {
            foreach($fields as $field => $type) {
                $text = $translator->getTranslation($lang, $field);
                if($text) {
                    $previous_values["trans-$zone-$id-$field-$lang"] = $text;
                }
            }
        }
        // print_R($previous_values);die;
        Session::store("previous_values", $previous_values);
        $this->createSidebar($zone);

        return $this->viewResponse('translate/edit', [
            'id' => $id,
            'fields' => $fields,
            'translator' => $translator
            ]);
    }

    public function listAction ($zone, Request $request) {
        return $this->doList($zone, '\Goteo\Library\Translator\ModelTranslator', $request);
    }

    public function editAction ($zone, $id, Request $request) {
        return $this->doEdit($zone, $id, '\Goteo\Library\Translator\ModelTranslator', $request);
    }

    // SPECIAL CASE: System Texts
    public function listTextAction (Request $request) {
        return $this->doList('texts', '\Goteo\Library\Translator\TextTranslator', $request);
    }

    public function editTextAction ($id, Request $request) {
        return $this->doEdit('texts', $id, '\Goteo\Library\Translator\TextTranslator', $request);
    }

}
