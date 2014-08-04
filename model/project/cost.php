<?php

namespace Goteo\Model\Project {

    use Goteo\Core\Error,
        Goteo\Library\Text;
    
    class Cost extends \Goteo\Core\Model {

        public
            $id,
            $project,
            $cost,
			$description,
            $type = 'task',
            $amount,
            $required,
            $from,
			$until;

	 	public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM cost WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public static function getAll ($project, $lang = null) {
            try {
                $array = array();

	            if(self::default_lang($lang)=='es') {
	                $different_select=" IFNULL(cost_lang.cost, cost.cost) as cost,
                        				IFNULL(cost_lang.description, cost.description) as description";
	                }
	            else {
	                    $different_select=" IFNULL(cost_lang.cost, IFNULL(eng.cost, cost.cost)) as cost,
                        					IFNULL(cost_lang.description, IFNULL(eng.description, cost.description)) as description";
	                    $eng_join=" LEFT JOIN cost_lang as eng
	                                    ON  eng.id = cost.id
                                        AND eng.project = :project
	                                    AND eng.lang = 'en'";
	                }                

                $sql = "
                    SELECT
                        cost.id as id,
                        cost.project as project,
                        $different_select,
                        cost.type as type,
                        cost.amount as amount,
                        cost.required as required,
                        cost.from as `from`,
                        cost.until as `until`
                    FROM cost
                    LEFT JOIN cost_lang
                        ON  cost_lang.id = cost.id
                        AND cost_lang.project = :project
                        AND cost_lang.lang = :lang
                    $eng_join
                    WHERE cost.project = :project
                    ORDER BY cost.id ASC";

				$query = self::query($sql, array(':project'=>$project,':lang'=>$lang));
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                    $array[$item->id] = $item;
                }
				return $array;
			} catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
			}
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->project))
                $errors[] = 'No hay proyecto al que asignar el coste';
                //Text::get('validate-cost-noproject');
/*
            if (empty($this->cost))
                $errors[] = 'No hay descripción de coste';
                //Text::get('mandatory-cost-name');

            if (empty($this->type))
                $errors[] = 'No hay tipo de coste';
                //Text::get('mandatory-cost-description');
*/
            if (empty($this->from) || $this->from == '0000-00-00') {
                $this->from = date('Y-m-d');
            }

            if (empty($this->until) || $this->until == '0000-00-00') {
                $this->until = date('Y-m-d');
            }

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {

			$fields = array(
				'id',
				'project',
				'cost',
				'description',
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
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO cost SET " . $set;
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
				return true;
			} catch(\PDOException $e) {
                $errors[] = "El coste {$this->cost} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

		public function saveLang (&$errors = array()) {

			$fields = array(
				'id'=>'id',
				'project'=>'project',
				'lang'=>'lang',
				'cost'=>'cost_lang',
				'description'=>'description_lang'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field=>$ffield) {
				if ($set != '') $set .= ", ";
				$set .= "`$field` = :$field ";
				$values[":$field"] = $this->$ffield;
			}

			try {
				$sql = "REPLACE INTO cost_lang SET " . $set;
				self::query($sql, $values);
            	
				return true;
			} catch(\PDOException $e) {
                $errors[] = "El coste {$this->cost} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
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
		public function remove (&$errors = array()) {
			$values = array (
				':project'=>$this->project,
				':id'=>$this->id,
			);

            try {
                self::query("DELETE FROM cost WHERE id = :id AND project = :project", $values);
				return true;
			} catch (\PDOException $e) {
                $errors[] = 'No se ha podido quitar el coste del proyecto ' . $this->project . ' ' . $e->getMessage();
                //Text::get('remove-cost-fail');
                return false;
			}
		}

		public static function types() {
			return array (
				'task'=>Text::get('cost-type-task'),
				'structure'=>Text::get('cost-type-structure'),
				'material'=>Text::get('cost-type-material')
			);
		}

	}

}