<?php

namespace Goteo\Model\Project {

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
                return false;
            }
		}

		public static function getAll ($project) {
            try {
				$query = self::query("SELECT * FROM cost WHERE project = ?", array($project));
				$items = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
				return $items;
			} catch (\PDOException $e) {
				return array();
			}
		}

		public function validate(&$errors = array()) {}

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
				if ($res = self::query($sql, $values))  {

					if (empty($this->id)) $this->id = \PDO::lastInsertId;

					return true;
				}
				else {
					echo "$sql<br /><pre>" . print_r($values, 1) . "</pre>";
				}
			} catch(\PDOException $e) {
				$errors[] = $e->getMessage();
				$errors[] = "El coste {$this->cost} no se ha grabado correctamente. Por favor, revise los datos.";
				return false;
			}
		}

		/*
		public static function create ($project, $data, &$errors) {
//			echo 'New cost <pre>' . print_r($data, 1) . '</pre>';
			$fields = array(
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
				$values[":$field"] = $data[$field];
			}

			if (!empty($values)) {
				$set .= ", id='', project = :project";
				$values[':project'] = $project;

				$sql = "INSERT INTO cost SET " . $set;
				if ($res = self::query($sql, $values)) {
					return true;
				} else {
					$errors[] = "El coste {$data['cost']} no se ha grabado correctamente. Por favor, revise los datos.";
					return false;
				}
			}
			else {
				return true;
			}
		}
		 * 
		 */


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

			if (self::query("DELETE FROM cost WHERE id = :id AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar el coste del proyecto ' . $project;
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