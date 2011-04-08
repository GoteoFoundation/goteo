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
                return false;
            }
		}

		public static function getAll ($project) {
            try {
				$query = self::query("SELECT * FROM support WHERE project = ?", array($project));
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
				if ($res = self::query($sql, $values))  {

					if (empty($this->id)) $this->id = self::insertId();

					return true;
				}
				else {
					echo "$sql<br /><pre>" . print_r($values, 1) . "</pre>";
				}
			} catch(\PDOException $e) {
				$errors[] = $e->getMessage();
				$errors[] = "La colaboración {$data['support']} no se ha grabado correctamente. Por favor, revise los datos.";
				return false;
			}
		}

		/*
		public static function create ($project, $data, &$errors) {
//			echo 'New support <pre>' . print_r($data, 1) . '</pre>';
			$fields = array(
				'support',
				'type',
				'description'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if (!empty($data[$field])) {
					if ($set != '') $set .= ", ";
					$set .= "$field = :$field ";
					$values[":$field"] = $data[$field];
				}
			}

			if (!empty($values)) {
				$set .= ", id='', project = :project";
				$values[':project'] = $project;

				$sql = "INSERT INTO support SET " . $set;
				if ($res = self::query($sql, $values)) {
					return $res->fetchObject();
				} else {
					$errors[] = "La colaboración {$data['support']} no se ha grabado correctamente. Por favor, revise los datos.";
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

			if (self::query("DELETE FROM support WHERE id = :id AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar la colaboracion del proyecto ' . $this->project;
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