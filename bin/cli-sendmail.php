<?php
/**
* Este es el proceso que envia un email al usuario especificado
* version linea de comandos
**/

use Goteo\Core\Model,
    Goteo\Application\Lang,
    Goteo\Application\Message,
    Goteo\Application\Config,
    Goteo\Library\Feed,
    Goteo\Model\Mail,
    Goteo\Model\Sender;

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
// constantes necesarias (las pone el dispatcher)
define('HTTPS_ON', Config::get('ssl') ? true : false); // para las url de project/media
$url = Config::get('url.main');
define('SITE_URL', (Config::get('ssl') ? 'https://' : 'http://') . preg_replace('|^(https?:)?//|i','',$url));
define('SEC_URL', SITE_URL);
// set Lang
Lang::setDefault(Config::get('lang'));
Lang::set(Config::get('lang'));

$debug = true;

$id = $argv[1];
if(empty($id)) {
	die("Se necesita un identificador de sender como argumento del script!\n");
}

$list = array();

$sql = "SELECT
        mailer_send.id,
        mailer_send.user,
        mailer_send.name,
        mailer_send.email,
        mailer_content.id as mailing_id,
        mailer_content.mail as mail_id
    FROM mailer_send
    RIGHT JOIN mailer_content ON mailer_content.id=mailer_send.mailing AND mailer_content.active=1
    WHERE mailer_send.id = ?
    AND mailer_send.sent IS NULL
    AND mailer_send.blocked IS NULL
    ";

if ($query = Model::query($sql, array($id))) {
	$user = $query->fetchObject();
}
if(!is_object($user)) {
	die("No se ha encontrado un usuario válido para el mailer_send.id=$id\n");
}

//si estamos aqui sabemos que el usuari es valido i el mailing tambien
if($debug) echo "dbg: Fecha inicio " .date("Y-m-d H:i:s"). "\n";
// cogemos el siguiente envío a tratar

$mailing = Sender::get($user->mailing_id);

// print_r($mailing);
// si no está activa fin
if (!$mailing->active) {
    die("Mailing {$user->mailing_id} inactivo!\n");
}

// cogemos el contenido y la plantilla desde el historial
$query = Model::query('SELECT html, template, lang FROM mail WHERE id = ?', array($mailing->mail));
$data = $query->fetch(\PDO::FETCH_ASSOC);
$content = $data['html'];
$template = $data['template'];

Lang::setDefault(Config::get('lang'));
Lang::set($data['lang']);

if (empty($content)) {
    die("Mailing {$user->mailing_id} sin contenido!\n");
}

if($debug) echo "dbg: Bloqueando registro {$user->id} ({$user->email}) mailing: {$user->mailing_id}\n";

//bloquear usuario
Model::query("UPDATE mailer_send SET blocked = 1 WHERE id = '{$user->id}' AND mailing =  '{$user->mailing_id}'");

//enviar email
$itime = microtime(true);
try {
    $mailHandler = new Mail($debug);

    $mailHandler->id = $user->mail_id; //does not saves email to db
    $mailHandler->lang = $lang;

    // reply, si es especial
    if (!empty($mailing->reply)) {
        $mailHandler->reply = $mailing->reply;
        if (!empty($mailing->reply_name)) {
            $mailHandler->replyName = $mailing->reply_name;
        }
    }

    $mailHandler->to = \trim($user->email);
    $mailHandler->toName = $user->name;
    $mailHandler->subject = $mailing->subject;
    $mailHandler->content = str_replace(
        array('%USERID%', '%USEREMAIL%', '%USERNAME%', '%SITEURL%'),
        array($user->user, $user->email, $user->name, SITE_URL),
        $content);
    $mailHandler->html = true;
    $mailHandler->template = $template;
    $mailHandler->massive = true;

    $errors = array();

    if ($mailHandler->send($errors)) {

        // Envio correcto
        Model::query("UPDATE mailer_send SET sent = 1, datetime = NOW() WHERE id = '{$user->id}' AND mailing =  '{$user->mailing_id}'");
        if ($debug) echo "dbg: Enviado OK a {$user->email}\n";

    } else {

        // falló al enviar
        $sql = "UPDATE mailer_send
        SET sent = 0 , error = ? , datetime = NOW()
        WHERE id = '{$user->id}' AND mailing =  '{$user->mailing_id}'
        ";
        Model::query($sql, array(implode(',', $errors)));
        if ($debug) echo "dbg: Fallo ERROR a {$user->email} ".implode(',', $errors)."\n";
    }

    unset($mailHandler);

    // tiempo de ejecución
    $now = (microtime(true) - $itime);
    if ($debug) echo "dbg: Tiempo de envio: $now segundos\n";


} catch (\Exception $e) {
    Message::error($e->errorMessage());
}

if($debug) {
    foreach(Message::getAll() as $msg) {
        echo '['. ($msg->type === 'error' ? "\033[31m" : "\033[33m") . $msg->type . "\033[0m] " . $msg->content . "\n";
    }
}

//desbloquear usuario
if($debug) echo "dbg: Desbloqueando registro {$user->id} ({$user->email}) mailing: {$user->mailing_id}\n";
Model::query("UPDATE mailer_send SET blocked = NULL WHERE id = '{$user->id}' AND mailing =  '{$user->mailing_id}'");


