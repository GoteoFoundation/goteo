<?php

namespace Goteo\Controller\Cron {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Library\Text,
        Goteo\Library\Feed,
        Goteo\Library\Template,
        Goteo\Library\Mail,
        Goteo\Core\Error;

    class Send {

        /**
         * Al autor del proyecto, se encarga de substituir variables en plantilla
         *
         * @param $type string
         * @param $project Object
         * @return bool
         */
        public static function toOwner ($type, $project) {
            $tpl = null;
            
            /// tipo de envio
            switch ($type) {
                // Estos son avisos de final de ronda
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
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->invested);
                    break;
                
                case 'tip_4': // template 44, "Llama a todas las puertas"
                    $tpl = 44;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%BACKERSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, SITE_URL.'/project/'.$project->id.'/supporters');
                    break;
                
                case 'tip_5': // template 45, "Busca dónde está tu comunidad"
                    $tpl = 45;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%NUMBACKERS%');
                    $replace = array($project->user->name, $project->name, SITE_URL.'/project/'.$project->id, $project->invested, $project->num_investors);
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
                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get($tpl);
                // Sustituimos los datos
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title); 
                $content = \str_replace($search, $replace, $template->text);
                // iniciamos mail
                $mailHandler = new Mail();
                $mailHandler->to = $project->user->email;
                $mailHandler->toName = $project->user->name;
                
                // si es un proyecto de nodo: reply al mail del nodo
                // si es de centra: reply a MAIL_GOTEO
                $mailHandler->reply = (!empty($project->nodeData->email)) ? $project->nodeData->email : \GOTEO_CONTACT_MAIL;
                
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send($errors)) {
                    return true;
                } else {
                    echo \trace($errors);
                    @mail('goteo_fail@doukeshi.org',
                        'Fallo al enviar email automaticamente al autor ' . SITE_URL,
                        'Fallo al enviar email automaticamente al autor: <pre>' . print_r($mailHandler, 1). '</pre>');
                }
            }

            return false;
        }


        /**
         * Al asesor del proyecto, se encarga de sustituir variables en plantilla
         *
         * @param $type string Identificador de la plantilla
         * @param $project Object Proyecto
         * @return bool
         */
        public static function toConsultants ($type, $project) {
            $tpl = null;
            
            if (!isset($project->consultants)) {
                $project->consultants = Model\Project::getConsultants($project->id);
            }

            // Si por cualquier motivo, el proyecto no tiene asignado ningún asesor, enviar a olivier
            if (empty($project->consultants)) { 
                $project->consultants = array('olivier' => 'Olivier');
            }

            /// tipo de envio
            switch ($type) {
                case 'commons':
                    $tpl = 56;
                    $search  = array('%PROJECTNAME%', '%URL%');
                    $replace = array($project->name, SITE_URL . '/admin/commons?project=' . $project->id);
                    break;
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
                    
                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    if ($mailHandler->send($errors)) {
                        return true;
                    } else {
                        echo \trace($errors);
                        @mail('goteo_fail@doukeshi.org',
                            'Fallo al enviar email automaticamente al asesor ' . SITE_URL,
                            'Fallo al enviar email automaticamente al asesor: <pre>' . print_r($mailHandler, true). '</pre>');
                    }
                }

            }

            return false;
        }

        /* A los cofinanciadores 
         * Se usa tambien para notificar cuando un proyecto publica una novedad.
         * Por eso añadimos el tercer parámetro, para recibir los datos del post
         */
        static public function toInvestors ($type, $project, $post = null) {

            $debug = false;

            // notificación
            $notif = $type == 'update' ? 'updates' : 'rounds';

            $anyfail = false;
            $tpl = null;

            // para usar el proceso Sender

            // - Separamos los replaces de contenido de los replaces individuales (%USERNAME%)
            switch ($type) {
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
            $sql = "
                SELECT
                    invest.user as id,
                    user.name as name,
                    user.email as email,
                    IFNULL(user.lang, 'es') as lang
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
                die;
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

            // Luego, un mailing para cada idioma


            // Obtenemos la plantilla para asunto y contenido
            $template = Template::get($tpl);
            

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




            // - se crea un registro de tabla mail
            $sql = "INSERT INTO mail (id, email, html, template, node) VALUES ('', :email, :html, :template, :node)";
            $values = array (
                ':email' => 'any',
                ':html' => $content,
                ':template' => $tpl,
                ':node' => \GOTEO_NODE
            );
            $query = \Goteo\Core\Model::query($sql, $values);
            $mailId = \Goteo\Core\Model::insertId();


            // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
            // - initiateSending ($mailId, $subject, $receivers, $autoactive = 0)
            if (\Goteo\Library\Sender::initiateSending($mailId, $subject, $receivers, 1)) 
                return false;
            else
                return true;

        }
        
        /* A los destinatarios de recompensa (regalo)
         * solo tipo 'fail' por ahora
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
                        @mail('goteo_fail@doukeshi.org',
                            'Fallo al enviar email automaticamente al amigo ' . SITE_URL,
                            'Fallo al enviar email automaticamente al amigo: <pre>' . print_r($mailHandler, 1). '</pre>');
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