<?php

namespace Goteo\Library {

    use Goteo\Core\Model;

    class Worth {
		
        /*
         * Devuelve el nombre de un nivel por id
         */
		public static function get ($id) {

            $values = array(':id'=>$id, ':lang' => \LANG);
            $sql = "SELECT
                        worthcracy.id as id,
                        IFNULL(worthcracy_lang.name, worthcracy.name) as name
                    FROM worthcracy
                    LEFT JOIN worthcracy_lang
                        ON  worthcracy_lang.id = worthcracy.id
                        AND worthcracy_lang.lang = :lang
                    WHERE worthcracy.id = :id
                    ";

            $query = Model::query($sql, $values);
            $level = $query->fetchObject();
            if (!empty($level->name))
                return $level->name;

            return false;
		}

        /*
         * Devuelve los niveles de meritocracia
         */
		public static function getAll () {
            $array = array();
            $values = array(':lang' => \LANG);
            $sql = "SELECT
                        worthcracy.id as id,
                        IFNULL(worthcracy_lang.name, worthcracy.name) as name,
                        worthcracy.amount as amount
                    FROM worthcracy
                    LEFT JOIN worthcracy_lang
                        ON  worthcracy_lang.id = worthcracy.id
                        AND worthcracy_lang.lang = :lang
                    ORDER BY worthcracy.amount ASC
                    ";

            $query = Model::query($sql, $values);
            foreach ( $query->fetchAll(\PDO::FETCH_CLASS) as $worth) {
                $array[$worth->id] = $worth;
            }
            return $array;
		}

        /*
         * Devuelve el importe para el siguiente nivel
         * @TODO tener en cuenta el nivel actual
         */
		public static function abitmore ($amount) {

            if (!is_numeric($amount))
                return $amount;

            $values = array(':amount'=>$amount, ':lang' => \LANG);
            $sql = "SELECT
                        IFNULL(worthcracy_lang.name, worthcracy.name) as name,
                        worthcracy.amount as amount
                    FROM worthcracy
                    LEFT JOIN worthcracy_lang
                        ON  worthcracy_lang.id = worthcracy.id
                        AND worthcracy_lang.lang = :lang
                    WHERE worthcracy.amount > :amount
                    ";

            $query = Model::query($sql, $values);
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
            
            $values = array(':amount'=>$amount, ':lang' => \LANG);
            $sql = "SELECT
                        worthcracy.id as id,
                        IFNULL(worthcracy_lang.name, worthcracy.name) as name
                    FROM worthcracy
                    LEFT JOIN worthcracy_lang
                        ON  worthcracy_lang.id = worthcracy.id
                        AND worthcracy_lang.lang = :lang
                    WHERE worthcracy.amount <= :amount
                    ORDER BY worthcracy.amount DESC
                    LIMIT 1
                    ";

            $query = Model::query($sql, $values);
            return $query->fetchObject();
		}

	}
	
}