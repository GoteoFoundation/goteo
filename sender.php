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
if ($debug) $txtdebug = '<html><head></head><body>';

// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
//        global $debug, $txtdebug;
        // @todo Insert error into buffer
//echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);


// cogemos el siguiente envío a tratar
$mailing = Sender::getSending();
// si no está activa fin
if (!$mailing->active) {
    // inactivo, nada de debug
    die;
}
if ($mailing->blocked) {
    if ($debug) $txtdebug .= 'BLOQUEADO!<br />';
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
    if ($debug) $txtdebug .= 'Sin contenido';
    $fail = true;
}

// voy a parar aquí, antes del bloqueo
if ($debug) $txtdebug .= '<pre>'.print_r($mailing,1).'</pre>';

if (!$fail) {
    if ($debug) $txtdebug .= "bloqueo este registro<br />";
    Model::query('UPDATE mailer_content SET blocked = 1 WHERE id = ?', array($mailing->id));

    // cargamos los destinatarios
    $users = Sender::getRecipients($mailing->id);

    // destinatarios
    if ($debug) $txtdebug .= '<pre>'.print_r($users,1).'</pre>';

    // si no quedan pendientes, grabamos el feed y desactivamos
    if (empty($users)) {

        if ($debug) $txtdebug .= "No hay destinatarios<br />";

        // Desactivamos
        Model::query('UPDATE mailer_content SET active = 0 WHERE id = ?', array($mailing->id));

        // evento feed
        $log = new Feed();
        $log->populate('Envio masivo (cron)', '/admin/mailing/newsletter', 'Se ha completado el envio masivo con asunto "'.$mailing->subject.'"');
        $log->doAdmin('system');
        unset($log);

        if ($debug) $txtdebug .= 'Se ha completado el envio masivo '.$mailing->id.'<br />';
    } else {

        foreach ($users as $user) {

            //@TODO::: Aquí hay que tener en cuenta si hay que reemplazar variables, si tenemos los datos



            $mailHandler = new Mail();

            $mailHandler->to = \trim($user->email);
            $mailHandler->toName = $user->name;
            $mailHandler->subject = $mailing->subject;
            $mailHandler->content = '<br />'.$content.'<br />';
            $mailHandler->html = true;
            $mailHandler->template = $template;
            $mailHandler->massive = true;
            
            $errors = array();
            if ($mailHandler->send($errors)) {

                // Envio correcto
                Model::query("UPDATE mailer_send SET sended = 1, datetime = NOW() WHERE id = '{$user->id}' AND mailing =  '{$mailing->id}'");
                if ($debug) $txtdebug .= "Enviado OK a $user->email<br />";

            } else {

                // falló al enviar
                $sql = "UPDATE mailer_send
                SET sended = 0 , error = ? , datetime = NOW()
                WHERE id = '{$user->id}' AND mailing =  '{$mailing->id}'
                ";
                Model::query($sql, array(implode(',', $errors)));
                if ($debug) $txtdebug .= "Fallo ERROR a $user->email ".implode(',', $errors)."<br />";
            }

            unset($mailHandler);
            
        }
    }

    if ($debug) $txtdebug .= "desbloqueo este registro<br />";
    Model::query('UPDATE mailer_content SET blocked = 0 WHERE id = ?', array($mailing->id));

    if ($debug) $txtdebug .= 'Listo';
} else {
    if ($debug) $txtdebug .= 'FALLO';
}

if ($debug) $txtdebug .= '</body></html>';

// mail debug
// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
if ($debug) mail('root-goteo@doukeshi.org', 'Debug enviador de mailing masivo', $txtdebug, $cabeceras);
if ($debug) echo $txtdebug;

// limpiamos antiguos procesados
Sender::cleanOld();