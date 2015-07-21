<?php
/**
 * Proceso para enviar avisos a los autores segun
 *  - Que lleven 3 meses sin publicar actualizaciones, envia cada mes
 *  - Que lleven 3 meses sin decir nada (?), envia cada 15 dias
 *  - Que hayan pasado dos meses desde que se dio por financiado, cada 15 dias
 *
 *  tiene en cuenta que se envía cada tantos días
 *  CRON SUGGESTED LINE:
 *  5 3 * * *       www-data        /usr/bin/php /..path.../bin/cli-daily.php --update  > /..path.../var/logs/last-cli-daily.log
 */

use Goteo\Application\Config;
use Goteo\Command\ProjectsWatcher;

if (PHP_SAPI !== 'cli') {
die('Console access only!');
}

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);


//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

// Config file...
Config::loadFromYaml('settings.yml');

echo "This script send advises and other mails to donors and creators of projects\n";

ob_start();

// subcontrolador Auto-tips
ProjectsWatcher::process(true);

// recogemos el buffer para grabar el log
@mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
$log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_projectswatcher.log';
file_put_contents($log_file, ob_get_contents(), FILE_APPEND);
chmod($log_file, 0666);

