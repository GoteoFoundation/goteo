<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console;

use Goteo\Model;
use Goteo\Application\Lang;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Core\View;
use Goteo\Core\Redirection;
use Goteo\Library\Text;
use Goteo\Model\Template;
use Goteo\Model\Mail;
use Goteo\Model\Project;
use Goteo\Model\Blog\Post;

class UsersSend extends AbstractCommandController {
    protected static $URL;

    // asesores por defecto si no un proyecto no tiene asesores
    // TODO: by config...
    public static $consultants = array(
        'root' => 'Root'
    );

    public static function setURL($url) {
        self::$URL = $url;
    }

    public static function getURL() {
        if(self::$URL) return self::$URL;
        return SITE_URL;
    }

    static public function setConsultants(array $consultants) {
        self::$consultants = $consultants;
    }

    /**
     * Al autor del proyecto, se encarga de substituir variables en plantilla
     *
     * @param $type string Identificador de la plantilla
     * @param $project Object El proyecto que está relacionado con el envío del email
     * @return bool
     */
    public static function toOwner ($type, Project $project) {
        $tpl = null; // Número de la plantilla que se obtendrá a partir del identificador
        $error_sending = false;

        // necesitamos saber los consultores
        // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, ponemos default
        // TODO: default from config, not here
        if($project->getConsultants()) {
            $consultants = implode(', ', $project->getConsultants());
        } else {
            $consultants = current(self::$consultants);
        }



        /// tipo de envio
        switch ($type) {
            // Estos son avisos de final de ronda
            case 'unique_pass': // template 20, proyecto finaliza la única ronda
                $tpl = 60;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/rewards');
                break;

            case 'r1_pass': // template 20, proyecto supera la primera ronda
                $tpl = 20;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/widgets');
                break;

            case 'fail': // template 21, caduca sin conseguir el mínimo
                $tpl = 21;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%SUMMARYURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/summary');
                break;

            case 'r2_pass': // template 22, finaliza segunda ronda
                $tpl = 22;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/rewards');
                break;

            // Estos son avisos de auto-tips de /cron/daily
            case '8_days': // template 13, cuando faltan 8 días y no ha conseguido el mínimo
                $tpl = 13;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/widgets');
                break;

            case '2_days': // template 14, cuando faltan 2 días y no ha conseguido el mínimo
                $tpl = 14;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/widgets');
                break;

            case 'two_weeks': // template 19, "no bajes la guardia!" 25 días de campaña
                $tpl = 19;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/widgets');
                break;

            case 'no_updates': // template 23, 3 meses sin novedades
                $tpl = 23;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATESURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/updates');
                break;

            case 'any_update': // template 24, no hay posts de novedades
                $tpl = 24;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATESURL%', '%NOVEDADES%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/updates', self::getURL().'/project/'.$project->id.'/updates');
                break;

            case '1d_after': // template 55, dia siguiente de financiado
                $tpl = 55;
                $search  = array('%USERNAME%', '%PROJECTNAME%');
                $replace = array($project->user->name, $project->name);
                break;

            case '2m_after': // template 25, dos meses despues de financiado
                $tpl = 25;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/rewards');
                break;

            case '8m_after': // template 52, ocho meses despues de financiado
                $tpl = 52;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%COMMONSURL%');
                $replace = array($project->user->name, $project->name, self::getURL() . '/dashboard/projects/shared-materials');
                break;

            case '20_backers': // template 46, "Apóyate en quienes te van apoyando "  (en cuanto se llega a los 20 backers
                $tpl = 46;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%NUMBACKERS%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->num_investors);
                break;

            case 'project_to_review': // template 8, "Confirmacion de proyecto enviado".  template 62, "Al enviar tras la negociación"
                // tener en cuenta si están enviando el draft o la negociación
                $tpl = ($project->draft) ? 8 : 62;

                $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%', '%NOMBREASESOR%');
                $replace = array($project->name, $project->user->name, self::getURL().'/project/'.$project->id, self::getURL().'/project/edit/'.$project->id, $consultants);
                break;

            case 'project_created':
                // Project created
                $tpl = Template::PROJECT_CREATED;

                $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%');
                $replace = array($project->name, $project->user->name, self::getURL().'/project/'.$project->id, self::getURL().'/project/edit/'.$project->id, $consultants);
                break;

            case 'tip_0':
                $tpl = 57;

                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%NOMBREASESOR%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $consultants);
                break;

