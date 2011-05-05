<?php

namespace Goteo\Model\Project {

    use Goteo\Core\Error;
    
    class Cost extends \Goteo\Core\Model {

        public
            $id,
            $project,
            $cost,
			$description,
            $type,
            $amount,
            $required,
            $from,
			$until;

	 	public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM cost WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public static function getAll ($project) {
            try {
                $array = array();
				$query = self::query("SELECT * FROM cost WHERE project = ? ORDER BY id ASC", array($project));
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                    $array[$item->id] = $item;
                }
				return $array;
			} catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
			}
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->project))
                $errors[] = 'No hay proyecto al que asignar el coste';

            if (empty($this->cost))
                $errors[] = 'No hay descripción de coste';

            if (empty($this->type))
                $errors[] = 'No hay tipo de coste';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {

			$fields = array(
				'id',
				'project',
				'cost',
				'description',
				'type',
				'amount',
				'required',
				'from',
				'until'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "`$field` = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO cost SET " . $set;
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
				return true;
			} catch(\PDOException $e) {
                $errors[] = "El coste {$this->cost} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

		/**
		 * Quitar un coste de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla cost
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':id'=>$this->id,
			);

            try {
                self::query("DELETE FROM cost WHERE id = :id AND project = :project", $values);
				return true;
			} catch (\PDOException $e) {
                $errors[] = 'No se ha podido quitar el coste del proyecto ' . $this->project . ' ' . $e->getMessage();
                return false;
			}
		}

		public static function types() {
			return array (
				'task'=>'Tarea',
				'structure'=>'Infraestructura',
				'equip'=>'Equipo'
			);
		}

	}

}