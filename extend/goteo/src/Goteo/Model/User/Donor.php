<?php

namespace Goteo\Model\User {

    use Goteo\Library\Check,
        Goteo\Library\FileHandler\File,
        Goteo\Library\Text;


    class Donor extends \Goteo\Core\Model {

        public
            $user,
            $amount,
            $name,
            $surname,
            $nif,
            $address,
            $region,
            $zipcode,
            $location,
            $country,
            $year,
            $edited = 0,
            $confirmed = 0,
            $pdf = null,
            $dates = array();

        /**
         * Get invest data if a user is a donor
         * @param varcahr(50) $id  user identifier
         *
         * si solo hay un aporte a un proyecto no financiado devolverá vacio
         */
        public static function get($id, $year = null) {

            if (empty($year)) return null;

            try {

                // si ya ha introducido los datos, sacamos de user_donation
                $sql = "SELECT * FROM user_donation WHERE user = :id AND year = :year";
                $values = array(':id' => $id, ':year' => $year);
                $query = static::query($sql, $values);
                if ($donation = $query->fetchObject(__CLASS__)) {
                    return $donation;
                } else {
                    // sino sacamos de invest_address
                    $sql = "SELECT
                                    invest.user as user,
                                    SUM(invest.amount) as amount
                                FROM  invest
                                INNER JOIN project
                                    ON project.id = invest.project
                                    AND project.passed IS NOT NULL
                                INNER JOIN user ON user.id = invest.user
                                LEFT JOIN invest_address ON invest_address.invest = invest.id
                                WHERE   invest.user = :id
                                AND invest.status IN ('1', '3')
                                AND ( (invest.invested >= '{$year}-01-01' AND invest.invested <= '{$year}-12-31')
                                  OR ( invest.invested < '{$year}-01-01' AND project.passed >= '{$year}-01-01' )
                                  )
                                GROUP BY invest.user
                            ";
                    $query = static::query($sql, array(':id' => $id));
                    if ($donation = $query->fetchObject(__CLASS__)) {
                        $donation->year = $year;
                    } else {
                        $donation = null;
                    }
                    return $donation;
                }


            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /*
        * Listado de datos de donativos que tenemos
        * @param csv boolean si procesamos los datos para el excel
        */
        public function getList($filters = array(), $csv = false) {


            // naturaleza según tipo de persona (F, J)
            $nt = array(
                       '' => 'F',
                    'nif' => 'F',
                    'nie' => 'F',
                    'cif' => 'J',
                    'vat' => 'J'
                );
            // porcentaje segun tipo de persona (25, 35)
            $pt = array(
                       '' => '25',
                    'nif' => '25',
                    'nie' => '25',
                    'cif' => '35',
                    'vat' => '35'
                );

            $year = empty($filters['year']) ? date('Y') : $filters['year'];

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
                    case 'pending': // Pendientes de revisar
                        $sqlFilter .= " AND (user_donation.edited IS NULL OR user_donation.edited = 0)";
                        break;
                    case 'edited': // Revisados no confirmados
                        $sqlFilter .= " AND user_donation.edited = 1 AND (user_donation.confirmed IS NULL OR user_donation.confirmed = 0)";
                        break;
                    case 'confirmed': // Confirmados
                        $sqlFilter .= " AND user_donation.confirmed = 1";
                        break;
                    case 'emited': // Certificado emitido
                        $sqlFilter .= " AND (user_donation.pdf IS NOT NULL OR user_donation.pdf != '')";
                        break;
                    case 'notemited': //Confirmado pero no emitido
                        $sqlFilter .= " AND user_donation.confirmed = 1 AND (user_donation.pdf IS NULL OR user_donation.pdf = '')";
                        break;
                }
            }

            $sql = "SELECT
                        user.id as id,
                        user.email,
                        user_donation.name as name,
                        user_donation.surname as surname,
                        user_donation.nif as nif,
                        user_donation.address as address,
                        user_donation.region as region,
                        user_donation.zipcode as zipcode,
                        user_donation.country as country,
                        user_donation.amount as amount,
                        user_donation.numproj as numproj,
                        CONCAT('{$year}') as year,
                        user_donation.edited as edited,
                        user_donation.confirmed as confirmed,
                        user_donation.pdf as pdf
                FROM  user_donation
                INNER JOIN user ON user.id = user_donation.user
                WHERE user_donation.year = '{$year}'
                $sqlFilter
                ORDER BY user.email ASC";

            $query = self::query($sql, $values);
            $items = $query->fetchAll(\PDO::FETCH_OBJ);
            foreach ($items as $item) {

                $name = (!empty($item->surname)) ? $item->surname.' '.$item->name : $item->name;

                // tipo de persona segun nif/nie/cif
                $type = '';
                Check::nif($item->nif, $type);
                $per = $pt[$type];
                $nat = $nt[$type];

                $cp = (string) $item->zipcode;
                $item->location = ($item->country == 'spain') ? substr($cp, 0, 2) : '99';

// NIF;NIF_REPRLEGAL;Nombre;Provincia;CLAVE;PORCENTAJE;VALOR;EN_ESPECIE;COMUNIDAD;PORCENTAJE_CA;NATURALEZA;REVOCACION;EJERCICIO;TIPOBIEN;BIEN
                $list[] = array($item->nif, '', $name, $item->location, 'A', $per, $item->amount, '', '', '', $nat, '', $year, '', '', '');
            }
            return $list;
        }

