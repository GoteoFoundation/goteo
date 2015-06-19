<?php

namespace Goteo\Model\Project {

    use Goteo\Application\Lang;
    use Goteo\Application\Config;

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
                    $array[$cat[0]] = $cat[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all categories available
         *
         * @param void
         * @return array
         */
		public static function getAll () {

            $array = array ();
            try {

                if(self::default_lang(Lang::current()) === Config::get('lang')) {
                $different_select=" IFNULL(category_lang.name, category.name) as name";
                }
                else {
                    $different_select=" IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name";
                    $eng_join=" LEFT JOIN category_lang as eng
                                    ON  eng.id = category.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                        category.id as id,
                        $different_select
                    FROM    category
                    LEFT JOIN category_lang
                        ON  category_lang.id = category.id
                        AND category_lang.lang = :lang
                    $eng_join
                    ORDER BY name ASC
                        ";

                $query = static::query($sql, array(':lang'=>Lang::current()));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    // la 15 es de testeos
                    if ($cat[0] == 15) continue;
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all categories for this project by name
         *
         * @param void
         * @return array
         */
		public static function getNames ($project = null, $limit = null, $lang = null) {
            if(empty($lang)) $lang = Lang::current();
            $array = array ();
            try {
                $sqlFilter = "";
                if (!empty($project)) {
                    $sqlFilter = " WHERE category.id IN (SELECT category FROM project_category WHERE project = '$project')";
                }

                if(self::default_lang($lang)=='es') {
                $different_select=" IFNULL(category_lang.name, category.name) as name";
                }
                else {
                    $different_select=" IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name";
                    $eng_join=" LEFT JOIN category_lang as eng
                                    ON  eng.id = category.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                            category.id,
                            $different_select
                        FROM category
                        LEFT JOIN category_lang
                            ON  category_lang.id = category.id
                            AND category_lang.lang = :lang
                        $eng_join
                        $sqlFilter
                        ORDER BY `order` ASC
                        ";


                if (!empty($limit)) {
                    $sql .= " LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>$lang));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    // la 15 es de testeos
                    if ($cat[0] == 15) continue;
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ninguna categoria para guardar';
                //Text::get('validate-category-empty');

            if (empty($this->project))
                $errors[] = 'No hay ningun proyecto al que asignar';
                //Text::get('validate-category-noproject');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO project_category (project, category) VALUES(:project, :category)";
                $values = array(':project'=>$this->project, ':category'=>$this->id);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La categoria {$category} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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

			try {
                self::query("DELETE FROM project_category WHERE category = :category AND project = :project", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' del proyecto ' . $this->project . ' ' . $e->getMessage();
                //Text::get('remove-category-fail');
                return false;
			}
		}

	}

}
