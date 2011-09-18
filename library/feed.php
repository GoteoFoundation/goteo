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
            $date, // fecha y hora del evento
            $html, // contenido del evento en codigo html
            $text,  // id del texto dinamico
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

        static public $color = array(
            'user' => 'blue',
            'project' => 'light-blue',
            'blog' => 'grey',
            'money' => 'violet',
            'relevant' => 'red',
            'comment' => 'green',
            'update-comment' => 'grey',
            'message' => 'green',
            'system' => 'grey',
            'update' => 'grey'
        );

        static public $page = array(
            'user' => '/user/profile/',
            'project' => '/project/',
            'blog' => '/blog/',
            'comment' => '/blog/',
            'update-comment' => '/project/',
            'message' => '/project/',
            'system' => '/admin/',
            'update' => '/project/'
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

            try {
                $values = array(':scope' => $scope);

                $sqlType = '';
                if ($type != 'all') {
                    $sqlType = " AND feed.type = :type";
                    $values[':type'] = $type;
                }

                $sql = "SELECT
                            feed.id as id,
                            feed.title as title,
                            feed.url as url,
                            DATE_FORMAT(feed.datetime, '%H:%i %d|%m|%Y') as date,
                            feed.datetime as timer,
                            feed.html as html
                        FROM feed
                        WHERE feed.scope = :scope $sqlType
                        ORDER BY datetime DESC
                        LIMIT 999
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                    //hace tanto
                    $item->timeago = self::time_ago($item->timer);

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

  			try {
                $values = array(
                    ':title' => $this->title,
                    ':url' => $this->url,
                    ':scope' => !empty($this->scope) ? $this->scope : 'admin' ,
                    ':type' => !empty($this->type) ? $this->type : 'system',
                    ':html' => $this->html
                );

				$sql = "INSERT INTO feed
                            (id, title, url, scope, type, html)
                        VALUES
                            ('', :title, :url, :scope, :type, :html)
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

		}
        
        /**
         * Metodo para transformar un TIMESTAMP en un "hace tanto"
         * 
         * 
         */
        public static function time_ago($date,$granularity=1) {

            $retval = '';
            $date = strtotime($date);
            $ahora = time();
            $difference = $ahora - $date;
            $periods = array('decada' => 315360000,
                'año' => 31536000,
                'mes' => 2628000,
                'semana' => 604800,
                'dia' => 86400,
                'hora' => 3600,
                'minuto' => 60,
                'segundo' => 1);

            foreach ($periods as $key => $value) {
                if ($difference >= $value) {
                    $time = floor($difference/$value);
                    $difference %= $value;
                    $retval .= ($retval ? ' ' : '').$time.' ';
                    $retval .= (($time > 1) ? $key.'s' : $key);
                    $granularity--;
                }
                if ($granularity == '0') { break; }
            }

            return $retval;
        }


        /**
         *  Genera codigo html para enlace o texto dentro de feed
         *
         */
        public static function item ($type = 'system', $label = 'label', $id = null) {

            // si llega id es un enlace
            if (isset($id)) {
                return '<a href="'.self::$page[$type].$id.'" class="'.self::$color[$type].'" target="_blank">'.$label.'</a>';
            } else {
                return '<span class="'.self::$color[$type].'">'.$label.'</span>';
            }


        }

    }
}