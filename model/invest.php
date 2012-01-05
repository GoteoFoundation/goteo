<?php

namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Model\Image,
        Goteo\Model\Call;

    class Invest extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $project,
            $account, // cuenta paypal o email del usuario
            $amount, //cantidad monetaria del aporte
            $preapproval, //clave del preapproval
            $payment, //clave del cargo
            $transaction, // id de la transacción
            $method, // metodo de pago paypal/tpv
            $status, //estado en el que se encuentra esta aportación:
                    // -1 en proceso, 0 pendiente, 1 cobrado (charged), 2 devuelto (returned)
            $anonymous, //no debe aparecer su careto ni su nombre, nivel, etc... pero si aparece en la cuenta de cofinanciadores y de aportes
            $resign, //renuncia a cualquier recompensa
            $invested, //fecha en la que se ha iniciado el aporte
            $charged, //fecha en la que se ha cargado el importe del aporte a la cuenta del usuario
            $returned, //fecha en la que se ha devuelto el importe al usurio por cancelación bancaria
            $rewards = array(), //datos de las recompensas que le corresponden
            $address = array(
                'name'     => '',
                'nif'      => '',
                'address'  => '',
                'zipcode'  => '',
                'location' => '',
                'country'  => ''),  // dirección de envio del retorno
            $call = null; // aportes que tienen capital riego asociado

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
				$invest->rewards = $query->fetchAll(\PDO::FETCH_CLASS);

				$query = static::query("
                    SELECT  address, zipcode, location, country, name, nif
                    FROM  invest_address
                    WHERE   invest_address.invest = ?
                    ", array($id));
				$invest->address = $query->fetchObject();

                // si no tiene dirección, sacamos la dirección del usuario
                if (empty($invest->address)) {
                    $usr_address = User::getPersonal($invest->user);
                    $usr_address->name = $usr_address->contract_name;
                    $usr_address->nif = $usr_address->contract_nif;

                    $invest->address = $usr_address;
                }

                return $invest;
        }

        /*
         * Lista de inversiones (individuales) de un proyecto
         *
         * el parametro filter es para la gestion de recompensas (no es un autentico filtro, hay ordenaciones y hay filtros)
         */
        public static function getAll ($project, $filter = null) {

            /*
             * Estos son los filtros
             */
            $filters = array(
                'date'      => 'Fecha',
                'user'      => 'Usuario',
                'reward'    => 'Recompensa',
                'pending'   => 'Pendientes',
                'fulfilled' => 'Cumplidos'
            );


            $invests = array();

            $query = static::query("
                SELECT  *
                FROM  invest
                WHERE   invest.project = ?
                AND invest.status IN ('0', '1', '3', '4')
                ", array($project));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $invest) {
                // datos del usuario
                $invest->user = User::get($invest->user);

				$query = static::query("
                    SELECT  *
                    FROM  invest_reward
                    INNER JOIN reward
                        ON invest_reward.reward = reward.id
                    WHERE   invest_reward.invest = ?
                    ", array($invest->id));
				$invest->rewards = $query->fetchAll(\PDO::FETCH_CLASS);

				$query = static::query("
                    SELECT  address, zipcode, location, country
                    FROM  invest_address
                    WHERE   invest_address.invest = ?
                    ", array($invest->id));
				$invest->address = $query->fetchObject();

                // si no tiene dirección, sacamos la dirección del usuario
                if (empty($invest->address)) {
                    $usr_address = User::getPersonal($invest->user->id);

                    $invest->address = $usr_address;
                }

                $invests[$invest->id] = $invest;
            }

            return $invests;
        }


        /*
         * Lista de aportes individuales
         *
         * Los filtros vienen de la gestión de aportes
         * Los datos que sacamos: usuario, proyecto, cantidad, estado de proyecto, estado de aporte, fecha de aporte, tipo de aporte, campaña
         * .... anonimo, resign, etc...
         */
        public static function getList ($filters = array()) {

            /*
             * Estos son los filtros
            $fields = array('method', 'status', 'investStatus', 'project', 'user', 'campaign', 'types');
             */

            $list = array();

            $sqlFilter = "";
            if (!empty($filters['methods'])) {
                $sqlFilter .= " AND invest.method = '{$filters['methods']}'";
            }
            if (!empty($filters['status'])) {
                $sqlFilter .= " AND project.status = '{$filters['status']}'";
            }
            if (is_numeric($filters['investStatus'])) {
                $sqlFilter .= " AND invest.status = '{$filters['investStatus']}'";
            }
            if (!empty($filters['projects'])) {
                $sqlFilter .= " AND invest.project = '{$filters['projects']}'";
            }
            if (!empty($filters['users'])) {
                $sqlFilter .= " AND invest.user = '{$filters['users']}'";
            }
            if (!empty($filters['campaigns'])) {
                $sqlFilter .= " AND invest.campaign = '{$filters['campaigns']}'";
            }
            if (!empty($filters['types'])) {
                switch ($filters['types']) {
                    case 'donative':
                        $sqlFilter .= " AND invest.resign = 1";
                        break;
                    case 'anonymous':
                        $sqlFilter .= " AND invest.anonymous = 1";
                        break;
                    case 'manual':
                        $sqlFilter .= " AND invest.admin IS NOT NULL";
                        break;
                    case 'campaign':
                        $sqlFilter .= " AND invest.campaign IS NOT NULL";
                        break;
                }
            }

            if (!empty($filters['review'])) {
                switch ($filters['review']) {
                    case 'collect': //  Recaudado: tpv cargado o paypal pendiente
                        $sqlFilter .= " AND ((invest.method = 'tpv' AND invest.status = 1)
                                        OR (invest.method = 'paypal' AND invest.status = 0))";
                        break;
                    case 'online': // Solo pagos online
                        $sqlFilter .= " AND (invest.method = 'tpv' OR invest.method = 'paypal')";
                        break;
                    case 'paypal': // Paypal pendientes o ok
                        $sqlFilter .= " AND (invest.method = 'paypal' AND (invest.status = -1 OR invest.status = 0))";
                        break;
                    case 'tpv': // Tpv pendientes o ok
                        $sqlFilter .= " AND (invest.method = 'tpv' AND (invest.status = -1 OR invest.status = 1))";
                        break;
                }
            }

            if (!empty($filters['date_from'])) {
                $sqlFilter .= " AND invest.invested >= '{$filters['date_from']}'";
            }
            if (!empty($filters['date_until'])) {
                $sqlFilter .= " AND invest.invested <= '{$filters['date_until']}'";
            }

            $sql = "SELECT
                        invest.id as id,
                        invest.user as user,
                        invest.project as project,
                        invest.method as method,
                        invest.status as investStatus,
                        project.status as status,
                        invest.campaign as campaign,
                        invest.amount as amount,
                        invest.anonymous as anonymous,
                        invest.resign as resign,
                        DATE_FORMAT(invest.invested, '%d/%m/%Y') as invested,
                        DATE_FORMAT(invest.charged , '%d/%m/%Y') as charged,
                        DATE_FORMAT(invest.returned, '%d/%m/%Y') as returned,
                        user.name as admin
                    FROM invest
                    INNER JOIN project
                        ON invest.project = project.id
                    LEFT JOIN user
                        ON invest.admin = user.id
                    WHERE invest.project IS NOT NULL
                        $sqlFilter
                    ORDER BY invest.id DESC
                    ";

            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item;
            }
            return $list;
        }




        public function validate (&$errors = array()) { 
            if (!is_numeric($this->amount))
                $errors[] = 'La cantidad no es correcta';
                //Text::get('validate-invest-amount');

            if (empty($this->method))
                $errors[] = 'Falta metodo de pago';
                //Text::get('mandatory-invest-method');

            if (empty($this->user))
                $errors[] = 'Falta usuario';
                //Text::get('mandatory-invest-user');

            if (empty($this->project))
                $errors[] = 'Falta proyecto';
                //Text::get('mandatory-invest-project');

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
                'amount',
                'preapproval',
                'payment',
                'transaction',
                'method',
                'status',
                'anonymous',
                'resign',
                'invested',
                'charged',
                'returned',
                'admin',
                'campaign',
                'call',
                'drops'
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
                if (empty($this->id)) {
                    $this->id = self::insertId();
                    if (empty($this->id)) {
                        $errors[] = 'No ha conseguido Id de aporte';
                        return false;
                    }

                    // si es de convocatoria,
                    if (isset($this->called) && $this->called instanceof Call) {
                        // si queda capital riego
                        if ($this->called->rest >= $this->amount) {
                            // se crea el aporte paralelo
                            $drop = new Invest(
                                array(
                                    'amount' => $this->amount,
                                    'user' => $this->called->owner,
                                    'project' => $this->project,
                                    'method' => 'cash',
                                    'status' => '-1', // en proceso
                                    'invested' => date('Y-m-d'),
                                    'anonymous' => null,
                                    'resign' => true,
                                    'campaign' => true,
                                    'drops' => $this->id,
                                    'call' => $this->called->id
                                )
                            ) ;

                            // se actualiza el registro de convocatoria
                            if ($drop->save($errors)) {
                                self::query("UPDATE invest SET droped=".$drop->id." WHERE id=".$this->id);
                                $this->droped = $drop->id;
                            } else {
                                $errors[] = 'No se ha podido actualizar el aporte con el capital riego que ha generado';
                            }
                            
                        } else {
                            $errors[] = 'No queda capital riego';
                            unset($this->called);
                        }
                    }

                }

                // y las recompensas
                foreach ($this->rewards as $reward) {
                    $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
                    self::query($sql, array(':invest'=>$this->id, ':reward'=>$reward));
                }

                // dirección
                if (!empty($this->address)) {
                    $sql = "REPLACE INTO invest_address (invest, user, address, zipcode, location, country, name, nif)
                        VALUES (:invest, :user, :address, :zipcode, :location, :country, :name, :nif)";
                    self::query($sql, array(
                        ':invest'=>$this->id,
                        ':user'=>$this->user,
                        ':address'=>$this->address->address,
                        ':zipcode'=>$this->address->zipcode, 
                        ':location'=>$this->address->location, 
                        ':country'=>$this->address->country,
                        ':name'=>$this->address->name,
                        ':nif'=>$this->address->nif
                        )
                    );
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "El aporte no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }

        /*
         * Lista de proyectos con aportes
         *
         * @param bool success solo los prroyectos en campaña, financiados o exitosos
         */
        public static function projects ($success = false, $node = \GOTEO_NODE) {

            $list = array();

            $sql = "
                SELECT
                    project.id as id,
                    project.name as name
                FROM    project
                INNER JOIN invest
                    ON project.id = invest.project
                    ";

            if ($success) {
                $sql .= " WHERE project.status >= 3 AND project.status <= 5 ";
            }
            $sql .= " ORDER BY project.name ASC";

            //, array(':node' => $node)
            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de usuarios que han aportado a algo
         */
        public static function users ($all = false) {

            $list = array();

            $sql = "
                SELECT
                    user.id as id,
                    user.name as name
                FROM    user
                INNER JOIN invest
                    ON user.id = invest.user
                ";
            
            if (!$all) {
                $sql .= "WHERE (user.hide = 0 OR user.hide IS NULL)
                    ";
            }
                $sql .= "ORDER BY user.name ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de campañas con aportes asociados
         */
        public static function campaigns () {

            $list = array();

            $query = static::query("
                SELECT
                    campaign.id as id,
                    campaign.name as name
                FROM    campaign
                INNER JOIN invest
                    ON campaign.id = invest.campaign
                ORDER BY campaign.name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Obtenido por un proyecto
         */
        public static function invested ($project, $only = null, $call = null) {

            $values = array(':project' => $project);

            $sql = "SELECT  SUM(amount) as much
                FROM    invest
                WHERE   project = :project
                AND     invest.status IN ('0', '1', '3', '4')
                ";

            if (isset ($only) && in_array($only, array('users', 'call'))) {
                switch ($only) {
                    case 'users':
                        $sql .= " AND (invest.campaign = 0 OR invest.campaign IS NULL)";
                        break;
                    case 'call':
                        $sql .= " AND invest.campaign = 1";
                        if (isset($call)) {
                            $sql .= " AND invest.call = :call";
                            $values['call'] = $call;
                        }
                        break;
                }
            }

            $query = static::query($sql, $values);
            $got = $query->fetchObject();
            return (int) $got->much;
        }

        /*
         * Usuarios que han aportado aun proyecto
         */
        public static function investors ($project, $projNum = true, $showall = false) {
            $investors = array();

            $sql = "
                SELECT
                    invest.user as user,
                    user.name as name,
                    user.avatar as avatar,
                    invest.amount as amount,
                    DATE_FORMAT(invest.invested, '%d/%m/%Y') as date,
                    ";
            if ($projNum) {
                $sql .= "(SELECT
                        COUNT(DISTINCT(project))
                     FROM invest as invb
                     WHERE invb.user = invest.user
                     AND  invb.status IN ('0', '1', '3', '4')
                     ) as projects,";
            }

            $sql .= "user.hide as hide,
                    invest.droped as droped,
                    invest.anonymous as anonymous
                FROM    invest
                INNER JOIN user
                    ON  user.id = invest.user
                WHERE   project = ?
                AND     invest.status IN ('0', '1', '3', '4')
                ORDER BY invest.invested DESC
                ";

            $query = self::query($sql, array($project));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {

                $investor->avatar = Image::get($investor->avatar);
                if (empty($investor->avatar->id) || !$investor->avatar instanceof Image) {
                    $investor->avatar = Image::get(1);
                }


                // si el usuario es hide o el aporte es anonymo, lo ponemos como el usuario anonymous (avatar 1)
                if (!$showall && ($investor->hide == 1 || $investor->anonymous == 1)) {

                    // mantenemos la fecha del anonimo mas reciente
                    $anonymous_date = empty($investors['anonymous']->date) ? $investor->date : $investors['anonymous']->date;

                    $investors[] = (object) array(
                        'user' => 'anonymous',
                        'name' => Text::get('regular-anonymous'),
                        'projects' => null,
                        'avatar' => Image::get(1),
                        'worth' => null,
                        'amount' => $investor->amount,
                        'date' => $investor->date,
                        'droped' => $investor->droped
                    );

                } else {

                    $investors[$investor->user] = (object) array(
                        'user' => $investor->user,
                        'name' => $investor->name,
                        'projects' => $investor->projects,
                        'avatar' => $investor->avatar,
                        'worth' => \Goteo\Model\User::calcWorth($investor->user),
                        'amount' => ($investors[$investor->user]->amount + $investor->amount),
                        'date' => $investor->date,
                        'droped' => empty($investors[$investor->user]->droped) ? $investor->droped : $investors[$investor->user]->droped
                    );

                }

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
                AND     invest.status IN ('0', '1', '3', '4')
                AND     (anonymous = 0 OR anonymous IS NULL)
                ORDER BY invested DESC";

            $query = self::query($sql, array(':user' => $user, ':project' => $project));
            return $query->fetchObject();
        }

        /*
         * Numero de cofinanciadores que han optado por cierta recompensa
         */
        public static function choosed ($reward) {

            $users = array();

            $sql = "
                SELECT  DISTINCT(user) as user
                FROM    invest
                INNER JOIN invest_reward
                    ON invest_reward.invest = invest.id
                    AND invest_reward.reward = ?
                INNER JOIN user
                    ON  user.id = invest.user
                    AND (user.hide = 0 OR user.hide IS NULL)
                WHERE   invest.status IN ('0', '1', '3', '4')
                ";

            $query = self::query($sql, array($reward));
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $investor) {
                $users[] = $investor['user'];
            }

            return $users;
        }


        /*
         * Asignar a la aportación una recompensas
         */
        public function setReward ($reward) {

            $values = array(
                ':invest' => $this->id,
                ':reward' => $reward
            );

            $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }
        }

        /*
         *  Actualiza el mail de la cuenta utilizada al registro del aporte
         */
        public function setAccount ($account) {

            $values = array(
                ':id' => $this->id,
                ':account' => $account
            );

            $sql = "UPDATE invest SET account = :account WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Marcar una recompensa como cumplida (o desmarcarla)
         */
        public static function setFulfilled ($invest, $reward, $value = '1') {

            $values = array(
                ':value' => $value,
                ':invest' => $invest,
                ':reward' => $reward
            );

            $sql = "UPDATE invest_reward SET fulfilled = :value WHERE invest=:invest AND reward=:reward";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
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
         *  Cambia el estado de un aporte
         */
        public function setStatus ($status) {

            if (!in_array($status, array('-1', '0', '1', '2', '3', '4', '5'))) {
                return false;
            }

            $values = array(
                ':id' => $this->id,
                ':status' => $status
            );

            $sql = "UPDATE invest SET status = :status WHERE id = :id";
            if (self::query($sql, $values)) {

                // si tiene capital riego asociado pasa al mismo estado
                if (!empty($this->droped)) {
                    $drop = Invest::get($this->droped);
                    // si estan reubicando o caducando
                    // cancelamos el riego como si nunca hubiera existido
                    if ($status == 4 || $status == 5) {
                        if ($drop->setStatus(2)) {
                            self::query("UPDATE invest SET droped = NULL WHERE id = :id", array(':id' => $this->id));
                        } else {
                            $drop->setStatus($status);
                        }
                    }
                }

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
         *  Pone el codigo de la transaccion al registro del aporte
         */
        public function setTransaction ($code) {

            $values = array(
                ':id' => $this->id,
                ':transaction' => $code
            );

            $sql = "UPDATE invest SET transaction = :transaction WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         *  marca un aporte como devuelto (devuelto el dinero despues de haber sido cargado)
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
         * Marcar esta aportación como cancelada
         */
        public function cancel () {

            $values = array(
                ':id' => $this->id,
                ':returned' => date('Y-m-d')
            );

            $sql = "UPDATE invest SET
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
         * Estados del aporte
         */
        public static function status ($id = null) {
            $array = array (
                -1 => 'En proceso',
                0  => 'Pendiente de cargo',
                1  => 'Cargo ejecutado',
                2  => 'Cancelado',
                3  => 'Pagado al proyecto',
                4  => 'Caducado',
                5  => 'Reubicado'
            );

            if (!empty($id)) {
                return $array[$id];
            } else {
                return $array;
            }

        }

        /*
         * Métodos de pago
         */
        public static function methods () {
            return array (
                'paypal' => 'Paypal',
                'tpv'    => 'Tarjeta',
                'cash'   => 'Manual'
            );
        }

    }
    
}