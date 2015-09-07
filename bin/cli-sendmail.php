<?php
/**
* Este es el proceso que envia un email al usuario especificado
* version linea de comandos
**/

use Goteo\Core\Model;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Library\Feed;
use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Mail\SenderRecipient;

if (PHP_SAPI !== 'cli') {
    die("Console access only!\n");
}
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors", 1);

//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

// Config file...
Config::loadFromYaml('settings.yml');

// constantes necesarias (las pone el dispatcher)
define('HTTPS_ON', Config::get('ssl') ? true : false); // para las url de project/media
$url = Config::get('url.main');
define('SITE_URL', (Config::get('ssl') ? 'https://' : 'http://') . preg_replace('|^(https?:)?//|i', '', $url));
define('SEC_URL', SITE_URL);

// set Lang
Lang::setDefault(Config::get('lang'));
Lang::set(Config::get('lang'));

$debug = true;

$id = $argv[1];
if(empty($id)) {
    die("Please specify the mailer_send table ID to send\n");
}

$recipient = SenderRecipient::get($id);

$itime = microtime(true);
if($debug) { echo "dbg: Start date " .date("Y-m-d H:i:s"). "\n";
}

if($debug) { echo "dbg: Locking recipient ID [{$recipient->id}] for email [{$recipient->email}] and mailing ID [{$recipient->mailing}]\n";
}
if(!$recipient->setLock(true)) {
    die("Error locking recipient ID [{$id}]\n");
}
$errors = [];
if(!$recipient->send($errors) ) {
    echo "ERROR SENDING: " . implode("\n", $errors) ."\n";
}

if($recipient->setLock(false) ) {
    die("Error unlocking recipient ID [{$id}]\n");
}
if($debug) { echo "dbg: Unlocking recipient ID [{$recipient->id}] for email [{$recipient->email}] and mailing ID [{$recipient->mailing}]\n";
}


// tiempo de ejecución
$now = (microtime(true) - $itime);
if ($debug) { echo "dbg: Total sent time: $now seconds\n";
}


if($debug) {
    foreach(Message::getAll() as $msg) {
        echo '['. ($msg->type === 'error' ? "\033[31m" : "\033[33m") . $msg->type . "\033[0m] " . $msg->content . "\n";
    }
}


