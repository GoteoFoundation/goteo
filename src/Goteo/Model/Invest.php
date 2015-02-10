<?php

namespace Goteo\Model {

    use Goteo\Core\Model;
    use Goteo\Library\Text,
        Goteo\Model\Image,
        Goteo\Model\User,
        Goteo\Model\Project,
        Goteo\Model\Call;

    class Invest extends \Goteo\Core\Model {

        public
            $id,
            $user,
            $project,
            $account, // cuenta paypal o email del usuario
            $amount, //cantidad monetaria del aporte
            $amount_original, // numero que el usuario ha puesto al aportar
            $currency, // divisa en que se estaba visualizando la web en el momento de hacer el aporte
            $currency_rate, // ratio de conversión a euros en el momento de hacer el aporte
            $preapproval, //clave del preapproval
            $payment, //clave del cargo
            $transaction, // id de la transacción
            $method, // metodo de pago paypal/tpv
            $status, //estado en el que se encuentra esta aportación:
                    // -1 en proceso, 0 pendiente, 1 cobrado (charged), 2 devuelto (returned)
            $issue, // aporte con incidencia
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
                'country'  => '',
                'regalo'  => '',
                'namedest'  => '',
                'emaildest'  => '',
                'message'  => ''),  // dirección de envio de la recompensa y datos de regalo
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
                    SELECT  *
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
				$invest->rewards = $query->fetchAll(\PDO::FETCH_OBJ);

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
        public static function getList ($filters = array(), $node = null, $limited = false) {

            $list = array();
            $values = array();

            $sqlFilter = "";
            if (!empty($filters['id'])) {
                $sqlFilter .= " AND invest.id = :id";
                $values[':id'] = $filters['id'];
            }
            if (!empty($filters['methods'])) {
                $sqlFilter .= " AND invest.method = :methods";
                $values[':methods'] = $filters['methods'];
            }
            if (is_numeric($filters['status'])) {
                $sqlFilter .= " AND project.status = :status";
                $values[':status'] = $filters['status'];
            }
            if (is_numeric($filters['investStatus'])) {
                $sqlFilter .= " AND invest.status = :investStatus";
                $values[':investStatus'] = $filters['investStatus'];
            }
            if (!empty($filters['projects'])) {
                $sqlFilter .= " AND invest.project = :projects";
                $values[':projects'] = $filters['projects'];
            }
            if (!empty($filters['amount'])) {
                $sqlFilter .= " AND invest.amount >= :amount";
                $values[':amount'] = $filters['amount'];
            }
            if (!empty($filters['maxamount'])) {
                $sqlFilter .= " AND invest.amount <= :maxamount";
                $values[':maxamount'] = $filters['maxamount'];
            }
            if (!empty($filters['users'])) {
                $sqlFilter .= " AND invest.user = :users";
                $values[':users'] = $filters['users'];
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND invest.user IN (SELECT id FROM user WHERE (name LIKE :name OR email LIKE :name))";
                $values[':name'] = "%{$filters['name']}%";
            }
            if (!empty($filters['calls'])) {
                $sqlFilter .= " AND invest.campaign = 1 AND invest.`call` = :calls";
                $values[':calls'] = $filters['calls'];
            }
            if (!empty($filters['issue'])) {
                switch ($filters['issue']) {
                    case 'show':
                        $sqlFilter .= " AND invest.issue = 1";
                        break;
                    case 'hide':
                        $sqlFilter .= " AND (invest.issue = 0 OR invest.issue IS NULL)";
                        break;
                }
            }
            if (!empty($filters['procStatus'])) {
                switch ($filters['procStatus']) {
                    case 'first': // en primera ronda
                        $sqlFilter .= " AND project.status = 3 AND (project.passed IS NULL OR project.passed = '0000-00-00' )";
                        break;
                    case 'second': // en segunda ronda
                        $sqlFilter .= " AND project.status = 3 AND (project.passed IS NOT NULL AND project.passed != '0000-00-00' )";
                        break;
                    case 'completed': // financiados
                        $sqlFilter .= " AND project.status = 4";
                        break;
                }
            }
            // else { $sqlFilter .= " AND (invest.campaign = 0 OR invest.campaign IS NULL)"; }
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
                        $sqlFilter .= " AND invest.droped IS NOT NULL";
                        break;
                    case 'drop':
                        $sqlFilter .= " AND invest.campaign = 1";
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
                $sqlFilter .= " AND invest.invested >= :date_from";
                $values[':date_from'] = $filters['date_from'];
            }
            if (!empty($filters['date_until'])) {
                $sqlFilter .= " AND invest.invested <= :date_until";
                $values[':date_until'] = $filters['date_until'];
            }

            if (!empty($node)) {
                $sqlFilter .= " AND project.node = :node";
                $values[':node'] = $node;
            }

            $sql = "SELECT
                        invest.id as id,
                        invest.user as user,
                        invest.project as project,
                        invest.method as method,
                        invest.status as investStatus,
                        project.status as status,
                        invest.campaign as campaign,
                        invest.call as `call`,
                        invest.droped as droped,
                        invest.amount as amount,
                        invest.anonymous as anonymous,
                        invest.resign as resign,
                        DATE_FORMAT(invest.invested, '%d/%m/%Y') as invested,
                        DATE_FORMAT(invest.charged , '%d/%m/%Y') as charged,
                        DATE_FORMAT(invest.returned, '%d/%m/%Y') as returned,
                        user.name as admin,
                        invest.issue as issue
                    FROM invest
                    INNER JOIN project
                        ON invest.project = project.id
                    LEFT JOIN user
                        ON invest.admin = user.id
                    WHERE invest.project IS NOT NULL
                        $sqlFilter
                    ORDER BY invest.id DESC
                    ";

