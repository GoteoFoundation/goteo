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
 * Gestion proyectos
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Application\Message;
use Goteo\Model\Mail;
use Goteo\Model\Template;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model;
use Goteo\Console\UsersSend;
use Goteo\Application\Event\FilterProjectEvent;
use Goteo\Application\AppEvents;

class ProjectsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'projects-lb-list',
      'move' => 'projects-lb-move',
      'execute' => 'projects-lb-execute',
      'cancel' => 'projects-lb-cancel',
      'report' => 'projects-lb-report',
      'conf' => 'projects-lb-conf',
      'dates' => 'projects-lb-dates',
      'accounts' => 'projects-lb-accounts',
      'images' => 'projects-lb-images',
      'assign' => 'projects-lb-assign',
      'open_tags' => 'projects-lb-open_tags',
      'rebase' => 'projects-lb-rebase',
      'consultants' => 'projects-lb-consultants',
    );


    static protected $label = 'projects-lb';


    protected $filters = array (
      'status' => -1,
      'category' => '',
      'proj_name' => '',
      'name' => '',
      'node' => '',
      'called' => '',
      'order' => '',
      'consultant' => '',
      'proj_id' => '',
    );


    /**
     * Some defaults
     */
    public function __construct($node, \Goteo\Model\User $user, Request $request) {
        parent::__construct($node, $user, $request);
        $this->admins = Model\User::getAdmins();
        // simple list of administrable nodes
        $this->all_nodes = Model\Node::getList();
        $this->nodes = array();
        foreach($user->getAdminNodes() as $node_id => $role) {
            $this->nodes[$node_id] = $this->all_nodes[$node_id];
        }
        $this->calls = array();
        if(class_exists('\Goteo\Controller\Admin\CallsSubController')) {
            if(\Goteo\Controller\Admin\CallsSubController::isAllowed($this->user, $this->node)) {
                $this->calls = Model\Call::getAvailable(true);
            }
        }

        // common vars
        $this->contextVars([
            'nodes' => $this->isMasterNode() ? $this->nodes : [],
            'calls' => $this->calls,
            'user' => $this->user,
            ], '/admin/projects');
    }

    /**
     * Get or exception to handle project
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getProject($id, $level = 'view') {
        $project = Model\Project::get($id);
        if($level === 'admin' && !$project->userCanAdmin($this->user)) {
            throw new ControllerAccessDeniedException('You cannot admin this project');
        }
        elseif($level === 'moderate' && !$project->userCanModerate($this->user)) {
            throw new ControllerAccessDeniedException('You cannot moderate this project');
        }
        elseif($level === 'delete' && !$project->userCanDelete($this->user)) {
            throw new ControllerAccessDeniedException('You cannot delete this project');
        }
        elseif($level === 'edit' && !$project->userCanEdit($this->user)) {
            throw new ControllerAccessDeniedException('You cannot edit this project');
        }
        elseif(!$project->userCanView($this->user)) {
            throw new ControllerAccessDeniedException('You cannot view this project');
        }

        if(!in_array($project->node, $this->nodes)) {
            return $project;
        }

        throw new ControllerAccessDeniedException('You cannot admin this project');
    }

    public function confAction($id = null) {
        $project = $this->getProject($id, 'edit');
        $conf = Model\Project\Conf::get($project->id);


        if ($this->isPost()) {
            $conf->days_round1 = (!empty($this->getPost('round1'))) ? $this->getPost('round1') : 40;
            $conf->days_round2 = (!empty($this->getPost('round2'))) ? $this->getPost('round2') : 40;
            $conf->one_round = $this->hasPost('oneround');
            // si es ronda única, los días de segunda deben grabarse a cero (para que el getActive no lo cuente para segunda)
            if ($conf->one_round) $conf->days_round2 = 0;
            $errors = array();
            if ($conf->save($errors)) {
                Message::info('Se han actualizado los días de campaña del proyecto ' . $project->name);
            } else {
                Message::error(implode('<br />', $errors));
            }
            return $this->redirect();
        }

        // cambiar fechas
        return array(
                'template' => 'admin/projects/conf',
                'project' => $project,
                'conf' => $conf
        );
    }


    public function consultantsAction($id) {
        $project = $this->getProject($id, 'admin');
        // cambiar el asesor
        $op = $this->getGet('op');
        $user = Model\User::get($this->getGet('user'));
        if (($user && $op === 'assignConsultant' && $user->hasRoleInNode($this->node, ['consultant', 'admin', 'superadmin'])) || $op === 'unassignConsultant') {
            if ($project->$op($user->id)) {
                // ok
            } else {
                Message::error('Error assigning consultant');
            }
        }

        return  array(
                'template' => 'admin/projects/consultants',
                'project' => $project,
                'admins' => $this->admins
            );

    }


    public function rebaseAction($id = null, $subaction = null) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        if ($this->getPost('newid')) {

            // verificamos que el nuevo id sea
            $newid = Model\Project::idealiza($this->getPost('newid'));
            try {
                // pimero miramos que no hay otro proyecto con esa id
                Model\Project::getMini($newid);
                Message::error('Ya hay un proyecto con ese Id.');
                return $this->redirect(self::getUrl('rebase', $id));

            } catch(ModelNotFoundException $e) {

                if ($project->status >= 3 && $this->getPost('force') != 1) {
                    Message::error('El proyecto no está ni en Edición ni en Revisión, no se modifica nada.');
                    return $this->redirect(self::getUrl('rebase', $id));
                }

                if ($project->rebase($newid)) {
                    Message::info('Verificar el proyecto -> <a href="/project/'.$newid.'" target="_blank">'.$project->name.'</a>');
                    return $this->redirect();
                } else {
                    Message::info('Ha fallado algo en el rebase, verificar el proyecto -> <a href="/project/'.$project->id.'" target="_blank">'.$project->name.' ('.$id.')</a>');
                    return $this->redirect(self::getUrl('rebase', $id));
                }
            }
        }

        // cambiar la id
        return array(
                'template' => 'admin/projects/rebase',
                'project' => $project
        );
    }


    public function reportAction($id) {
        $project = $this->getProject($id, 'edit');
        // informe financiero
        // Datos para el informe de transacciones correctas
        $data = Model\Invest::getReportData($project->id, $project->status, $project->round, $project->passed);
        $account = Model\Project\Account::get($project->id);

        return array(
                'template' => 'admin/projects/report',
                'project' => $project,
                'account' => $account,
                'data' => $data
        );
    }


    public function open_tagsAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'admin');

        // cambiar la agrupacion
        $op = $this->getGet('op');
        if ($this->hasGet('op') && $this->hasGet('open_tag') &&
            ($op === 'assignOpen_tag' || $op === 'unassignOpen_tag')) {
            if ($project->$op($this->getGet('open_tag'))) {
                // ok
            } else {
                Message::error(implode('<br />', $errors));
            }
        }

        $project->open_tags = Model\Project::getOpen_tags($project->id);
        // disponibles
        $open_all_tags = Model\Project\OpenTag::getAll();
        return array(
                'template' => 'admin/projects/open_tags',
                'project' => $project,
                'open_tags' => $open_all_tags
        );
    }


    public function assignAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        // asignar a una convocatoria solo si
        //   está en edición a campaña
        //   y no está asignado
        if (!in_array($project->status, array(Model\Project::STATUS_EDITING, Model\Project::STATUS_REVIEWING, Model\Project::STATUS_IN_CAMPAIGN)) || $project->called) {
            Message::error("No se puede asignar en este estado o ya esta asignado a una convocatoria");
            return $this->redirect();
        }

        if($this->isPost()) {
            $values = array(':project' => $project->id, ':call' => $this->getPost('call'));

            try {
                $sql = "REPLACE INTO call_project (`call`, `project`) VALUES (:call, :project)";
                if (Model\Project::query($sql, $values)) {
                    $log_text = 'El admin %s ha <span class="red">asignado a la convocatoria call/'.$this->getPost('call').'</span> el proyecto '.$project->name.' %s';
                } else {
                    $log_text = 'Al admin %s le ha <span class="red">fallado al asignar a la convocatoria call/'.$this->getPost('call').'</span> el proyecto '.$project->name.' %s';
                }
                Model\Call\Project::addOneApplied($this->getPost('call'));

                // feed
                $this->doFeed($project, $log_text);

            } catch(\PDOException $e) {
                Message::error("Ha fallado! " . $e->getMessage());
            }
            return $this->redirect();
        }

        // disponibles
        $available = Model\Call::getAvailable();

        return array(
                'template' => 'admin/projects/assign',
                'project' => $project,
                'available' => $available
        );
    }


    public function moveAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'admin');

        if ($this->isPost()) {

            if (!array_key_exists($this->getPost('node'), $this->nodes)) {
                Message::error('El nodo '.$this->getPost('node').' no existe! ');
            } else {

                $values = array(':id' => $project->id, ':node' => $this->getPost('node'));
                $values2 = array(':id' => $project->owner, ':node' => $this->getPost('node'));
                try {
                    $sql = "UPDATE project SET node = :node WHERE id = :id";
                    $sql2 = "UPDATE user SET node = :node WHERE id = :id";
                    if (Model\Project::query($sql, $values)) {
                        $log_text = 'El admin %s ha <span class="red">movido al nodo '.$nodes[$this->getPost('node')].'</span> el proyecto '.$project->name.' %s';
                        if (Model\User::query($sql2, $values2)) {
                            $log_text .= ', tambien se ha movido al impulsor';
                        } else {
                            $log_text .= ', pero no se ha movido al impulsor';
                        }
                    } else {
                        $log_text = 'Al admin %s le ha <span class="red">fallado al mover al nodo '.$nodes[$this->getPost('node')].'</span> el proyecto '.$project->name.' %s';
                    }
                } catch(\PDOException $e) {
                    Message::error("Ha fallado! " . $e->getMessage());
                }
                //feed this action
                $this->doFeed($project, $log_text);
            }
            return $this->redirect();

        }
        // cambiar el nodo
        return array(
                'template' => 'admin/projects/move',
                'project' => $project,
        );

    }


    public function imagesAction($id) {
        // Project && permission check

        $project = $this->getProject($id, 'edit');
        // FROM POST
        $section = $this->getPost('section');
        $url = $this->getPost('url');
        $order = $this->getPost('order');
        if(is_array($section) && is_array($url) && is_array($order)) {
            $ok = true;

            $i = 0;
            foreach ($section as $image_id => $value) {
                $sql = "UPDATE project_image SET `section` = :section, `url` = :url, `order` = :order WHERE project = :project AND image = :image";
                $n = (int) $order[$image_id];
                if(empty($n)) $n = $i++;
                $values = array(':project' => $project->id, ':image' => $image_id, ':section' => $value, ':url' => $url[$image_id], ':order' => $n);

                if (Model\Project::query($sql, $values)) {
                    // OK
                } else {
                    $ok = false;
                    Message::error("No se ha podido actualizar la imagen [$image_id] al valor [$value]");
                }
            }

            if ($ok) {
                Message::info('Se han actualizado los datos');
                // recalculamos las galerias e imagen

                // getGalleries en Project\Image  procesa todas las secciones
                $galleries = Model\Project\Image::getGalleries($id);
                Model\Project\Image::setImage($id, $galleries['']);
            }

            return $this->redirect(self::getUrl('images', $id));

        }

        // imagenes
        $images = array();

        // secciones
        $sections = Model\Project\Image::sections();
        foreach ($sections as $sec => $secName) {
            $secImages = Model\Project\Image::get($project->id, $sec);
            foreach ($secImages as $img) {
                $images[$sec][] = $img;
            }
        }

        return array(
                'template' => 'admin/projects/images',
                'project' => $project,
                'images' => $images,
                'image_sections' => $sections
        );
    }


    public function accountsAction($id) {
        $project = $this->getProject($id, 'moderate');

        if ($this->isPost()) {
            $accounts = Model\Project\Account::get($project->id);
            $accounts->bank = $this->getPost('bank');
            $accounts->bank_owner = $this->getPost('bank_owner');
            $accounts->paypal = $this->getPost('paypal');
            $accounts->paypal_owner = $this->getPost('paypal_owner');
            $accounts->skip_login = $this->getPost('skip_login') ? true : false;
            if ($accounts->save($errors)) {
                Message::info('Se han actualizado las cuentas del proyecto '.$project->name);
            } else {
                Message::error(implode('<br />', $errors));
            }
            return $this->redirect();

        }

        $accounts = Model\Project\Account::get($project->id);

        // cambiar fechas
        return array(
                'template' => 'admin/projects/accounts',
                'project' => $project,
                'accounts' => $accounts
        );

    }

    public function locationAction($id) {
        $project = $this->getProject($id, 'edit');

        if ($this->isPost()) {
            if($this->getPost('latitude') && $this->getPost('longitude')){
                $loc = new ProjectLocation(array(
                    'id'           => $project->id,
                    'city'         => $this->getPost('city'),
                    'region'       => $this->getPost('region'),
                    'country'      => $this->getPost('country'),
                    'country_code' => $this->getPost('country'),
                    'longitude'    => $this->getPost('longitude'),
                    'latitude'     => $this->getPost('latitude'),
                    'radius'       => $this->getPost('radius'),
                    'method'       => 'manual'
                ));
                $errors = [];
                if ($loc->save($errors)) {
                    Message::info('Localización actualizada a '.$this->getPost('city') .', '.$this->getPost('country'));
                } else {
                    Message::error(implode("<br>", $errors));
                }
            }
            else {
                Message::error('Error: geolocalización no cambiada!');
            }
            return $this->redirect('/admin/projects/location/' . $project->id);
        }

        $location = ProjectLocation::get($project);

        // cambiar fechas
        if(empty($location->city)) {
            $location->city = $project->location;
        }
        return array(
                'template' => 'admin/projects/location',
                'project' => $project,
                'location' => $location,
                'with_radius' => true,
                'radius' => $location->radius
        );

    }


    public function datesAction($id = null, $subaction = null) {

        $project = $this->getProject($id, 'edit');

        if($this->isPost()) {
            $fields = array(
                'created',
                'updated',
                'published',
                'success',
                'closed',
                'passed'
                );

            $set = '';
            $values = array(':id' => $id);

            foreach ($fields as $field) {
                $val = $this->getPost($field);
                if (empty($val) || $val === '0000-00-00')
                    $val = null;
                if(!is_null($val))
                {
                    //validate date
                    $d = \DateTime::createFromFormat('Y-m-d', $val);
                    if($d && $d->format('Y-m-d') == $val) {
                        $values[":$field"] = $val;
                        if ($set != '') $set .= ", ";
                        $set .= "`$field` = :$field ";
                    }
                    else {
                        Message::error("Error en formato de fecha [$val]");

                    }
                }
            }

            try {
                $sql = "UPDATE project SET " . $set . " WHERE id = :id";
                if (Model\Project::query($sql, $values)) {
                    $log_text = 'El admin %s ha <span class="red">tocado las fechas</span> del proyecto '.$project->name.' %s';

                } else {
                    $log_text = 'Al admin %s le ha <span class="red">fallado al tocar las fechas</span> del proyecto '.$project->name.' %s';
                }

                $project = $this->getProject($id, 'edit');
                // feed this action
                $this->doFeed($project, $log_text);

            } catch(\PDOException $e) {
                Message::error("Ha fallado! " . $e->getMessage());
            }
        }
        // cambiar fechas
        return array(
                'template' => 'admin/projects/dates',
                'project' => $project
        );

    }


    public function reviewAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');
        // pasar un proyecto a revision
        if ($project->ready($errors)) {
            // feed this action
            $this->doFeed($project, 'El admin %s ha pasado el proyecto %s al estado <span class="red">Revision</span>');
            return $this->redirect('/admin/reviews?project='. $project->id);
        } else {
            $this->doFeed('Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Revision</span>');
        }
        return $this->redirect();
    }

    public function publishAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        // PUBLISH EVENT
        $event = $this->dispatch(AppEvents::PROJECT_PUBLISH, new FilterProjectEvent($project));

        return $this->redirect();
    }

    public function enableAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');
        $consultants = $project->getConsultants();
        // si no esta en edicion, recuperarlo

        // Si el proyecto no tiene asesor, asignar al admin que lo ha pasado a negociación
        // No funciona con el usuario root
        // TODO
        if ((empty($consultants)) && $this->user->id != 'root') {
            if ($project->assignConsultant($this->user->id, $errors)) {
                $msg = 'Se ha asignado tu usuario (' . $this->user->id . ') como asesor del proyecto "' . $project->id . '"';
                Message::info($msg);
            }
        }

        if ($project->enable($errors)) {
            $this->doFeed($project, 'El admin %s ha pasado el proyecto %s al estado <span class="red">Edicion</span>');
        } else {
            $this->doFeed($project, 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Edicion</span>');
        }
        return $this->redirect();
    }

    // descartar un proyecto por malo
    public function cancelAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');
        $consultants = $project->getConsultants();
        // Asignar como asesor al admin que lo ha descartado
        if ($this->user->id != 'root') {
            if ((!isset($consultants[$this->user->id])) && ($project->assignConsultant($this->user->id, $errors))) {
                $msg = 'Se ha asignado tu usuario (' . $this->user->id . ') como asesor del proyecto "' . $project->id . '"';
                Message::info($msg);
            }
        }

        if ($project->cancel($errors)) {
            $this->doFeed($project, 'El admin %s ha pasado el proyecto %s al estado <span class="red">Descartado</span>');
        } else {
            $this->doFeed($project, 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Descartado</span>');
        }
        return $this->redirect();
    }

    public function fulfillAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        // marcar que el proyecto ha cumplido con los retornos colectivos
        if ($project->satisfied($errors)) {
            $this->doFeed($project, 'El admin %s ha pasado el proyecto %s al estado <span class="red">Retorno cumplido</span>');
        } else {
            $this->doFeed($project, 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Retorno cumplido</span>');
        }

        return $this->redirect();
    }

    public function unfulfillAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        // dar un proyecto por financiado manualmente
        if ($project->rollback($errors)) {
            $this->doFeed($project, 'El admin %s ha pasado el proyecto %s al estado <span class="red">Financiado</span>');
        } else {
            $this->doFeed($project, 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Financiado</span>');
        }

        return $this->redirect();
    }

    //rechazo express
    public function rejectAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');
        $consultants = $project->getConsultants();

        //  idioma de preferencia
        $comlang = Model\User::getPreferences($project->user)->comlang;

        // Obtenemos la plantilla para asunto y contenido
        $template = Template::get(Template::PROJECT_EXPRESS_DISCARD, $comlang);
        // Sustituimos los datos
        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
        $search  = array('%USERNAME%', '%PROJECTNAME%');
        $replace = array($project->user->name, $project->name);
        $content = \str_replace($search, $replace, $template->parseText());
        // iniciamos mail
        $mailHandler = new Mail();
        $mailHandler->lang = $comlang;
        $mailHandler->to = $project->user->email;
        $mailHandler->toName = $project->user->name;
        $mailHandler->subject = $subject;
        $mailHandler->content = $content;
        $mailHandler->html = true;
        $mailHandler->template = $template->id;
        if ($mailHandler->send()) {
            Message::info('Se ha enviado un email a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
        } else {
            Message::error('Ha fallado al enviar el mail a <strong>'.$project->user->name.'</strong> a la dirección <strong>'.$project->user->email.'</strong>');
        }
        unset($mailHandler);

        // Asignar como asesor al admin que lo ha rechazado
        if ($this->user->id != 'root') {
            if ((!isset($consultants[$this->user->id])) && ($project->assignConsultant($this->user->id, $errors))) {
                $msg = 'Se ha asignado tu usuario (' . $this->user->id . ') como asesor del proyecto "' . $project->id . '"';
                Message::info($msg);
            }
        }

        $project->cancel();

        return $this->redirect();
    }

    //  Reject derivating to other plattform
    public function derivationAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');
        $consultants = $project->getConsultants();

        //  idioma de preferencia
        $comlang = Model\User::getPreferences($project->user)->comlang;
        $dest= 'owner';

        $vars=[ '%PROJECTNAME%' => $project->name,
        ];

        // Send mail to owner: project not accepted in the Matcher
        $tpl = Template::PROJECT_DERIVATION_DISCARD;
        $mail = Mail::createFromTemplate($project->user->email, $project->user->name, $tpl, $vars, $comlang);

        $errors = [];

        if ($mail->send($errors)) {
            $this->notice("Communication sent successfully to $dest", ['type' => 'project', $project, 'email' => $mail->to, 'bcc' => $mail->bcc, 'template' => $mail->template]);
        } else {
            $this->critical("ERROR sending communication to $dest", ['type' => 'project', $project, 'email' => $mail->to, 'bcc' => $mail->bcc, 'template' => $mail->template, 'errors' => $errors]);
        }

        // Admin as consultor
        if ($this->user->id != 'root') {
            if ((!isset($consultants[$this->user->id])) && ($project->assignConsultant($this->user->id, $errors))) {
                $msg = 'Se ha asignado tu usuario (' . $this->user->id . ') como asesor del proyecto "' . $project->id . '"';
                Message::info($msg);
            }
        }

        $project->cancel();

        return $this->redirect();
    }

    // cortar el grifo
    public function noinvestAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'admin');

        if (Model\Project\Conf::closeInvest($project->id)) {
            $this->doFeed($project, 'El admin %s ha <span class="red">cerrado el grifo</span> al proyecto %s');
        } else {
            Message::error('Ha fallado <strong>cerrar el grifo</strong>');
            $this->doFeed($project, 'Al admin %s le ha <span class="red">fallado al cerrar el grifo</span> al proyecto %s');
        }

        return $this->redirect();
    }

    // abrir el grifo
    public function openinvestAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'admin');

        if (Model\Project\Conf::openInvest($project->id)) {
            $this->doFeed($project, 'El admin %s ha <span class="red">abierto el grifo</span> al proyecto %s');
        } else {
            Message::error('Ha fallado <strong>abrir el grifo</strong>');
            $this->doFeed($project, 'Al admin %s le ha <span class="red">fallado al abrir el grifo</span> al proyecto %s');
        }

        return $this->redirect();
    }

    // Vigilar
    public function watchAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        if (Model\Project\Conf::watch($project->id)) {
            $this->doFeed($project, 'El admin %s ha empezado a <span class="red">vigilar</span> el proyecto %s');
        } else {
            Message::error('Ha fallado <strong>empezar a vigilar</strong>');
            $this->doFeed($project, 'Al admin %s le ha <span class="red">fallado la vigilancia</span> el proyecto %s');
        }
        return $this->redirect();
    }

    // Dejar de vigilar
    public function unwatchAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        if (Model\Project\Conf::unwatch($project->id)) {
            $this->doFeed($project, 'El admin %s ha <span class="red">dejado de vigilar</span> el proyecto %s');
        } else {
            Message::error('Ha fallado <strong>dejar de vigilar</strong>');
            $this->doFeed($project, 'Al admin %s le ha <span class="red">fallado dejar de vigilar</span> el proyecto %s');
        }

        return $this->redirect();
    }

    // Finalizar campaña
    public function finishAction($id) {
        // Project && permission check
        $project = $this->getProject($id, 'moderate');

        if (Model\Project\Conf::finish($project)) {
            $this->doFeed($project, 'El admin %s ha <span class="red">finalizado la campaña</span> del proyecto %s');
        } else {
            Message::error('Ha fallado <strong>finalizar campaña</strong>');
            $this->doFeed($project, 'Al admin %s le ha <span class="red">fallado finalizar campaña</span> del proyecto %s');
        }

        return $this->redirect();
    }

    public function listAction($id = null, $subaction = null) {
        $filters = $this->getFilters();
        $limit = 10;
        $projects = Model\Project::getList($filters, $this->node, $this->getGet('pag') * $limit, $limit);
        $total = Model\Project::getList($filters, $this->node, 0, 0 , true);


        $status = Model\Project::status();
        $categories = Model\Project\Category::getAll();
        $contracts = Model\Contract::getProjects();
        $open_tags = Model\Project\OpenTag::getAll();
        $orders = array(
            'name' => 'Nombre',
            'updated' => 'Enviado a revision',
            'publishing_estimation' => 'Fecha publicación estimada'
        );

        return  array(
                'template' => 'admin/projects/list',
                'projects' => $projects,
                'filters' => $filters,
                'status' => $status,
                'categories' => $categories,
                'contracts' => $contracts,
                'admins' => $this->admins,
                'open_tags' => $open_tags,
                'orders' => $orders,
                'limit' => $limit,
                'total' => $total
        );
    }


    private function doFeed($project, $log_text) {
        // Feed
        if ($log_text) {
            // Evento Feed
            $log = new Feed();
            $log->setTarget($project->id);
            $log->populate('feed-admin-project-action', '/admin/projects',
                \vsprintf($log_text, array(
                    Feed::item('user', $this->user->name, $this->user->id),
                    Feed::item('project', $project->name, $project->id)
                )));
            $log->doAdmin('admin');

            Message::info($log->html);
            if (!empty($errors)) {
                Message::error(implode('<br />', $errors));
            }

            if ($action == 'publish') {
                // si es publicado, hay un evento publico
                $log->populate($project->name, '/project/'.$project->id, Text::html('feed-new_project'), $project->image);
                $log->doPublic('projects');
            }
        }

    }
}
