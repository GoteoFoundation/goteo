<?php
/**
 * Gestion de la newsletter
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Library\Mail,
    Goteo\Application\Lang,
    Goteo\Application\Message,
    Goteo\Application\Config,
    Goteo\Model\Template,
	Goteo\Model\User,
    Goteo\Library\Newsletter,
	Goteo\Library\Sender;

class NewsletterSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Estado del envío automático',
      'init' => 'Iniciando un nuevo envío',
      'detail' => 'Viendo destinatarios',
    );


    static protected $label = 'Boletín';


    protected $filters = array (
      'show' => 'receivers',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function detailAction($id) {
        $filters = $this->request->query->all();
        if(empty($filters['show'])) $filters['show'] = 'receivers';
        $mailing = Sender::getSending($id);
        $list = Sender::getDetail($id, $filters['show']);

        return array(
                'template' => 'admin/newsletter/detail',
                'detail' => $filters['show'],
                'mailing' => $mailing,
                'list' => $list,
                'link' => Sender::getLink($mailing->id, $mailing->mail)
        );
    }

    public function initAction() {
        $current_lang = Lang::current();

        $node = $this->node;

        if ($this->isPost()) {
            $current_lang = Lang::current();
            // plantilla
            $template = $this->getPost('template');
            // sin idiomas
            $nolang = $this->getPost('nolang');

            // all user languages
            $user_langs = User::getAvailableLangs();

            // all templates languages
            if($nolang) {
                $template_langs = [$current_lang];
            }
            else {
                $template_langs = Template::getAvailableLangs($template);
            }
            foreach($template_langs as $lang) {
                Lang::set($lang);
                $lang = Lang::current();


                // datos de la plantilla
                $tpl = Template::get($template, $lang);

                // contenido de newsletter
                $content = ($template == Template::NEWSLETTER) ? Newsletter::getContent($tpl->text, $lang) : $content = $tpl->text;

                // asunto
                $mailHandler = new Mail();
                $mailHandler->template = $template;
                $mailHandler->content = $content;
                $mailHandler->node = $node;
                $mailHandler->lang = $lang;
                $mailHandler->massive = true;
                $mailId = $mailHandler->saveEmailToDB();

                // create the sender cue
                $sender = new Sender($mailId, $tpl->title);
                $sender->save(); //persists in database

                // get the equivalent communication languages from preferences
                $comlangs = [];
                foreach($user_langs as $user_lang) {
                    $comlang = $user_lang;
                    while(!in_array($comlang, $template_langs)) {
                        $comlang = Lang::getFallback($comlang);
                    }
                    if($comlang === $lang) {
                        $comlangs[] = $user_lang;
                    }
                }
                // add subscribers from sql
                if ($this->getPost('test')) {
                    $sql = Newsletter::getTestersSQL($comlangs, $sender->id . ',');
                } elseif ($template == Template::NEWSLETTER || $template == Template::TEST) {
                    $sql = Newsletter::getReceiversSQL($comlangs, $sender->id . ',');
                } elseif ($template == Template::DONORS_WARNING || $template == Template::DONORS_REMINDER) {
                    // los cofinanciadores de este año
                    $sql = Newsletter::getDonorsSQL($comlangs, $sender->id . ',');
                }

                // add subscribers
                $sender->addSubscribersFromSQL($sql);

                // activate
                $sender->activate();

            }

            Lang::set($current_lang);

        }

        return $this->redirect('/admin/newsletter');

    }

    public function listAction() {
        $list = Sender::getMailings();

        $templates = array(
            Template::DONORS_WARNING => 'Aviso a los donantes',
            Template::DONORS_REMINDER => 'Recordatorio a los donantes',
            Template::NEWSLETTER => 'Newsletter',
            Template::TEST => 'Testeo'
        );

        return array(
                'template' => 'admin/newsletter/list',
                'list' => $list,
                'templates' => $templates
        );
    }


}
