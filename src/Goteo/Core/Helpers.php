<?php

namespace {

    /**
     * Obtiene dirección ip del cliente
     * @return ip address
     */
    function myip() {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
               // si 'HTTP_X_FORWARDED_FOR' lleva una coma, la conexión es a través de  un proxy sin anonimato,
               // en este caso la id del proxy viene en  [HTTP_X_PROXY_ID] y los detalles en HTTP_VIA
               // basta que el servidor sea un poco anónimo para no tener esta info aquí
               if (strpos($ip, ',') > 0) {
                   // nos quedamos con la primera parte
                   $parts = explode(',', $ip);
                   $ip = trim($parts[1]); // temporalmente la segunda para investigar
               }
			}
			elseif (isset($_SERVER['HTTP_VIA'])) {
			   $ip = $_SERVER['HTTP_VIA'];
			}
			elseif (isset($_SERVER['REMOTE_ADDR'])) {
			   $ip = $_SERVER['REMOTE_ADDR'];
			}
			else {
			   $ip = NULL;
			}
		return $ip;
    }


    /**
     * Traza información sobre el recurso especificado de forma legible.
     *
    * @param    type mixed  $resource   Recurso
     */
    function trace ($resource = null) {
        echo '<pre>' . print_r($resource, true) . '</pre>';
    }

    /**
     * Vuelca información sobre el recurso especificado de forma detallada.
     *
     * @param   type mixed  $resource   Recurso
     */
    function dump ($resource = null) {
        echo '<pre>' . var_dump($resource) . '</pre>';
    }

    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from
     * $params are are in the same order as specified in $query
     *
     * @param string $query The sql query with parameter placeholders
     * @param array $params The array of substitution parameters
     * @return string The interpolated query
     */
    function sqldbg ($query, array $params = []) {
        $keys = array();

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/' . ($key{0} === ':' ? '' : ':') . $key . '\b/';
            } else {
                $keys[] = '/[?]/';
            }
            $params[$key] = "'" . str_replace("'", "\'", $value) . "'";
        }
        // print_r($keys);print_r($params);
        $query = preg_replace($keys, $params, $query, -1, $count);

        // trigger_error('replaced '.$count.' keys');

        return $query;
    }

    function date_interval($date1, $date2 = 'now', $format = '%a') {
        $datetime1 = new \DateTime($date1);
        $datetime2 = new \DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        return $interval->format($format);
    }

    /**
     * Genera un mktime (UNIX_TIMESTAMP) a partir de una fecha (DATE/DATETIME/TIMESTAMP)
     * @param $str
     */
    function date2time ($str) {
    	list($date, $time) = explode(' ', $str);
    	list($year, $month, $day) = explode('-', $date);
    	list($hour, $minute, $second) = explode(':', $time);
        $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $timestamp;
    }

    /**
     * Checkea si todos los indices del array son vacios
     * @param array $mixed
     * @return boolean
     */
    function array_empty($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $value) {
                if (!array_empty($value)) {
                    return false;
                }
            }
        }
        elseif (!empty($mixed)) {
            return false;
        }
        return true;
    }

    /**
     * Numberformat para convertir importes
     */
    function amount_format($amount, $decs = 0, $nosymbol = false, $revert = false) {


        return \Goteo\Library\Currency::amount_format($amount, $decs, $nosymbol, $revert);
    }

    /**
     * Numberformat para convertir importes solo euro
     */
    function euro_format($amount, $decs = 0) {

        return number_format($amount, $decs, ',', '.');
    }

    /*
     * Verifica si una cadena es sha1
     */
    function is_sha1($str) {
        return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
    }

    /*
     * Asegura una url si está en entorno seguro
     */
    function sec($url) {
        return (defined('SECENV')) ? str_replace('http://', 'https://', $url) : $url;

    }

    /* problema de la barra en base64*/
    function mybase64_encode($s) {
        return str_replace(array('+', '/'), array('_', '-'), base64_encode($s));
    }

    function mybase64_decode($s) {
        return base64_decode(str_replace(array('_', '-'), array('+', '/'), $s));
    }


    /* para convertir código de controlador (con undescore) 'dos_palabras' a nombre de clase (CamelCase) 'DosPalabras'*/
    // /admin/open_tags sería \Goteo\Controller\Admin\OpenTags (para cumplir con el standard del framework)
    function strtoCamelCase ($str) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

}
