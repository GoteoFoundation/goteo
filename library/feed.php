<?php
namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Library\Text;

	/*
	 * Clase para loguear eventos
	 */
    class Feed {

        public
            $id,
            $title, // titulo entrada o nombre usuario
            $url, // enlace del titulo
            $scope, // ambito del evento (public, admin)
            $type, // tipo de evento  ($public_types , $admin_types)
            $timeago, // el hace tanto
            $datetime, // fecha y hora del evento
            $html, // contenido del evento en codigo html
            $text,  // id del texti dinamico
            $params,  // (array serializado en bd) parametros para el texto dinamico
            $user, // usuario asociado al evento
            $project, // proyecto asociado al evento
            $node; // nodo asociado al evento

        static public $admin_types = array(
            'all' => array(
                'label' => 'Todo',
                'color' => 'light-blue'
            ),
            'admin' => array(
                'label' => 'Administrador',
                'color' => 'red'
            ),
            'user' => array(
                'label' => 'Usuario',
                'color' => 'blue'
            ),
            'project' => array(
                'label' => 'Proyecto',
                'color' => 'light-blue'
            ),
            'money' => array(
                'label' => 'Transferencias',
                'color' => 'violet'
            ),
            'system' => array(
                'label' => 'Sistema',
                'color' => 'grey'
            )
        );

        static public $public_types = array(
            'goteo' => array(
                'label' => 'Goteo'
            ),
            'projects' => array(
                'label' => 'Proyectos'
            ),
            'community' => array(
                'label' => 'Comunidad'
            )
        );

        /*
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
        */
        
		/**
		 *  Metodo para sacar los eventos
         *
         * @param string $type  tipo de evento (public: columnas goteo, proyectos, comunidad;  admin: categorias de filtro)
         * @param string $scope ambito de eventos (public | admin)
         * @return array list of items
		 */
		public static function getAll($type = 'all', $scope = 'public') {

            $list = array();
            return $list;

            try {
                $values = array(':type' => $type, ':scope' => $scope);

                $sql = "SELECT
                            feed.id as id,
                            feed.title as title,
                            feed.url as url,
                            feed.html as html
                        FROM feed
                        ORDER BY datetime DESC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                    $list[] = $item;
                }
                return $list;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

		/**
		 *  Metodo para grabar eventos
         *
         *  Los datos del evento estan en el objeto
         *
         *
         * @param array $errors
         *
         * @access public
         * @return boolean true | false   as success
         *
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