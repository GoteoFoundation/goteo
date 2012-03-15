<?php

use Goteo\Core\Resource,
    Goteo\Core\Error,
    Goteo\Core\Redirection,
    Goteo\Core\Model,
    Goteo\Library\Feed,
    Goteo\Library\Mail,
    Goteo\Library\Newsletter;

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

// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
        // @todo Insert error into buffer
//        echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);


/**
 * Sesión.
 */
session_name('goteo');
session_start();

// set Lang
define('LANG', 'es');

$mailing = Newsletter::getSending();
// si no está activa fin
if (!$mailing->active) {
    die('NADA');
}
if ($mailing->blocked) {
    die('BLOQUEADA');
}

// bloqueo la tabla
Model::query('UPDATE mailer_content SET blocked = 1 WHERE id = ?', array($mailing->id));


// ponemos el id del envio
$_SESSION['NEWSLETTER_SENDID'] = $mailing->mail;

// cogemos el contenido desde el historial
$query = Model::query('SELECT html FROM mail WHERE id = ?', array($mailing->mail));
$content = $query->fetchColumn();
if (empty($content)) {
    die('Sin contenido');
}

// cargamos los destinatarios
$users = array();
$sql = "SELECT
        id,
        user,
        name,
        email
    FROM mailer_send
    WHERE sended IS NULL
    ORDER BY id
    LIMIT 500
    ";

if ($query = Model::query($sql)) {
    foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $user) {
        $users[] = $user;
    }
}

// si no quedan pendientes, grabamos el feed y desactivamos
if (empty($users)) {
    // Desactivamos
    Model::query('UPDATE mailer_content SET active = 0 WHERE id = ?', array($mailing->id));

    // evento feed
    $log = new Feed();
    $log->populate('Envio newsletter (cron)', '/admin/mailing/newsletter', 'Se ha completado el envio del boletin');
    $log->doAdmin('system');
    unset($log);

    die('FIN');
}

foreach ($users as $user) {
    $mailHandler = new Mail();

    $mailHandler->to = \trim($user->email);
    $mailHandler->toName = $user->name;
    $mailHandler->subject = $mailing->subject;
    $mailHandler->content = '<br />'.$content.'<br />';
    $mailHandler->html = true;
    $mailHandler->template = 33; // porsupuesto estamos enviando la plantilla de boletin
    
    if ($mailHandler->send($errors)) {

        // Envio correcto
        Model::query("UPDATE mailer_send SET sended = 1, datetime = NOW() WHERE id = '{$user->id}'");
//        echo "Enviado OK a $user->email<br />";
    } else {

        // falló al enviar
        $sql = "UPDATE mailer_send
        SET sended = 0 , error = ? , datetime = NOW()
        WHERE		id = '{$user->id}'
        ";
        Model::query($sql, array(implode(',', $errors)));
//        echo "Fallo ERROR a $user->email ".implode(',', $errors)."<br />";
    }

    unset($mailHandler);
    
}

// desbloqueo la tabla
Model::query('UPDATE mailer_content SET blocked = 0 WHERE id = ?', array($mailing->id));

die('Listo');