<?php

namespace Goteo\Model\Project {
    
    class Keyword extends \Goteo\Core\Model {

	 public
		$id,
		$project,
		$keyword;


	 	public static function get ($id) {}

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
		public function save ($data, &$errors = array()) {
//			echo 'Save keyword <pre>' . print_r($data, 1) . '</pre>';
		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $project id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove ($project, $id, &$errors = array()) {
			$values = array (
				':project'=>$project,
				':id'=>$id,
			);

			if (self::query("DELETE FROM keyword WHERE id = :id AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar la palabra clave del proyecto ' . $project;
				return false;
			}
		}

		public static function create ($project, $keyword, &$errors) {
			$values = array (
				':project'=>$project,
				':keyword'=>trim($keyword),
			);

			if ($res = self::query("INSERT INTO keyword (id, project, keyword) VALUES('', :project, :keyword)", $values)) {
				return $res->fetchObject();
			}
			else {
				$errors[] = 'No se ha guardado la palabra clave ' . $keyword;
				return false;
			}
		}

	}
    
}