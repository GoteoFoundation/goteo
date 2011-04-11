<?php

namespace Goteo\Library {

    use Goteo\Core\Model;

    class Worth {
		
        /*
         * Devuelve el nombre de un nivel por id
         */
		public static function get ($id) {

            $query = Model::query("SELECT name FROM worthcracy WHERE id = ?", array($id));
            $level = $query->fetchObject();
            if (!empty($level->name))
                return $level->name;

            return false;
		}

        /*
         * Devuelve los niveles de meritocracia
         */
		public static function getAll () {
			$query = Model::query("SELECT id, name, amount FROM worthcracy ORDER BY amount ASC");
            return $query->fetchAll(\PDO::FETCH_ASSOC);
		}

        /*
         * Devuelve el importe para el siguiente nivel
         * @TODO tener en cuenta el nivel actual
         */
		public static function abitmore ($amount) {

            if (!is_numeric($amount))
                return $amount;

			$query = Model::query("SELECT name, amount FROM worthcracy WHERE amount > :amount", array(':amount'=>$amount));
			$next = $query->fetchObject();
            $abit = $next->amount - $amount; //cuanto para el siguiente nivel
            
			return array('amount'=>$abit, 'name'=>$next->name);
		}

        /*
         * Devuelve el nombre de un nivel por importe acumulado
         */
		public static function reach ($amount) {
            if (!is_numeric($amount))
                return false;
            
            $query = Model::query("SELECT id, name FROM worthcracy WHERE amount <= ? ORDER BY amount DESC LIMIT 1", array($amount));
            return $query->fetchObject();
		}

	}
	
}