<?php

namespace Goteo\Model\Project {

    class Reward extends \Goteo\Core\Model {

        public
            $id,
			$project,
			$reward,
			$description,
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
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public static function getAll ($project, $type) {
            try {
                $array = array();
				$query = self::query("SELECT * FROM reward WHERE project = ? AND type= ? ORDER BY id DESC", array($project, $type));
				foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item ) {
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
                $errors[] = 'No hay proyecto al que asignar la recompensa/rettorno';

            if (empty($this->reward))
                $errors[] = 'No hay nombre de recompensa/retorno';

            if (empty($this->type))
                $errors[] = 'No hay tipo de recompensa/retorno';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;
            
			$fields = array(
				'id',
				'project',
				'reward',
				'description',
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
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
        		return true;
			} catch(\PDOException $e) {
				$errors[] = "El retorno {$this->reward} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
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

            try {
                self::query("DELETE FROM reward WHERE id = :id AND project = :project", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el retorno '. $this->id. '. ' . $e->getMessage();
                return false;
			}
		}

		public static function icons($type = 'social') {
            $icons = array(
                1=>'Archivos digitales',
                2=>'Dinero',
                3=>'Código fuente',
                4=>'Servicios',
                5=>'Manuales');

			if ($type == 'individual') {
				$icons[6] = 'Producto';
			}

            return $icons;
		}

		public static function licenses() {
            return array(
                1=>'Open Hardware',
                2=>'Creative Commons',
                3=>'General Public',
                4=>'Open Database',
                5=>'Red Abierta',
                6=>'GNU Affero');
		}

	}

}