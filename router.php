<?php
//router.php

// die(":".m_temp_dir());

if (call_user_func(function() {
	$f = $_SERVER['SCRIPT_FILENAME'];
	$root = $_SERVER["DOCUMENT_ROOT"];
	$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	// return if requested file exists
	if ($f != __FILE__ && file_exists($root . $path) ) {

		return true;
	}
	$_SERVER['SCRIPT_NAME'] = "/index.php"; // fix SCRIPT_NAME that change when REQUEST_URI contains dot
})) return false;

require_once 'index.php';