        public function validate(&$errors = array()) {

            // limpio nombre y apellidos
            $this->name = self::idealiza($this->name, false, true);
            $this->name = str_replace('-', ' ', $this->name);
            $this->name = strtoupper(trim($this->name));

            $this->surname = self::idealiza($this->surname, false, true);
            $this->surname = str_replace('-', ' ', $this->surname);
            $this->surname = strtoupper(trim($this->surname));

			// quitamos puntos y guiones
			$this->nif = str_replace(array('_', '.', ' ', '-', ','), '', $this->nif);

        }

        /*
         *  Guarda los datos de donativo de un usuario
         */

        public function save(&$errors = array()) {
            $this->validate();


            $fields = array(
                'user',
                'amount',
                'name',
                'surname',
                'nif',
                'address',
                'location',
                'zipcode',
                'region',
                'country',
                'countryname',
                'year',
                'edited'
            );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '')
                    $set .= ', ';
                $set .= "$field = :$field";
                $values[":{$field}"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO user_donation (" . implode(', ', $fields) . ") VALUES (" . implode(', ', array_keys($values)) . ")";
//                die(sqldbg($sql, $values));
                self::query($sql, $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }

        }

        public static function setConfirmed($user, $year, $confirmed = 1) {
            try {
                $sql = "UPDATE user_donation SET confirmed = :confirmed WHERE user = :user AND year = :year";
                if (self::query($sql, array(':user' => $user, ':year' => $year, ':confirmed'=>$confirmed))) {
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
                    $this->pdf = $filename;
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
         * Nombre del archivo de certificado guardado
         */
        static public function getPdf($user, $year) {
            try {
                $sql = "SELECT pdf FROM user_donation WHERE user = :user AND year = :year";
                if ($filename = self::query($sql, array(':user' => $user, 'year' => $year))) {
                    return $filename->fetchColumn();
                } else {
                    return null;
                }
            } catch (\PDOException $e) {
                $errors[] = "No se puede recuperar pdf." . $e->getMessage();
                return false;
            }
        }


        /*
         * Resetear pdf
         */
        static public function resetPdf($xfilename) {
            $ok = false;

            try {
                $sql = "UPDATE user_donation SET pdf = NULL WHERE MD5(pdf) = :pdf";
                if (self::query($sql, array(':pdf'=>$xfilename))) {

                    $fp = File::factory(array('bucket' => AWS_S3_BUCKET_DOCUMENT));
                    $fp->setPath('certs/');

                    $fp->delete($xfilename);

                    $ok = true;
                }
            } catch (\PDOException $e) {
                $errors[] = "Los datos no se han guardado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }

            return $ok;
        }


        /**
         * fechas de aportes realizados por el usuario $user durante el año $year
         *
         * @param $user
         * @param $year
         * @param ( boolean) $fundedonly : para filtrar a portes que se muestran en dashboard pero no se muestran en el pdf
         *    - aportes a proyectos pendientes de financiar
         *    - aportes preaprobados
         *    - aportes con incidencias
         *
         * @return array de fechas y proyectos que ha aportado
         *
         * getDates da todos los aportes, incluso a proyectos aun no financiados
         *  y filtra estados de proyecto, no muestra aportes aproyectos archivados
         *
         */
        static public function getDates ($user, $year, $fundedonly = true) {

            $fechas = array();

            // solo aportes cobrados y a proyectos financiados
            if ($fundedonly) {
                $sqlFilter = " AND project.passed IS NOT NULL
                    AND invest.status IN ('1', '3')
                    AND (invest.issue IS NULL OR invest.issue = 0)
                ";
            } else {
                // aportes preaprobados, con incidencia y a proyectos pendientes de financiar
                $sqlFilter = " AND invest.status IN ('0', '1', '3')
                ";

            }

            $sql = "SELECT
                        DATE_FORMAT(invest.invested, '%d-%m-%Y') as date,
                        invest.amount as amount,
                        project.name as project,
                        IF(project.passed IS NULL, 0, 1) as funded,
                        IF(invest.status = 0, 1, 0) as preapproval,
                        invest.issue as issue
                    FROM invest
                    INNER JOIN project
                        ON project.id = invest.project
                        AND project.status IN (3, 4, 5)
                    WHERE invest.user = :id
                    AND ( (invest.invested >= '{$year}-01-01' AND invest.invested <= '{$year}-12-31')
                      OR ( invest.invested < '{$year}-01-01' AND project.passed >= '{$year}-01-01' )
                      )
                    {$sqlFilter}
                    ORDER BY invest.invested ASC
                    ";

            $values = array(':id' => $user);

            // echo '<br /><br />user: ' . $user . ' year: ' . $year . ' fundedonly: ' . $fundedonly.'<br />';
            // echo sqldbg($sql, $values);
            $query = static::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $fechas[] = $row;
            }

            return $fechas;
        }

        /*
         * Año fiscal actual
         */
        static public function currYear(&$confirm_closed = false) {

            $year = date('Y');
            $month = date('m');
            $day = date('d');
            // hasta julio es el año anterior
            if ($month <= 6) {
                $year--;
            }

            // si ha pasado el día limite después de año nuevo ya no se permite confirmar datos
            if ($year != date('Y') && ( ($month == 1 && $day > 25) || $month > 1 ) )
                $confirm_closed = true;


            return $year;
        }



    }

}
