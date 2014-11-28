<?php

use Goteo\Core\Error;

if (!isset($error) || !($error instanceof Error)) {
    $error = new Error;
}
$code = $error->getCode();
$message = $error->getMessage();


header("HTTP/1.0 {$code} {$message}");

if (file_exists(__DIR__ . "/error/{$code}.html.php")) {
    require __DIR__ . "/error/{$code}.html.php";
} else {
    require __DIR__ . "/error/default.html.php";
}
