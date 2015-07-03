<?php

namespace Goteo\Model\Call {

    use Goteo\Application\Lang;
    use Goteo\Application\Config;

    class Icon extends \Goteo\Core\Model {

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
                $query = static::query("SELECT icon FROM call_icon WHERE `call` = :call", array(':call'=>$id));
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
            $lang = Lang::current();
            $array = array ();
            try {

                if(self::default_lang($lang) === Config::get('lang')) {
                    $different_select=" IFNULL(icon_lang.name, icon.name) as name";
                    }
                else {
                        $different_select=" IFNULL(icon_lang.name, IFNULL(eng.name, icon.name)) as name";
                        $eng_join=" LEFT JOIN icon_lang as eng
                                        ON  eng.id = icon.id
                                        AND eng.lang = 'en'";
                    }

                $sql = "
                    SELECT
                        icon.id as id,
                        $different_select
                    FROM    icon
                    LEFT JOIN icon_lang
                        ON  icon_lang.id = icon.id
                        AND icon_lang.lang = :lang
                    $eng_join
                    ORDER BY name ASC
                    ";

                $query = static::query($sql, array(':lang'=>$lang));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
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
            $lang = Lang::current();
            $array = array ();
            try {
                $sqlFilter = "";
                if (!empty($call)) {
                    $sqlFilter = " WHERE icon.id IN (SELECT icon FROM call_icon WHERE `call` = '$call')";
                }

                if(self::default_lang($lang) === Config::get('lang')) {
                    $different_select=" IFNULL(icon_lang.name, icon.name) as name";
                    }
                else {
                    $different_select=" IFNULL(icon_lang.name, icon.name) as name";
                    $eng_join=" LEFT JOIN icon_lang as eng
                                    ON  eng.id = icon.id
                                    AND eng.lang = 'en'";
                    }

                $sql = "SELECT
                            icon.id,
                            $different_select
                        FROM icon
                        LEFT JOIN icon_lang
                            ON  icon_lang.id = icon.id
                            AND icon_lang.lang = :lang
                        $eng_join
                        $sqlFilter
                        ORDER BY `order` ASC
                        ";
                if (!empty($limit)) {
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, array(':lang'=>$lang));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
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
                $errors[] = 'No hay ninguna icono para guardar';
                //Text::get('validate-icon-empty');

            if (empty($this->call))
                $errors[] = 'No hay ningun proyecto al que asignar';
                //Text::get('validate-icon-nocall');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO call_icon (`call`, icon) VALUES(:call, :icon)";
                $values = array(':call'=>$this->call, ':icon'=>$this->id);
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "La icono {$icon} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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
				':icon'=>$this->id,
			);

			try {
                self::query("DELETE FROM call_icon WHERE icon = :icon AND `call` = :call", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el icono ' . $this->id . ' de la convocatoria ' . $this->call . ' ' . $e->getMessage();
                //Text::get('remove-icon-fail');
                return false;
			}
		}

	}

}
