<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Goteo\Application\Session;
use Goteo\Core\Error;
use Goteo\Core\Resource;
use Goteo\Core\View;
use Symfony\Component\HttpFoundation\Response;

// Get URI without query string
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$segments = preg_split('!\s*/+\s*!', $uri, -1, PREG_SPLIT_NO_EMPTY);

// Normalize URI
$uri = '/' . implode('/', $segments);

$controller = '';
if (!empty($segments) && is_array($segments)) {
	// Take first segment as controller
	$firstSegment = ucfirst(array_shift($segments));

	if (class_exists("Goteo\\Controller\\$firstSegment")) {
		$controller = $firstSegment;
	}
}
if (empty($controller)) {
	throw new Error(Error::NOT_FOUND, "Related url: $uri");
}

try {
	$class = new ReflectionClass("Goteo\\Controller\\{$controller}");

	if (!empty($segments) && $class->hasMethod($segments[0])) {
		$method = array_shift($segments);
	} else {
		$method = 'index';
	}

	$method = $class->getMethod($method);
	$numParams = $method->getNumberOfParameters();
	$reqParams = $method->getNumberOfRequiredParameters();
	$gvnParams = count($segments);

	if ($gvnParams >= $reqParams && (!($gvnParams > $numParams && $numParams <= $reqParams))) {
		$instance = $class->newInstance();
		$result = $method->invokeArgs($instance, $segments);

		if ($result === null) {
			ob_start();
			$result = ob_get_contents();
			ob_end_clean();
		}

		if ($result instanceof Resource\MIME) {
			$mime_type = $result->getMIME();
			header("Content-type: $mime_type");
		}

		//esto suele llamar a un metodo magic: __toString de la vista View
		if ($result instanceOf View) {
			echo $result->render();
		}
		//Provisional mientras la transicion
		elseif ($result instanceOf Response) {
			return $result;
		} else {
			echo $result;
		}

		if ($mime_type == "text/html") {
			echo '<!-- legacy: ' . (microtime(true) - Session::getStartTime()) . 's -->';
		}

		return;
	}

} catch (ReflectionException $e) {
	// esto tendría que notificar a Config::getMail('fail')
	throw new Error(Error::BAD_REQUEST, $e->getMessage());
}

throw new Error(Error::NOT_FOUND);
