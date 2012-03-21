<?php

namespace Goteo\Model {

    use \Goteo\Library\Message,
        \Goteo\Model\Image,
        \Goteo\Core\ACL;

    class Node extends \Goteo\Core\Model {

        public
            $id = null,
            $name,
            $admins = array(), // administradores
            $logo,
            $image;



        /**
         * Obtener datos de un nodo
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        static public function get ($id) {
                $sql = static::query("
                    SELECT
                        *
                    FROM node
                    WHERE id = :id
                    ", array(':id' => $id));
                $item = $sql->fetchObject(__CLASS__);

                // y sus administradores
                $item->admins = self::getAdmins($id);

                // logo
                if (!empty($item->logo)) {
                    $item->logo = Image::get($item->logo);
                }

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
                WHERE id != 'goteo'
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

            if (!isset($this->active))
                $this->active = 0;

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
                'name'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO node SET " . $set;
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
                $sql = "REPLACE INTO node SET " . $set;
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
                    ACL::allow('/manage', $this->id, 'admin', $user);
    				return true;
                } else {
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
                    self::query("DELETE FROM acl WHERE node = :node AND user = :user", $values);
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
                    Message::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->logo = '';
                }
            }

            $fields = array(
                'name',
                'subtitle',
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

    }
    
}