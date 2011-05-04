<?php

namespace Goteo\Model\Project {

    class Support extends \Goteo\Core\Model {

        public
            $id,
			$project,
			$support,
			$description,
			$type;

	 	public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM support WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public static function getAll ($project) {
            try {
                $array = array();
				$query = self::query("SELECT * FROM support WHERE project = ? ORDER BY id DESC", array($project));
				foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item ) {
                    $array[$item->id] = $item;
                }
				return $array;
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->project))
                $errors[] = 'No hay proyecto al que asignar la colaboración';

            if (empty($this->support))
                $errors[] = 'No hay colaboración';

            if (!isset($this->description))
                $errors[] = 'No hay descripción de la colaboración';

            if (empty($this->type))
                $errors[] = 'No hay tipo de colaboración';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			$fields = array(
				'id',
				'project',
				'support',
				'type',
				'description'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "$field = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO support SET " . $set;
				self::query($sql, $values);
    			if (empty($this->id)) $this->id = self::insertId();
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La colaboración {$data['support']} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

		/**
		 * Quitar una colaboracion de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla support
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':id'=>$this->id,
			);

            try {
                self::query("DELETE FROM support WHERE id = :id AND project = :project", $values);
				return true;
			} catch (\PDOException $e) {
                $errors[] = 'No se ha podido quitar la colaboracion del proyecto ' . $this->project . ' ' . $e->getMessage();
                return false;
			}
		}

		public static function types() {
			return array(
				'task'=>'Tarea',
				'lend'=>'Préstamo'
			);

		}

	}

}