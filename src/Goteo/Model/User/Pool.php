<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\User {

    use Goteo\Model\Invest;
    /**
     * Class Pool
     * @package Goteo\Model\User
     *
     *  Gestiona la reserva de Gotas obtenidas por aportes tipo crédito a proyectos que fallan
     *
     */
    class Pool extends \Goteo\Core\Model {

        public
            $user,
            $amount;


        /**
         * Recupera la reserva de este usario
         * @param varcahr(50) $id  user identifier
         * @return object $pool
         */
	 	public static function get ($id) {

            try {
                $sql = "SELECT * FROM user_pool WHERE user = :id";

                $query = static::query($sql, array(':id' => $id));
                $pool = $query->fetchObject(__CLASS__);
                // si no tiene reserva devolvemos una instancia válida
                if (!$pool instanceof  \Goteo\Model\User\Pool) {

                    $pool = new self;
                    $pool->user = $id;
                    $pool->amount = 0;

                }

                return $pool;


            } catch(\PDOException $e) {
				throw new \Goteo\Application\Exception\ModelException($e->getMessage());
            }
		}

        /**
         * Recupera el importe de gotas en la reserva de este usario
         * @param varcahr(50) $id  user identifier
         * @return integer  $amount
         */
	 	public static function getAmount ($id) {

            try {
                $query = static::query("SELECT `amount` FROM user_pool WHERE user = ?", array($id));
                $amount = $query->fetchColumn();
                return (!empty($amount)) ? $amount : null;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            if (empty($this->amount)) return false;
            if (empty($this->user)) return false;
        }

		/*
		 *  Guarda la clave del usuario
		 */
		public function save (&$errors = array()) {

            $values = array(':user'=>$this->user, ':amount'=>$this->amount);

			try {
	            $sql = "REPLACE INTO user_pool (user, amount) VALUES(:user, :amount)";
				if (self::query($sql, $values)) {
                    return true;
                } else {
                    return false;
                }
			} catch(\PDOException $e) {
				$errors[] = "No se ha podido crear la reserva. Por favor, revise los datos." . $e->getMessage();
				return false;
			}

		}

        /**
         * Refunds a investion to the pool
         *
         * @param $invest invest instance
         * @return Pool object
         */
        public static function refundInvest(Invest $invest, &$errors = array()) {

            // iniciar instancia de reserva
            $pool = static::get($invest->user);

            // añadir amount
            $pool->amount += $invest->amount;

            // grabar reserva
            return $pool->save($errors);

        }

        /**
         * quita gotas de la reserva
         *
         * @param $amount importe a quitar
         * @return boolean
         */
        public static function withdraw($user, $amount, &$errors = array()) {

            // iniciar instancia de reserva
            $pool = static::get($user);

            // añadir amount
            $pool->amount -= $amount;

            if ($pool->amount < 0) {
                $error[] = "Not enough pool to withdraw";
                return false;
            }

            // grabar reserva
            $pool->save($errors);

            return true;
        }


        /*
         * ver cual es el último proyecto
         * (en el que se usaron gotas o el que guardó las primeras gotas)
         */
        public static function lastProject($user) {

            $sql = "SELECT project FROM invest WHERE user = ? AND ( method = 'pool' OR pool = 1 ) ORDER BY id desc LIMIT 1";
            $query = static::query($sql, array($user));
            $last = $query->fetchColumn();
            return $last;

        }

	}

}
