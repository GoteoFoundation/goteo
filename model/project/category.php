<?php

namespace Goteo\Model\Project {
    
    class Category extends \Goteo\Core\Model {

        public
            $id,
            $project;


        /**
         * Get the categories for a project
         * @param varcahr(50) $id  Project identifier
         * @return array of categories identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT category FROM project_category WHERE project = ?", array($id));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[] = $cat[0];
                }

                return $array;
            } catch(\PDOException $e) {
				echo $e->getMessage();
                return false;
            }
		}

        /**
         * Get all categories available
         *
         * @param void
         * @return array
         */
		public static function getAll () {
            return array(
                1=>'Educación',
                2=>'Economía solidaria',
                3=>'Empresa abierta',
                4=>'Formación técnica',
                5=>'Desarrollo',
                6=>'Software',
                7=>'Hardware');
		}

		public function validate(&$errors = array()) {}

		/*
		 *  save... al ser un solo campo quiza no lo usemos
		 */
		public function save (&$errors = array()) {

            $values = array(':project'=>$this->project, ':category'=>$this->id);

			try {
	            $sql = "REPLACE INTO project_category (project, category) VALUES(:project, :category)";
				if ($res = self::query($sql, $values))  {
					return true;
				}
				else {
					echo "$sql<br /><pre>" . print_r($values, 1) . "</pre>";
				}
			} catch(\PDOException $e) {
				$errors[] = $e->getMessage();
				$errors[] = "La categoria {$category} no se ha grabado correctamente. Por favor, revise los datos.";
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
				':category'=>$this->id,
			);

			if (self::query("DELETE FROM project_category WHERE category = :category AND project = :project", $values)) {
				return true;
			}
			else {
				$errors[] = 'No se ha podido quitar la palabra clave ' . $this->id . ' del proyecto ' . $this->project;
				return false;
			}
		}

	}
    
}