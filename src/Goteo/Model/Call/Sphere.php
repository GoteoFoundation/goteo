<?php

namespace Goteo\Model\Call {

    use Goteo\Application\Lang;
    use Goteo\Application\Config;
    use Goteo\Application\Exception\ModelException;

    class Sphere extends \Goteo\Core\Model {

        protected $Table = 'call_sphere';

        public
            $id,
            $call;


        /**
         * Get the sphere for a call
         * @param varcahr(50) $id  Call identifier
         * @return array of spheres identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT sphere FROM call_sphere WHERE call = ?", array($id));
                $spheres = $query->fetchAll();
                foreach ($spheres as $sphere) {
                    $array[$sphere[0]] = $sphere[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new ModelException($e->getMessage());
            }
		}

        /**
         * Get all spheres available
         *
         * @param void
         * @return array
         */
		public static function getAll () {
            $lang = Lang::current();
            $array = array ();
            try {

                if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(sphere_lang.name, sphere.name) as name";
                }
                else {
                    $different_select=" IFNULL(sphere_lang.name, IFNULL(eng.name,sphere.name)) as name";
                    $eng_join=" LEFT JOIN sphere_lang as eng
                                    ON  eng.id = sphere.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                            sphere.id as id,
                            $different_select
                        FROM    sphere
                        LEFT JOIN sphere_lang
                            ON  sphere_lang.id = sphere.id
                            AND sphere_lang.lang = :lang
                        $eng_join
                        ORDER BY name ASC ";

                $query = static::query($sql, array(':lang'=>$lang));
                $spheres = $query->fetchAll();
                foreach ($spheres as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new ModelException($e->getMessage());
            }
		}

        /**
         * Get all spheres for this project by name
         *
         * @param void
         * @return array
         */
		public static function getNames ($call = null, $limit = null) {
            $lang = Lang::current();
            $array = array ();

            try {
                $sqlFilter = "";
                if (!empty($call)) {
                    $sqlFilter = " WHERE sphere.id IN (SELECT sphere FROM call_sphere WHERE call = '$call')";
                }

                if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(sphere_lang.name, sphere.name) as name";
                }
                else {
                    $different_select=" IFNULL(sphere_lang.name, IFNULL(eng.name,sphere.name)) as name";
                    $eng_join=" LEFT JOIN sphere_lang as eng
                                    ON  eng.id = sphere.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                            sphere.id,
                            $different_select
                        FROM sphere
                        LEFT JOIN sphere_lang
                            ON  sphere_lang.id = sphere.id
                            AND sphere_lang.lang = :lang
                        $eng_join
                        $sqlFilter
                        ORDER BY `order` ASC ";

                if (!empty($limit)) {
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>$lang));
                $spheres = $query->fetchAll();
                foreach ($spheres as $cat) {
                    $array[$cat[0]] = $cat[1];
                    $array[post] = $cat[2];
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
                //Text::get('validate-open_tag-empty');

            if (empty($this->project))
                $errors[] = 'No hay ningun proyecto al que asignar';
                //Text::get('validate-open_tag-noproject');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO project_open_tag (project, open_tag) VALUES(:project, :open_tag)";
                $values = array(':project'=>$this->project, ':open_tag'=>$this->id);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La categoria {$open_tag} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $call id de call
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':open_tag'=>$this->id,
			);

			try {
                self::query("DELETE FROM project_open_tag WHERE open_tag = :open_tag AND project = :project", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' del proyecto ' . $this->project . ' ' . $e->getMessage();
                //Text::get('remove-open_tag-fail');
                return false;
			}
		}

	}

}
