<?php

/*
 * Este modelo es para la asignación de tareas pendientes de administracion
 */

namespace Goteo\Model {
    
    class Task extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $text,
            $url,
            $datetime,
            $done = null;


        /**
         * Obtener los datos de una tarea
         */
        static public function get ($id) {
            try {
                $query = static::query("SELECT * FROM task WHERE id = ?", array($id));
                $item = $query->fetchObject(__CLASS__);
                if (!empty($item->done)) {
                    $item->user = \Goteo\Model\User::getMini($item->done);
                }
                return $item;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /**
         * Lista de tareas
         *
         * @param  bool $visible    true|false
         * @return mixed            Array de objetos de tareas
         */
        public static function getAll ($filters = array(), $node = null, $undoneOnly = false ) {

            $values = array();

            $list = array();

            $sqlFilter = "";
            $and = " WHERE";
            if (!empty($filters['done'])) {
                if ($filters['done'] == 'done') {
                    $sqlFilter .= "$and done IS NOT NULL";
                    $and = " AND";
                } else {
                    $sqlFilter .= "$and done IS NULL";
                    $and = " AND";
                }
            }
            if (!empty($filters['user'])) {
                $sqlFilter .= "$and done = :user";
                $values[':user'] = $filters['user'];
                $and = " AND";
            }
            if (!empty($filters['node'])) {
                $sqlFilter .= "$and node = :node";
                $values[':node'] = $filters['node'];
                $and = " AND";
            } elseif (!empty($node)) {
                $sqlFilter .= "$and node = :node";
                $values[':node'] = $node;
                $and = " AND";
            }
            if ($undoneOnly) {
                $sqlFilter .= "$and done IS NULL";
                $and = " AND";
            }

            $sql = "SELECT *
                    FROM task
                    $sqlFilter
                    ORDER BY datetime DESC
                    ";

            echo $sql . '<br />';

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                if (!empty($item->done)) {
                    $item->user = \Goteo\Model\User::getMini($item->done);
                }
                $list[] = $item;
            }
            return $list;
        }

        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function save (&$errors = array()) {
             if (!$this->validate()) return false;

            $values = array(':id'=>$this->id, ':node'=>$this->node, ':text'=>$this->text, ':url'=>$this->url, ':done'=>$this->done);

			try {
	            $sql = "REPLACE INTO task (id, node, text, url, done) VALUES(:id, :node, :text, :url, :done)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "HA FALLADO!!! " . $e->getMessage();
				return false;
			}
         }

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate (&$errors = array()) {
            if (empty($this->node)) {
                $this->node = \GOTEO_NODE;
            }
            return true;
        }


        /**
         * Este método marca el usuario en el campo Done
         */
        public function setDone (&$errors = array()) {

            $values = array(':id'=>$this->id, ':done'=>$_SESSION['user']->id);

            try {
                $sql = "UPDATE task SET `done` = :done WHERE id = :id";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = 'Algo ha fallado';
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }

        }

        /**
         * Este método marca el usuario en el campo Done
         */
        public function remove (&$errors = array()) {

            $values = array(':id'=>$this->id);

            try {
                $sql = "DELETE task WHERE id = :id";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = 'Algo ha fallado';
                    return false;
                }
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }

        }

        

    }
}