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
        $confirmed = 0,
        $pdf = null,
        $dates = array();

        /**
         * Get invest data if a user is a donor
         * @param varcahr(50) $id  user identifier
         */
        public static function get($id) {

            $year = '2012';
            // ESTA PRIMERA VEZ ESESPECIAL  porque el cif no lo tuvimos hasta el 2012
            $year0 = '2011';
            $year1 = '2013';

            try {

                // primero saber si es donante
                $sql = "SELECT COUNT(invest.id)
                        FROM invest
                        WHERE   invest.resign = 1
                        AND invest.status IN ('1', '3')
                        AND invest.invested >= '{$year0}-01-01'
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
                    $query = static::query($sql, array(':id' => $id));
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
                                AND invest.invested >= '{$year0}-01-01'
                                AND invest.invested < '{$year1}-01-01'
                                GROUP BY invest.user
                                ORDER BY user.email ASC
                            ";
                        $query = static::query($sql, array(':id' => $id));
                        $donation = $query->fetchObject(__CLASS__);
                        return $donation;
                    }
                }
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        public function getList($filters = array()) {

            $year = empty($filter['year']) ? '2012' : $filter['year'];
            $year0 = $year == 2012 ? $year - 1 : $year; // solo para el 2012
            $year1 = $year + 1;

            $values = array();

            $list = array();

            $sqlFilter = '';
            if (!empty($filters['user'])) {
                $user = $filters['user'];
                $sqlFilter .= " AND (user.id LIKE :user OR user.name LIKE :user OR user.email LIKE :user)";
                $values[':user'] = "%{$user}%";
            }

            if (!empty($filters['status'])) {
                switch ($filters['status']) {
                    case 'pending': // Pendientes de confirmar
                        $sqlFilter .= " AND user_donation.user IS NOT NULL";
                        break;
                    case 'confirmed': // Confirmados
                        $sqlFilter .= " AND user_donation.edited = 1";
                        break;
                    case 'emited': // Certificado emitido
                        $sqlFilter .= " AND user_donation.confirmed = 1";
                        break;
                    case 'notemited': //Confirmado pero no emitido
                        $sqlFilter .= " AND user_donation.edited = 1 AND (user_donation.confirmed = 0 OR user_donation.confirmed IS NULL)";
                        break;
                }
                $values[':user'] = "%{$user}%";
            }

            $sql = "SELECT
                        user.id as id,
                        user.email,
                        IFNULL(user_donation.name, invest_address.name) as name,
                        IFNULL(user_donation.nif, invest_address.nif) as nif,
                        IFNULL(user_donation.address, invest_address.address) as address,
                        IFNULL(user_donation.zipcode, invest_address.zipcode) as zipcode,
                        IFNULL(user_donation.location, invest_address.location) as location,
                        IFNULL(user_donation.country, invest_address.country) as country,
                        IFNULL(user_donation.amount, SUM(invest.amount)) as amount,
                        IFNULL(user_donation.numproj, COUNT(invest.project)) as numproj,
                        IFNULL(user_donation.year, '{$year}') as year,
                        IFNULL(user_donation.user, 'Pendiente') as pending,
                        user_donation.edited as edited,
                        user_donation.confirmed as confirmed
                FROM  invest
                INNER JOIN user ON user.id = invest.user
                LEFT JOIN user_donation ON user_donation.user = invest.user AND user_donation.year = '{$year}'
                LEFT JOIN invest_address ON invest_address.invest = invest.id
                WHERE   invest.resign = 1
                AND invest.status IN ('1', '3')
                AND invest.invested >= '{$year0}-01-01'
                AND invest.invested < '{$year1}-01-01'
                $sqlFilter
                GROUP BY invest.user
                ORDER BY user.email ASC";

//die ($sql);
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[] = $item;
            }
            return $list;
        }

        public function validate(&$errors = array()) {

        }

        /*
         *  Guarda los datos de donativo de un usuario
         */

        public function save(&$errors = array()) {

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
                if ($set != '')
                    $set .= ', ';
                $set .= "$field = :$field";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO user_donation (" . implode(', ', $fields) . ") VALUES (" . implode(', ', array_keys($values)) . ")";
                self::query($sql, $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }

        }

        public static function setConfirmed($user, $year = '2012') {
            try {
                $sql = "UPDATE user_donation SET confirmed = 1 WHERE user = :user AND year = :year";
                if (self::query($sql, array(':user' => $user, 'year' => $year))) {
                    return true;
                } else {
                    return false;
                }
            } catch (\PDOException $e) {
                $errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }
        
        /*
         * Nombre del archivo de certificado generado
         */
        public function setPdf($filename) {
            try {
                $sql = "UPDATE user_donation SET pdf = :pdf WHERE user = :user AND year = :year";
                if (self::query($sql, array(':pdf'=>$filename,':user' => $this->user, 'year' => $this->year))) {
                    return true;
                } else {
                    return false;
                }
            } catch (\PDOException $e) {
                $errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }


        static public function getDates ($user, $year = '2012') {

            $year0 = $year == 2012 ? $year - 1 : $year; // solo para el 2012
            $year1 = $year + 1;

            $fechas = array();

            // primero saber si es donante
            $sql = "SELECT 
                        DATE_FORMAT(invest.charged, '%d-%m-%Y') as date,
                        invest.amount as amount,
                        project.name as project
                    FROM invest
                    INNER JOIN project
                        ON project.id = invest.project
                    WHERE   invest.resign = 1
                    AND invest.status IN ('1', '3')
                    AND invest.invested >= '{$year0}-01-01'
                    AND invest.invested < '{$year1}-01-01'
                    AND invest.user = :id
                    ORDER BY invest.invested ASC
                    ";
//                    echo($sql . '<br />' . $user);
            $query = static::query($sql, array(':id' => $user));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $fechas[] = $row;
            }

            return $fechas;
        }

    }

}