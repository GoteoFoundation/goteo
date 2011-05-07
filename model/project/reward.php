<?php

namespace Goteo\Model\Project {

    class Reward extends \Goteo\Core\Model {

        public
            $id,
			$project,
			$reward,
			$description,
			$type = 'social',
			$icon,
			$license,
			$amount,
			$units,
            $taken, // recompensas comprometidas por aporte
            $none; // si no quedan unidades de esta recompensa

	 	public static function get ($id) {
            try {
                $query = static::query("SELECT * FROM reward WHERE id = :id", array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public static function getAll ($project, $type = 'social') {
            try {
                $array = array();
				$query = self::query("SELECT * FROM reward WHERE project = ? AND type= ? ORDER BY id ASC", array($project, $type));
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

        /**
         * Calcula y actualiza las unidades de recompensa comprometidas por aporte
         * @param void
         * @return numeric
         */
        public function getTaken () {

            // cuantas de esta recompensa en aportes no cancelados
            $sql = "SELECT
                        COUNT(invest_reward.reward) as taken
                    FROM invest_reward
                    INNER JOIN invest
                        ON invest.id = invest_reward.invest
                        AND invest.status <> 2
                        AND invest.project = :project
                    WHERE invest_reward.reward = :reward
                ";

            $values = array(
                ':project' => $this->project,
                ':reward' => $this->id
            );

            $query = self::query($sql, $values);
            if ($taken = $query->fetchColumn()) {
                return $taken;
            } else {
                return 0;
            }
        }

		public static function icons($type = 'social') {
            $icons = array(
                'file' => 'Archivos digitales',
                'money' => 'Dinero',
                'code' => 'Código fuente',
                'service' => 'Servicios',
                'manual' => 'Manuales');

			if ($type == 'individual') {
				$icons['product'] = 'Producto';
			}

            return $icons;
		}

		public static function licenses() {
            return array(
                'ohl' => 'Open Hardware',
                'cc' => 'Creative Commons',
                'gpl' => 'General Public',
                'odbl' => 'Open Database',
                'xoln' => 'Red Abierta',
                'agpl' => 'GNU Affero');
		}

	}

}