            // consejos normales
            case 'tip_1': // template 41, "Difunde, difunde, difunde"
                $tpl = 41;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%PORCENTAJE%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->percent);
                break;

            case 'tip_2': // template 42, "Comienza por lo más próximo"
                $tpl = 42;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%PORCENTAJE%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->percent);
                break;

            case 'tip_3': // template 43, "Una acción a diario, por pequeña que sea"
                $tpl = 43;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->amount);
                break;

            case 'tip_4': // template 44, "Llama a todas las puertas"
                $tpl = 44;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%BACKERSURL%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, self::getURL().'/project/'.$project->id.'/supporters');
                break;

            case 'tip_5': // template 45, "Busca dónde está tu comunidad"
                $tpl = 45;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%NUMBACKERS%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->amount, $project->num_investors);
                break;

            case 'tip_8': // template 47, "Agradece en público e individualmente"
                $tpl = 47;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%MESSAGESURL%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, self::getURL().'/project/'.$project->id.'/messages');
                break;

            case 'tip_9': // template 48, "Busca prescriptores e implícalos"
                $tpl = 48;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%PORCENTAJE%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->percent);
                break;

            case 'tip_10': // template 49, "Luce tus recompensas y retornos"
                $tpl = 49;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%LOWREWARD%', '%HIGHREWARD%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->lower, $project->higher);
                break;

            case 'tip_11': // template 50, "Refresca tu mensaje de motivacion"
                $tpl = 50;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id);
                break;

            case 'tip_15': // template 51, "Sigue los avances y calcula lo que falta"
                $tpl = 51;
                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%DIASCAMPAÑA%', '%DAYSTOGO%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, $project->days, $project->days);
                break;

        }


        if (empty($tpl)) {
            static::error("ERROR: template not found for type [$type]", ['type' => $type, $project]);
            return false;
        }

        static::info('Sending communication to owner', ['type' => $type, $project, 'email' => $project->user->email, 'template' => $tpl]);

        $errors = array();

        //  idioma de preferencia del usuario
        $comlang = Model\User::getPreferences($project->user->id)->comlang;

        // Obtenemos la plantilla para asunto y contenido
        $template = Template::get($tpl, $comlang);

        // Sustituimos los datos
        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
        $content = \str_replace($search, $replace, $template->parseText());
        // iniciamos mail
        $mailHandler = new Mail();
        $mailHandler->lang = $comlang;
        $mailHandler->to = $project->user->email;
        $mailHandler->toName = $project->user->name;

        // vigilancia de proyectos (añade en copia oculta a asesores + otros)
        $monitors = array();
        if (Model\Project\Conf::isWatched($project->id)) {
            foreach ($project->getConsultants() as $id => $name) {
                $user = Model\User::getMini($id);
                $monitors[] = $user->email;
            }
            $mailHandler->bcc = $monitors;
        }

        // si es un proyecto de nodo: reply al mail del nodo
        // si es de centra: reply a MAIL_GOTEO
        $mailHandler->reply = (!empty($project->nodeData->email)) ? $project->nodeData->email : Config::getMail('transport.from');

        $mailHandler->subject = $subject;
        $mailHandler->content = $content;
        $mailHandler->html = true;
        $mailHandler->template = $template->id;
        if ($mailHandler->send($errors)) {
            static::notice("Communication sent successfully to owner", ['type' => $type, $project, 'email' => $project->user->email, 'bcc' => $monitors, 'template' => $tpl]);
        } else {
            static::critical("ERROR sending communication to owner", ['type' => $type, $project, 'email' => $project->user->email, 'bcc' => $monitors, 'template' => $tpl, 'errors' => $errors]);
            $error_sending = true;
        }

        return !$error_sending;
    }

    /**
     * Al asesor del proyecto, se encarga de sustituir variables en plantilla
     *
     * @param $type string Identificador de la plantilla
     * @param $project Object Proyecto
     * @return bool
     */
    public static function toConsultants ($type, $project) {

        $error_sending = false;
        $tpl = null;

        $consultants = $project->getConsultants();
        // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, enviar a los asesores por defecto
        if (empty($consultants)) {
            $consultants = self::$consultants;
        }

        /// tipo de envio
        switch ($type) {
            case 'commons': // template 56, "Mensaje al asesor de un proyecto 10 meses despues de financiado sin haber cumplido"
                $tpl = 56;

                $contact = Model\Project::getContact($project->id);
                $info_html = new View('admin/commons/contact.html.php', array('contact' => $contact));

                $search  = array('%PROJECTNAME%', '%URL%', '%INFO%');
                $replace = array($project->name, self::getURL() . '/admin/commons?project=' . $project->id, $info_html);

                break;

            case 'tip_0':
                $tpl = 57;

                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%NOMBREASESOR%');
                $replace = array($project->user->name, $project->name, self::getURL().'/project/'.$project->id, implode(', ', $consultants));
                break;

            case 'rewardfulfilled': // template 58, "Aviso a asesores cuando un impulsor indica la url de retorno colectivo"
                $tpl = 58;

                $commons_url = self::getURL() . '/admin/commons/view/' . $project->id;
                $reward = Model\Project\Reward::get($_POST['reward']);

                // También podríamos usar Session::getUser()->name
                $search  = array('%PROJECTNAME%', '%WHO%', '%WHOROLE%', '%RETURN%', '%URL%', '%COMMONSURL%');
                $replace = array($project->name, $project->whodidit, $project->whorole, $reward->reward, $_POST['value'], $commons_url);
                break;

            case 'project_to_review_consultant': // template 59, "Aviso a asesores cuando un impulsor envia el proyecto a revisión"
                $tpl = 59;

                $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%', '%COMMENT%');
                $replace = array($project->name, $project->user->name, self::getURL().'/project/'.$project->id, self::getURL().'/project/edit/'.$project->id, $project->comment);
                break;

            case 'project_preform_to_review_consultant': // template 63, "Aviso a asesores cuando un impulsor envia el proyecto a revisión desde preform"
                $tpl = 63;

                // get the project configuration
                $conf = Model\Project\Conf::get($project->id);
                $date=new \Datetime($conf->publishing_estimation);
                $date_publishing=$date->format("d-m-Y");

                //Creamos el mensaje que avisa si ha solicitado ayuda a través de los checkbox
                $help="";
                if($project->help_cost) $help.=Text::get('help-cost-to-consultant').'<br>';
                if($project->help_license) $help.=Text::get('help-license-to-consultant').'<br>';
                $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%', '%HELP%', '%SPREAD%', '%PROJECTDESCRIPTION%', '%PROJECTMIN%', '%COMMENT%', '%PUBLISHINGESTIMATION%');
                $replace = array($project->name, $project->user->name, self::getURL().'/project/'.$project->id, self::getURL().'/project/edit/'.$project->id, $help, $project->spread, nl2br($project->description), $project->mincost, $project->comment, $date_publishing);
                break;

                //Pasamos la difusión
        }

        if (empty($tpl)) {
            static::error("ERROR: template not found for type [$type]", ['type' => $type, $project]);
            return false;
        }


        $errors = array();
        // Obtenemos la plantilla para asunto y contenido
        $template = Template::get($tpl);
        // Sustituimos los datos
        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
        $pre_content = \str_replace($search, $replace, $template->parseText());

        foreach ($consultants as $id=>$name) {
            $consultant = Model\User::getMini($id);

            // Sustituimos el nombre del asesor en el cuerpo del e-mail
            $content = \str_replace('%USERNAME%', $name, $pre_content);

            // iniciamos mail
            $mailHandler = new Mail();
            $mailHandler->to = $consultant->email;
            $mailHandler->toName = $name;

            static::info('Sending communication to consultant', ['type' => $type, 'consultant' => $id, 'name' => $name, 'email' => $consultant->email, $project, 'template' => $tpl]);

            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->html = true;
            $mailHandler->template = $template->id;
            if ($mailHandler->send($errors)) {
                static::notice("Communication sent successfully to owner", ['type' => $type, 'consultant' => $id, 'name' => $name, 'email' => $consultant->email, $project, 'template' => $tpl]);
            } else {
                static::critical("ERROR sending communication to consultant", ['type' => $type, 'consultant' => $id, 'name' => $name, 'email' => $consultant->email, $project, 'template' => $tpl, 'errors' => $errors]);
                $error_sending = true;
            }

        }
        return !$error_sending;
    }

    /**
     *  A los cofinanciadores
     * Se usa tambien para notificar cuando un proyecto publica una novedad.
     * Por eso añadimos el tercer parámetro, para recibir los datos del post
     *
     * @param $type string
     * @param $project Object
     * @param $post Object
     * @return bool
     */
    static public function toInvestors ($type, Project $project, array $invest_status = null, Post $post = null) {

        // notificación
        $notif = $type == 'update' ? 'updates' : 'rounds';

        $anyfail = false;
        $tpl = null;

        // para usar el proceso Sender

        // - Separamos los replaces de contenido de los replaces individuales (%USERNAME%)
        switch ($type) {
            case 'unique_pass': // template 61, finaliza única ronda
                    $tpl = 61;
                    $search  = array('%PROJECTNAME%', '%PROJECTURL%');
                    $replace = array($project->name, self::getURL() . '/project/' . $project->id);
                break;

            case 'r1_pass': // template 15, proyecto supera la primera ronda
                    $tpl = 15;
                    $search  = array('%PROJECTNAME%', '%PROJECTURL%');
                    $replace = array($project->name, self::getURL() . '/project/' . $project->id);
                break;

            case 'fail': // template 17, proyecto no consigue el mínimo
                    $tpl = 17;
                    $search  = array('%PROJECTNAME%', '%DISCOVERURL%');
                    $replace = array($project->name, self::getURL() . '/discover');
                break;

            case 'r2_pass': // template 16, finaliza segunda ronda
                    $tpl = 16;
                    $search  = array('%PROJECTNAME%', '%PROJECTURL%');
                    $replace = array($project->name, self::getURL() . '/project/' . $project->id);
                break;

            case 'update': // template 18, publica novedad
                    $tpl = 18;
                    $post_url = self::getURL().'/project/'.$project->id.'/updates/'.$post->id;
                    // contenido del post
                    $post_content = "<p><strong>{$post->title}</strong><br />".  nl2br( Text::recorta($post->text, 500) )  ."</p>";
                    // y preparar los enlaces para compartir en redes sociales
                    $share_urls = Text::shareLinks($post_url, $post->title);

                    $search  = array('%PROJECTNAME%', '%UPDATEURL%', '%POST%', '%SHAREFACEBOOK%', '%SHARETWITTER%');
                    $replace = array($project->name, $post_url, $post_content, $share_urls['facebook'], $share_urls['twitter']);
                break;
        }


        if (empty($tpl)) {
            static::error("ERROR: template not found for type [$type]", ['type' => $type, $project]);
            return false;
        }

        // con esto montamos el receivers
        $receivers = array();

        // para cada inversor que no tenga bloqueado esta notificacion
        // sacamos idioma de preferencia
        // Y esto también tendía que mirar idioma alternativo al de preferencia
        if(empty($invest_status)) $invest_status = ['0', '1', '3', '4'];
        $status = array();
        foreach($invest_status as $val) {
            $status[":status$val"] = $val;
        }
        $sql = "
            SELECT
                invest.user as id,
                user.name as name,
                user.email as email,
                IFNULL(user_prefer.comlang, IFNULL(user.lang, 'es')) as lang
            FROM  invest
            INNER JOIN user
                ON user.id = invest.user
                AND user.active = 1
            LEFT JOIN user_prefer
                ON user_prefer.user = invest.user
            WHERE   invest.project = :project
            AND invest.status IN (" . implode(", ", array_keys($status)) . ")
            AND (user_prefer.{$notif} = 0 OR user_prefer.{$notif} IS NULL)
            GROUP BY user.id
            ";
        $values = $status + [':project' => $project->id];
        // static::debug('Retrieving donors list SQL', ['sql' => str_replace("\n", " ", \sqldbg($sql, $values))]);

        if ($query = Model\Invest::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User') as $investor) {
                static::info('Adding donor to massive sending', [$investor, 'type' => $type, $project, 'template' => $tpl]);

                // $receivers[$investor->lang][] = (object) array(
                $receivers[] = (object) array(
                    'user' => $investor->id,
                    'name' => $investor->name,
                    'email' => $investor->email,
                    'lang' => $investor->lang
                    );
            }
        }
        if(empty($receivers)) {
            static::warning("No receivers found for massive sending", ['type' => $type, $project, 'template' => $tpl]);
        }

        $comlang = Lang::current();

        // Obtenemos la plantilla para asunto y contenido
        $template = Template::get($tpl, $comlang);


        // - subject
        if (!empty($post)) {
            $subject = str_replace(array('%PROJECTNAME%', '%OWNERNAME%', '%P_TITLE%')
                    , array($project->name, $project->user->name, $post->title)
                    , $template->title);
        } else {
            $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
        }

        // content
        $content = \str_replace($search, $replace, $template->parseText());

        $mailHandler = new Mail();
        $mailHandler->template = $tpl;
        $mailHandler->subject = $subject;
        $mailHandler->content = $content;
        $mailHandler->node = \GOTEO_NODE;
        $mailHandler->lang = $comlang;
        $mailHandler->massive = true;
        if( ! $mailHandler->save() ) {
            static::critical("ERROR saving mailHandler", ['type' => $type, $project, $mailHandler]);
            return false;
        }


        // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
        if (\Goteo\Model\Mail\Sender::initiateSending($mailHandler->id, $receivers, 1)) {
            static::notice('Newsletter activated', ['type' => $type, $project, $mailHandler]);
        }
        else {
            static::critical("ERROR initiating massive sending", ['type' => $type, $project, $mailHandler]);
        }

        return true;

    }

    /**
     * @deprecated ?
     * NO CURRENTLY USED
     *
     * A los destinatarios de recompensa (regalo)
     * solo tipo 'fail' por ahora
     * @param $type string (FIXME: sin uso)
     * @param $project Object
     * @return bool
     */
    static public function toFriends ($type, $project) {

        $anyfail = false;
        $tpl = null;

        // para cada persona (que no es usuario) que espera una recompensa
        $sql = "
            SELECT
                invest.id as id,
                user.name as user,
                invest_address.namedest as name,
                invest_address.emaildest as email
            FROM  invest
            INNER JOIN invest_address
                ON invest_address.invest = invest.id
                AND invest_address.regalo = 1
            INNER JOIN user
                ON user.id = invest.user
            WHERE   invest.project = ?
            AND invest.status IN ('0', '1', '3', '4')
            ";
        if ($query = Model\Invest::query($sql, array($project->id))) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {

                // la recompensa
                $txt_rewards = Model\Project\Reward::txtRewards($investor->id);

                $search  = array('%USERNAME%', '%DESTNAME%', '%PROJECTNAME%', '%URL%', '%REWNAME%');
                $replace = array($investor->name, $investor->name, $project->name, self::getURL(), $txt_rewards);

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(Template::PROJECT_FAILED_RECEIVERS, $comlang);
                // Sustituimos los datos
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                $content = \str_replace($search, $replace, $template->parseText());
                // iniciamos mail
                $mailHandler = new Mail();
                $mailHandler->lang = $comlang;
                $mailHandler->to = $investor->email;
                $mailHandler->toName = $investor->name;
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send()) {

                } else {
                    $anyfail = true;
                    @mail(Config::getMail('fail'),
                        'Fallo al enviar email automaticamente al amigo ' . self::getURL(),
                        'Fallo al enviar email automaticamente al amigo: <pre>' . print_r($mailHandler, true). '</pre>');
                }
                unset($mailHandler);
            }
            // fin bucle inversores
        } else {
            echo '<p>'.str_replace('?', $project->id, $sql).'</p>';
            $anyfail = true;
        }

        if ($anyfail)
            return false;
        else
            return true;

    }

}
