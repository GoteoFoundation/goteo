<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//system timezone

use Goteo\Core\Resource;
use Goteo\Core\Error;
use Goteo\Core\Redirection;
use Goteo\Core\Model;
use Goteo\Model\Mail;
use Goteo\Library\AmazonSns;
use Goteo\Application\Config;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);

require_once __DIR__ . '/../src/autoload.php';


// Config file...
$config = getenv('GOTEO_CONFIG_FILE');
if(!is_file($config)) $config = __DIR__ . '/config/settings.yml';
Config::load($config);

try {

    $contents = file_get_contents('php://input');
    file_put_contents(GOTEO_LOG_PATH . 'aws-sns-input.log', $contents);

    if (!$contents)
        throw new Exception('No se ha recibido información');

    $contentsJson = json_decode($contents);

    if (!$contentsJson)
        throw new Exception('La entrada no tiene un código JSON válido');

    if (!AmazonSns::verify($contentsJson, Config::get('mail.sns.client_id'), Config::get('mail.sns.region'), array(Config::get('mail.sns.bounces_topic'), Config::get('mail.sns.complaints_topic'))))
        throw new Exception('Petición incorrecta');

    if ($contentsJson->Type == 'SubscriptionConfirmation') {
        //suscribimos (esto solo debe pasar cuando se configura una nueva URL de notificacion)
        file_get_contents($contentsJson->SubscribeURL);
    }
    elseif ($contentsJson->Type == 'Notification') {
        $msg = json_decode($contentsJson->Message);
        //Si es un bounce, lo añadimos, pero solo bloqueamos si es permanente
        if($msg->notificationType == 'Bounce') {
            foreach($msg->bounce->bouncedRecipients as $ob) {
                $block = false;
                if($msg->bounce->bounceType == 'Permanent') $block = true;
                Mail::addBounce($ob->emailAddress, $ob->diagnosticCode, $block);
            }
        }
        //si es un complaint, añadimos y bloqueamos
        if($msg->notificationType == 'Complaint') {
            foreach($msg->complaint->complainedRecipients as $ob) {
                Mail::addComplaint($ob->emailAddress, $msg->complaint->complaintFeedbackType);
            }
        }
    }
}
catch (Exception $e) {
    file_put_contents(GOTEO_LOG_PATH . 'aws-sns-errors.log',$e->getMessage());
}
