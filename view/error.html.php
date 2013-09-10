<?php 

use Goteo\Core\Error;

if (!isset($error) || !($error instanceof Error)) {
    $error = new Error;
}

$code = $error->getCode();
$message = $error->getMessage();

header("HTTP/1.0 {$code} {$message}");

if (file_exists("view/error/{$code}.html.php")) {
    include "view/error/{$code}.html.php";
} else {
    include "view/error/default.html.php";
}