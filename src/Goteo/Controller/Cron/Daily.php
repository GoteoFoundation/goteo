<?php

namespace Goteo\Controller\Cron {

    use Goteo\Model,
        Goteo\Controller\Cron\Send,
        Goteo\Library\Text,
        Goteo\Library\Feed;

    class Daily {

        /*
         * Control diario de proyectos
         *  Para envio de tips y avisos
         * @param bool $debug
         */
        public static function Projects ($debug = false) {

            $time = microtime();
            $time = explode(' ', $time);
            $time = $time[1] + $time[0];
            $start = $time;

            if ($debug) echo '<strong>cron/daily: Projects() start</strong><br />';

            // Publicación automática de campañas:
            // Busca proyectos en estado revisión (2) que tengan fecha de publicación ese día.
            // A esos les cambia el estado a publicado.
            $projects = Model\Project::getPublishToday();
            if ($debug) {
                echo 'Publicación de proyectos automática: ';
                if (count($projects) > 0) {
                    echo 'se van a publicar ' . count($projects) . ' proyectos';
                } else {
                    echo 'no hay ningún proyecto para publicar hoy';
                }
                echo '.<br/><br/>';
            }
            foreach ($projects as $project) {
                $res = $project->publish();

                if ($res) {
                    $log_text = 'Se ha pasado automáticamente el proyecto %s al estado <span class="red">en Campaña</span>';
                } else {
                    $log_text = 'El sistema ha fallado al pasar el proyecto %s al estado <span class="red">en Campaña</span>';
                }
                $log_text = \vsprintf($log_text, array(Feed::item('project', $project->name, $project->id)));
                if ($debug) echo $log_text;

                // Evento Feed
                $log = new Feed();
                $log->setTarget($project->id);
                $log->populate('Publicación automática de un proyecto', '/admin/projects', $log_text);
                $log->doAdmin('admin');

                $log->populate($project->name, '/project/'.$project->id, Text::html('feed-new_project'), $project->image);
                $log->doPublic('projects');
                unset($log);
            }

            // proyectos a notificar
            $projects = Model\Project::review();

            foreach ($projects as $project) {
                // por ahora solo tratamos los de primera ronda y hasta 2 meses tras la financiación
                // FIXME: la segunda condicion del if (depende de days_total)
                if ($project->days > $project->days_round1 + 2 || $project->days > 360) {
                    // if ($debug) echo "Proyecto <strong>{$project->name}</strong> SKIP<br/>"; // no necesitamos este feedback
                    continue;
                }

                if ($debug) echo "Proyecto <strong>{$project->name}</strong>, Impulsor: {$project->user->name}, email: {$project->user->email}, estado {$project->status}, lleva {$project->days} dias, conseguido {$project->amount}<br />";
                
                // primero los que no se bloquean
                //Solicitud de datos del contrato
                if ($project->days == $project->days_round1 + 1) {
                    // si ha superado el mínimo
                    if ($project->amount >= $project->mincost) {
                        if ($debug) echo "Solicitud de datos contrato<br />";
                        Send::toOwner('1d_after', $project);
                    } else {
                        if ($debug) echo "Solicitud de datos, no se envía porque no está financiado<br />";
                    }
                }

                // Recuerdo al autor proyecto, 2 meses despues de campaña finalizada
                // FIXME  (depende de days_total)
                if ($project->days == 140) {
                        // si quedan recompensas/retornos pendientes por cumplir
                        if (!Model\Project\Reward::areFulfilled($project->id) || !Model\Project\Reward::areFulfilled($project->id, 'social') ) {
                            if ($debug) echo "Recompensas/Retornos pendientes<br />";
                            Send::toOwner('2m_after', $project);
                        } else {
                            if ($debug) echo "Recompensas/Retornos cumplidas, no se envía<br />";
                        }
                }
                
                // Recuerdo al autor proyecto, 8 meses despues de campaña finalizada
                // FIXME  (depende de days_total)
                if ($project->days == 320) {
                        // si quedan retornos pendientes por cumplir
                        if (!Model\Project\Reward::areFulfilled($project->id, 'social') ) {
                            if ($debug) echo "Retornos pendientes<br />";
                            Send::toOwner('8m_after', $project);
                        } else {
                            if ($debug) echo "Retornos cumplidos, no se envía<br />";
                        }
                }
                
                // ahora checkeamos bloqueo de consejos
                $prefs = Model\User::getPreferences($project->owner);
                if ($prefs->tips) {
                    if ($debug) echo "Bloqueado por preferencias<hr />";
                    continue;
                }

                // E idioma de preferencia del impulsor
                $comlang = !empty($prefs->comlang) ? $prefs->comlang : $project->user->lang;

                // flag de aviso
                $avisado = false;

                // TODO : se comentó que para proyectos con campañas cortas, los consejos se envien proporcionalmente
                // Consejos/avisos puntuales
                switch ($project->days) {
                    
                    // NO condicionales
                    case 0: // Proyecto publicado
                        $template = 'tip_0';
                        if ($debug) echo "Envío {$template}<br />";
                        Send::toOwner($template, $project);
                        Send::toConsultants($template, $project);
                        break;
                    case 1: // Difunde, difunde, difunde
                    case 2: // Comienza por lo más próximo
                    case 3: // Una acción a diario, por pequeña que sea
                    case 4: // Llama a todas las puertas
                    case 5: // Busca dónde está tu comunidad
                    case 8: // Agradece en público e individualmente
                        $template = 'tip_'.$project->days;
                        if ($debug) echo "Envío {$template}<br />";
                        Send::toOwner($template, $project);
                        break;
                    
                    // periodico condicional
                    case 6: // Publica novedades! 
                    // y  se repite cada 6 días (fechas libres) mientras no haya posts
                    case 12: 
                    case 18: 
                    case 24: 
                    case 30: 
                    case 36: 
                        // si ya hay novedades, nada
                        if (Model\Blog::hasUpdates($project->id)) {
                            if ($debug) echo "Ya ha publicado novedades<br />";
                        } else {
                            if ($debug) echo "Envío aviso de que no ha publicado novedades<br />";
                            Send::toOwner('any_update', $project);
                            $avisado = true;
                        }
                        break;
                    
                    // comprobación periódica pero solo un envío
                    case 7: // Apóyate en quienes te van apoyando, si más de 20 cofinanciadores
                        // o en cuanto llegue a 20 backers (en fechas libres)
                    case 14: 
                    case 17: 
                    case 21: 
                    case 24: 
                    case 27: 
                        // Si ya se mandó esta plantilla (al llegar a los 20 por primera vez) no se envía de nuevo
                        $sql = "
                            SELECT
                                id
                            FROM mail
                            WHERE mail.email = :email
                            AND mail.template = 46
                            ORDER BY mail.date DESC
                            LIMIT 1";
                        $query = Model\Project::query($sql, array(':email' => $project->user->email));
                        $sended = $query->fetchColumn(0);
                        if (!$sended) {
                            if ($project->num_investors >= 20) {
                                if ($debug) echo "Tiene 20 backers y no se le habia enviado aviso antes<br />";
                                Send::toOwner('20_backers', $project);
                            } else {
                                if ($debug) echo "No llega a los 20 backers<br />";
                            }
                        } else {
                            if ($debug) echo "Ya enviado<br />";
                        }
                        break;
                    
                    case 9: // Busca prescriptores e implícalos
                        // si no tiene padrinos
                        if ($project->patrons > 0) {
                            if ($debug) echo "Tiene padrino<br />";
                        } else {
                            if ($debug) echo "No tiene padrino<br />";
                            Send::toOwner('tip_9', $project);
                        }
                        break;
                    
                    case 10: // Luce tus recompensas y retornos
                        // que no se envie a los que solo tienen recompensas de agradecimiento
                        $thanksonly = true;
                        // recompensas
                        $rewards = Model\Project\Reward::getAll($project->id, 'individual', $comlang);
                        foreach ($rewards as $rew) {
                            if ($rew->icon != 'thanks') {
                                $thanksonly = false;
                                break; // ya salimos del bucle, no necesitamos más
                            }
                        }
                        if ($thanksonly) {
                            if ($debug) echo "Solo tiene recompensas de agradecimiento<br />";
                        } else {
                            if ($debug) echo "Tienen recompensas<br />";
                            uasort($rewards,
                                function ($a, $b) {
                                    if ($a->amount == $b->amount) return 0;
                                    return ($a->amount > $b->amount) ? 1 : -1;
                                    }
                                );
                            // sacar la primera y la última
                            $lower = reset($rewards); $project->lower = $lower->reward;
                            $higher = end($rewards); $project->higher = $higher->reward;

                            Send::toOwner('tip_10', $project);
                        }
                        break;
                        
                        
                    case 11: // Refresca tu mensaje de motivacion
                        // si no tiene video motivacional
                        if (empty($project->video)) {
                            if ($debug) echo "No tiene video motivacional<br />";
                            Send::toOwner('tip_11', $project);
                        } else {
                            if ($debug) echo "Tiene video motivacional<br />";
                        }
                        break;
                    
                    case 15: // Sigue los avances y calcula lo que falta
                        // si no ha llegado al mínimo
                        if ($project->amount < $project->mincost) {
                            if ($debug) echo "No ha llegado al mínimo<br />";
                            Send::toOwner('tip_15', $project);
                        } else {
                            if ($debug) echo "Ha llegado al mínimo<br />";
                        }
                        break;
                    
                    case 25: // No bajes la guardia!
                        // si no ha llegado al mínimo
                        if ($project->amount < $project->mincost) {
                            if ($debug) echo "No ha llegado al mínimo<br />";
                            Send::toOwner('two_weeks', $project);
                        } else {
                            if ($debug) echo "Ha llegado al mínimo<br />";
                        }
                        break;
                    
                    case 32: // Al proyecto le faltan 8 días para archivarse
                        // si no ha llegado al mínimo
                        if ($project->amount < $project->mincost) {
                            if ($debug) echo "No ha llegado al mínimo<br />";
                            Send::toOwner('8_days', $project);
                        } else {
                            if ($debug) echo "Ha llegado al mínimo<br />";
                        }
                        break;
                    
                    case 38: // Al proyecto le faltan 2 días para archivarse 
                        // si no ha llegado al mínimo pero está por encima del 70%
                        if ($project->amount < $project->mincost && $project->percent >= 70) {
                            if ($debug) echo "No ha llegado al mínimo<br />";
                            Send::toOwner('2_days', $project);
                        } else {
                            if ($debug) echo "Ha llegado al mínimo o lleva menos de 70%<br />";
                        }
                        break;
                }
                
                // Avisos periodicos 
                // si lleva más de 15 días: si no se han publicado novedades en la última semana 
                // Ojo! que si no ha enviado ninguna no lanza este sino la de cada 6 días
                if (!$avisado && $project->days > 15) {
                    if ($debug) echo "ya lleva una quincena de campaña, verificamos novedades<br />";
                    
                    // veamos si ya le avisamos hace una semana
                    // Si ya se mandó esta plantilla (al llegar a los 20 por primera vez) no se envía de nuevo
                    $sql = "
                        SELECT
                            id,
                            DATE_FORMAT(
                                from_unixtime(unix_timestamp(now()) - unix_timestamp(date))
                                , '%j'
                            ) as days
                        FROM mail
                        WHERE mail.email = :email
                        AND mail.template = 23
                        ORDER BY mail.date DESC
                        LIMIT 1";
                    $query = Model\Project::query($sql, array(':email' => $project->user->email));
                    $lastsend = $query->fetchObject();
                    if (!$lastsend->id || $lastsend->days > 7) {
                        // veamos cuanto hace de la última novedad
                        $sql = "
                            SELECT
                                DATE_FORMAT(
                                    from_unixtime(unix_timestamp(now()) - unix_timestamp(date))
                                    , '%j'
                                ) as days
                            FROM post
                            INNER JOIN blog
                                ON  post.blog = blog.id
                                AND blog.type = 'project'
                                AND blog.owner = :project
                            WHERE post.publish = 1
                            ORDER BY post.date DESC
                            LIMIT 1";
                        $query = Model\Project::query($sql, array(':project' => $project->id));
                        $lastUpdate = $query->fetchColumn(0);
                        if ($lastUpdate > 7) {
                            if ($debug) echo "Ultima novedad es de hace más de una semana<br />";
                            Send::toOwner('no_updates', $project);
                        } elseif (is_numeric($lastUpdate)) {
                            if ($debug) echo "Publicó novedad hace menos de una semana<br />";
                        } else {
                            if ($debug) echo "No se ha publicado nada, recibirá el de cada 6 días<br />";
                        }
                    } else {
                        if ($debug) echo "Se le avisó por novedades hace menos de una semana<br />";
                    }
                    
                    
                }
                
                if ($debug) echo "<br />";
                
            }
            
            // Obtiene los proyectos que llevan 10 meses con status=4 (proyecto financiado) y
            // envía un correo a los asesores del proyecto en caso de que no consten aún los retornos colectivos
            $projects = Model\Project::getFunded(10);

            $filtered_projects = array_filter($projects, 
                function($project) {
                    $rewards_fulfilled = Model\Project\Reward::areFulfilled($project->id, 'social');
                    $project_fulfilled = $project->status == 5;
                    return !($rewards_fulfilled || $project_fulfilled);
                }
            );

            if ($debug) {
                echo "<hr/>";
                echo "Buscando proyectos financiados hace 10 meses ({$success_date}). Encontrados: " . count($projects) . "  <br />";
                echo "De ellos, no han cumplido con los retornos colectivos: " . count($filtered_projects) . "  <br /><br />";
            }

            foreach ($filtered_projects as $project) {
                if ($debug) {
                    echo "Proyecto {$project->name}, Impulsor: {$project->user->name}, email: {$project->user->email} lleva 10 meses financiado y no constan retornos colectivos.<br />";
                }
                Send::toConsultants('commons', $project);
            }

            $time = microtime();
            $time = explode(' ', $time);
            $time = $time[1] + $time[0];
            $finish = $time;
            $total_time = round(($finish - $start), 4);

            if ($debug) echo "<br /><strong>cron/daily: Projects() finish (executed in ".$total_time." seconds)</strong><hr />";

            return;
        }

        
        /**
         * Control diario de convocatorias
         * @param bool $debug
         */
        public static function Calls ($debug = false) {
            
            $time = microtime();
            $time = explode(' ', $time);
            $time = $time[1] + $time[0];
            $start = $time;

            if ($debug) echo '<strong>cron/daily: Calls() start</strong><br />';

            // convocatorias con aplicación abierta
            $calls = Model\Call::getActive(3);
            foreach ($calls as $call) {
                // a ver cuantos días le quedan para que acabe la convocatoria
                $open = strtotime($call->opened);
                $until = mktime(0, 0, 0, date('m', $open), date('d', $open)+$call->days, date('Y', $open));
                $now = strtotime(date('Y-m-d'));
                $diference = $until - $now;
                $days = \round($diference/24/60/60);

                $doFeed = false;
                switch ($days) {
                    case 7:
                        $log_text = 'Falta una semana para que acabe la convocatoria %s';
                        $log_text_public = 'Falta una semana para que se cierre la aplicación de proyectos';
                        $doFeed = true;
                        break;
                    case 3:
                        $log_text = 'Faltan 3 dias para que acabe la convocatoria %s';
                        $log_text_public = 'Faltan 3 dias para que se cierre la aplicación de proyectos';
                        $doFeed = true;
                        break;
                    case 1:
                        $log_text = 'Ultimo día para la convocatoria %s';
                        $log_text_public = 'Hoy es el último día para aplicar proyectos!';
                        $doFeed = true;
                        break;
                }

                // feed
                if ($doFeed) {
                    $log = new Feed();
                    $log->setTarget($call->id, 'call');
                    $log->unique = true;
                    $log->populate('Convocatoria terminando (cron)', '/admin/calls/'.$call->id.'?days='.$days,
                        \vsprintf($log_text, array(
                            Feed::item('call', $call->name, $call->id))
                        ));
                    $log->doAdmin('call');
                    $log->populate('Convocatoria: ' . $call->name, '/call/'.$call->id.'?days='.$days, $log_text_public, $call->logo);
                    $log->doPublic('projects');
                    unset($log);
                    echo \vsprintf($log_text, array($call->name)).'<br />';
                }
            }

            // campañas dando dinero
            $campaigns = Model\Call::getActive(4);
            foreach ($campaigns as $campaign) {
                $errors = array();

                // tiene que tener presupuesto
                if (empty($campaign->amount)) {
                    continue;
                }

                // a ver cuanto le queda de capital riego
                $rest = $campaign->rest;
                if (empty($rest)) {
                    continue;
                }

                $doFeed = false;
                if ($rest < 100) {
                    $amount = 100;
                    $doFeed = true;
                } elseif ($rest < 500) {
                    $amount = 500;
                    $doFeed = true;
                } elseif ($rest < 1000) {
                    $amount = 1000;
                    $doFeed = true;
                }
                // feed
                if ($doFeed) {
                    $log = new Feed();
                    $log->setTarget($campaign->id, 'call');
                    $log->unique = true;
                    $log->populate('Campaña terminando (cron)', '/admin/calls/'.$campaign->id.'?rest='.$amount,
                        \vsprintf('Quedan menos de %s en la campaña %s', array(
                            Feed::item('money', $amount.' &euro;')
                                . ' de '
                                . Feed::item('drop', 'Capital Riego', '/service/resources'),
                            Feed::item('call', $campaign->name, $campaign->id))
                        ));
                    $log->doAdmin('call');
                    $log->populate($campaign->name, '/call/'.$campaign->id.'?rest='.$amount,
                        \vsprintf('Quedan menos de %s en la campaña %s', array(
                            Feed::item('money', $amount.' &euro;') 
                                . ' de '
                                . Feed::item('drop', 'Capital Riego', '/service/resources'),
                            Feed::item('call', $campaign->name, $campaign->id))
                        ), $call->logo);
                    $log->doPublic('projects');
                    unset($log);
                }
            }
            
            $time = microtime();
            $time = explode(' ', $time);
            $time = $time[1] + $time[0];
            $finish = $time;
            $total_time = round(($finish - $start), 4);

            if ($debug) echo "<br /><strong>cron/daily: Calls() finish (executed in ".$total_time." seconds)</strong><hr />";

            return;
        }
        
    }

}
