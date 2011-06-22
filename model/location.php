<?php

/*
 * Este modelo es para la geo localizacion
 */

namespace Goteo\Model {
    
    class Location extends \Goteo\Core\Model {
    
        /**
         * Obtener datos de una localizacion (longitud, latitud, nombbre completo )
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        static public function get ($id) {
            return new stdClass();
        }

        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function save (&$errors = array()) {
             if (!$this->validate()) return false;

             return true;
         }

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate (&$errors = array()) {
            return true;
        }

    }
}