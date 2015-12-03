<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Gestion de la newsletter
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ModelException;
use Goteo\Library\Text;
use Goteo\Model\Mail;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Library\Feed;
use Goteo\Model\Template;
use Goteo\Model\User;
use Goteo\Library\Newsletter;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Mail\SenderRecipient;

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
        $filters = $this->request->query->all(); // we don't want to use the getFilters(), this filters does not persist
        if(empty($filters['show'])) $filters['show'] = 'receivers';
        $mailing = Sender::get($id);
        $limit = 50;
        $list = SenderRecipient::getList($id, $filters['show'], $this->getGet('pag') * $limit, $limit);
        $total = SenderRecipient::getList($id, $filters['show'], 0, 0, true);

        return array(
                'template' => 'admin/newsletter/detail',
                'detail' => $filters['show'],
                'mailing' => $mailing,
                'list' => $list,
                'total' => $total,
                'link' => $mailing->getLink()
        );
    }

    public function activateAction($id) {
        $mailing = Sender::get($id);
        if($mailing->getStatus() == 'inactive' && $mailing->setActive(true)) {
            Message::info("Newsletter [$id] activated for immediate sending!");
            $ok = true;
            // Evento Feed
            $log = new Feed();
            $log->populate(Text::sys('feed-admin-newsletter-activate-subject'), '/admin/newsletter',
                Text::sys('feed-admin-newsletter-activate', [
                    '%USER%' =>  Feed::item('user', $this->user->name, $this->user->id),
                    '%SUBJECT%' =>  Feed::item('relevant', $mailing->subject),
                    '%ID%' => $mailing->id
                ]))
                ->doAdmin('admin');
        }
        else {
            Message::error("Newsletter [$id] cannot be activated for immediate sending!");
            $ok = false;
            // Evento Feed
            $log = new Feed();
            $log->populate(Text::sys('feed-admin-newsletter-activate-subject'), '/admin/newsletter',
                Text::sys('feed-admin-newsletter-activate-failed', [
                    '%USER%' =>  Feed::item('user', $this->user->name, $this->user->id),
                    '%SUBJECT%' =>  Feed::item('relevant', $mailing->subject),
                    '%ID%' => $mailing->id
                ]))
                ->doAdmin('admin');
        }
        $this->notice('Newsletter activated', [$mailing, $this->user]);
        return $this->redirect('/admin/newsletter/detail/' . $id);
    }

    public function cancelAction($id) {
        if($mailing = Sender::get($id)) {
            $this->notice('Newsletter deleted', [$mailing, $this->user]);
            $mailing->dbDelete();
            Message::error("Newsletter [$id] removed!");
        }
        return $this->redirect();
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

                $mailHandler = Mail::createFromTemplate('any', '', $template, [], $lang);

                $errors = [];
                if( !$mailHandler->save($errors) ) {
                    Message::error('Error saving mailing: ' . implode('<br>', $errors));
                    return $this->redirect('/admin/newsletter');
                }

                // create the sender cue
                $sender = new Sender(['mail' => $mailHandler->id]);
                $errors = [];
                if ( ! $sender->save($errors) ) { //persists in database
                    Message::error(implode('<br>', $errors));
                    return $this->redirect('/admin/newsletter');
                }

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
                Sender::addSubscribersFromSQL($sql);


                Message::info('Se ha generado el boletín [' . $sender->id . ']. <strong><a href="/admin/newsletter/detail/' . $sender->id . '">Recuerda activarlo</a> para que se envíe!</strong>');

            }

        }

        return $this->redirect('/admin/newsletter');

    }

    public function listAction() {
        $list = Sender::getMailingList();

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
