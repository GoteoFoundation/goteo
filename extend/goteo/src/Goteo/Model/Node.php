<?php

namespace Goteo\Model {

    use Goteo\Model\Image,
        Goteo\Core\ACL;

    class Node extends \Goteo\Core\Model {

        public
            $id = null,
            $name,
            $email,
            $admins = array(), // administradores
            $logo,
            $image;



        /**
         * Obtener datos de un nodo
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        static public function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'node_lang', $lang);

                $sql = static::query("
                    SELECT
                        node.id as id,
                        node.name as name,
                        node.email as email,
                        IFNULL(node_lang.subtitle, node.subtitle) as subtitle,
                        IFNULL(node_lang.description, node.description) as description,
                        node.logo as logo,
                        node.location as location,
                        node.url as url,
                        node.active as active,
                        node.twitter as twitter,
                        node.facebook as facebook,
                        node.linkedin as linkedin,
                        node.google as google
                    FROM node
                    LEFT JOIN node_lang
                        ON  node_lang.id = node.id
                        AND node_lang.lang = :lang
                    WHERE node.id = :id
                    ", array(':id' => $id, ':lang' => $lang));
                $item = $sql->fetchObject(__CLASS__);

                // y sus administradores
                $item->admins = self::getAdmins($id);

                // logo
                $item->logo = (!empty($item->logo)) ? Image::get($item->logo) : null;

                return $item;
        }

        static public function getMini ($id) {
                $sql = static::query("
                    SELECT
                        id, name, url, email
                    FROM node
                    WHERE id = :id
                    ", array(':id' => $id));
                $item = $sql->fetchObject();

                return $item;
        }

        /*
         * Array asociativo de administradores de un nodo
         *  (o todos los que administran si no hay filtro)
         */
        public static function getAdmins ($node = null) {

            $list = array();

            $sqlFilter = "";
            if (!empty($node)) {
                $sqlFilter .= " WHERE user_node.node = '{$node}'";
            }


            $query = static::query("
                SELECT
                    DISTINCT(user_node.user) as admin,
                    user.name as name
                FROM user_node
                INNER JOIN user
                    ON user.id = user_node.user
                $sqlFilter
                ORDER BY user.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->admin] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de nodos
         */
        public static function getAll ($filters = array()) {

            $list = array();

            $sqlFilter = "";
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND ( name LIKE ('%{$filters['name']}%') OR id = '{$filters['name']}' )";
            }
            if (!empty($filters['status'])) {
                $active = $filters['status'] == 'active' ? '1' : '0';
                $sqlFilter .= " AND active = '$active'";
            }
            if (!empty($filters['admin'])) {
                $sqlFilter .= " AND id IN (SELECT node FROM user_node WHERE user = '{$filters['admin']}')";
            }

            $sql = static::query("
                SELECT
                    *
                FROM node
                WHERE id IS NOT NULL
                    $sqlFilter
                ORDER BY `name` ASC
                ");

            foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                // y sus administradores
                $item->admins = self::getAdmins($item->id);

                $list[] = $item;
            }

            return $list;
        }

        /*
         * Lista simple de nodos
         */
        public static function getList () {

            $list = array();

            $query = static::query("
                SELECT
                    id,
                    name
                FROM node
                ORDER BY name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate (&$errors = array()) {
            if (empty($this->id))
                $errors[] = 'Falta Identificador';

            if (empty($this->name))
                $this->name = $this->id;

            if (empty($this->email))
                $this->email = \GOTEO_MAIL;

            if (!isset($this->active))
                $this->active = 0;

            if (isset($this->logo->id)) {
                $this->logo = $this->logo->id;
            }

            if (empty($errors))
                return true;
            else
                return false;
        }

        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'name',
                'email',
                'active'
                );

            $set = '';
            $values = array(':id' => $this->id);

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "UPDATE node SET " . $set . " WHERE id = :id";
                self::query($sql, $values);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
         }

