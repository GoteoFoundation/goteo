<?php

// This script is used to extract emails from the old database schema to html files.
// Remember to add first the new field to the mail table:
//   ALTER TABLE `mail` ADD `content` VARCHAR(50) NULL DEFAULT NULL COMMENT 'ID del archivo con HTML estático'
// Copy this script to the root of the repository and run it from there otherwise you will face some errors about missing namespaces
// It you run this script as is, it will create new directories inside data: data/mail/{news,sys}
// In the CONFIG section you can set $use_data_dir to false to use a temp dir instead of data/
// You can also upload these files to Amazon S3 setting $upload_to_s3 to true. Note that you need to set the FILE_HANDLER constant in config.php
//
// Once the migration is done you can remove the content of the html field:
//   UPDATE mail SET html = ''
// It may take a while depending on how many emails were sent and stored. Then you can safely remove this field (it won't be used anymore):
//   ALTER TABLE `mail` DROP `html`

use \Goteo\Core\Model;
use Goteo\Library\FileHandler\File;
use Goteo\Core\View;

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

require_once 'config.php';

///// CONFIG
$pag = '0';
$pag_inc = '50';
$upload_to_s3 = false;
$use_data_dir = true;
//// CONFIG

if ($use_data_dir) {
    $tmp = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mail';
} else {
    $tmp = uniqid('/tmp/htmls3');
}

if ($upload_to_s3 && FILE_HANDLER != 's3') {
    die("Para migrar a S3 es necesario define('FILE_HANDLER', 's3'); en config.php\n");
}

$old_umask = umask(0);
@mkdir($tmp, 0777, true);
@mkdir($tmp . '/sys');
@mkdir($tmp . '/news');
umask($old_umask);

echo "temp: $tmp\n";

$mails = Model::query("SELECT * FROM mail WHERE content IS NULL LIMIT {$pag},{$pag_inc}");

if (!$mails)  {
    die("Error retrieving emails");
}

$pag += $pag_inc;
$mails = $mails->fetchAll(\PDO::FETCH_OBJ);

while(sizeof($mails) != 0) {

    foreach($mails as $mail) {
        //echo "---------------------\n";

        $prefix = ($mail->email == "any") ? "/news/" : "/sys/";

        $filename = md5("{$mail->id}_{$mail->email}_{$mail->template}_" . GOTEO_MISC_SECRET) . ".html";
        $filepath = $tmp . $prefix . $filename;

        if ($mail->lang == null) {
            define('LANG', 'es');
            //echo "null: es\n";
        } else {
            define('LANG', $mail->lang);
            //echo "lang: {$mail->lang}\n";
        }

        if ($mail->email == "any") {
            $content = View::get('email/newsletter.html.php', array('content'=>$mail->html, 'baja' => ''));
        } else {
            $baja = SEC_URL . '/user/leave/?email=' . $mail->email;

            if ($mail->node != null && $mail->node != \GOTEO_NODE && \file_exists('nodesys/'. $mail->node .'/view/email/default.html.php')) {
                $content = View::get('nodesys/'. $mail->node .'/view/email/default.html.php', array('content'=>$mail->html, 'baja' => $baja));
            } else {
                $content = View::get('email/goteo.html.php', array('content'=>$mail->html, 'baja' => $baja));
            }

        }

        if ($mail->node == null || $mail->node == \GOTEO_NODE) {
            $logo_url = "https://goteoassets.s3-eu-west-1.amazonaws.com/goteo_logo.png";
        } else {
            $logo_url = "https://goteoassets.s3-eu-west-1.amazonaws.com/nodesys/" . $mail->node . "/view/css/logo.png";
        }

        //echo "logo: {$logo_url}\n";

        $content = str_replace("cid:logo", $logo_url, $content);

        $fp = fopen($filepath, 'w');
        if ($fp) {
            fwrite($fp, $content);
            fclose($fp);
        } else {
            echo "error grabando archivo {$filepath}\n";
            die;
        }

        echo "WRITTEN {$filepath} ({$mail->id} {$mail->email} {$mail->template}) ";
        //echo "Sube a " . $prefix . $filename . "\n";

        // Update content field
        $sql = "UPDATE mail SET content = :content WHERE id = :id";
        $values = array (
            ':content' => $prefix . $filename,
            ':id' => $mail->id
        );
        $query = Model::query($sql, $values);

        if (!$query) {
            die("Error updating content field");
        }

        // Amazon S3
        if ($upload_to_s3) {

            $fpremote = File::factory(array('bucket' => AWS_S3_BUCKET_MAIL));
            if ($fpremote->upload($filepath, $prefix . $filename, 'public-read')) {
                echo "UPLOADED";
            } else {
                echo "ERROR";
            }
        }

        echo "\n";
    }

    $mails = Model::query("SELECT * FROM mail WHERE content IS NULL LIMIT {$pag},{$pag_inc}");
    if (!$mails)  {
        die("Error retrieving emails");
    }

    $pag += $pag_inc;
    $mails = $mails->fetchAll(\PDO::FETCH_OBJ);
}

echo "END\n";

?>
