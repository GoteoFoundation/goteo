<?php

namespace Goteo\Model\Project {
    
    class Keyword extends \Goteo\Core\Model {

	 public
		$id,
		$project,
		$keyword;


	 	public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM keyword WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
				echo $e->getMessage();
                return false;
            }
		}

		public static function getAll ($project) {
            try {
				$query = self::query("SELECT * FROM keyword WHERE project = ?", array($project));
				$items = $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
				return $items;
			} catch (\PDOException $e) {
				echo $e->getMessage();
				return array();
			}
		}

		public function validate(&$errors = array()) {}

		/*
		 *  Sacar las palabras clave que son categorias
		 */
		public static function categories() {
			$list = array();

			if ($query = self::query("SELECT id, keyword FROM keyword WHERE category = 1")) {
				$keys = $query->fetchAll();
				foreach ($keys as $key) {
					$list[] = (object) array('id'=>$key['id'], 'keyword'=>$key['keyword']);
				}
			}

			return $list;
		}

		/*
		 *  Sacar las palabras clave relacionadas alos proyectos de cierta categoria
		 */
		// @TODO

		/*
		 *  save... al ser un solo campo quiza no lo usemos
		 */
		public function save (&$errors = array()) {

			$fields = array(
				'id',
				'project',
				'keyword'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "`$field` = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO keyword SET " . $set;
				if ($res = self::query($sql, $values))  {

					if (empty($this->id)) $this->id = \PDO::lastInsertId;

					return true;
				}
				else {
					echo "$sql<br /><pre>" . print_r($values, 1) . "</pre>";
				}
			} catch(\PDOException $e) {
				$errors[] = $e->getMessage();
				$errors[] = "La palabra clave {$this->keyword} no se ha grabado correctamente. Por favor, revise los datos.";
				return false;
			}
		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':id'=>$this->id,
			);

			if (self::query("DELETE FROM keyword WHERE id = :id AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar la palabra clave ' . $this->id . ' del proyecto ' . $this->project;
				return false;
			}
		}

	}
    
}