        /**
		 * Guarda lo imprescindible para crear el registro.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function create (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name',
                'email',
                'url',
                'active'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "INSERT INTO node SET " . $set;
                self::query($sql, $values);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
         }

        /**
         * Obtener el nodo que administra cierto usuario
         * @param   type varchar(50)  $id   Usuario admin
         * @return  type varchar(50)  $id   Id Nodo
         */
        static public function getAdminNode ($admin) {
            $query = static::query("
                SELECT
                    node
                FROM user_node
                WHERE `user` = :admin
                LIMIT 1
                ", array(':admin' => $admin));

            return $query->fetchColumn();
        }

        /*
         * Asignar a un usuario como administrador de un nodo
         */
		public function assign ($user, &$errors = array()) {

            $values = array(':user'=>$user, ':node'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_node (user, node) VALUES(:user, :node)";
				if (self::query($sql, $values)) {
    				return true;
                } else {
                    $errors[] = 'No se ha creado el registro `user_node`';
    				return false;
                }
			} catch(\PDOException $e) {
				$errors[] = "No se ha podido asignar al usuario {$user} como administrador del nodo {$this->id}. Por favor, revise el metodo Node->assign." . $e->getMessage();
				return false;
			}

		}

        /*
         * Quitarle a un usuario la administración de un nodo
         */
		public function unassign ($user, &$errors = array()) {
			$values = array (
				':user'=>$user,
				':node'=>$this->id,
			);

            try {
                if (self::query("DELETE FROM user_node WHERE node = :node AND user = :user", $values)) {
                    return true;
                } else {
                    return false;
                }
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar al usuario ' . $this->user . ' de la administracion del nodo ' . $this->id . '. ' . $e->getMessage();
                return false;
			}
		}

        /*
         * Para actualizar los datos de descripción
         */
         public function update (&$errors) {
             if (empty($this->id)) return false;

            // Primero tratamos la imagen
            if (is_array($this->logo) && !empty($this->logo['name'])) {
                $image = new Image($this->logo);

                if ($image->save($errors)) {
                    $this->logo = $image->id;
                } else {
                    \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->logo = '';
                }
            }
            if (is_null($this->logo)) {
                $this->logo = '';
            }

            $fields = array(
                'name',
                'subtitle',
                'email',
                'location',
                'logo',
                'description'
                );

            $values = array (':id' => $this->id);

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                if ($set != '') {
                    $sql = "UPDATE node SET " . $set ." WHERE id = :id";
                    self::query($sql, $values);
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
         }

        /*
         * Para actualizar la traduccion
         */
         public function updateLang (&$errors) {
             if (empty($this->id)) return false;

  			try {
                $fields = array(
                    'id'=>'id',
                    'lang'=>'lang_lang',
                    'subtitle'=>'subtitle_lang',
                    'description'=>'description_lang'
                    );

                $set = '';
                $values = array();

                foreach ($fields as $field=>$ffield) {
                    if ($set != '') $set .= ', ';
                    $set .= "$field = :$field";
                    $values[":$field"] = $this->$ffield;
                }

				$sql = "REPLACE INTO node_lang SET " . $set;
				if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = $sql . '<pre>' . print_r($values, true) . '</pre>';
                    return false;
                }
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar la traduccion del nodo.' . $e->getMessage();
                return false;
			}

         }

        /**
         * Saca una lista de nodos disponibles para traducir
         *
         * @param array filters
         * @param string node id
         * @return array of project instances
         */
        public static function getTranslates($filters = array()) {
            $list = array();

            $values = array();

            $and = " WHERE";
            $sqlFilter = "";
            if (!empty($filters['admin'])) {
                $sqlFilter .= "$and id IN (
                    SELECT node
                    FROM user_node
                    WHERE user = :admin
                    )";
                $and = " AND";
                $values[':admin'] = $filters['admin'];
            }
            if (!empty($filters['translator'])) {
                $sqlFilter .= "$and id IN (
                    SELECT item
                    FROM user_translate
                    WHERE user = :translator
                    AND type = 'node'
                    )";
                $and = " AND";
                $values[':translator'] = $filters['translator'];
            }

