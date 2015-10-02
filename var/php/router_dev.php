<?php
//router.php
// Default index file
if(!defined('DIRECTORY_INDEX')) define('DIRECTORY_INDEX', 'index_dev.php');

if (call_user_func(function() {
    $f = $_SERVER['SCRIPT_FILENAME'];
    $root = $_SERVER['DOCUMENT_ROOT'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


    // return if requested file exists
    if ($f != __FILE__ && file_exists($root . $path) && $path != '/') {

        return true;
    }
    $_SERVER['SCRIPT_NAME'] = '/' . DIRECTORY_INDEX; // fix SCRIPT_NAME that change when REQUEST_URI contains dot
})) return false;

require_once $_SERVER['DOCUMENT_ROOT'] . '/'. DIRECTORY_INDEX;
