<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);
/*
{
  "Type" : "SubscriptionConfirmation",
  "MessageId" : "665c0e46-2f10-42f9-8386-cdfa0f37f2d4",
  "Token" : "2336412f37fb687f5d51e6e241d638b05feae9c86090d7ccb09fc282b09a7fbd2c75980e22f31ed27629527434ca15ff5186b6349ae55e513dc612390d47a666a23ff04229789bf5979325e44ece95825571bda22519b3409d543ee377179e400f209a59549fe56b30d2b6e7732a10ff64
bacf2a252d75516a81aa8615ddadd4",
  "TopicArn" : "arn:aws:sns:us-east-1:918923091822:Goteo-notifications",
  "Message" : "You have chosen to subscribe to the topic arn:aws:sns:us-east-1:918923091822:Goteo-notifications.\nTo confirm the subscription, visit the SubscribeURL included in this message.",
  "SubscribeURL" : "https://sns.us-east-1.amazonaws.com/?Action=ConfirmSubscription&TopicArn=arn:aws:sns:us-east-1:918923091822:Goteo-notifications&Token=2336412f37fb687f5d51e6e241d638b05feae9c86090d7ccb09fc282b09a7fbd2c75980e22f31ed276295
27434ca15ff5186b6349ae55e513dc612390d47a666a23ff04229789bf5979325e44ece95825571bda22519b3409d543ee377179e400f209a59549fe56b30d2b6e7732a10ff64bacf2a252d75516a81aa8615ddadd4",
  "Timestamp" : "2014-01-24T18:50:17.241Z",
  "SignatureVersion" : "1",
  "Signature" : "NqiUZzzRa5x+3V6aB0YuF9o5XmSvsUq5V30sYH+idFLHoak2ob6dxNMM0y6v3a/AolgGNg2M3Mp0pjlVMkFqPBMpkGcnaJ9Ua2GBVxm+yBeB9ArysOk2a7g9rcI1o024FYKeOgrr8vQJqXTFtbn69saAP9VVgNGBpeD7bRjoK61ZkTQ7Xwb2AVRgG34KyNIXE9/QCgxPpc/H3uhgk+IFE9qonBUKyh
fAw+ocTKmvFFRQKlqhv7v91Fbr1+zfUL9DBiZtq95+iIdIHzFoaqUR3+VFK66yRVy7mFTs6D33BiOJ6G4uVRGT+h8HC3PPpZIiWPGQaFTrcFO+Jwe9zsb7cQ==",
  "SigningCertURL" : "https://sns.us-east-1.amazonaws.com/SimpleNotificationService-e372f8ca30337fdb084e8ac449342c77.pem"
}

 */
require_once('amazonsns.php');

try {

  $contents = file_get_contents('php://input');
  file_put_contents("logs/aws-sns-0.log", $contents);

  if (!$contents)
    throw new Exception('No se ha recibido información');

  $contentsJson = json_decode($contents);
  if (!$contentsJson)
    throw new Exception('La entrada no tiene un código JSON válido');

  if (!AmazonSns::verify($contentsJson,'918923091822', 'us-east-1', array('amazon-ses-bounces', 'amazon-ses-complaints')))
    throw new Exception('Petición incorrecta');

  file_put_contents("logs/aws-sns-1.log", print_r($contentsJson,1));
  if ($contentsJson->Type == 'SubscriptionConfirmation')
    {
      /* Aquí podremos enviar un mensaje a un usuario para que manualmente lo acepte, tendremos 3 días para hacerlo */
      file_get_contents($contentsJson->SubscribeURL);
  	file_put_contents("logs/aws-sns-2.log", 'suscrito!');
    }
  elseif ($contentsJson->Type == 'Notification')
    {
      /* procesaMensaje(); */
  	file_put_contents("logs/aws-sns-2.log", 'notificacion!');
    }
}
catch (Exception $e)
{
  file_put_contents("logs/aws-sns-errors.log",$e->getMessage());
}