            $sql = "SELECT
                        id
                    FROM `node`
                    $sqlFilter
                    ORDER BY name ASC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $item) {
                $anode = self::get($item['id']);
                $anode->translators = \Goteo\Model\User\Translate::translators($item['id'], 'node');
                $list[] = $anode;
            }
            return $list;
        }



        /** Resumen proyectos: (asignados a este nodo)
         * total proyectos,
         * activos (en campaña),
         * exitosos (que han llegado al mínimo),
         * cofinanciadores (diferentes),
         * colaboradores (diferentes)
         * total de dinero recaudado
         *
         * @return array asoc
         */
        public function getSummary () {

            // sacamos registro de la tabla de calculos
            $sql = "
                SELECT
                    projects,
                    active,
                    success,
                    investors,
                    supporters,
                    amount,
                    unix_timestamp(now()) - unix_timestamp(updated) as timeago
                FROM node_data
                WHERE node = :node
                LIMIT 1
                ";
            $query = self::query($sql, array(':node' => $this->id));
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            // si el calculo tiene más de 30 minutos (ojo, timeago son segundos) , calculamos de nuevo
            if (empty($data) || $data['timeago'] > (30*60)) {
                if ($newdata = $this->updateData()) {
                    return $newdata;
                }
            }

            return $data;
        }

        /** Resumen convocatorias: (destacadas por el nodo)
         * nº campañas abiertas
         * nº convocatorias activas
         * importe total de las campañas
         * resto total
         *
         * @return array asoc
         */
        public function getSumcalls () {

            // sacamos registro de la abla de calculos
            $sql = "
                SELECT
                    budget,
                    rest,
                    calls,
                    campaigns,
                    unix_timestamp(now()) - unix_timestamp(updated) as timeago
                FROM node_data
                WHERE node = :node
                LIMIT 1
                ";
            $query = self::query($sql, array(':node' => $this->id));
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            // si el calculo tiene más de 30 minutos (ojo, timeago son segundos) , calculamos de nuevo
            if (empty($data) || $data['timeago'] > (30*60)) {
                if ($newdata = $this->updateData()) {
                    return $newdata;
                }
            }

            return $data;
        }

        private function updateData () {
            $values = array(':node' => $this->id);
            $data = array();

            // primero calculamos y lo metemos tanto en values como en data
            // datos de proyectos
            // nº de proyectos
            $query = static::query("
                SELECT
                    COUNT(project.id)
                FROM    project
                WHERE node = :node
                AND status IN (3, 4, 5, 6)
                ", $values);
            $data['projects'] = $query->fetchColumn();

            // proyectos activos
            $query = static::query("
                SELECT
                    COUNT(project.id)
                FROM    project
                WHERE node = :node
                AND status = 3
                ", $values);
            $data['active'] = $query->fetchColumn();

            // proyectos exitosos
            // ojo! hay que tener en cuenta los que llegan al mínimo
            $query = static::query("
                SELECT
                    project.id,
                    (SELECT  SUM(amount)
                    FROM    cost
                    WHERE   project = project.id
                    AND     required = 1
                    ) as `mincost`,
                    (SELECT  SUM(amount)
                    FROM    invest
                    WHERE   project = project.id
                    AND     invest.status IN ('0', '1', '3', '4')
                    ) as `getamount`
                FROM    project
                WHERE node = :node
                AND status IN ('3', '4', '5')
                HAVING getamount >= mincost
                ", $values);
            $data['success'] = $query->rowCount();

            // cofinanciadores
            $query = static::query("
                SELECT
                    COUNT(DISTINCT(invest.user))
                FROM  invest
                INNER JOIN project
                    ON project.id = invest.project
                WHERE project.node = :node
                AND invest.status IN ('0', '1', '3', '4')
                ", $values);
            $data['investors'] = $query->fetchColumn();

            // colaboradores (que han enviado algun mensaje)
            $query = static::query("
                SELECT
                    COUNT(DISTINCT(message.user))
                FROM  message
                INNER JOIN project
                    ON project.id = message.project
                WHERE project.node = :node
                AND message.user != project.owner
                ", $values);
            $data['supporters'] = $query->fetchColumn();

            // cantidad recaudada en total
            $query = static::query("
                SELECT
                    SUM(invest.amount)
                FROM  invest
                INNER JOIN project
                    ON project.id = invest.project
                WHERE project.node = :node
                AND invest.status IN ('0', '1', '3')
                ", $values);
            $data['amount'] = $query->fetchColumn();

            // datos de convocatorias (destacadas por el nodo)
            // presupuesto
            $query = static::query("
                SELECT
                    SUM(amount)
                FROM    `call`
                INNER JOIN campaign
                    ON call.id = campaign.call
                    AND node = :node
                ", $values);
            $data['budget'] = $query->fetchColumn();

            // por repartir
            $query = static::query("
                SELECT SUM(invest.amount)
                FROM invest
                INNER JOIN campaign
                    ON invest.call = campaign.call
                    AND node = :node
                WHERE invest.campaign = 1
                AND invest.status IN ('0', '1', '3')
                ", $values);
            $data['rest'] = $data['budget'] - $query->fetchColumn();

            // proyectos activos
            $query = static::query("
                SELECT
                    COUNT(call.id)
                FROM    `call`
                INNER JOIN campaign
                    ON call.id = campaign.call
                    AND node = :node
                WHERE call.status = 3
                ", $values);
            $data['calls'] = $query->fetchColumn();

            // proyectos activos
            $query = static::query("
                SELECT
                    COUNT(call.id)
                FROM   `call`
                INNER JOIN campaign
                    ON call.id = campaign.call
                    AND node = :node
                WHERE call.status = 4
                ", $values);
            $data['campaigns'] = $query->fetchColumn();



            //grabamos los datos en la tabla
            $set = 'node = :node';

            $fields = array(
                'projects',
                'active',
                'success',
                'investors',
                'supporters',
                'amount',
                'budget',
                'rest',
                'calls',
                'campaigns'
                );

            foreach ($fields as $field) {
                $set .= ', ';
                $set .= "$field = :$field";
                $values[":$field"] = $data[$field];
            }

            $sql = "REPLACE node_data SET " . $set;
            if (self::query($sql, $values)) {
                // devolvemos los datos
                return $data;
            } else {
                return false;
            }

        }

    }

}
