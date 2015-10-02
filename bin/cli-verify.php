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
 *  Proceso que verifica si los preapprovals han sido coancelados
 *  Solamente trata transacciones paypal pendientes de proyectos en campaña
 *
 *  CRON SUGGESTED LINE:
 *  5 3 * * *       www-data        /usr/bin/php /..path.../bin/cli-verify.php --update  > /..path.../var/logs/last-cli-verify.log
 */

use Goteo\Application\Config;
use Goteo\Command\DbVerifier;

if (PHP_SAPI !== 'cli') {
    die('Console access only!');
}

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);


//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/public/');

require_once __DIR__ . '/../src/autoload.php';

// Config file...
Config::load();

// constantes necesarias (las pone el dispatcher)
define('HTTPS_ON', Config::get('ssl') ? true : false); // para las url de project/media
$url = Config::get('url.main');
define('SITE_URL', (Config::get('ssl') ? 'https://' : 'http://') . preg_replace('|^(https?:)?//|i','',$url));
define('SEC_URL', SITE_URL);

echo "This script verifies some paypal preaproval cancelations\n";

ob_start();

DbVerifier::process(true);

// recogemos el buffer para grabar el log
@mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
$log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_verify.log';
file_put_contents($log_file, ob_get_contents(), FILE_APPEND);
chmod($log_file, 0666);

