<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Project;

use Goteo\Application\Config;
use Goteo\Core\Exception;
use Goteo\Core\Model;

class Account extends Model
{
    public
        $project,
        $bank,
        $bank_owner,
        $fee,
        $skip_login = false,
        $allowpp, // para permitir usar el boton paypal
        $allow_stripe;

    /**
     * @deprecated
     */
    public $paypal;

    /**
     * @deprecated
     */
    public $paypal_owner;

    /**
     * @throws Config\ConfigException
     * @throws Exception
     */
    public static function get($id)
    {

        try {
            $query = static::query("SELECT * FROM project_account WHERE project = ?", array($id));
            $accounts = $query->fetchObject(__CLASS__);
            if (!empty($accounts)) {
                // porcentaje de comisión por defecto
                if (!isset($accounts->fee)) $accounts->fee = Config::get('fee');

                return $accounts;
            } else {
                $accounts = new Account();
                $accounts->project = $id;
                $accounts->allowpp = false;
                $accounts->allow_stripe = false;
                $accounts->fee = Config::get('fee');
                return $accounts;
            }
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Config\ConfigException
     */
    public function validate(&$errors = array()): bool
    {
        if (empty($this->project)) {
            $errors[] = 'No hay ningun proyecto al que asignar cuentas';
            return false;
        }
        if (!isset($this->fee)) $this->fee = Config::get('fee');

        return true;
    }

    /**
     * @throws Config\ConfigException
     */
    public function save(&$errors = array()): bool
    {
        if (!$this->validate($errors)) return false;

        try {
            $sql = "
                REPLACE INTO project_account (project, bank, bank_owner, paypal, paypal_owner, allowpp, allow_stripe, fee, skip_login)
                VALUES(:project, :bank, :bank_owner, :paypal, :paypal_owner, :allowpp, :allow_stripe, :fee, :skip_login)
            ";
            $values = [
                ':project' => $this->project,
                ':bank' => $this->bank,
                ':bank_owner' => $this->bank_owner,
                ':paypal' => $this->paypal,
                ':paypal_owner' => $this->paypal_owner,
                ':allowpp' => $this->allowpp,
                ':allow_stripe' => $this->allow_stripe,
                ':fee' => $this->fee,
                ':skip_login' => $this->skip_login
            ];
            self::query($sql, $values);

            return true;
        } catch (\PDOException $e) {
            $errors[] = "Las cuentas no se han asignado correctamente. Por favor, revise los datos." . $e->getMessage();
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public static function getAllowpp(string $id): bool
    {
        try {
            $query = static::query("SELECT allowpp FROM project_account WHERE project = ?", array($id));
            return $query->fetchColumn();
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
            return false;
        }
    }

    public static function getAllowStripe(string $id): bool
    {
        try {
            $query = static::query("SELECT allow_stripe FROM project_account WHERE project = ?", array($id));
            return (bool) $query->fetchColumn();
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
            return false;
        }
    }
}
