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
                   // PROXY
                   /*
                   @mail('proxy_alert@doukeshi.org', 'Acceso mediante proxy en '. SITE_URL, 'Detalles: 
SEVER
----------------
'.print_r($_SERVER, 1).'
    
SESSION
----------------
'.print_r($_SESSION, 1));
                    */
                   
                   // nos quedamos con la primera parte
                   $parts = explode(',', $ip);
                   $ip = strim($parts[1]); // temporalmente la segunda para investigar
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
        echo '<pre>' . print_r($resource, 1) . '</pre>';
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
     * Numberformat para importes
     */
    function amount_format($amount, $decs = 0) {
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



}