<?php

// $debug = \defined('CRON_EXEC'); es necesario, ya que funciones como toConsultants() se llaman desde otros sitios (no solo por cron)
// Hay un AJAX que comprueba si la respuesta es 'OK' y si se escriben datos de depuracion no es válida.

namespace Goteo\Controller\Cron {

    use Goteo\Model;
    use Goteo\Application\Lang;
    use Goteo\Application\Session,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Library\Text,
        Goteo\Library\Template,
        Goteo\Library\Mail,
        Goteo\Core\Error;

    class Send {

        // asesores por defecto si no un proyecto no tiene asesores
        public static $consultants = array(
            'esenabre' => 'Enric Senabre',
            'olivier' => 'Olivier Schulbaum',
            'merxxx' => 'Mercè Moreno Tarrés'
        );

        /**
         * Al autor del proyecto, se encarga de substituir variables en plantilla
         *
         * @param $type string Identificador de la plantilla
         * @param $project Object El proyecto que está relacionado con el envío del email
         * @return bool
         */
        public static function toOwner ($type, $project) {
            $tpl = null; // Número de la plantilla que se obtendrá a partir del identificador
            $debug = \defined('CRON_EXEC');
            $error_sending = false;

            if ($debug) echo 'toOwner: ';

            /// tipo de envio
            switch ($type) {
                // Estos son avisos de final de ronda
                case 'unique_pass': // template 20, proyecto finaliza la única ronda
                    $tpl = 60;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/rewards');
                    break;

                case 'r1_pass': // template 20, proyecto supera la primera ronda
                    $tpl = 20;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case 'fail': // template 21, caduca sin conseguir el mínimo
                    $tpl = 21;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%SUMMARYURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/summary');
                    break;

                case 'r2_pass': // template 22, finaliza segunda ronda
                    $tpl = 22;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/rewards');
                    break;

                // Estos son avisos de auto-tips de /cron/daily
                case '8_days': // template 13, cuando faltan 8 días y no ha conseguido el mínimo
                    $tpl = 13;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case '2_days': // template 14, cuando faltan 2 días y no ha conseguido el mínimo
                    $tpl = 14;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case 'two_weeks': // template 19, "no bajes la guardia!" 25 días de campaña
                    $tpl = 19;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case 'no_updates': // template 23, 3 meses sin novedades
                    $tpl = 23;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATESURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/updates');
                    break;

                case 'any_update': // template 24, no hay posts de novedades
                    $tpl = 24;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATESURL%', '%NOVEDADES%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/updates', SITE_URL.'/project/'.$project->id.'/updates');
                    break;

                case '1d_after': // template 55, dia siguiente de financiado
                    $tpl = 55;
                    $search  = array('%USERNAME%', '%PROJECTNAME%');
                    $replace = array($project->user->name, $project->name);
                    break;

                case '2m_after': // template 25, dos meses despues de financiado
                    $tpl = 25;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/rewards');
                    break;

                case '8m_after': // template 52, ocho meses despues de financiado
                    $tpl = 52;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%COMMONSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/commons');
                    break;

                case '20_backers': // template 46, "Apóyate en quienes te van apoyando "  (en cuanto se llega a los 20 backers
                    $tpl = 46;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%NUMBACKERS%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->num_investors);
                    break;

                case 'project_to_review': // template 8, "Confirmacion de proyecto enviado".  template 62, "Al enviar tras la negociación"
                    // tener en cuenta si están enviando el draft o la negociación
                    $tpl = ($project->draft) ? 8 : 62;

                    // necesitamos saber los consultores (lo hemos quitado del Project::get  )
                    $project->consultants = Model\Project::getConsultants($project->id);

                    // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, ponemos Enric
                    if(empty($project->consultants)) {
                        $consultants = 'Mercè Moreno Tarrés';
                    } else {
                        $consultants_copy = $project->consultants;
                        $consultants = array_shift($consultants_copy);
                        foreach ($consultants_copy as $userId=>$userName) {
                            $consultants .= ', ' . $userName;
                        }
                    }

                    $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%', '%NOMBREASESOR%');
                    $replace = array($project->name, $project->user->name, SITE_URL.'/project/'.$project->id, SITE_URL.'/project/edit/'.$project->id, $consultants);
                    break;

                case 'tip_0':
                    $tpl = 57;

                    // necesitamos saber los consultores (lo hemos quitado del Project::get  )
                    $project->consultants = Model\Project::getConsultants($project->id);

                    // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, ponemos Enric
                    if(empty($project->consultants)) {
                        $consultants = 'Mercè Moreno Tarrés';
                    } else {
                        $consultants_copy = $project->consultants;
                        $consultants = array_shift($consultants_copy);
                        foreach ($consultants_copy as $userId=>$userName) {
                            $consultants .= ', ' . $userName;
                        }
                    }

                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%NOMBREASESOR%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $consultants);
                    break;

