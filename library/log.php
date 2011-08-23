<?php
namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Library\Text;

	/*
	 * Clase para loguear eventos
	 */
    class Log {

        public
            $id,
            $node,
            $title,
            $subject, // sujeto del evento
            $item,  // id del sujeto
            $type, // tipo de evento
            $action, // evento que ocurre  (dice que tipo de informacion encontraremos en target)
            $target, // objetivo del evento (proyecto-cantidad que aporta, tabla-registro que opera, minimo/optimo alcanzado, dias para caducar, operacion/subaccion efectuada)
            $url, // enlace
            $date, // fecha en la que ocurre
            $time; // hora en la que ocurre

        public $types = array(
            'admin' => 'admin',
            'public' => 'publico',
            'node' => 'nodo',
            'user' => 'usuario',
            'system' => 'sistema'
        );

        public $subjects = array(
            'user' => 'el usuario', // + item
            'project' => 'el proyecto', // + item
            'blog' => 'el blog', // + item
            'transaction' => 'la transaccion', // + item
            'campaign' => 'la campaña', // + item
            'node' => 'el nodo' // + item
        );

        public $action = array(
            'register' => 'se ha registrado',   // no mas target
            'publish' => 'ha publicado',        // target = un post
            'reach' => 'ha alcanzado',          // target = minimum/optimum
            'expire' => 'caduca en',   //
            'invest' => 'ha aportado',   //
            'message' => 'ha escrito',   // target = message
            'comment' => 'ha comentado',   // target = comment
            'insert' => 'ha hecho nuevo',   // target = tabla ¬ registro
            'update' => 'ha modificado',   // target = tabla ¬ registro
            'delete' => 'ha borrado',   // target = tabla ¬ registro
            'translate' => 'ha traducido',   // target = tabla ¬ registro
            'execute' => 'ha ejecutado',   // target = operacion (cargo, cancelacion, rollback, fail)
            'made' => 'ha hecho',   // target = subaccion exitosa
            'tried' => 'ha intentado'   // target = subaccion fallida
        );

        static public function get ($id, $lang = \LANG, $node = \GOTEO_NODE) {

            // buscamos si se ha dado un evento en particular
		}

		/*
		 *  Metodo para sacar los eventos
		 */
		public static function getAll($subjects = array(), $types = array(), $item = null) {
		}

		/*
		 *  Para añadir un registro de log
		 */
		public function add(&$errors = array()) {
            return false;

            /*
  			try {
                $values = array(
                    ':page' => $this->id,
                    ':lang' => $this->lang,
                    ':node' => $this->node,
                    ':contenido' => $this->content
                );

				$sql = "REPLACE INTO page_node
                            (page, node, lang, content)
                        VALUES
                            (:page, :node, :lang, :contenido)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}
            */

		}

	}
}