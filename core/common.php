<?php

namespace {

    /**
     * Traza información sobre el recurso especificado de forma legible.
     *
    * @param    type mixed  $resource   Recurso
     */
    function trace($resource = null) {
        echo '<pre>' . print_r($resource, 1) . '</pre>';
    }

    /**
     * Vuelca información sobre el recurso especificado de forma detallada.
     *
     * @param   type mixed  $resource   Recurso
     */
    function dump($resource = null) {
        echo '<pre>' . var_dump($resource) . '</pre>';
    }

    /**
     * Devuelve el nombre de una clase mediante get_class cortando los espacios de nombre.
     * @FIXME: Esto no funcionará dentro de un ámbito de clase debido a get_class ()
     * Workaround: Una solución para es utilizar get_class_name(get_class());
     *
     * @param   type object|string  $object Objeto o nombre de clase a recuperar
     * @return  type string|false           Nombre de la clase sin espacios de nombre.
     */
    function get_class_name($object = null) {
        if (!is_object($object) && !is_string($object)) {
            return false;
        }
        $class = explode('\\', (is_string($object) ? $object : get_class($object)));
        return $class[count($class) - 1];
    }

}