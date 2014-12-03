<?php

namespace Goteo\Model\Call {

    class Category extends \Goteo\Core\Model {

        public
            $id,
            $call;


        /**
         * Get the categories for a call
         * @param varcahr(50) $id  Call identifier
         * @return array of categories identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT category FROM call_category WHERE `call` = :call", array(':call'=>$id));
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

            $lang=\LANG;

            $array = array ();

            try {
                if(self::default_lang(\LANG)=='es') {
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

                $query = static::query($sql, array(':lang'=>\LANG));
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
         * Get all categories for this call by name
         *
         * @param void
         * @return array
         */
		public static function getNames ($call = null, $limit = null) {

            $lang=\LANG;
            $array = array ();
            try {
                $sqlFilter = "";
                if (!empty($call)) {
                    $sqlFilter = " WHERE category.id IN (SELECT category FROM call_category WHERE `call` = '$call')";
                }

                if(self::default_lang(\LANG)=='es') {
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
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>\LANG));
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

            if (empty($this->call))
                $errors[] = 'No hay ningun proyecto al que asignar';
                //Text::get('validate-category-nocall');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO call_category (`call`, category) VALUES(:call, :category)";
                $values = array(':call'=>$this->call, ':category'=>$this->id);
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
		 * @param varchar(50) $call id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':call'=>$this->call,
				':category'=>$this->id,
			);

			try {
                self::query("DELETE FROM call_category WHERE category = :category AND `call` = :call", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' de la convocatoria ' . $this->call . ' ' . $e->getMessage();
                //Text::get('remove-category-fail');
                return false;
			}
		}

	}
    
}