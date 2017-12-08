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

class Account extends \Goteo\Core\Model {

    public
        $project,
        $bank,
        $bank_owner,
        $paypal,
        $paypal_owner,
        $fee,
        $skip_login = false,
        $allowpp; // para permitir usar el boton paypal


    /**
     * Get the accounts for a project
     * @param varcahr(50) $id  Project identifier
     * @return array of accounts
     */
 	public static function get ($id) {

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
                $accounts->fee = Config::get('fee');
                return $accounts;
            }
        } catch(\PDOException $e) {
			throw new \Goteo\Core\Exception($e->getMessage());
        }
	}

	public function validate(&$errors = array()) {
        // Estos son errores que no permiten continuar
        if (empty($this->project)) {
            $errors[] = 'No hay ningun proyecto al que asignar cuentas';
            //Text::get('validate-account-noproject');
            return false;
        }
        if (!isset($this->fee)) $this->fee = Config::get('fee');

        return true;
    }

	public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

		try {
            $sql = "REPLACE INTO project_account (project, bank, bank_owner, paypal, paypal_owner, allowpp, fee, skip_login)
             VALUES(:project, :bank, :bank_owner, :paypal, :paypal_owner, :allowpp, :fee, :skip_login)";
            $values = array(':project' => $this->project, ':bank' => $this->bank, ':bank_owner' => $this->bank_owner, ':paypal' => $this->paypal, ':paypal_owner' => $this->paypal_owner, ':allowpp' => $this->allowpp, ':fee' => $this->fee, ':skip_login' => $this->skip_login);
			self::query($sql, $values);

            // Update in contract if exists
            $sql = "UPDATE IGNORE contract SET paypal=:paypal WHERE project=:project";
            self::query($sql, [':project' => $this->project, ':paypal' => $this->paypal]);
			return true;
		} catch(\PDOException $e) {
			$errors[] = "Las cuentas no se han asignado correctamente. Por favor, revise los datos." . $e->getMessage();
            return false;
		}

	}

    // comprobar, para aportar con PayPal tiene que tener puesta la cuenta
    public static function getAllowpp ($id) {

        try {
            $query = static::query("SELECT paypal FROM project_account WHERE project = ?", array($id));
            $paypal = $query->fetchColumn();
            return (!empty($paypal));
        } catch(\PDOException $e) {
            return false;
        }

        /*
         * Esto al final no se usa, se podrá aprovechar el campo para marcar que la cuenta está verificada (o algo así)
        try {
            $query = static::query("SELECT allowpp FROM project_account WHERE project = ?", array($id));
            $allowpp = $query->fetchColumn(0);
            return $allowpp;
        } catch(\PDOException $e) {
			throw new \Goteo\Core\Exception($e->getMessage());
            return false;
        }
        */
    }


}

