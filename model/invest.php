<?php

namespace Goteo\Model {
    
    class Invest extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $project,
            $account, // cuenta paypal
            $amount, //cantidad monetaria del aporte
            $preapproval, //clave del preapproval
            $payment, //clave del cargo
            $transaction, // id paypal de la transacción
            $status, //estado en el que se encuentra esta aportación: 0 pendiente, 1 cobrado (charged), 2 devuelto (returned)
            $anonymous, //no quiere aparecer en la lista de aportadores
            $resign, //renuncia a cualquier recompensa
            $invested, //fecha en la que se ha iniciado el aporte
            $charged, //fecha en la que se ha cargado el importe del aporte a la cuenta del usuario
            $returned, //fecha en la que se ha devuelto el importe al usurio por cancelación bancaria
            $rewards = array(); //datos de las recompensas que le corresponden

        // añadir los datos del cargo


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

				$query = static::query("
                    SELECT  *
                    FROM  invest_reward
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

            if (empty($this->account))
                $errors[] = 'Falta cuenta paypal o email';

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
                'account',
                'amount',
                'preapproval',
                'payment',
                'transaction',
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

                // y las recompensas
                foreach ($this->rewards as $reward) {
                    $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
                    self::query($sql, array(':invest'=>$this->id, ':reward'=>$reward));
                }

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
            //@FIXME, cada inversor muestra el aporte toal a este proyecto y la fecha del último aporte (cuando me lo confirme olivier)
            $investors = array();

            $sql = "
                SELECT  DISTINCT(user) as id
                FROM    invest
                WHERE   project = ?";

            $query = self::query($sql, array($project));
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $investor) {

                // para cada uno sacar: cantidad total aportada a este proyecto y fecha de último aporte
                $support = self::supported($investor['id'], $project);
                /* Aqui segun lo que nos haga Philipp */
                //$user = User::get($investor);
                $user = (object) array(
                    'name' => 'Platoniq',
                    'support' => array(1,2,3,4,5,6,7,8,9,11,12,13),
                    'avatar' => 'url',
                    'worth' => rand(1, 5),

                );

                $investors[] = (object) array(
                    'user' => $investor['id'],
                    'name' => $user->name,
                    'projects' => count($user->support),
                    'avatar' => $user->avatar,
                    'worth' => $user->worth,
                    'amount' => $support->total,
                    'date' => $support->date
                );
            }
            
            return $investors;
        }

        /*
         *  Aportaciones realizadas por un usaurio
         *  devuelve total y fecha de la última
         */
        public static function supported ($user, $project) {

            $sql = "
                SELECT  SUM(amount) as total, DATE_FORMAT(invested, '%d/%m/%Y') as date
                FROM    invest
                WHERE   user = :user
                AND     project = :project
                AND     status != 2
                ORDER BY invested DESC";

            $query = self::query($sql, array(':user' => $user, ':project' => $project));
            return $query->fetchObject();
        }

        /*
         * Asignar a la aportación las recompensass a las que opta
         */
        public function setReward ($reward) {

            $values = array(
                ':invest' => $this->id,
                ':reward' => $reward
            );

            $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
            if (self::query($sql, $values)) {
                return true;
            }
        }

        /*
         *  Pone el preapproval key al registro del aporte
         */
        public function setPreapproval ($key) {

            $values = array(
                ':id' => $this->id,
                ':preapproval' => $key
            );

            $sql = "UPDATE invest SET preapproval = :preapproval WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }
            
        }

        /*
         *  Pone el pay key al registro del aporte y la fecha de cargo
         */
        public function setPayment ($key) {

            $values = array(
                ':id' => $this->id,
                ':payment' => $key,
                ':charged' => date('Y-m-d')
            );

            $sql = "UPDATE  invest
                    SET
                        payment = :payment,
                        charged = :charged, 
                        status = 1
                    WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         *  marca un aporte como devuelto (devuelto el dinero despues de haber sido cargado)
         * si hay que cancelar el preapproval se elimina el registro completamente
         */
        public function returnPayment () {

            $values = array(
                ':id' => $this->id,
                ':returned' => date('Y-m-d')
            );

            $sql = "UPDATE  invest
                    SET
                        returned = :returned,
                        status = 2
                    WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Eliminar esta aportacion y sus recompensas
         */
        public function cancel () {
            
            $values = array(
                ':id' => $this->id
            );

            $sql = "DELETE FROM invest WHERE id = :id";
            if (self::query($sql, $values)) {
                $sql = "DELETE FROM invest_reward WHERE invest = :id";
                self::query($sql, $values);

                return true;
            } else {
                return false;
            }

        }

    }
    
}