            if ($limited > 0 && is_numeric($limited)) {
                $sql .= "LIMIT $limited";
            }

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item;
            }
            return $list;
        }

        public function validate (&$errors = array()) {
            if (!is_numeric($this->amount))
                $errors[] = 'La cantidad no es correcta';

            if (empty($this->method))
                $errors[] = 'Falta metodo de pago';

            if (empty($this->user))
                $errors[] = 'Falta usuario';

            if (empty($this->project))
                $errors[] = 'Falta proyecto';

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
                'amount_original',
                'currency',
                'currency_rate',
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

                        // si el aporte es más de lo que puede
                        $drop_amount = ($this->amount > $this->maxdrop) ? $this->maxdrop : $this->amount;

                        // si queda capital riego
                        if ($drop_amount > 0) {
                            // se crea el aporte paralelo
                            $drop = new Invest(
                                array(
                                    'amount' => $drop_amount,
                                    'user' => $this->called->owner,
                                    'project' => $this->project,
                                    'method' => 'drop',
                                    'status' => $this->status,
                                    'invested' => date('Y-m-d'),
                                    'anonymous' => null,
                                    'resign' => false,
                                    'campaign' => true,
                                    'drops' => $this->id,
                                    'call' => $this->called->id
                                )
                            ) ;

                            // se actualiza el registro de convocatoria
                            if ($drop->save($errors)) {
                                self::query("UPDATE invest SET droped = :drop, `call`= :call WHERE id = :id",
                                        array(':id' => $this->id, ':drop' => $drop->id, ':call' => $this->called->id));
                                $this->droped = $drop->id;
                                $this->call = $this->called->id;
                            }

                        }
                    }

                }

                // tabla para obtener aportaciones por nodo

                // FIX: aseguramos que no hay ningun valor nulo
                $pnode = Project::getMini($this->project)->node;
                if (empty($pnode)) $pnode = \GOTEO_NODE;
                $unode = User::getMini($this->user)->node;
                if (empty($unode)) $unode = \GOTEO_NODE;

                $sql = "REPLACE INTO invest_node (project_id, project_node, user_id, user_node, invest_id, invest_node) VALUES (:pid, :pnode, :uid, :unode, :iid, :inode)";
                self::query($sql, array(
                    ':pid' => $this->project,
                    ':pnode' => $pnode,
                    ':uid' => $this->user,
                    ':unode' => $unode,
                    ':iid' => $this->id,
                    ':inode' => NODE_ID)
                );

                // y las recompensas
                foreach ($this->rewards as $reward) {
                    $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
                    self::query($sql, array(':invest'=>$this->id, ':reward'=>$reward));
                }

                // dirección
                if (!empty($this->address)) {
                    $sql = "REPLACE INTO invest_address (invest, user, address, zipcode, location, country, name, nif, regalo, namedest, emaildest, message)
                        VALUES (:invest, :user, :address, :zipcode, :location, :country, :name, :nif, :regalo, :namedest, :emaildest, :message)";
                    self::query($sql, array(
                        ':invest'   => $this->id,
                        ':user'     => $this->user,
                        ':address'  => $this->address->address,
                        ':zipcode'  => $this->address->zipcode,
                        ':location' => $this->address->location,
                        ':country'  => $this->address->country,
                        ':name'     => $this->address->name,
                        ':nif'      => $this->address->nif,
                        ':regalo'   => $this->address->regalo,
                        ':namedest' => $this->address->namedest,
                        ':emaildest'=> $this->address->emaildest,
                        ':message'  => $this->address->message
                        )
                    );
                }

                // mantenimiento de registros relacionados (usuario, proyecto, ...)
                $this->keepUpdated($this->called->id);

                return true;

            } catch(\PDOException $e) {
                // TODO: Revertir últimas transacciones
                $errors[] = "El aporte no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }

        /*
         * Para actualizar recompensa (o renuncia) y dirección
         */
        public function update (&$errors = array()) {

            self::query("START TRANSACTION");

            try {
                // si renuncia
                $sql = "UPDATE invest SET anonymous = :anonymous WHERE id = :id";
                self::query($sql, array(':id'=>$this->id, ':anonymous'=>$this->anonymous));

                // borramos als recompensas
                $sql = "DELETE FROM invest_reward WHERE invest = :invest";
                self::query($sql, array(':invest'=>$this->id));

                // y grabamos las nuevas
                foreach ($this->rewards as $reward) {
                    $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
                    self::query($sql, array(':invest'=>$this->id, ':reward'=>$reward));
                }

                // dirección
                if (!empty($this->address)) {
                    $sql = "REPLACE INTO invest_address (invest, user, address, zipcode, location, country, name, nif, regalo, namedest, emaildest)
                        VALUES (:invest, :user, :address, :zipcode, :location, :country, :name, :nif, :regalo, :namedest, :emaildest)";
                    self::query($sql, array(
                        ':invest'   => $this->id,
                        ':user'     => $this->user,
                        ':address'  => $this->address->address,
                        ':zipcode'  => $this->address->zipcode,
                        ':location' => $this->address->location,
                        ':country'  => $this->address->country,
                        ':name'     => $this->address->name,
                        ':nif'      => $this->address->nif,
                        ':regalo'   => $this->address->regalo,
                        ':namedest' => $this->address->namedest,
                        ':emaildest'=> $this->address->emaildest
                        )
                    );
                }

                self::query("COMMIT");

                return true;

            } catch (\PDOException $e) {
                self::query("ROLLBACK");
                $errors[] = "Envíanos esto: <br />" . $e->getMessage();
                return false;
            }
        }

        /*
         * Para pasar un aporte con incidencia a resuelta, cash y cobrado
         */
        public function solve (&$errors = array()) {

            self::query("START TRANSACTION");

            try {
                // si renuncia
                $sql = "UPDATE invest SET  method = 'cash', status = 1, issue = 0 WHERE id = :id";
                self::query($sql, array(':id'=>$this->id));

                // añadir detalle
                $sql = "INSERT INTO invest_detail (invest, type, log, date)
                    VALUES (:id, 'solved', :log, NOW())";

                self::query($sql, array(':id'=>$this->id, ':log'=>'Incidencia resuelta por el admin '.$_SESSION['user']->name.', aporte pasado a cash y cobrado'));


                self::query("COMMIT");

                return true;

            } catch (\PDOException $e) {
                self::query("ROLLBACK");
                $errors[] = $e->getMessage();
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
            $values = array();

            $and = " WHERE";

            $sql = "
                SELECT
                    project.id as id,
                    project.name as name
                FROM    project
                INNER JOIN invest
                    ON project.id = invest.project
                    ";

            if ($success) {
                $sql .= "$and project.status >= 3 AND project.status <= 5 ";
                $and = " AND";
            }
            if ($node != \GOTEO_NODE) {
                $sql .= "$and project.node = :node";
                $and = " AND";
                $values[':node'] = $node;
            }
            $sql .= " ORDER BY project.name ASC";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de usuarios que han aportado a algo
         */
        public static function users ($all = false, $node = \GOTEO_NODE) {

            $list = array();
            $values = array();

            $sql = "
                SELECT
                    user.id as id,
                    user.name as name
                FROM    user
                INNER JOIN invest
                    ON user.id = invest.user
                ";
            if ($node != \GOTEO_NODE) {
                $sql .= "
                INNER JOIN project
                    ON  project.id = invest.project
                    AND project.node = :node
                    ";
                $values[':node'] = $node;
            }
            if (!$all) {
                $sql .= "WHERE (user.hide = 0 OR user.hide IS NULL)
                    ";
            }
                $sql .= "
                    GROUP BY user.id
                    ORDER BY user.name ASC
                ";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de emails de usuarios que han aportado a algo
         */
        public static function emails ($all = false) {

            $list = array();

            $sql = "
                SELECT
                    user.id as id,
                    user.email as email
                FROM    user
                INNER JOIN invest
                    ON user.id = invest.user
                ";

            if (!$all) {
                $sql .= "WHERE (user.hide = 0 OR user.hide IS NULL)
                    ";
            }
                $sql .= "ORDER BY user.id ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->email;
            }

            return $list;
        }

        /*
         * Lista de convocatorias con aportes asociados
         */
        public static function calls () {

            $list = array();

            $query = static::query("
                SELECT
                    call.id as id,
                    call.name as name
                FROM `call`
                INNER JOIN invest
                    ON call.id = invest.call
                    AND invest.campaign = 1
                ORDER BY call.name ASC
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
                        $sql .= " AND invest.method != 'drop'";
                        break;
                    case 'call':
                        $sql .= " AND invest.method = 'drop'";
                        if (isset($call)) {
                            $sql .= " AND invest.campaign = 1 AND invest.call = :call";
                            $values['call'] = $call;
                        }
                        break;
                }
            }

            $query = static::query($sql, $values);
            $got = $query->fetchObject();
            if(!isset ($only)) {
                //actualiza el el amount en proyecto (aunque se quede a cero)
                static::query("UPDATE project SET amount = :num WHERE id = :project", array(':num' => (int) $got->much, ':project' => $project));

            } elseif ($only == 'users') {
                // actualiza el amount invertido por los usuarios
                static::query("UPDATE project SET amount_users = :num WHERE id = :project", array(':num' => (int) $got->much, ':project' => $project));

            } elseif ($only == 'call' && !empty($call)) {
                // actualiza el amount invertido por el convocador
                static::query("UPDATE project SET amount_call = :num WHERE id = :project", array(':num' => (int) $got->much, ':project' => $project));

            }

            return (int) $got->much;
        }

        /*
         * Aportes individuales a un proyecto
         */
        public static function investors ($project, $projNum = false, $showall = false) {
            $investors = array();

            $sql = "
                SELECT
                    invest.user as user,
                    user.name as name,
                    user.avatar as avatar,
                    user.worth as worth,
                    invest.amount as amount,
                    DATE_FORMAT(invest.invested, '%d/%m/%Y') as date,
                    user.hide as hide,
                    invest.droped as droped,
                    invest.campaign as campaign,
                    invest.call as `call`,
                    invest.anonymous as anonymous
                FROM    invest
                INNER JOIN user
                    ON  user.id = invest.user
                WHERE   project = ?
                AND     invest.status IN ('0', '1', '3', '4')
                ORDER BY invest.invested DESC, invest.id DESC
                ";

            $query = self::query($sql, array($project));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {

                $investor->avatar = Image::get($investor->avatar);

                // si el usuario es hide o el aporte es anonymo, lo ponemos como el usuario anonymous (avatar 1)
                if (!$showall && ($investor->hide == 1 || $investor->anonymous == 1)) {

                    // mantenemos la fecha del anonimo mas reciente
                    $anonymous_date = empty($investors['anonymous']->date) ? $investor->date : $investors['anonymous']->date;

                    $investors[] = (object) array(
                        'user' => 'anonymous',
                        'name' => Text::get('regular-anonymous'),
                        'projects' => null,
                        'avatar' => Image::get(1),
                        'worth' => $investor->worth,
                        'amount' => $investor->amount,
                        'date' => $investor->date,
                        'droped' => $investor->droped,
                        'campaign' => $investor->campaign,
                        'call' => $investor->call
                    );

                } else {

                    $investors[] = (object) array(
                        'user' => $investor->user,
                        'name' => $investor->name,
                        'projects' => $investor->projects,
                        'avatar' => $investor->avatar,
                        'worth' => $investor->worth,
                        'amount' => $investor->amount,
                        'date' => $investor->date,
                        'droped' => $investor->droped,
                        'campaign' => $investor->campaign,
                        'call' => $investor->call
                    );

                }

            }

            return $investors;
        }

        /*
         * Aportes individuales a un proyecto
         */
        public static function myInvestors ($owner, $limit = 999) {
            $investors = array();

            $sql = "
                SELECT
                    invest.user as id,
                    invest.user as user,
                    user.name as name,
                    user.avatar as avatar,
                    user.worth as worth,
                    user.num_invested as projects,
                    SUM(invest.amount) as amount,
                    DATE_FORMAT(invest.invested, '%d/%m/%Y') as date,
                    user.hide as hide,
                    invest.anonymous as anonymous
                FROM    invest
                INNER JOIN project
                    ON project.id = invest.project
                    AND project.owner = :id
                    AND project.status > 2
                INNER JOIN user
                    ON  user.id = invest.user
                WHERE   (invest.campaign IS NULL OR invest.campaign = '' )
                AND     invest.status IN ('0', '1', '3', '4')
                GROUP BY invest.user
                ORDER BY amount DESC
                LIMIT {$limit}
                ";

            $values = array(':id'=>$owner);

            //die(\sqldbg($sql, $values));


            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\User') as $investor) {

                // si el usuario es hide o el aporte es anonymo, lo ponemos como el usuario anonymous (avatar 1)
                if ( $investor->hide == 1 || $investor->anonymous == 1 ) {
                    // mantenemos la fecha del anonimo mas reciente
                    $investor->date = empty($investors['anonymous']->date) ? $investor->date : $investors['anonymous']->date;
                    $investor->user = 'anonymous';
                    $investors->avatar = 1;
                    $investors->name = Text::get('regular-anonymous');
                }

                $investors[$investor->user] = (object) array(
                    'user' => $investor->user,
                    'name' => $investor->name,
                    'projects' => (isset($investor->projects)) ? $investor->projects : $investor->get_numInvested,
                    'avatar' => Image::get($investor->avatar),
                    'worth' => (isset($investor->worth)) ? $investor->worth : $investor->get_worth,
                    'amount' => $investor->amount,
                    'date' => $investor->date
                );


            }

            return $investors;
        }

        /*
         *  Numero de inversores en un proyecto
         */
        public static function numInvestors ($project) {

            $debug = false;

            $values = array(':project' => $project);

            $sql = "SELECT  COUNT(DISTINCT(invest.user)) as investors, project.num_investors as num, project.num_messengers as pop
                FROM    invest
                INNER JOIN project
                    ON project.id = invest.project
                WHERE   invest.project = :project
                AND     invest.status IN ('0', '1', '3', '4')
                ";

            if ($debug) {
                echo \trace($values);
                echo $sql;
                die;
            }

            $query = static::query($sql, $values);
            if($got = $query->fetchObject()) {
                // si ha cambiado, actualiza el numero de inversores en proyecto
                if ($got->investors != $got->num) {
                    static::query("UPDATE project SET num_investors = :num, popularity = :pop WHERE id = :project", array(':num' => (int) $got->investors, ':pop' => ( $got->investors + $got->pop), ':project' => $project));
                }
            }

            return (int) $got->investors;
        }

        public static function my_numInvestors ($owner) {
            $values = array(':owner' => $owner);

            $sql = "SELECT  COUNT(DISTINCT(user)) as investors
                FROM    invest
                WHERE   project IN (SELECT id FROM project WHERE owner = :owner)
                AND     invest.status IN ('0', '1', '3', '4')
                ";

            $query = static::query($sql, $values);
            $got = $query->fetchObject();
            return (int) $got->investors;
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
                    // liberamos el capital riego
                    if (in_array($status, array(2, 4, 5))) {
                        $drop->setStatus(2);
                        self::query("UPDATE invest SET droped = NULL WHERE id = :id", array(':id' => $this->id));
                    } else {
                        $drop->setStatus($status);
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
                        charged = :charged
                    WHERE id = :id";
            if (self::query($sql, $values)) {

                // si tiene capital riego asociado pasa al mismo estado
                if (!empty($this->droped)) {
                    $drop = Invest::get($this->droped);
                    $drop->setStatus(1);
                }

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
         *  Modifica el campo resign para marcar/desmarcar donativo (independientemente de la recompensa)
         */
        public function setResign ($resign) {

            $values = array(
                ':id' => $this->id,
                ':resign' => $resign
            );

            $sql = "UPDATE invest SET resign = :resign WHERE id = :id";
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

                // si tiene capital riego asociado, lo liberamos
                if (!empty($this->droped)) {
                    $drop = Invest::get($this->droped);
                    if ($drop->setStatus(2)) {
                        self::query("UPDATE invest SET droped = NULL WHERE id = :id", array(':id' => $this->id));
                    }
                }

                return true;
            } else {
                return false;
            }

        }

        /*
         * Marcar esta aportación como cancelada
         */
        public function cancel ($fail = false) {

            $values = array(
                ':id' => $this->id,
                ':returned' => date('Y-m-d')
            );

            // si es un proyecto fallido el aporte se queda en el termometro
            if ($fail) {
                $status = 4;
            } else {
                $status = 2;
            }

            $sql = "UPDATE invest SET
                        returned = :returned,
                        status = $status
                    WHERE id = :id";

            if (self::query($sql, $values)) {

                // si tiene capital riego asociado, lo liberamos
                if (!empty($this->droped)) {
                    $drop = Invest::get($this->droped);
                    if ($drop->setStatus(2)) {
                        self::query("UPDATE invest SET droped = NULL WHERE id = :id", array(':id' => $this->id));
                    }
                }

                return true;
            } else {
                return false;
            }

        }

        /* Para marcar que es una incidencia */
        public static function setIssue($id) {
           self::query("UPDATE invest SET issue = 1 WHERE id = :id", array(':id' => $id));
        }

        /* Para desmarcar incidencia */
        public static function unsetIssue($id) {
           self::query("UPDATE invest SET issue = 0 WHERE id = :id", array(':id' => $id));
        }

        /*
         * Estados del aporte
         */
        public static function status ($id = null) {
            $array = array (
                -1 => 'Incompleto',
                0  => 'Preaprobado',
                1  => 'Cobrado por Goteo',
                2  => 'Cancelado',
                3  => 'Pagado al proyecto',
                4  => 'Devuelto (archivado)',
                5  => 'Reubicado'
            );

            if (isset($id)) {
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
                'drop'   => 'Riego',
                'cash'   => 'Manual'
            );
        }


        /*
         * Metodo para obtener datos para el informe completo (con incidencias y netos)
         */
         public static function getReportData($project, $status, $round, $passed) {
             $Data = array();

            // segun estado, ronda y fecha de pase a segunda
            // el cash(1) es igual para todos
            switch ($status) {
                case 0: // descartado
                case 1: // edicion
                case 2: // revision
                case 6: // caducado
                    // Para estos cuatro estados es lo mismo:
                    // - Solo finaciacion actual
                    //      (aunque hiciera una ronda, aunque se descartara en segunda ronda)
                    // - Puede tener aportes en cash
                    // - Puede tener aportes caducados (pero no los mostramos)
                    // - Si tiene aportes de paypal(0,1) o tpv(1) es un problema

                    // A ver si tiene cash
                    // si hay aportes de cash activos no es incidencia porque puede venir de taller
                    // a menos que sea de convocatoria (que deberian estar cancelados)
                    $inv_cash = self::getList(array(
                        'methods' => 'cash',
                        'projects' => $project,
                        'investStatus' => '1'
                    ));
                    if (!empty($inv_cash)) {
                        $Data['cash']['total']['fail'] = 0;
                        foreach ($inv_cash as $invId => $invest) {
                            $Data['cash']['total']['users'][$invest->user] = $invest->user;
                            $Data['cash']['total']['invests']++;
                            $Data['cash']['total']['amount'] += $invest->amount;
                            if ($invest->campaign == 1) {
                                $Data['cash']['total']['fail'] += $invest->amount;
                                $Data['note'][] = "Aporte de capital riego {$invId} debería estar cancelado. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
                            }
                        }
                    }

                    // A ver si tiene paypal
                    // si estan pendientes, ejecutados o pagados al proyecto es una incidencia
                    $inv_paypal = self::getList(array(
                        'methods' => 'paypal',
                        'projects' => $project
                    ));
                    if (!empty($inv_paypal)) {
//                        $Data['note'][] = "Los aportes de paypal son incidencias si están activos";
                        foreach ($inv_paypal as $invId => $invest) {
                            if (in_array($invest->investStatus, array(0, 1, 3))) {
                                $Data['paypal']['total']['fail'] += $invest->amount;
                                $Data['note'][] = "El aporte PayPal {$invId} no debería estar en estado '" . self::status($invest->investStatus) . "'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
                            }
                        }
                    }

                    // A ver si tiene tpv
                    // si estan pendientes, ejecutados o pagados al proyecto es una incidencia
                    $inv_tpv = self::getList(array(
                        'methods' => 'tpv',
                        'projects' => $project
                    ));
                    if (!empty($inv_tpv)) {
//                        $Data['note'][] = "Los aportes de tpv son incidencias si están activos";
                        foreach ($inv_tpv as $invId => $invest) {
                            if ($invest->investStatus == 1) {
                                $Data['tpv']['total']['fail'] += $invest->amount;
                                $Data['note'][] = "El aporte TPV {$invId} no debería estar en estado '" . self::status($invest->investStatus) . "'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
                            }
                        }
                    }


                    break;
                case 4: // financiado
                case 5: // exitoso
                    // en etos dos estados paypal(0) es incidencia en cualquier ronda
                    $p0 = (string) 'all';
                case 3: // en marcha
                    // si tiene fecha $project->passed de pase a segunda ronda: paypal(0) no es incidencia para los aportes de segunda ronda

                    if (Project\Conf::isOneRound($project)) {
                        $act_eq = (string) 'first';
                    } else {

                        if (!empty($passed)) {
                            if ($round == 1) {
                                // esto es mal
                                $Data['note'][] = "ATENCION! Está marcada la fecha de pase a segunda ronda (el {$passed}) pero sique en primera ronda!!!";
                                $act_eq = (string) 'first';
                            } else {
                                // en segunda ronda
                                if (!isset($p0)) {
                                    $p0 = (string) 'first'; // paypal(0) es incidencia paralos de primera ronda solamente
                                }
                                // si está en segunda ronda; la financiacion actual es un merge de usuarios y suma de aportes correctos, incidencias, correctos y cantidad total
                                $act_eq = (string) 'sum';
                            }
                        } else {
                            // si no tiene fecha de pase y esta en ronda 2: es un problema se trata como solo financiacion actual y paypal(0) no son incidencias
                            if ($round == 2) {
                                $Data['note'][] = "ATENCION! En segunda ronda pero NO está marcada la fecha de pase a segunda ronda!!!";
                                $act_eq = (string) 'first';
                            } else {
                                // ok, en primera ronda sin  fecha marcada, informe solo actual = primera
                                $act_eq = (string) 'first';
                            }
                        }

                    }

                    // si solamente financiacion actual=primera
                    //   simple: no filtramos fecha
                    if ($act_eq === 'first') {
                        // CASH
                        $inv_cash = self::getList(array(
                            'methods' => 'cash',
                            'projects' => $project,
                            'investStatus' => '1'
                        ));
                        if (!empty($inv_cash)) {
                            $Data['cash']['first']['fail'] = 0;
                            foreach ($inv_cash as $invId => $invest) {
                                $Data['cash']['first']['users'][$invest->user] = $invest->user;
                                $Data['cash']['first']['invests']++;
                                $Data['cash']['first']['amount'] += $invest->amount;
                            }
                            $Data['cash']['total'] = $Data['cash']['first'];
                        }

                        // Cash no cobrados (aportes fantasma)
                        $inv_ghost = self::getList(array(
                            'methods' => 'cash',
                            'projects' => $project,
                            'investStatus' => '0'
                        ));
                        if (!empty($inv_ghost)) {
                            $Data['ghost']['first']['fail'] = 0;
                            foreach ($inv_ghost as $invId => $invest) {
                                $Data['ghost']['first']['users'][$invest->user] = $invest->user;
                                $Data['ghost']['first']['invests']++;
                                $Data['ghost']['first']['amount'] += $invest->amount;
                            }
                            $Data['ghost']['total'] = $Data['ghost']['first'];
                        }

                        // TPV
                        $inv_tpv = self::getList(array(
                            'methods' => 'tpv',
                            'projects' => $project,
                            'investStatus' => '1'
                        ));
                        if (!empty($inv_tpv)) {
                            $Data['tpv']['first']['fail'] = 0;
                            foreach ($inv_tpv as $invId => $invest) {
                                $Data['tpv']['first']['users'][$invest->user] = $invest->user;
                                $Data['tpv']['first']['invests']++;
                                $Data['tpv']['first']['amount'] += $invest->amount;
                            }
                            $Data['tpv']['total'] = $Data['tpv']['first'];
                        }


                        // PAYPAL
                        $inv_paypal = self::getList(array(
                            'methods' => 'paypal',
                            'projects' => $project
                        ));
                        if (!empty($inv_paypal)) {
                            $Data['paypal']['first']['fail'] = 0;
                            foreach ($inv_paypal as $invId => $invest) {
                                if (in_array($invest->investStatus, array('0', '1', '3'))) {
                                    $Data['paypal']['first']['users'][$invest->user] = $invest->user;
                                    $Data['paypal']['first']['invests']++;
                                    $Data['paypal']['first']['amount'] += $invest->amount;
                                }
                            }
                            $Data['paypal']['total'] = $Data['paypal']['first'];
                        }

                        // DROP
                        $inv_drop = self::getList(array(
                            'methods' => 'drop',
                            'projects' => $project,
                            'investStatus' => '1'
                        ));
                        if (!empty($inv_drop)) {
                            $Data['drop']['first']['fail'] = 0;
                            foreach ($inv_drop as $invId => $invest) {
                                $Data['drop']['first']['users'][$invest->user] = $invest->user;
                                $Data['drop']['first']['invests']++;
                                $Data['drop']['first']['amount'] += $invest->amount;
                            }
                            $Data['drop']['total'] = $Data['drop']['first'];
                        }

                    } elseif ($act_eq === 'sum') {
                        // complicado: primero los de primera ronda, luego los de segunda ronda sumando al total
                        // calcular ultimo dia de primera ronda segun la fecha de pase
                        $passtime = strtotime($passed);
                        $last_day = date('Y-m-d', \mktime(0, 0, 0, date('m', $passtime), date('d', $passtime)-1, date('Y', $passtime)));

                        // CASH first
                        $inv_cash = self::getList(array(
                            'methods' => 'cash',
                            'projects' => $project,
                            'investStatus' => '1',
                            'date_until' => $last_day
                        ));
                        if (!empty($inv_cash)) {
                            $Data['cash']['first']['fail'] = 0;
                            foreach ($inv_cash as $invId => $invest) {
                                $Data['cash']['first']['users'][$invest->user] = $invest->user;
                                $Data['cash']['first']['invests']++;
                                $Data['cash']['first']['amount'] += $invest->amount;
                            }
                            $Data['cash']['total'] = $Data['cash']['first'];
                        }

                        // Cash no cobrados (aportes fantasma) first
                        $inv_ghost = self::getList(array(
                            'methods' => 'cash',
                            'projects' => $project,
                            'investStatus' => '0',
                            'date_until' => $last_day
                        ));
                        if (!empty($inv_ghost)) {
                            $Data['ghost']['first']['fail'] = 0;
                            foreach ($inv_ghost as $invId => $invest) {
                                $Data['ghost']['first']['users'][$invest->user] = $invest->user;
                                $Data['ghost']['first']['invests']++;
                                $Data['ghost']['first']['amount'] += $invest->amount;
                            }
                            $Data['ghost']['total'] = $Data['ghost']['first'];
                        }

                        // TPV first
                        $inv_tpv = self::getList(array(
                            'methods' => 'tpv',
                            'projects' => $project,
                            'investStatus' => '1',
                            'date_until' => $last_day
                        ));
                        if (!empty($inv_tpv)) {
                            $Data['tpv']['first']['fail'] = 0;
                            foreach ($inv_tpv as $invId => $invest) {
                                $Data['tpv']['first']['users'][$invest->user] = $invest->user;
                                $Data['tpv']['first']['invests']++;
                                $Data['tpv']['first']['amount'] += $invest->amount;
                            }
                            $Data['tpv']['total'] = $Data['tpv']['first'];
                        }


                        // PAYPAL first
                        $inv_paypal = self::getList(array(
                            'methods' => 'paypal',
                            'projects' => $project,
                            'date_until' => $last_day
                        ));
                        if (!empty($inv_paypal)) {
                            $Data['paypal']['first']['fail'] = 0;
                            foreach ($inv_paypal as $invId => $invest) {
                                if (in_array($invest->investStatus, array('0', '1', '3'))) {
                                    // a ver si cargo pendiente es incidencia...
                                    if ($invest->investStatus == 0 && ($p0 === 'first' || $p0 === 'all')) {
                                        $Data['paypal']['first']['fail'] += $invest->amount;
                                        $Data['note'][] = "El aporte paypal {$invId} no debería estar en estado '".self::status($invest->investStatus)."'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
                                        continue;
                                    }
                                    $Data['paypal']['first']['users'][$invest->user] = $invest->user;
                                    $Data['paypal']['first']['invests']++;
                                    $Data['paypal']['first']['amount'] += $invest->amount;
                                }
                            }
                            $Data['paypal']['total'] = $Data['paypal']['first'];
                        }

                        // DROP first
                        $inv_drop = self::getList(array(
                            'methods' => 'drop',
                            'projects' => $project,
                            'investStatus' => '1',
                            'date_until' => $last_day
                        ));
                        if (!empty($inv_drop)) {
                            $Data['drop']['first']['fail'] = 0;
                            foreach ($inv_drop as $invId => $invest) {
                                $Data['drop']['first']['users'][$invest->user] = $invest->user;
                                $Data['drop']['first']['invests']++;
                                $Data['drop']['first']['amount'] += $invest->amount;
                            }
                            $Data['drop']['total'] = $Data['drop']['first'];
                        }

                        // -- Los de segunda

                        // CASH  second
                        $inv_cash = self::getList(array(
                            'methods' => 'cash',
                            'projects' => $project,
                            'investStatus' => '1',
                            'date_from' => $passed

                        ));
                        if (!empty($inv_cash)) {
                            $Data['cash']['second']['fail'] = 0;
                            foreach ($inv_cash as $invId => $invest) {
                                $Data['cash']['second']['users'][$invest->user] = $invest->user;
                                $Data['cash']['total']['users'][$invest->user] = $invest->user;
                                $Data['cash']['second']['invests']++;
                                $Data['cash']['total']['invests']++;
                                $Data['cash']['second']['amount'] += $invest->amount;
                            }
                            $Data['cash']['total']['amount'] += $Data['cash']['second']['amount'];
                        }

                        // CASH no cobrado (fantasmas)  second
                        $inv_ghost = self::getList(array(
                            'methods' => 'cash',
                            'projects' => $project,
                            'investStatus' => '0',
                            'date_from' => $passed

                        ));
                        if (!empty($inv_ghost)) {
                            $Data['ghost']['second']['fail'] = 0;
                            foreach ($inv_ghost as $invId => $invest) {
                                $Data['ghost']['second']['users'][$invest->user] = $invest->user;
                                $Data['ghost']['total']['users'][$invest->user] = $invest->user;
                                $Data['ghost']['second']['invests']++;
                                $Data['ghost']['total']['invests']++;
                                $Data['ghost']['second']['amount'] += $invest->amount;
                            }
                            $Data['ghost']['total']['amount'] += $Data['ghost']['second']['amount'];
                        }

                        // TPV  second
                        $inv_tpv = self::getList(array(
                            'methods' => 'tpv',
                            'projects' => $project,
                            'investStatus' => '1',
                            'date_from' => $passed

                        ));
                        if (!empty($inv_tpv)) {
                            $Data['tpv']['second']['fail'] = 0;
                            foreach ($inv_tpv as $invId => $invest) {
                                $Data['tpv']['second']['users'][$invest->user] = $invest->user;
                                $Data['tpv']['total']['users'][$invest->user] = $invest->user;
                                $Data['tpv']['second']['invests']++;
                                $Data['tpv']['total']['invests']++;
                                $Data['tpv']['second']['amount'] += $invest->amount;
                            }
                            $Data['tpv']['total']['amount'] += $Data['tpv']['second']['amount'];
                        }

                        // PAYPAL second
                        $inv_paypal = self::getList(array(
                            'methods' => 'paypal',
                            'projects' => $project,
                            'date_from' => $passed
                        ));
                        if (!empty($inv_paypal)) {
                            $Data['paypal']['second']['fail'] = 0;
                            foreach ($inv_paypal as $invId => $invest) {
                                if (in_array($invest->investStatus, array('0', '1', '3'))) {
                                    // a ver si cargo pendiente es incidencia...
                                    if ($invest->investStatus == 0 && $p0 === 'all') {
                                        $Data['paypal']['second']['fail'] += $invest->amount;
                                        $Data['paypal']['total']['fail'] += $invest->amount;
                                        $Data['note'][] = "El aporte paypal {$invId} no debería estar en estado '".self::status($invest->investStatus)."'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
                                        continue;
                                    }
                                    $Data['paypal']['second']['users'][$invest->user] = $invest->user;
                                    $Data['paypal']['total']['users'][$invest->user] = $invest->user;
                                    $Data['paypal']['second']['invests']++;
                                    $Data['paypal']['total']['invests']++;
                                    $Data['paypal']['second']['amount'] += $invest->amount;
                                }
                            }
                            $Data['paypal']['total']['amount'] += $Data['paypal']['second']['amount'];
                        }

                        // DROP second
                        $inv_drop = self::getList(array(
                            'methods' => 'drop',
                            'projects' => $project,
                            'investStatus' => '1',
                            'date_from' => $passed
                        ));
                        if (!empty($inv_drop)) {
                            $Data['drop']['second']['fail'] = 0;
                            foreach ($inv_drop as $invId => $invest) {
                                $Data['drop']['second']['users'][$invest->user] = $invest->user;
                                $Data['drop']['total']['users'][$invest->user] = $invest->user;
                                $Data['drop']['second']['invests']++;
                                $Data['drop']['total']['invests']++;
                                $Data['drop']['second']['amount'] += $invest->amount;
                            }
                            $Data['drop']['total']['amount'] += $Data['drop']['second']['amount'];
                        }

                    } else {
                        $Data['note'][] = 'ERROR INFORME!! No se ha calculado bien el parametro $act_eq';
                    }



                    break;
            }

            // incidencias
            $Data['issues'] = self::getReportIssues($project);

             return $Data;
         }

         public static function getReportIssues($id) {

             $status = self::status();

             $list = array();
             $drops = array();

             $values = array(':id' => $id);

             // el riego de incidencias
             $sql = "SELECT droped FROM invest
                        WHERE issue = 1
                        AND droped IS NOT NULL
                        AND invest.project = :id";

             $query = self::query($sql, $values);
             foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                 $drops[] = $item->droped;
             }

             $sqlFilter = (!empty($drops)) ? "OR invest.id IN (".implode(',', $drops).")" : '';

             // las incidencias
             $sql = "SELECT
                        invest.id as invest,
                        invest.user as user,
                        invest.amount as amount,
                        invest.status as status,
                        user.id as user_id,
                        user.name as user_name,
                        user.email as user_email
                    FROM invest
                    INNER JOIN user
                        ON user.id=invest.user
                    WHERE invest.project = :id
                    AND invest.issue = 1
                    $sqlFilter
                    ORDER BY invest.id DESC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $item->statusName = $status[$item->status];

                if (in_array($item->id, $drops))
                    $item->statusName .= ' (CAPITAL RIEGO)';

                // datos del usuario. Eliminación de user::getMini

                $item->userName = $item->user_name;
                $item->userEmail = $item->user_email;

                $list[] = $item;
            }

            return $list;


         }

         public static function setDetail($id, $type, $log) {
             $values = array(
                ':id' => $id,
                ':type' => $type,
                ':log' => $log
            );

            $sql = "REPLACE INTO invest_detail (invest, type, log, date)
                VALUES (:id, :type, :log, NOW())";

            self::query($sql, $values);


         }

         public static function getDetails($id) {

             $list = array();

             $values = array(':id' => $id);

             $sql = "SELECT
                        type,
                        log,
                        DATE_FORMAT(invest_detail.date, '%d/%m/%Y %H:%i:%s') as date
                    FROM invest_detail
                    WHERE invest = :id
                    ORDER BY invest_detail.date DESC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[] = $item;
            }
            return $list;
         }

        /**
         * Tratamiento de aportes pendientes en cron/execute
         */
        public static function getPending($id) {

            // esta distinción de métodos @TODO capa de pagos

            $query = \Goteo\Core\Model::query("
                SELECT  *
                FROM  invest
                WHERE   invest.project = ?
                AND     (invest.status = 0
                    OR (invest.method = 'tpv'
                        AND invest.status = 1
                    )
                    OR (invest.method = 'cash'
                        AND invest.status = 1
                    )
                )
                AND (invest.campaign IS NULL OR invest.campaign = 0)
                ", array($id));

            return $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');
        }

        /**
         * Keep updated any related data entities
         *
         * @return success boolean
         */
        public function keepUpdated($call_id = null) {

            // numero de proyectos aportados
            User::numInvested($this->user);

            // cantidad total aportada en goteo
            $amount = User::updateAmount($this->user);
            // nivel de meritocracia
            User::updateWorth($this->user, $amount);

            // proyecto
            self::invested($this->project); // conseguido
            self::numInvestors($this->project); // inversores


            // si es aporte de riego
            if (!empty($call_id)) {
                self::invested($this->project, 'users');
                self::invested($this->project, 'call', $call_id);
            }

        }

    }

}
