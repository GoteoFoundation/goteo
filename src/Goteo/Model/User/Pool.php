<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\User;

use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Application\Exception\ModelException;

/**
 * Class Pool
 * @package Goteo\Model\User
 *
 *  Gestiona la reserva de Gotas obtenidas por aportes tipo crédito a proyectos que fallan
 *
 */
class Pool extends \Goteo\Core\Model {
    protected $Table = 'user_pool';
    public
        $user,
        $amount;


    /**
     * Recupera la reserva de este usario
     * @param varcahr(50) $id  user identifier
     * @return object $pool
     */
 	public static function get ($id) {
        if($id instanceOf User) {
            $user = $id;
        } else {
            $user = User::get($id);
        }
        if( ! $user instanceOf User) {
            throw new ModelException("User [$id] not found!");
        }
        try {
            $sql = "SELECT * FROM user_pool WHERE user = :user";

            $query = static::query($sql, array(':user' => $user->id));
            $pool = $query->fetchObject(__CLASS__);
            // si no tiene reserva devolvemos una instancia válida
            if (!$pool instanceof Pool) {
                $pool = new self;
                $pool->user = $user->id;
                $pool->amount = 0;
            }

            return $pool;


        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

    /**
     * Recalculates the proper amount for the pool based on Invests Status went to pool
     * @return [type] [description]
     */
    public function calculate($save = false) {
        $sql = "SELECT SUM(amount) AS total FROM invest WHERE user=:user AND method!='pool' AND status=:status";
        $query = static::query($sql, [':user' => $this->user, ':status' => Invest::STATUS_TO_POOL]);
        $total_to_pool = (int) $query->fetchColumn();

        $sql = "SELECT SUM(amount) AS total FROM invest WHERE user=:user AND method='pool' AND status IN (:status1, :status2)";
        $query = static::query($sql, [':user' => $this->user, ':status1' => Invest::STATUS_PAID, ':status2' => Invest::STATUS_CHARGED]);
        $total_from_pool = (int) $query->fetchColumn();

        $diff = max(0, $total_to_pool - $total_from_pool);
        // echo "[total_from_pool: $total_from_pool total_to_pool: $total_to_pool expected: $diff current: {$this->amount}]\n";die;
        $this->amount = $diff;
        if($save) {
            $errors = [];
            if(!$this->save($errors)) {
                throw new ModelException(implode("\n", $errors));
            }
        }
        return $this;
    }

    /**
     * Recupera el importe de gotas en la reserva de este usario
     * @param varcahr(50) $id  user identifier
     * @return integer  $amount
     */
 	public function getAmount () {
        return (int) $this->amount;
	}

	public function validate(&$errors = []) {
        if (empty($this->amount)) return false;
        if (empty($this->user)) return false;
    }

	/*
	 *  Guarda la clave del usuario
	 */
	public function save (&$errors = []) {

        $values = [':user' => $this->user, ':amount' => $this->amount];

		try {
            $sql = "REPLACE INTO user_pool (user, amount) VALUES(:user, :amount)";
            return (bool) self::query($sql, $values);

		} catch(\PDOException $e) {
			$errors[] = "Error saving pool data!" . $e->getMessage();
			return false;
		}

	}


    /**
     * Refunds a investion to the pool
     * @deprecated just use ->calculate(true) instead
     *
     * @param $invest invest instance
     * @return Pool object
     */
    public static function refundInvest(Invest $invest, &$errors = []) {

        // iniciar instancia de reserva
        $pool = static::get($invest->getUser());
        $pool->calculate();
        // grabar reserva
        return $pool->save($errors);

    }

    /**
     * quita gotas de la reserva
     *
     * @param $amount importe a quitar
     * @return boolean
     */
    public function withdraw($amount, &$errors = []) {

        // añadir amount
        $this->amount -= $amount;

        if ($this->amount < 0) {
            $errors[] = "Not enough pool to withdraw";
            return false;
        }

        // grabar reserva
        $this->save($errors);

        return true;
    }

    /*
     * ver cual es el último proyecto
     * (en el que se usaron gotas o el que guardó las primeras gotas)
     */
    public function lastProject() {
        $sql = "SELECT project FROM invest WHERE user = ? AND ( method = 'pool' OR pool = 1 ) AND !ISNULL(project) ORDER BY id desc LIMIT 1";
        $query = static::query($sql, array($this->user));
        $last = $query->fetchColumn();
        return $last;

    }

}

