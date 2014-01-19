<?php
/**
* Este es el proceso que va procesando envios masivos
**/


use Goteo\Core\Resource,
    Goteo\Core\Error,
    Goteo\Core\Redirection,
    Goteo\Core\Model,
    Goteo\Library\Feed,
    Goteo\Library\Mail,
    Goteo\Library\Sender;

require_once 'config.php';
require_once 'core/common.php';

// Autoloader
spl_autoload_register(

    function ($cls) {

        $file = __DIR__ . '/' . implode('/', explode('\\', strtolower(substr($cls, 6)))) . '.php';
        $file = realpath($file);

        if ($file === false) {

            // Try in library
            $file = __DIR__ . '/library/' . strtolower($cls) . '.php';
        }

        if ($file !== false) {
            include $file;
        }

    }

);

/**
 * Sesión.
 */
session_name('goteo');
session_start();

// set Lang
define('LANG', 'es');

$debug = true;
$fail = false;

// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
        global $debug, $txtdebug;
        // @todo Insert error into buffer
if ($debug) echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
die;
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);

// check the limit
if (!Mail::checkLimit()) {
    die;
}


define("MAIL_MAX_RATE", 500000); // microsegundos minimo entre envios individuales (1 seg = 100000 microseg)

$itime = microtime(true);

// cogemos el siguiente envío a tratar
$mailing = Sender::getSending();
// si no está activa fin
if (!$mailing->active) {
    // inactivo, nada de debug
    die;
}
if ($mailing->blocked) {
    if ($debug) echo 'dbg: BLOQUEADO!<br />';
    $fail = true;
}

// Solo si es boletin grabamos un solo sinoves
$_SESSION['NEWSLETTER_SENDID'] = $mailing->mail;

// cogemos el contenido y la plantilla desde el historial
$query = Model::query('SELECT html, template FROM mail WHERE id = ?', array($mailing->mail));
$data = $query->fetch(\PDO::FETCH_ASSOC);
$content = $data['html'];
$template = $data['template'];
if (empty($content)) {
    if ($debug) echo 'dbg: Sin contenido';
    $fail = true;
}

// voy a parar aquí, antes del bloqueo
if ($debug) echo 'dbg: <pre>'.print_r($mailing,1).'</pre>';

if (!$fail) {
    if ($debug) echo "dbg: bloqueo este registro<br />";
    Model::query('UPDATE mailer_content SET blocked = 1 WHERE id = ?', array($mailing->id));

    // cargamos los destinatarios
    $users = Sender::getRecipients($mailing->id);

    // si no quedan pendientes, grabamos el feed y desactivamos
    if (empty($users)) {

        if ($debug) echo "dbg: No hay destinatarios<br />";

        // Desactivamos
        Model::query('UPDATE mailer_content SET active = 0 WHERE id = ?', array($mailing->id));

        // evento feed
        $log = new Feed();
        $log->populate('Envio masivo (cron)', '/admin/mailing/newsletter', 'Se ha completado el envio masivo con asunto "'.$mailing->subject.'"');
        $log->doAdmin('system');
        unset($log);

        if ($debug) echo 'dbg: Se ha completado el envio masivo '.$mailing->id.'<br />';
    } else {

        // destinatarios
        if ($debug) echo 'dbg: Enviamos a '.count($users).' usuarios <br />';

        // si me paso con estos no sigo
        if (Mail::checkLimit(null, true) < count($users)) {
            if ($debug) echo 'dbg: Hoy no podemos enviarlos<br />';
            break;
        }

        foreach ($users as $user) {

            // tiempo de ejecución
            $ntime = microtime(true);
            if ($debug) echo "dbg: Llevo ".$ntime - $itime.". microsegundos de ejecución<br />";

            $mailHandler = new Mail();

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
                array('%USERID%', '%USEREMAIL%', '%USERNAME%'), 
                array($user->user, $user->email, $user->name), 
                $content);
            $mailHandler->html = true;
            $mailHandler->template = $template;
            $mailHandler->massive = true;
            
            $errors = array();
            if ($mailHandler->send($errors)) {

                // Envio correcto
                Model::query("UPDATE mailer_send SET sended = 1, datetime = NOW() WHERE id = '{$user->id}' AND mailing =  '{$mailing->id}'");
                if ($debug) echo "dbg: Enviado OK a $user->email<br />";

            } else {

                // falló al enviar
                $sql = "UPDATE mailer_send
                SET sended = 0 , error = ? , datetime = NOW()
                WHERE id = '{$user->id}' AND mailing =  '{$mailing->id}'
                ";
                Model::query($sql, array(implode(',', $errors)));
                if ($debug) echo "dbg: Fallo ERROR a $user->email ".implode(',', $errors)."<br />";
            }

            unset($mailHandler);
            
            // pausa de medio segundo
            usleep(MAIL_MAX_RATE);
        }

    }

    if ($debug) echo "dbg: desbloqueo este registro<br />";
    Model::query('UPDATE mailer_content SET blocked = 0 WHERE id = ?', array($mailing->id));

} else {
    if ($debug) echo 'dbg: FALLO';
}

if ($debug) echo 'dbg: FIN';

// limpiamos antiguos procesados
Sender::cleanOld();