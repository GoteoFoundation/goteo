<?php

namespace Goteo\Model\Project {

    class Cost extends \Goteo\Core\Model {

        public
            $id,
            $project,
            $cost,
            $type,
            $amount,
            $required,
            $from,
			$until;

	 	public static function get ($id) {}

		public function save ($data, &$errors = array()) {
//			echo 'Save cost <pre>' . print_r($data, 1) . '</pre>';

			$fields = array(
				'cost',
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
				$values[':id'] = $this->id;
				$values[':project'] = $this->project;

				$sql = "UPDATE cost SET " . $set . " WHERE id = :id AND project = :project";
				if ($res = self::query($sql, $values)) {
					foreach ($fields as $field) {
						if (isset($data[$field])) {
							$this->$field = $data[$field];
						}
					}
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

		public static function create ($project, $data, &$errors) {
//			echo 'New cost <pre>' . print_r($data, 1) . '</pre>';
			$fields = array(
				'cost',
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

		/**
		 * Quitar un coste de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla cost
		 * @param array $errors
		 * @return boolean
		 */
		public function remove ($project, $id, &$errors = array()) {
			$values = array (
				':project'=>$project,
				':id'=>$id,
			);

			if (self::query("DELETE FROM cost WHERE id = :id AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar el coste del proyecto ' . $project;
				return false;
			}
		}


	}

}