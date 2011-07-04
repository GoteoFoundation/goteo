<?php

namespace {

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

}