<?php

namespace Goteo\Model\User {

    class Donor extends \Goteo\Core\Model {

        public
            $user,
            $amount,
            $name,
            $nif,
            $address,
            $zipcode,
            $location,
            $country,
            $numproj,
            $year = 2012,
            $edited = 0,
            $confirmed = 0;


        /**
         * Get invest data if a user is a donor
         * @param varcahr(50) $id  user identifier
         */
	 	public static function get ($id) {
            
            $year = '2012';
            $year1 = '2013';
                    
            try {
                
                // primero saber si es donante
                $sql = "SELECT COUNT(invest.id)
                        FROM invest
                        WHERE   invest.resign = 1
                        AND invest.status IN ('1', '3')
                        AND invest.invested >= '{$year}-01-01'
                        AND invest.invested < '{$year1}-01-01'
                        AND invest.user = :id
                        ";
                $query = static::query($sql, array(':id' => $id));
                $donativo = $query->fetchColumn();
                if (empty($donativo)) {
                    return false;
                } else {
                    
                    // si ya ha introducido los datos, sacamos de user_donation
                    $sql = "SELECT * FROM user_donation WHERE user = :id AND year = '{$year}'";
                    $query = static::query($sql, array(':id'=>$id));
                    if ($donation = $query->fetchObject(__CLASS__)) {
                        return $donation;
                    } else {
                        // sino sacamos de invest_address
                        $sql = "SELECT  
                                    user.id as user,
                                    SUM(invest.amount) as amount,
                                    IF(invest_address.name,
                                        invest_address.name,
                                        user.name) as name,
                                    invest_address.nif as nif,
                                    IFNULL(invest_address.address, user_personal.address) as address,
                                    IFNULL(invest_address.zipcode, user_personal.zipcode) as zipcode,
                                    IFNULL(invest_address.country, user_personal.country) as country,
                                    COUNT(DISTINCT(invest.project)) as numproj,
                                    CONCAT('{$year}') as year 
                                FROM  invest
                                INNER JOIN user ON user.id = invest.user
                                LEFT JOIN invest_address ON invest_address.invest = invest.id
                                LEFT JOIN user_personal ON user_personal.user = invest.user
                                WHERE   invest.resign = 1
                                AND invest.user = :id
                                AND invest.status IN ('1', '3')
                                AND invest.invested >= '{$year}-01-01'
                                AND invest.invested < '{$year1}-01-01'
                                GROUP BY invest.user
                                ORDER BY user.email ASC
                            ";
                        $query = static::query($sql, array(':id'=>$id));
                        $donation = $query->fetchObject(__CLASS__);
                        return $donation;
                    }
                }
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {}

		/*
		 *  Guarda los datos de donativo de un usuario
		 */
		public function save (&$errors = array()) {

            $fields = array(
                'user',
                'amount',
                'name',
                'nif',
                'address',
                'zipcode',
                'location',
                'country',
                'numproj',
                'year',
                'edited'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ', ';
                $set .= "$field = :$field";
                $values[":$field"] = $this->$field;
            }

			try {
	            $sql = "REPLACE INTO user_donation (".implode(', ', $fields).") VALUES (".implode(', ', array_keys($values)).")";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
				return false;
			}

		}

        public static function setConfirmed($user, $year = '2012') {
			try {
	            $sql = "UPDATE user_donation SET confirmed = 1 WHERE user = :user AND year = :year";
				if (self::query($sql, array(':user'=>$user, 'year'=>$year))) {
                    return true;
                } else {
                    return false;
                }
			} catch(\PDOException $e) {
				$errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
				return false;
			}
        }
        
        
	}

}