<?php

namespace Goteo\Model\Project {

    class Reward extends \Goteo\Core\Model {

        public
            $id,
			$project,
			$reward,
			$type,
			$icon,
			$license,
			$amount,
			$units;

	 	public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM reward WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                return false;
            }
		}

		public static function getAll ($project, $type) {
            try {
				$query = self::query("SELECT * FROM reward WHERE project = :project AND type= :type", array($project, $type));
				$items = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
				return $items;
			} catch (\PDOException $e) {
				return array();
			}
		}

		public function save (&$errors = array()) {
			$fields = array(
				'id',
				'project',
				'reward',
				'type',
				'icon',
				'license',
				'amount',
				'units'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "$field = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO reward SET " . $set;
				$res = self::query($sql, $values);

				if (empty($this->id)) {
					$this->id = \PDO::lastInsertId;
				}

				return true;
			} catch (\PDOException $e) {
				$errors[] = "El retorno {$this->reward} no se ha grabado correctamente. Por favor, revise los datos.";
				return false;
			}
		}

		/**
		 * Quitar un retorno de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla reward
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':id'=>$this->id,
			);

			if (self::query("DELETE FROM reward WHERE id = :id AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar el retorno del proyecto ' . $this->project;
				return false;
			}
		}

		public function validate(&$errors = array()) {}

	}

}