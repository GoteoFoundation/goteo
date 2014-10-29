<?php

/*
 * Este modelo es para la asignación de tareas pendientes de administracion
 */

namespace Goteo\Model {

    use Goteo\Model\User,
        Goteo\Model\Image;

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
         *
         * @param   type int    $id         id de la tarea.
         * @return  type bool   true|false
         */
        static public function get($id) {
            try {
                $sql="SELECT 
                            task.*,
                            user.id as user_id,
                            user.name as user_name,
                            user.email as user_email
                      FROM task
                      LEFT JOIN user
                      ON user.id=task.done 
                      WHERE task.id = ?";
                $query = static::query($sql, array($id));
                $item = $query->fetchObject(__CLASS__);
                if (!empty($item->done)) {

                     // datos del usuario. Eliminación de user::getMini
                    $user = new User;
                    $user->id = $item->user_id;
                    $user->name = $item->user_name;
                    $user->email = $item->user_email;

                    $item->user = $user;
                }
                return $item;
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /**
         * Lista de tareas
         *
         * @param  bool $visible    true|false
         * @return mixed            Array de objetos de tareas
         */
        public static function getAll($filters = array(), $node = null, $undoneOnly = false) {

            $values = array();

            $list = array();

            $sqlFilter = "";
            $and = " WHERE";
            if (!empty($filters['done'])) {
                if ($filters['done'] == 'done') {
                    $sqlFilter .= "$and task.done IS NOT NULL";
                    $and = " AND";
                } else {
                    $sqlFilter .= "$and task.done IS NULL";
                    $and = " AND";
                }
            }
            if (!empty($filters['user'])) {
                $sqlFilter .= "$and task.done = :user";
                $values[':user'] = $filters['user'];
                $and = " AND";
            }
            if (!empty($filters['node'])) {
                $sqlFilter .= "$and task.node = :node";
                $values[':node'] = $filters['node'];
                $and = " AND";
            } elseif (!empty($node)) {
                $sqlFilter .= "$and task.node = :node";
                $values[':node'] = $node;
                $and = " AND";
            }
            if ($undoneOnly) {
                $sqlFilter .= "$and (done IS NULL OR done = '')";
                $and = " AND";
            }

            $sql = "SELECT task.*,
                           user.id as user_id,
                           user.name as user_name,
                           user.email as user_email
                    FROM task
                    LEFT JOIN user
                         ON user.id=task.done
                    $sqlFilter
                    ORDER BY datetime DESC
                    ";

            // echo $sql . '<br />';

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                if (!empty($item->done)) {
                    // datos del usuario. Eliminación de user::getMini
                    $user = new User;
                    $user->id = $item->user_id;
                    $user->name = $item->user_name;
                    $user->email = $item->user_email;

                    $item->user = $user;
                }
                $list[] = $item;
            }
            return $list;
        }

        /**
         * Guardar.
         *
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function save(&$errors = array()) {
            if (!$this->validate())
                return false;

            $values = array(':id' => $this->id, ':node' => $this->node, ':text' => $this->text, ':url' => $this->url, ':done' => $this->done);

            try {
                $sql = "REPLACE INTO task (id, node, text, url, done) VALUES(:id, :node, :text, :url, :done)";
                self::query($sql, $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /**
         * Validar.
         *
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate(&$errors = array()) {
            if (empty($this->node)) {
                $this->node = \GOTEO_NODE;
            }
            return true;
        }

        /*
         * Guarda solo si no hay una tarea con esa url
         *
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function saveUnique(&$errors = array()) {
            if (empty($this->node)) {
                $this->node = \GOTEO_NODE;
            }

            $query = static::query("SELECT id FROM task WHERE url = :url", array(':url'=>$this->url));
            $exists = $query->fetchColumn();
            if (!empty($exists)) {
                // ya existe
                return true;
            } else {
                return $this->save($errors);
            }
        }

        /**
         * Este método marca el usuario en el campo Done
         *
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function setDone(&$errors = array()) {

            $values = array(':id' => $this->id, ':done' => $_SESSION['user']->id);

            try {
                $sql = "UPDATE task SET `done` = :done WHERE id = :id";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = 'Algo ha fallado';
                    return false;
                }
            } catch (\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /**
         * Borrar una tarea
         *
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function remove(&$errors = array()) {

            $values = array(':id' => $this->id);

            try {
                $sql = "DELETE FROM task WHERE id = :id";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = 'Algo ha fallado';
                    return false;
                }
            } catch (\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

    }

}