                // consejos normales
                case 'tip_1': // template 41, "Difunde, difunde, difunde"
                    $tpl = 41;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%PORCENTAJE%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->percent);
                    break;

                case 'tip_2': // template 42, "Comienza por lo más próximo"
                    $tpl = 42;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%PORCENTAJE%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->percent);
                    break;

                case 'tip_3': // template 43, "Una acción a diario, por pequeña que sea"
                    $tpl = 43;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->amount);
                    break;

                case 'tip_4': // template 44, "Llama a todas las puertas"
                    $tpl = 44;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%BACKERSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, SITE_URL.'/project/'.$project->id.'/supporters');
                    break;

                case 'tip_5': // template 45, "Busca dónde está tu comunidad"
                    $tpl = 45;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%NUMBACKERS%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->amount, $project->num_investors);
                    break;

                case 'tip_8': // template 47, "Agradece en público e individualmente"
                    $tpl = 47;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%MESSAGESURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, SITE_URL.'/project/'.$project->id.'/messages');
                    break;

                case 'tip_9': // template 48, "Busca prescriptores e implícalos"
                    $tpl = 48;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%PORCENTAJE%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->percent);
                    break;

                case 'tip_10': // template 49, "Luce tus recompensas y retornos"
                    $tpl = 49;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%LOWREWARD%', '%HIGHREWARD%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->lower, $project->higher);
                    break;

                case 'tip_11': // template 50, "Refresca tu mensaje de motivacion"
                    $tpl = 50;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id);
                    break;

                case 'tip_15': // template 51, "Sigue los avances y calcula lo que falta"
                    $tpl = 51;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%DIASCAMPAÑA%', '%DAYSTOGO%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->days, $project->days);
                    break;

            }

            if (!empty($tpl)) {
                $errors = array();

                //  idioma de preferencia del usuario
                $prefer = Model\User::getPreferences($project->user->id);
                $comlang = !empty($prefer->comlang) ? $prefer->comlang : $project->user->lang;

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get($tpl, $comlang);

                // Sustituimos los datos
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                $content = \str_replace($search, $replace, $template->text);
                // iniciamos mail
                $mailHandler = new Mail();
                $mailHandler->to = $project->user->email;
                $mailHandler->toName = $project->user->name;

                // vigilancia de proyectos (añade en copia oculta a asesores + otros)
                if (Model\Project\Conf::isWatched($project->id)) {
                    $consultants = Model\Project::getConsultants($project->id);
                    $monitors = array();

                    foreach ($consultants as $id=>$name) {
                        $user = Model\User::getMini($id);
                        $monitors[] = $user->email;
                    }

                    $mailHandler->bcc = $monitors;
                }

                if ($debug) echo $project->user->email . ', ';

                // si es un proyecto de nodo: reply al mail del nodo
                // si es de centra: reply a MAIL_GOTEO
                $mailHandler->reply = (!empty($project->nodeData->email)) ? $project->nodeData->email : \GOTEO_CONTACT_MAIL;

                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if (!$mailHandler->send($errors)) {
                    echo \trace($errors);
                    @mail(\GOTEO_FAIL_MAIL,
                        'Fallo al enviar email automaticamente al autor en ' . SITE_URL,
                        'Fallo al enviar email automaticamente al autor: <pre>' . print_r($mailHandler, true). '</pre>');
                    $error_sending = true;
                }
            }

            if ($debug) echo '<br/>';

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
            $debug = \defined('CRON_EXEC');
            $error_sending = false;
            $tpl = null;

            if ($debug) echo 'toConsultants: ';

            // ya no está por defecto en el ::get()
            $project->consultants = Model\Project::getConsultants($project->id);

            /// tipo de envio
            switch ($type) {
                case 'commons': // template 56, "Mensaje al asesor de un proyecto 10 meses despues de financiado sin haber cumplido"
                    $tpl = 56;

                    $contact = Model\Project::getContact($project->id);
                    $info_html = new View('admin/commons/contact.html.php', array('contact' => $contact));

                    $search  = array('%PROJECTNAME%', '%URL%', '%INFO%');
                    $replace = array($project->name, SITE_URL . '/admin/commons?project=' . $project->id, $info_html);

                    // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, enviar a los asesores por defecto
                    if (empty($project->consultants)) {
                        $project->consultants = self::$consultants;
                    }
                    break;

                case 'tip_0':
                    $tpl = 57;

                    // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, ponemos Enric
                    if (empty($project->consultants)) {
                        $project->consultants = self::$consultants;
                    }

                    $consultants_copy = $project->consultants;
                    $consultants = array_shift($consultants_copy);
                    foreach ($consultants_copy as $userId=>$userName) {
                        $consultants .= ', ' . $userName;
                    }


                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%NOMBREASESOR%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $consultants);
                    break;

                case 'rewardfulfilled': // template 58, "Aviso a asesores cuando un impulsor indica la url de retorno colectivo"
                    $tpl = 58;

                    $commons_url = SITE_URL . '/admin/commons/view/' . $project->id;
                    $reward = Model\Project\Reward::get($_POST['reward']);

                    // También podríamos usar Session::getUser()->name
                    $search  = array('%PROJECTNAME%', '%WHO%', '%WHOROLE%', '%RETURN%', '%URL%', '%COMMONSURL%');
                    $replace = array($project->name, $project->whodidit, $project->whorole, $reward->reward, $_POST['value'], $commons_url);
                    break;

                case 'project_to_review_consultant': // template 59, "Aviso a asesores cuando un impulsor envia el proyecto a revisión"
                    $tpl = 59;

                    // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, ponemos Enric
                    if (empty($project->consultants)) {
                        $project->consultants = self::$consultants;
                    }
                    $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%', '%COMMENT%');
                    $replace = array($project->name, $project->user->name, SITE_URL.'/project/'.$project->id, SITE_URL.'/project/edit/'.$project->id, $project->comment);
                    break;

                case 'project_preform_to_review_consultant': // template 63, "Aviso a asesores cuando un impulsor envia el proyecto a revisión desde preform"
                    $tpl = 63;

                    // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, ponemos Enric
                    if (empty($project->consultants)) {
                        $project->consultants = self::$consultants;
                    }

                    //Creamos el mensaje que avisa si ha solicitado ayuda a través de los checkbox
                    $help="";
                    if($project->help_cost) $help.=Text::get('help-cost-to-consultant').'<br\>';
                    if($project->help_license) $help.=Text::get('help-license-to-consultant').'<br\>';
                    $search  = array('%PROJECTNAME%', '%USERNAME%', '%PROJECTURL%', '%PROJECTEDITURL%', '%HELP%', '%SPREAD%', '%PROJECTDESCRIPTION%', '%COMMENT%');
                    $replace = array($project->name, $project->user->name, SITE_URL.'/project/'.$project->id, SITE_URL.'/project/edit/'.$project->id, $help, $project->spread, $project->description, $project->comment);
                    break;

                    //Pasamos la difusión
            }

            if (!empty($tpl)) {
                $errors = array();
                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get($tpl);
                // Sustituimos los datos
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                $pre_content = \str_replace($search, $replace, $template->text);

                foreach ($project->consultants as $id=>$name) {
                    $consultant = Model\User::getMini($id);

                    // Sustituimos el nombre del asesor en el cuerpo del e-mail
                    $content = \str_replace('%USERNAME%', $name, $pre_content);

                    // iniciamos mail
                    $mailHandler = new Mail();
                    $mailHandler->to = $consultant->email;
                    $mailHandler->toName = $name;

                    if ($debug) echo $consultant->email . ', ';

                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    if (!$mailHandler->send($errors)) {
                        echo \trace($errors);
                        @mail(\GOTEO_FAIL_MAIL,
                            'Fallo al enviar email automaticamente al asesor ' . SITE_URL,
                            'Fallo al enviar email automaticamente al asesor: <pre>' . print_r($mailHandler, true). '</pre>');
                        $error_sending = true;
                    }
                }

            }

            if ($debug) echo '<br/>';

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
        static public function toInvestors ($type, $project, $post = null) {

            // activar debug para mostrar menajes en el log
            $debug = false;

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
                        $replace = array($project->name, SITE_URL . '/project/' . $project->id);
                    break;

                case 'r1_pass': // template 15, proyecto supera la primera ronda
                        $tpl = 15;
                        $search  = array('%PROJECTNAME%', '%PROJECTURL%');
                        $replace = array($project->name, SITE_URL . '/project/' . $project->id);
                    break;

                case 'fail': // template 17, proyecto no consigue el mínimo
                        $tpl = 17;
                        $search  = array('%PROJECTNAME%', '%DISCOVERURL%');
                        $replace = array($project->name, SITE_URL . '/discover');
                    break;

                case 'r2_pass': // template 16, finaliza segunda ronda
                        $tpl = 16;
                        $search  = array('%PROJECTNAME%', '%PROJECTURL%');
                        $replace = array($project->name, SITE_URL . '/project/' . $project->id);
                    break;

                case 'update': // template 18, publica novedad
                        $tpl = 18;
                        $post_url = SITE_URL.'/project/'.$project->id.'/updates/'.$post->id;
                        // contenido del post
                        $post_content = "<p><strong>{$post->title}</strong><br />".  nl2br( Text::recorta($post->text, 500) )  ."</p>";
                        // y preparar los enlaces para compartir en redes sociales
                        $share_urls = Text::shareLinks($post_url, $post->title);

                        $search  = array('%PROJECTNAME%', '%UPDATEURL%', '%POST%', '%SHAREFACEBOOK%', '%SHARETWITTER%');
                        $replace = array($project->name, $post_url, $post_content, $share_urls['facebook'], $share_urls['twitter']);
                    break;
            }


            if (empty($tpl)) return false;

            // con esto montamos el receivers
            $receivers = array();

            // para cada inversor que no tenga bloqueado esta notificacion
            // sacamos idioma de preferencia
            // Y esto también tendía que mirar idioma alternativo al de preferencia
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
                WHERE   invest.project = ?
                AND invest.status IN ('0', '1', '3', '4')
                AND (user_prefer.{$notif} = 0 OR user_prefer.{$notif} IS NULL)
                GROUP BY user.id
                ";
            if ($debug) {
                echo "Template: $tpl<br />";
                echo str_replace('?',"'{$project->id}'",$sql);
            }
            if ($query = Model\Invest::query($sql, array($project->id))) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {

//                    $receivers[$investor->lang][] = (object) array(
                    $receivers[] = (object) array(
                        'user' => $investor->id,
                        'name' => $investor->name,
                        'email' => $investor->email,
                        'lang' => $investor->lang
                        );
                }
            }

            // preparamos el contenido
            // veamos los idiomas que necesitamos
            // array_keys

            // sacamos la plantilla en cada idioma
            // $template_lang['es'] = Template::get($tpl, 'es');

            // Luego, un mailing para cada idioma (como al enviar boletín)

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
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();
            $mailHandler->template = $tpl;
            $mailHandler->content = $content;
            $mailHandler->node = \GOTEO_NODE;
            $mailHandler->lang = $comlang;
            $mailHandler->massive = true;
            $mailId = $mailHandler->saveEmailToDB();


            // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
            if (\Goteo\Library\Sender::initiateSending($mailId, $subject, $receivers, 1))
                return false;
            else
                return true;

        }

        /**
         * A los destinatarios de recompensa (regalo)
         * solo tipo 'fail' por ahora
         *
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
                    $replace = array($investor->name, $investor->name, $project->name, SITE_URL, $txt_rewards);

                    // Obtenemos la plantilla para asunto y contenido
                    $template = Template::get(54);
                    // Sustituimos los datos
                    $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                    $content = \str_replace($search, $replace, $template->text);
                    // iniciamos mail
                    $mailHandler = new Mail();
                    $mailHandler->to = $investor->email;
                    $mailHandler->toName = $investor->name;
                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    if ($mailHandler->send()) {

                    } else {
                        $anyfail = true;
                        @mail(\GOTEO_FAIL_MAIL,
                            'Fallo al enviar email automaticamente al amigo ' . SITE_URL,
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
}
