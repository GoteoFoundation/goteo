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
	Goteo\Library\Template,
    Goteo\Library\Newsletter as Boletin,
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

    public function detailAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('detail', $id, $this->getFilters(), $subaction));
    }


    public function activateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('activate', $id, $this->getFilters(), $subaction));
    }


    public function initAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('init', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {
        $current_lang = Lang::current();
        $debug = false;

        $node = $this->node;

        switch ($action) {
            case 'init':
                if ($this->isPost()) {

                    // plantilla
                    $template = $this->getPost('template');

                    // destinatarios
                    if ($this->getPost('test')) {
                        $users = Boletin::getTesters();
                    } elseif ($template == 33) {
                        // los destinatarios de newsletter
                        $users = Boletin::getReceivers();
                    } elseif ($template == 35) {
                        // los destinatarios para testear a subscriptores
                        $users = Boletin::getReceivers();
                    } elseif ($template == 27 || $template == 38) {
                        // los cofinanciadores de este año
                        $users = Boletin::getDonors(Model\User\Donor::currYear());
                    }

                    // sin idiomas
                    $nolang = $this->getPost('nolang');
                    if ($nolang) {
                        foreach ($users as $usr) {
                            $receivers[$current_lang][$usr->user] = $usr;
                        }
                    } else {
                        // separamos destinatarios en idiomas
                        $receivers = array();
                        foreach ($users as $usr) {

                            // idioma de preferencia
                            $comlang = !empty($usr->comlang) ? $usr->comlang : $usr->lang;
                            if (empty($comlang)) $comlang = $current_lang;

                            // he visto un 'eN' raro en beta, pongo esto hasta que confirme en real
                            $comlang = strtolower($comlang);

                            // piñon para newsletter issue #48
                            $newslang = (in_array($comlang, array('es', 'ca', 'gl', 'eu'))) ? 'es' : 'en';

                            $receivers[$newslang][$usr->user] = $usr;
                        }
                    }

                    // idiomas que vamos a enviar
                    $langs = array_keys($receivers);

                    if ($debug) {
                        echo \trace($receivers);
                        echo \trace($langs);
                        die;
                    }

                    // para cada idioma
                    foreach ($langs as $lang) {

                        // destinatarios
                        $recipients = $receivers[$lang];

                        // datos de la plantilla
                        $tpl = Template::get($template, $lang);

                        // contenido de newsletter
                        $content = ($template == 33) ? Boletin::getContent($tpl->text, $lang) : $content = $tpl->text;

                        // asunto
                        $subject = $tpl->title;

                        $mailHandler = new Mail();
                        $mailHandler->template = $template;
                        $mailHandler->content = $content;
                        $mailHandler->node = $node;
                        $mailHandler->lang = $lang;
                        $mailHandler->massive = true;
                        $mailId = $mailHandler->saveEmailToDB();

                        // inicializamos el envío
                        if (Sender::initiateSending($mailId, $subject, $recipients, 1)) {
                            // ok...
                        } else {
                            Message::error('No se ha podido iniciar el mailing con asunto "'.$subject.'"');
                        }
                    }

                    // cancelamos idioma variable usado para generar contenido de newsletter
                    unset($_SESSION['VAR_LANG']);

                }

                return $this->redirect('/admin/newsletter');

                break;
            case 'activate':
                if (Sender::activateSending($id)) {
                    Message::info('Se ha activado un nuevo envío automático');
                } else {
                    Message::error('No se pudo activar el envío. Iniciar de nuevo');
                }
                return $this->redirect('/admin/newsletter');
                break;
            case 'detail':

                $mailing = Sender::getSending($id);
                $list = Sender::getDetail($id, $filters['show']);

                return array(
                        'folder' => 'newsletter',
                        'file' => 'detail',
                        'detail' => $filters['show'],
                        'mailing' => $mailing,
                        'list' => $list
                );
                break;
            default:
                $list = Sender::getMailings();

                return array(
                        'folder' => 'newsletter',
                        'file' => 'list',
                        'list' => $list
                );
        }

    }
}
