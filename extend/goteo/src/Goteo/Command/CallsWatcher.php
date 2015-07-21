<?php

namespace Goteo\Command;

use Goteo\Model,
    Goteo\Library\Feed;

class CallsWatcher {

 /**
     * Control diario de convocatorias
     * @param bool $debug
     */
    public static function process ($debug = false) {

        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;

        if ($debug) echo "Calls watcher start\n";

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
                echo \vsprintf($log_text, array($call->name))."\n";
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

        if ($debug) echo "\nCalls finished (executed in ".$total_time." seconds)\n";
    }
}

