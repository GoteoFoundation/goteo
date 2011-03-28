<?php

namespace Goteo\Core {

	use Goteo\Core\Error;
    
    abstract class Model {
        
        protected static 
            $__connections = array();

        /**
         * Constructor.
         */
        public function __construct () {
            if (\func_num_args() >= 1) {
                $data = \func_get_arg(0);
                if (is_array($data) || is_object($data)) {
                    foreach ($data as $k => $v) {
                        $this->$k = $v;
                    }
                }
            }
        }
        
        /**
         * Obtener.
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        abstract static public function get ($id);
        
        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         abstract public function save (&$errors = array());
        
        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        abstract public function validate (&$errors = array());
        
        /**
         * Consulta.
         * Devuelve un objeto de la clase PDOStatement
         * http://www.php.net/manual/es/class.pdostatement.php
         * 
         * @param   type string $query      Consulta SQL
         * @param   type array  $params     Parámetros
         * $return  type object PDOStatement
         */
        public static function query ($query, $params = null) {
			$db = new DB;
			$result = $db->prepare($query);
			$params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);
			if ($result->execute($params)) {
				return $result;
			}
			throw new Exception($query);
        }

		/**
		 * Formato.
		 * Formatea una cadena para ser usada como id varchar(50)
		 *
		 * @param string $value
		 * @return string $id
		 */
		public static function idealiza ($value) {
			$id = trim(strtolower($value));
			// Acentos
			$id = strtr($id, "ÁÀÄÂáàâäÉÈËÊéèêëÍÌÏÎíìîïÓÒÖÔóòôöÚÙÛÜúùûüÇçÑñ", "aaaaaaaaeeeeeeeeiiiiiiiioooooooouuuuuuuuccnn");
			// Separadores
			$id = preg_replace("/[\s\,\;\_\/\-]+/i", "-", $id);
			$id = preg_replace("/[^a-z0-9\.\-\+]/", "", $id);
			$id = substr($id, 0, 50);

			return $id;
		}

    }

}