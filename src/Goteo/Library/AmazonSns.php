<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library;

/**
 * Clase para la validacion de mensajes SNS de amazon
 */
class AmazonSns {

    public static function verify($inputJson, $account, $region, $topics) {
        // Probamos si la región es la que indicamos, si la cuenta a la que va dirigido es la nuestra
        // y si el topic es uno de los que hemos indicado nosotros
        $topicarn = explode(':', $inputJson->TopicArn);
        $_region = $topicarn[3];
        $_account = $topicarn[4];
        $_topic = $topicarn[5];

        if ( ($region != $_region) || ($account != $_account) || (!in_array($_topic, $topics)) ) {
          	// throw new Exception('error en comprovacion: ' . print_r($topics));
          	return false;
        }

        // Miramos que la URL del certificado pertenece a AmazonAWS
        if(!self::endswith(parse_url($inputJson->SigningCertURL, PHP_URL_HOST), '.amazonaws.com')) {
          	// throw new Exception('error en url: ' . print_r(parse_url($inputJson->SigningCertURL, PHP_URL_HOST)));
            return false;
        }

        // Descargamos el certificado y extraemos la clave pública
        $cert = file_get_contents($inputJson->SigningCertURL);
        $pubkey = openssl_get_publickey($cert);
        if(!$pubkey) {
          	// throw new Exception('error en pubkey: ' . print_r($pubkey));
            return false;
        }

        // Esto nos sirve para generar la cadena de verificación del certificado
        $validationGeneration = array('Notification' => array('Message', 'MessageId', 'Subject', 'Timestamp', 'TopicArn', 'Type'),
                        'SubscriptionConfirmation' => array('Message', 'MessageId', 'SubscribeURL', 'Timestamp', 'Token', 'TopicArn', 'Type'));

        $text='';
        $valid = (isset($validationGeneration[$inputJson->Type]))?$validationGeneration[$inputJson->Type]:false;

        if (!$valid) {
          	// throw new Exception('error en valid: ' . print_r($validationGeneration));
            return false;
        }

        foreach ($valid as $t) {
            if ( (isset($inputJson->{$t})) && ($inputJson->{$t}) ) {
                $text.=$t."\n".$inputJson->{$t}."\n";
            }
        }

        // Decodificamos la firma
        $signature = base64_decode($inputJson->Signature);

        // Miramos si el mensaje se corresponde
        if(openssl_verify($text, $signature, $pubkey, OPENSSL_ALGO_SHA1))
            return true;

        return false;
    }

    private static function endswith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, -$testlen) === 0;
    }
}
