<?php

namespace Goteo\Model {
    
    class Invest extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $project,
            $amount, //cantidad monetaria del aporte
            $status, //estado en el que se encuentra esta aportación: 0 pendiente, 1 cobrado (charged), 2 devuelto (returned)
            $anonymous, //no quiere aparecer en la lista de aportadores
            $resign, //renuncia a cualquier recompensa
            $invested, //fecha en la que se ha iniciado el aporte
            $charged, //fecha en la que se ha cargado el importe del aporte a la cuenta del usuario
            $returned, //fecha en la que se ha devuelto el importe al usurio por cancelación bancaria
            $rewards; //recompensas que le corresponden

        /*
         *  Devuelve datos de una inversión
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT  *
                    FROM    invest
                    WHERE   id = :id
                    ", array(':id' => $id));
                $invest = $query->fetchObject(__CLASS__);

				$query = self::query("
                    SELECT  *
                    FROM    reward
                    INNER JOIN reward
                        ON invest_reward.reward = reward.id
                    WHERE   invest_reward.invest = ?
                    ", array($id));
				$invest->rewards = $query->fetchAll(\PDO::FETCH_ASSOC);

                return $invest;
        }

        public function validate (&$errors = array()) { 
            if (!is_numeric($this->amount))
                $errors[] = 'La cantidad no es correcta';

            if (empty($this->user))
                $errors[] = 'Falta usuario';

            if (empty($this->project))
                $errors[] = 'Falta proyecto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'user',
                'project',
                'amount',
                'status',
                'anonymous',
                'resign',
                'invested',
                'charged',
                'returned'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if (!empty($this->$field)) {
                    if ($set != '') $set .= ", ";
                    $set .= "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }
            }

            try {
                $sql = "REPLACE INTO invest SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();
                return true;
            } catch(\PDOException $e) {
                $errors[] = "El aporte no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }

        /*
         * Obtenido por un proyecto
         */
        public static function invested ($project) {
            $query = static::query("
                SELECT  SUM(amount) as much
                FROM    invest
                WHERE   project = :project
                ", array(':project' => $project));
            $got = $query->fetchObject();
            if (!empty($got->much))
                return $got->much;
            else
                return 0;
        }

        public static function investors ($project) {
            //@TODO añadir los datos que sean necesarios
            $investors = array();

            $sql = "
                SELECT  invest.id as invest,
                        user.name as name,
                        invest.amount as amount
                FROM    invest
                INNER JOIN user ON invest.user = user.id
                WHERE   invest.project = ?
                AND     (invest.anonymous IS NULL OR invest.anonymous = 0)
                ";
            $query = self::query($sql, array($project));
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $investor) {
                $investors[] = $investor;
            }
            return $investors;
        }

        /*
         * Asignar a la aportación las recompensass a las que opta
         */
        public function setReward ($reward, $fulfill = null) {

            $values = array(
                ':invest' => $this->id,
                ':reward' => $reward,
                ':fulfill' => $fulfill
            );

            $sql = "REPLACE INTO invest_reward (invest, reward, fulfilled) VALUES (:invest, :reward, :fulfill)";
            if (self::query($sql, $values)) {
                return true;
            }
        }

        //@TODO metodos para aplicar cargo y para devolver

    }
    
}