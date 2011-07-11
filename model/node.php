<?php

namespace Goteo\Model {
    
    class Node extends \Goteo\Core\Model {

        /**
         * Obtener datos de un nodo
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