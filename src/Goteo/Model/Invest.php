<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model;

use Goteo\Core\Model;
use Goteo\Application\Lang;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Model\User;
use Goteo\Model\User\Pool;
use Goteo\Model\Project;
use Goteo\Model\Invest\InvestLocation;
use Goteo\Payment\Payment;

/**
 * Invest Model
 */
class Invest extends \Goteo\Core\Model {

    // @deprecated
    const METHOD_PAYPAL = 'paypal';
    const METHOD_TPV    = 'tpv';
    const METHOD_CASH   = 'cash';
    const METHOD_DROP   = 'drop';
    const METHOD_POOL   = 'pool';

    // INVEST STATUS IDs
    const STATUS_PROCESSING = -1; // payment gateway not reached yet or just a failed payment
    const STATUS_PENDING    = 0;  // In a status that requires post-processing (former paypal preapprovals)
    const STATUS_CHARGED    = 1;  // charged by the platform
    const STATUS_CANCELLED  = 2;  // refunded to the user by some admin manual action, won't be added to any total
    const STATUS_PAID       = 3;  // paid to the project (successful project) NOT REALLY USED
    const STATUS_RETURNED   = 4;  // automatically refunded to the user due a failed project
    const STATUS_RELOCATED  = 5;  // deprecated status
    const STATUS_TO_POOL    = 6;  // refunded to user's pool

    static $ACTIVE_STATUSES = [self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_PAID];
    static $FAILED_STATUSES = [self::STATUS_RELOCATED, self::STATUS_RETURNED, self::STATUS_TO_POOL, self::STATUS_CANCELLED];
    static $RETURNED_STATUSES = [self::STATUS_RETURNED, self::STATUS_CANCELLED];
    // STATUS_CANCELLED may rise the achieved amount in projects but it is not included in fee/comissions calculations
    static $RAISED_STATUSES = [self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_PAID, self::STATUS_RETURNED, self::STATUS_TO_POOL];
    static $RAW_STATUSES = [self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_PAID, self::STATUS_CANCELLED, self::STATUS_RETURNED, self::STATUS_TO_POOL];

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
        $transaction, // id de la transacción / token expresscheckout
        $method, // metodo de pago paypal/tpv/cash/drop/pool (ver Invest::methods() )
        $status, //estado en el que se encuentra esta aportación:
                // -1 en proceso, 0 pendiente, 1 cobrado (charged), 2 devuelto (returned)
        $issue = false, // aporte con incidencia
        $anonymous = false, //no debe aparecer su careto ni su nombre, nivel, etc... pero si aparece en la cuenta de cofinanciadores y de aportes
        $resign = false, //renuncia a cualquier recompensa
        $invested, //fecha en la que se ha iniciado el aporte
        $charged, //fecha en la que se ha cargado el importe del aporte a la cuenta del usuario
        $returned, //fecha en la que se ha devuelto el importe al usurio por cancelación bancaria
        $rewards = array(), //datos de las recompensas que le corresponden
        $address = null,  // dirección de envio de la recompensa y datos de regalo
        $drops = null, // id del aporte que provoca este riego
        $droped = null, // id del riego generado por este aporte
        $campaign = false, // si es un aporte de capital riego
        $call = null, // aportes que tienen capital riego asociado
        $matcher = null, // invests with matcher funding associated
        $pool = false; // aportes a reservar si el proyecto falla

    // añadir los datos del cargo

    /*
     * Estados del aporte
     */
    public static function status ($id = null) {
        $array = array (
            self::STATUS_PROCESSING => Text::get('invest-status-processing'),
            self::STATUS_PENDING    => Text::get('invest-status-pending'),
            self::STATUS_CHARGED    => Text::get('invest-status-charged'),
            self::STATUS_CANCELLED  => Text::get('invest-status-cancelled'),
            self::STATUS_PAID       => Text::get('invest-status-paid'),
            self::STATUS_RETURNED   => Text::get('invest-status-returned'),
            self::STATUS_RELOCATED  => Text::get('invest-status-relocated'),
            self::STATUS_TO_POOL    => Text::get('invest-status-to-pool')
        );

        if (isset($id)) {
            return $array[$id];
        } else {
            return $array;
        }
    }

    /* handy methods */
    public function inPool() {
        return $this->status == self::STATUS_TO_POOL;
    }

    public function isCharged() {
        return in_array($this->status, self::$ACTIVE_STATUSES);
    }

    public function isReturned() {
        return in_array($this->status, [self::STATUS_RELOCATED, self::STATUS_RETURNED, self::STATUS_TO_POOL]);
    }

    public function isCancelled() {
        return $this->status == self::STATUS_CANCELLED;
    }

    public function getStatusText($simple = false) {
        $status = $this->status;
        if($simple) {
            if($this->isReturned()) {
                $status = self::STATUS_RETURNED;
            }
            if($this->isCharged()) {
                $status = self::STATUS_CHARGED;
            }
        }
        return self::status($status);
    }

    /*
     * Métodos de pago
     *
     * paypal puede ser tanto preappoval como checkout
     *
     * Gotas es el uso de aportado a crédito (ver Model\User\Pool)
     */
    public static function methods () {
        $methods = Payment::getMethods();
        $ret = [];
        foreach($methods as $id => $clas) {
            $name = Payment::getMethod($id)->getName();
            $ret[$id] = $name ? $name : $id;
        }
        return $ret;
    }

    /*
     *  Devuelve datos de una inversión
     */
    public static function get ($id) {
        return self::getFrom('id', $id);
    }

    public static function getFrom($key = 'id', $value) {
        $query = static::query("
            SELECT  *
            FROM    invest
            WHERE   `$key` = :value
            ORDER BY id DESC LIMIT 1", array(':value' => $value));
        $invest = $query->fetchObject(__CLASS__);

        if(!$invest instanceOf Invest) return false;
        $id = $invest->id;

        $invest->rewards = $invest->getRewards();

        $invest->address = $invest->getAddress();

        return $invest;
    }

    /*
     * Lista de inversiones (individuales) de un proyecto
     *
     * el parametro filter es para la gestion de recompensas (no es un autentico filtro, hay ordenaciones y hay filtros)
     */
    public static function getAll ($project, $filter = null) {

        $invests = array();

        $values = array(':p' => $project, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL);

        $query = static::query("
            SELECT  *
            FROM  invest
            WHERE   invest.project = :p
            AND invest.status IN (:s0, :s1, :s3, :s4, :s5)
            ", $values);
        // echo \sqldbg($sql, $values);
        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $invest) {

            $invest->rewards = $invest->getRewards();
            $invest->address = $invest->getAddress();

            $invests[$invest->id] = $invest;
        }

        return $invests;
    }


    /**
     * reusable sql filters for searching in invests table
     */
    public static function getSQLFilter($filters = []) {
        $values = [];
        $sqlFilter = [];

        if (!empty($filters['id'])) {
            $sqlFilter[] = "invest.id = :id";
            $values[':id'] = $filters['id'];
        }
        if (!empty($filters['methods'])) {
            $i = 0;
            $parts = [];
            if(!is_array($filters['methods'])) $filters['methods'] = [$filters['methods']];
            foreach($filters['methods'] as $u) {
                $parts[] = ":method$i";
                $values[":method$i"] = is_object($u) ? $u->id : $u;
                $i++;
            }
            $sqlFilter[] = 'invest.method IN(' . implode(',', $parts) . ')';

        }
        if (is_numeric($filters['projectStatus'])) {
            $sqlFilter[] = "project.status = :projectStatus";
            $values[':projectStatus'] = $filters['projectStatus'];
        }
        elseif (is_array($filters['projectStatus'])) {
            $parts = [];
            foreach(array_values($filters['projectStatus']) as $i => $status) {
                if(is_numeric($status)) {
                    $parts[] = ':projectStatus' . $i;
                    $values[':projectStatus' . $i] = $status;
                }
            }
            if($parts) $sqlFilter[] = "project.status IN (" . implode(',', $parts) . ")";
        }
        if (!empty($filters['consultants'])) {
            $i = 0;
            $parts = [];
            if(!is_array($filters['consultants'])) $filters['consultants'] = [$filters['consultants']];
            foreach($filters['consultants'] as $u) {
                $parts[] = ":consultant$i";
                $values[":consultant$i"] = is_object($u) ? $u->id : $u;
                $i++;
            }
            $sqlFilter[] = 'invest.project IN (SELECT project FROM user_project WHERE user IN(' . implode(',', $parts) . '))';
        }

        if (is_numeric($filters['status'])) {
            $sqlFilter[] = "invest.status = :status";
            $values[':status'] = $filters['status'];
        }
        elseif (is_array($filters['status'])) {
            $parts = [];
            foreach(array_values($filters['status']) as $i => $status) {
                if(is_numeric($status)) {
                    $parts[] = ':status' . $i;
                    $values[':status' . $i] = $status;
                }
            }
            if($parts) $sqlFilter[] = "invest.status IN (" . implode(',', $parts) . ")";
        }
        if (!empty($filters['amount'])) {
            $sqlFilter[] = "invest.amount >= :amount";
            $values[':amount'] = $filters['amount'];
        }
        if (!empty($filters['maxamount'])) {
            $sqlFilter[] = "invest.amount <= :maxamount";
            $values[':maxamount'] = $filters['maxamount'];
        }
        if (!empty($filters['name'])) {
            $sqlFilter[] = "invest.user IN (SELECT id FROM user WHERE (name LIKE :name OR email LIKE :name))";
            $values[':name'] = "%{$filters['name']}%";
        }
        if (!empty($filters['projects'])) {
            $parts = [];
            if(!is_array($filters['projects'])) $filters['projects'] = [$filters['projects']];
            foreach($filters['projects'] as $i => $prj) {
                $parts[] = ':prj' . $i;
                $values[':prj' . $i] = is_object($prj) ? $prj->id : $prj;
            }
            $sqlFilter[] = "invest.project IN (" . implode(',', $parts) . ")";
        }
        if (!empty($filters['users'])) {
            $i = 0;
            $parts = [];
            if(!is_array($filters['users'])) $filters['users'] = [$filters['users']];
            foreach($filters['users'] as $u) {
                $parts[] = ":user$i";
                $values[":user$i"] = is_object($u) ? $u->id : $u;
                $i++;
            }
            $sqlFilter[] = 'invest.user IN(' . implode(',', $parts) . ')';
        }
        if (!empty($filters['name'])) {
            $sqlFilter[] = "invest.user IN (SELECT id FROM user WHERE (id LIKE :name OR name LIKE :name OR email LIKE :name))";
            $values[':name'] = "%{$filters['name']}%";
        }
        if (!empty($filters['calls'])) {
            $parts = [];
            if(!is_array($filters['calls'])) $filters['calls'] = [$filters['calls']];
            foreach($filters['calls'] as $i => $call) {
                $parts[] = ':call' . $i;
                $values[':call' . $i] = is_object($call) ? $call->id : $call;
            }
            // This may lead to a confusion, some invests are in projects belonging to a call
            // but they have the invest.call field empty:
            // $sqlFilter[] = "invest.call IN (" . implode(',', $parts) . ")";
            // Search all invests where the project is in that call instead:
            $sqlFilter[] = 'invest.project IN (SELECT project FROM call_project WHERE call_project.call IN (' . implode(',', $parts) . '))';
        }
        if (!empty($filters['matchers'])) {
            $parts = [];
            if(!is_array($filters['matchers'])) $filters['matchers'] = [$filters['matchers']];
            foreach($filters['matchers'] as $i => $matcher) {
                $parts[] = ':matcher' . $i;
                $values[':matcher' . $i] = is_object($matcher) ? $matcher->id : $matcher;
            }
            // Same case as calls
            // $sqlFilter[] = "invest.matcher IN (" . implode(',', $parts) . ")";
            $sqlFilter[] = 'invest.project IN (SELECT project_id FROM matcher_project WHERE matcher_project.matcher_id IN (' . implode(',', $parts) . '))';
        }
        if (!empty($filters['issue'])) {
            switch ($filters['issue']) {
                case 'show':
                    $sqlFilter[] = "invest.issue = 1";
                    break;
                case 'hide':
                    $sqlFilter[] = "invest.issue = 0";
                    break;
            }
        }
        if (!empty($filters['procStatus'])) {
            switch ($filters['procStatus']) {
                case 'first': // en primera ronda
                    $sqlFilter[] = "project.status = " . Project::STATUS_IN_CAMPAIGN . " AND (project.passed IS NULL OR project.passed = '0000-00-00' )";
                    break;
                case 'second': // en segunda ronda
                    $sqlFilter[] = "project.status = " . Project::STATUS_IN_CAMPAIGN . " AND (project.passed IS NOT NULL AND project.passed != '0000-00-00' )";
                    break;
                case 'completed': // financiados
                    $sqlFilter[] = "project.status IN (" . Project::STATUS_FUNDED . ',' . Project::STATUS_FULFILLED . ")";
                    break;
                case 'archived': // no financiados
                    $sqlFilter[] = "project.status = " .  Project::STATUS_UNFUNDED;
                    break;
            }
        }
        // else { $sqlFilter[] = "invest.campaign = 0"; }
        if (!empty($filters['types'])) {
            switch ($filters['types']) {
                case 'donative':
                    $sqlFilter[] = "invest.resign = 1";
                    break;
                case 'nondonative':
                    $sqlFilter[] = "invest.resign = 0";
                    $sqlFilter[] = "invest.campaign = 0";
                    break;
                case 'anonymous':
                    $sqlFilter[] = "invest.anonymous = 1";
                    break;
                case 'nonanonymous':
                    $sqlFilter[] = "invest.anonymous = 0";
                    break;
                case 'wallet':
                    $sqlFilter[] = "ISNULL(invest.project)";
                    break;
                case 'to_wallet':
                    $sql_failed_projects = "SELECT id FROM project WHERE status NOT IN (" . Project::STATUS_IN_CAMPAIGN . ',' . Project::STATUS_FUNDED. ',' . Project::STATUS_FULFILLED . ")";

                    $values[':status0'] = self::STATUS_TO_POOL;
                    $sqlFilter[] = "(invest.project IN ($sql_failed_projects) OR ISNULL(invest.project) OR invest.status = :status0)";
                    $sqlFilter[] = "invest.method!='pool'";
                    $sqlFilter[] = "invest.status>0";
                    $sqlFilter[] = "invest.pool=1";

                    break;
                case 'matcher_wallet':
                    $sqlFilter[] = "ISNULL(invest.project)";
                    $sqlFilter[] = "invest.user IN (SELECT user_id FROM matcher_user)";
                    break;
                case 'to_matcher_wallet':
                    $sqlFilter[] = "ISNULL(invest.project)";
                    $sqlFilter[] = "invest.method!='pool'";
                    $sqlFilter[] = "invest.user IN (SELECT user_id FROM matcher_user)";
                    break;
                case 'from_wallet':
                    $sqlFilter[] = "invest.method='pool'";
                    $values[':status0'] = self::STATUS_PAID;
                    $values[':status1'] = self::STATUS_CHARGED;
                    $sqlFilter[] = "invest.status IN (:status0, :status1)";
                    break;
                case 'from_matcher_wallet':
                    $sqlFilter[] = "invest.method='pool'";
                    $values[':status0'] = self::STATUS_PAID;
                    $values[':status1'] = self::STATUS_CHARGED;
                    $sqlFilter[] = "invest.status IN (:status0, :status1)";
                    $sqlFilter[] = "invest.user IN (SELECT user_id FROM matcher_user)";
                    break;
                case 'project':
                    $sqlFilter[] = "!ISNULL(invest.project)";
                    break;
                case 'matcher':
                    $sqlFilter[] = "invest.campaign = 1";
                    $sqlFilter[] = "invest.project IN (SELECT project_id FROM matcher_project)";
                    break;
                case 'matchfunding': // all invests involving matchfunding campaings
                    $sqlFilter[] = "(invest.project IN (SELECT project_id FROM matcher_project) OR
                                     invest.project IN (SELECT project FROM call_project))";
                    break;
                case 'manual':
                    $sqlFilter[] = "!ISNULL(invest.admin)";
                    break;
                case 'campaign':
                    $sqlFilter[] = "!ISNULL(invest.droped)";
                    break;
                case 'drop':
                    $sqlFilter[] = "invest.campaign = 1";
                    break;
                case 'nondrop':
                    $sqlFilter[] = "invest.campaign = 0";
                    break;
                case 'pool':
                    $sqlFilter[] = "invest.pool = 1";
                    break;
                case 'nopool':
                    $sqlFilter[] = "invest.pool = 0";
                    break;
                case 'fulfilled':
                    $sqlFilter[] = "invest_reward.fulfilled = 1";
                    $sqlFilter[] = "invest.resign = 0";
                    $sqlFilter[] = "invest.campaign = 0";
                    break;
                case 'pending':
                    $sqlFilter[] = "invest_reward.fulfilled = 0";
                    $sqlFilter[] = "invest.resign = 0";
                    $sqlFilter[] = "invest.campaign = 0";
                    break;
            }
        }

        if (!empty($filters['review'])) {
            switch ($filters['review']) {
                case 'collect': //  Recaudado: tpv cargado o paypal pendiente
                    $sqlFilter[] = "((invest.method = 'tpv' AND invest.status = 1)
                                    OR (invest.method = 'paypal' AND invest.status = 0))";
                    break;
                case 'online': // Solo pagos online
                    $sqlFilter[] = "(invest.method = 'tpv' OR invest.method = 'paypal')";
                    break;
                case 'paypal': // Paypal pendientes o ok
                    $sqlFilter[] = "(invest.method = 'paypal' AND (invest.status = -1 OR invest.status = 0))";
                    break;
                case 'tpv': // Tpv pendientes o ok
                    $sqlFilter[] = "(invest.method = 'tpv' AND (invest.status = -1 OR invest.status = 1))";
                    break;
            }
        }

        if (!empty($filters['date_from'])) {
            $sqlFilter[] = "invest.invested >= :date_from";
            $values[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_until'])) {
            $sqlFilter[] = "invest.invested <= :date_until";
            $values[':date_until'] = $filters['date_until'];
        }
        if (!empty($filters['datetime_from'])) {
            $sqlFilter[] = "invest.datetime >= :datetime_from";
            $values[':datetime_from'] = $filters['datetime_from'];
        }
        if (!empty($filters['datetime_until'])) {
            $sqlFilter[] = "invest.datetime <= :datetime_until";
            $values[':datetime_until'] = $filters['datetime_until'];
        }
        if (isset($filters['fulfilled'])) {
            $sqlFilter[] = "invest_reward.fulfilled=" . ($filters['fulfilled'] ? '1' : '0');
        }

        if ((int)$filters['reward']) {
            $sqlFilter[] = "invest_reward.reward=" . (int)$filters['reward'];
        }

        if (isset($filters['node'])) {
            // $sqlFilter[] = "(project.node = :node OR ISNULL(invest.project))";
            if($filters['node']) $sqlFilter[] = "project.node = :node";
            else $sqlFilter[] = "ISNULL(invest.project)";
            $values[':node'] = $filters['node'];
        }

        if($sqlFilter) {
            $sqlFilter = 'WHERE ' . implode(' AND ', $sqlFilter);
        } else {
            $sqlFilter = '';
        }

        return [$sqlFilter, $values];
    }

    /**
     * Lista de aportes individuales
     *
     * Los filtros vienen de la gestión de aportes
     * Los datos que sacamos: usuario, proyecto, cantidad, estado de proyecto, estado de aporte, fecha de aporte, tipo de aporte, campaña
     * .... anonimo, resign, etc...
     * @param $count if true, counts the total. If it's 'money' sum all money instead, if 'users' gets the number of different users
     */
    public static function getList ($filters = array(), $node = null, $offset = 0, $limit = 10, $count = false, $order = 'id DESC') {

        $list = [];
        if($node) $filters['node'] = $node; // For old compatibility
        list($sqlFilter, $values) = self::getSQLFilter($filters);

        if($count) {
            if($count === 'all') {
                $what = 'SUM(invest.amount) AS total_amount,
                COUNT(invest.id) AS total_invests,
                COUNT(DISTINCT invest.user) AS total_users';
            }
            elseif($count === 'money') {
                $what = 'SUM(invest.amount)';
            }
            elseif($count === 'user') {
                $what = 'COUNT(DISTINCT invest.user)';
            }
            else {
                $what = 'COUNT(invest.id)';
            }
            // Return count
            $sql = "SELECT DISTINCT $what
                FROM invest
                LEFT JOIN project
                    ON invest.project = project.id
                LEFT JOIN user
                    ON invest.admin = user.id
                LEFT JOIN invest_reward
                    ON invest_reward.invest = invest.id
                $sqlFilter";

                // echo sqldbg($sql, $values)."\n";

            if($count === 'all') {
                $ob = self::query($sql, $values)->fetchObject();
                return ['amount' => (float) $ob->total_amount, 'invests' => (int) $ob->total_invests, 'users' => (int) $ob->total_users];
            }
            $total = self::query($sql, $values)->fetchColumn();
            if($count === 'money') {
                return (float) $total;
            }
            return (int) $total;
        }

        $offset = (int) $offset;
        $limit = (int) $limit;
        $sql = "SELECT
                    DISTINCT invest.*,
                    invest_reward.reward as reward,
                    invest_reward.fulfilled as fulfilled,
                    project.status as projectStatus,
                    user.name as admin
                FROM invest
                LEFT JOIN project
                    ON invest.project = project.id
                LEFT JOIN user
                    ON invest.admin = user.id
                LEFT JOIN invest_reward
                    ON invest_reward.invest = invest.id
                $sqlFilter
                " . ($order ? "ORDER BY $order" : '') ."
                LIMIT $offset, $limit
                ";

        // print_r($filters);echo sqldbg($sql, $values);
        $query = self::query($sql, $values);
        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
            $list[$item->id] = $item;
        }
        return $list;
    }

    /**
     * Lists invests as distinct users
     */
    public static function getUsersList($filters = []) {
        $list = [];
        list($sqlFilter, $values) = self::getSQLFilter($filters);
        $sql = "SELECT
                    DISTINCT user.*
                FROM invest
                RIGHT JOIN user
                    ON invest.user = user.id
                LEFT JOIN project
                    ON invest.project = project.id
                LEFT JOIN invest_reward
                    ON invest_reward.invest = invest.id
                $sqlFilter
                ";

        // print_r($filters);echo sqldbg($sql, $values);die;
        $query = self::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User');
    }

    /**
     * Returns the platform fee associate with a list of invests
     */
    public static function calculateFees($filters = []) {
        $fee = (float) Config::get('fee'); // default platform fee
        list($sqlFilter, $values) = self::getSQLFilter($filters);
        $sqlFilter = preg_replace('/^WHERE/', 'AND', $sqlFilter);
        // Normal invests fee
        $sql = "SELECT SUM(IFNULL(project_account.fee, $fee) * invest.amount) / 100
                FROM invest
                LEFT JOIN project ON invest.project = project.id
                LEFT JOIN project_account ON invest.project = project_account.project
                WHERE invest.campaign=0 $sqlFilter";
        $users_fee = (float) self::query($sql, $values)->fetchColumn();

        // Call Matchfunding invests fee
        $sql = "SELECT SUM(IFNULL(`call`.fee_projects_drop, $fee) * invest.amount) / 100
                FROM invest
                LEFT JOIN project ON invest.project = project.id
                LEFT JOIN `call` ON invest.call = `call`.id
                WHERE invest.campaign=1 AND invest.method='drop' $sqlFilter";
        $calls_fee = (float) self::query($sql, $values)->fetchColumn();
        // echo \sqldbg($sql, $values);
        // Matcher Matchfunding invests fee
        $sql = "SELECT SUM(IFNULL(`matcher`.fee, $fee) * invest.amount) / 100
                FROM invest
                LEFT JOIN project ON invest.project = project.id
                LEFT JOIN `matcher` ON invest.matcher = `matcher`.id
                WHERE invest.campaign=1 AND invest.method!='drop' $sqlFilter";
        $matchers_fee = (float) self::query($sql, $values)->fetchColumn();

        return ['user' => $users_fee, 'call' => $calls_fee, 'matcher' => $matchers_fee];
    }

    // returns the current project
    public function getProject() {
        if(isset($this->projectObject)) return $this->projectObject;
        try {
            $this->projectObject = Project::get($this->project);
        } catch(ModelNotFoundException $e) {
            $this->projectObject = false;
        }
        return $this->projectObject;
    }

    // returns the current user
    public function getUser() {
        if($this->userObject) return $this->userObject;
        $this->userObject = User::get($this->user);
        return $this->userObject;
    }

    // returns the current location
    public function getLocation() {
        if($this->locationObject) return $this->locationObject;
        $this->locationObject = InvestLocation::get($this);
        return $this->locationObject;
    }

    // returns payment method
    public function getMethod() {
        $method = Payment::getMethod($this->method, $this->getUser());
        $method->setInvest($this);
        return $method;
    }

    /**
     * Returns the address of the invest (where to send the reward)
     * @return array Address
     */
    public function getAddress() {
        if(!is_object($this->address)) {
            $query = static::query("
                SELECT  *
                FROM  invest_address
                WHERE   invest_address.invest = ?
                ", array($this->id));
            $this->address = $query->fetchObject();

            if(!is_object($this->address)) $this->address = new \stdClass;

            foreach(['name', 'nif', 'address', 'location', 'zipcode', 'country'] as $part) {
                if(empty($this->address->{$part})) {
                    if(empty($personal_address)) $personal_address = User::getPersonal($this->user);
                    $this->address->{$part} = $personal_address->{$part};
                }
                // still empty? serach from and old invest
                if(empty($this->address->{$part})) {
                    $query = static::query("
                        SELECT  `$part` FROM  invest_address WHERE invest_address.user = ?
                        AND !ISNULL(invest_address.`$part`) AND invest_address.`$part` != ''
                        ORDER BY invest_address.invest DESC LIMIT 1 ", array($this->user));
                    $val = $query->fetchColumn();
                    if($val) {
                        $this->address->{$part} = $val;
                    }
                }
            }

            // Repair country code
            if(empty($this->address->country)) $this->address->country = 'ES';

            if(strlen($this->address->country) > 2) {
                $this->address->country = Lang::getCountryCode($this->address->country);
            }

        }
        return $this->address;
    }

    /**
     * Saves an address for a Investion
     * @param array $address [description]
     */
    public function setAddress(array $address) {
        $sql = "REPLACE INTO invest_address (invest, user, address, zipcode, location, country, name, nif, regalo, namedest, emaildest, message)
            VALUES (:invest, :user, :address, :zipcode, :location, :country, :name, :nif, :regalo, :namedest, :emaildest, :message)";
        if(self::query($sql, array(
            ':invest'   => $this->id,
            ':user'     => $this->user,
            ':address'  => $address['address'],
            ':zipcode'  => $address['zipcode'],
            ':location' => $address['location'],
            ':country'  => $address['country'],
            ':name'     => $address['name'],
            ':nif'      => $address['nif'],
            ':regalo'   => $address['regalo'],
            ':namedest' => $address['namedest'],
            ':emaildest'=> $address['emaildest'],
            ':message'  => $address['message']
            )
        )) {
            $this->address = $address;
            // TODO: save into personal data if empty
            return true;
        }
        return false;
    }

    /**
     * Returns the rewards of the invest
     * @return array of Reward objects
     */
    public function getRewards() {
        if(!$this->rewards) {
            $query = static::query("
                SELECT  *
                FROM  invest_reward
                INNER JOIN reward
                    ON invest_reward.reward = reward.id
                WHERE   invest_reward.invest = ?
                ", array($this->id));
            $this->rewards = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Project\Reward');
        }
        foreach($this->rewards as $i => $reward) {
            if(!$reward instanceOf \Goteo\Model\Project\Reward) {
                $this->rewards[$i] = \Goteo\Model\Project\Reward::get($reward);
            }
        }
        return $this->rewards;
    }

    /**
     * Saves a collection of rewards for a Investion
     * @param array $rewards array of rewards
     */
    public function setRewards(array $rewards) {
        // borramos als recompensas
        $sql = "DELETE FROM invest_reward WHERE invest = :invest";
        if(self::query($sql, array(':invest' => $this->id))) {
            $this->rewards = [];
        }

        foreach ($rewards as $reward) {
            if(!$this->addReward($reward)) {
                return false;
            }
        }

        return true;
    }

    /*
     * Asignar a la aportación una recompensas
     */
    public function addReward ($reward) {
        if(!$reward instanceOf \Goteo\Model\Project\Reward) {
            $reward = \Goteo\Model\Project\Reward::get($reward);
            if(!$reward) return false;
        }
        $values = array(
            ':invest' => $this->id,
            ':reward' => $reward->id
        );

        $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
        if (self::query($sql, $values)) {
            $exists = false;
            foreach ($this->rewards as $r) {
                if($r instanceOf \Goteo\Model\Project\Reward) {
                    $r = $r->id;
                }
                if($r === $reward->id) {
                    $exists = true;
                    break;
                }
            }
            if(!$exists) {
                $this->rewards[] = $reward;
            }
            return $reward;
        }
        return false;
    }

    /**
     * Obtains the first reward
     * @return [type] [description]
     */
    public function getFirstReward() {
        $rewards = $this->getRewards();
        return reset($rewards);
    }

    public function validate (&$errors = array()) {
        if (!is_numeric($this->amount))
            $errors[] = 'La cantidad no es correcta';

        if (empty($this->method))
            $errors[] = 'Falta metodo de pago';

        if (empty($this->user))
            $errors[] = 'Falta usuario';

        /*if (empty($this->project))
            $errors[] = 'Falta proyecto';*/

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
            'matcher',
            'drops',
            'pool'
            );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            // tabla para obtener aportaciones por nodo

            // FIX: aseguramos que no hay ningun valor nulo
            $pnode = $this->getProject()->node;
            if (empty($pnode)) $pnode = Config::get('current_node');
            $unode = $this->getUser()->node;
            if (empty($unode)) $unode = Config::get('current_node');

            $sql = "REPLACE INTO invest_node (project_id, project_node, user_id, user_node, invest_id, invest_node) VALUES (:pid, :pnode, :uid, :unode, :iid, :inode)";
            self::query($sql, array(
                ':pid' => $this->project,
                ':pnode' => $pnode,
                ':uid' => $this->user,
                ':unode' => $unode,
                ':iid' => $this->id,
                ':inode' => Config::get('current_node'))
            );

            // recompensas
            if (!empty($this->rewards)) {
                $this->setRewards($this->rewards);
            }
            // dirección
            if (!empty($this->address)) {
                $this->setAddress((array)$this->address);
            }

            // mantenimiento de registros relacionados (usuario, proyecto, ...)
            $this->keepUpdated();

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

            // recompensas
            if (is_array($this->rewards)) {
                $this->setRewards($this->rewards);
            }
            // dirección
            if (is_array($this->address)) {
                $this->setAddress($this->address);
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

            self::query($sql, array(':id'=>$this->id, ':log'=>'Incidencia resuelta por el admin '.Session::getUser()->name.', aporte pasado a cash y cobrado'));


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
    public static function invested ($project, $scope = null, $call = null) {
        if($project instanceOf Project) $project = $project->id;

        $values = array(':project' => $project,
                    ':s0' => self::STATUS_PENDING,
                    ':s1' => self::STATUS_CHARGED,
                    ':s3' => self::STATUS_PAID,
                    ':s4' => self::STATUS_RETURNED,
                    ':s5' => self::STATUS_TO_POOL);

        $sql = "SELECT  SUM(amount) as mutch
            FROM    invest
            WHERE   project = :project
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)
            ";

        switch ($scope) {
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

        // die(\sqldbg($sql, $values));

        $query = static::query($sql, $values);
        $got = $query->fetchObject();

        if($mutch = (int) $got->mutch) {
            if ($scope == 'users') {
                // actualiza el amount invertido por los usuarios
                static::query("UPDATE project SET amount_users = :num WHERE id = :project", array(':num' => $mutch, ':project' => $project));

            } elseif ($scope == 'call' && !empty($call)) {
                // actualiza el amount invertido por el convocador
                static::query("UPDATE project SET amount_call = :num WHERE id = :project", array(':num' => $mutch, ':project' => $project));
            } else {
                //actualiza el el amount en proyecto (aunque se quede a cero)
                static::query("UPDATE project SET amount = :num WHERE id = :project", array(':num' => $mutch, ':project' => $project));
            }
        }

        return $mutch;
    }

    /*
     * Aportes individuales a un proyecto
     */
    public static function investors ($project, $projNum = false, $showall = false, $offset = 0, $limit = 10, $count = false) {
        $investors = array();

        // Return total count for pagination
        if($count) {
            $sql = "SELECT COUNT(invest.id) FROM invest WHERE project = :p
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)";
                return (int) self::query($sql, array(':p' => $project, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL))->fetchColumn();
        }

        $offset = (int) $offset;
        $limit = (int) $limit;

        if($limit)
            $limit_sql='LIMIT '.$offset.','.$limit;

        $sql = "
            SELECT
                invest.id as id,
                invest.user as user,
                user.name as name,
                user.avatar as user_avatar,
                user.worth as worth,
                invest.amount as amount,
                DATE_FORMAT(invest.invested, '%d/%m/%Y') as date,
                user.hide as hide,
                invest.droped as droped,
                invest.campaign as campaign,
                invest.call as `call`,
                invest.anonymous as anonymous,
                invest_msg.msg as msg
            FROM    invest
            LEFT JOIN invest_msg
                ON invest_msg.invest=invest.id
            INNER JOIN user
                ON  user.id = invest.user
            WHERE   project = :p
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)
            ORDER BY invest.invested DESC, invest.id DESC
            $limit_sql
            ";

        $query = self::query($sql, array(':p' => $project, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL));
        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {

            $investor->avatar = Image::get($investor->user_avatar);

            // si el usuario es hide o el aporte es anonymo, lo ponemos como el usuario anonymous (avatar 1)
            if (!$showall && ($investor->hide == 1 || $investor->anonymous == 1)) {

                // mantenemos la fecha del anonimo mas reciente
                $anonymous_date = empty($investors['anonymous']->date) ? $investor->date : $investors['anonymous']->date;

                $investors[] = (object) array(
                    'id' => $investor->id,
                    'user' => 'anonymous',
                    'name' => Text::get('regular-anonymous'),
                    'projects' => null,
                    'avatar' => Image::get(1),
                    'worth' => $investor->worth,
                    'amount' => $investor->amount,
                    'date' => $investor->date,
                    'droped' => $investor->droped,
                    'campaign' => $investor->campaign,
                    'call' => $investor->call,
                    'msg' => $investor->msg
                );

            } else {

                $investors[] = (object) array(
                    'id' => $investor->id,
                    'user' => $investor->user,
                    'name' => $investor->name,
                    'projects' => $investor->projects,
                    'avatar' => $investor->avatar,
                    'worth' => $investor->worth,
                    'amount' => $investor->amount,
                    'date' => $investor->date,
                    'droped' => $investor->droped,
                    'campaign' => $investor->campaign,
                    'call' => $investor->call,
                    'msg' => $investor->msg
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
                user.avatar as user_avatar,
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
            WHERE   invest.campaign = 0
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)
            GROUP BY invest.user
            ORDER BY amount DESC
            LIMIT {$limit}
            ";

        $values = array(':id'=>$owner, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL);

        //die(\sqldbg($sql, $values));


        $query = self::query($sql, $values);
        foreach ($query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\User') as $investor) {

            // si el usuario es hide o el aporte es anonymo, lo ponemos como el usuario anonymous (avatar 1)
            if ( $investor->hide == 1 || $investor->anonymous == 1 ) {
                // mantenemos la fecha del anonimo mas reciente
                $investor->date = empty($investors['anonymous']->date) ? $investor->date : $investors['anonymous']->date;
                $investor->user = 'anonymous';
                $investor->avatar = new Image();
                $investor->name = Text::get('regular-anonymous');
            }

            $investors[$investor->user] = (object) array(
                'user' => $investor->user,
                'name' => $investor->name,
                'projects' => (isset($investor->projects)) ? $investor->projects : $investor->get_numInvested,
                'avatar' => Image::get($investor->user_avatar),
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

        $values = array(':project' => $project, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL);

        $sql = "SELECT  COUNT(DISTINCT(invest.user)) as investors, project.num_investors as num, project.num_messengers as pop
            FROM    invest
            INNER JOIN project
                ON project.id = invest.project
            WHERE   invest.project = :project
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)
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
        $values = array(':owner' => $owner, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL);

        $sql = "SELECT  COUNT(DISTINCT(user)) as investors
            FROM    invest
            WHERE   project IN (SELECT id FROM project WHERE owner = :owner)
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)
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
            AND     invest.status IN (:s0, :s1, :s3, :s4, :s5)
            AND     anonymous = 0
            ORDER BY invested DESC";

        $query = self::query($sql, array(':user' => $user, ':project' => $project, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL));
        return $query->fetchObject();
    }

    /*
     * Numero de cofinanciadores que han optado por cierta recompensa
     * TODO: remove this
     */
    public static function choosed ($reward) {

        $users = array();

        $sql = "
            SELECT  DISTINCT(user) as user
            FROM    invest
            INNER JOIN invest_reward
                ON invest_reward.invest = invest.id
                AND invest_reward.reward = :reward
            INNER JOIN user
                ON  user.id = invest.user
                AND (user.hide = 0 OR user.hide IS NULL)
            WHERE   invest.status IN (:s0, :s1, :s3, :s4, :s5)
            ";

        $query = self::query($sql, array(':reward' => $reward, ':s0' => self::STATUS_PENDING, ':s1' => self::STATUS_CHARGED, ':s3' => self::STATUS_PAID, ':s4' => self::STATUS_RETURNED, ':s5' => self::STATUS_TO_POOL));
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $investor) {
            $users[] = $investor['user'];
        }

        return $users;
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
            $this->account = $account;
            return true;
        } else {
            return false;
        }

    }

    /*
     * Marcar una recompensa como cumplida (o desmarcarla)
     */
    public static function setFulfilled ($invest, $reward = null, $value = '1') {

        if($invest instanceOf Invest) {
            if(!$reward) {
                $reward = $invest->getRewards() ? $invest->getRewards()[0]->id : null;
            }
            $invest = $invest->id;
        }
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
            $this->preapproval = $key;
            return true;
        } else {
            return false;
        }

    }

    /*
     *  Cambia el estado de un aporte
     */
    public function setStatus ($status) {

        if (!in_array($status, array(self::STATUS_PROCESSING, self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_CANCELLED, self::STATUS_PAID, self::STATUS_RETURNED, self::STATUS_RELOCATED, self::STATUS_TO_POOL))) {
            throw new \Exception("Error: Invest status unknow! [$status]");
        }

        $values = array(
            ':id' => $this->id,
            ':status' => $status
        );

        $sql = "UPDATE invest SET status = :status WHERE id = :id";
        if (self::query($sql, $values)) {
            $this->status = $status;
            // si tiene capital riego asociado pasa al mismo estado
            if (!empty($this->droped)) {
                $drop = Invest::get($this->droped);
                // si estan reubicando o caducando
                // liberamos el capital riego
                if (in_array($status, array(self::STATUS_CANCELLED, self::STATUS_RETURNED, self::STATUS_RELOCATED, self::STATUS_TO_POOL))) {
                    $drop->setStatus(self::STATUS_CANCELLED);
                    self::query("UPDATE invest SET droped = NULL WHERE id = :id", array(':id' => $this->id));
                } else {
                    $drop->setStatus($status);
                }
            }
            return true;
        }

        throw new \Exception("Error: Invest setting payment status! [$status]");
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
            $this->payment = $key;
            $this->charged = $values[':charged'];

            // si tiene capital riego asociado pasa al mismo estado
            if (!empty($this->droped)) {
                $drop = Invest::get($this->droped);
                $drop->setStatus(self::STATUS_CHARGED);
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
            $this->transaction = $code;
            return true;
        } else {
            return false;
        }

    }

    public function switchResign() {
       return $this->setResign(!$this->resign);
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
            $this->resign = (bool) $resign;
            return true;
        }
        return false;

    }

    /*
     * cancels/refunds an investion
     * Transfer the Invest to the pool if choosed
     */
    public function cancel ($status = false, &$errors = []) {

        // true marks as returned so totals will added to a failed project's thermometer
        if($this->pool) {
            $status = self::STATUS_TO_POOL;
        } elseif ($status) {
            $status = self::STATUS_RETURNED;
        } else {
            $status = self::STATUS_CANCELLED;
        }

        $values = array(
            ':id' => $this->id,
            ':status' => $status,
            ':returned' => date('Y-m-d')
        );

        $sql = "UPDATE invest SET
                    returned = :returned,
                    status = :status
                WHERE id = :id";

        if (self::query($sql, $values)) {
            $this->status = $status;
            $this->returned = $values[':returned'];

            // // should this invest go to pool?
            if($this->pool) {
                return $this
                        ->getUser()
                        ->getPool()
                        ->calculate()
                        ->save($errors);
            }

            return true;
        }

        return false;
    }

    /**
     * Return if a invest is derived to pool
     * @return boolean [description]
     */
    public function isOnPool() {
        return $this->status == Invest::STATUS_TO_POOL;
    }
    /*
     * Switch credit option
     */
    public function switchPoolOnFail() {
       return $this->setPoolOnFail(!$this->pool);
    }

    /**
     *  Sets pool status
     *  @param boolean $status
     */
    public function setPoolOnFail($value) {
        if(self::query("UPDATE invest SET pool = :pool WHERE id = :id", array(':id' => $this->id, ':pool' => (bool)$value))) {
            $this->pool = (bool) $value;
            return true;
        }
        return false;
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
     * Metodo para obtener datos para el informe completo (con incidencias y netos)
     */
     public static function getReportData($project, $status, $round, $passed) {
        $Data = array();
        // update details, just in case
        // proyecto
        self::invested($project); // conseguido
        self::numInvestors($project); // inversores

        // segun estado, ronda y fecha de pase a segunda
        // el cash(1) es igual para todos
        // TODO: que son estos numeros? estados de que? proyecto?
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
                    'methods' => self::METHOD_CASH,
                    'projects' => $project,
                    'status' => self::STATUS_CHARGED
                ), null, 0, 9999);
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
                    'methods' => self::METHOD_PAYPAL,
                    'projects' => $project
                ), null, 0, 9999);
                if (!empty($inv_paypal)) {
                    // $Data['note'][] = "Los aportes de paypal son incidencias si están activos";
                    foreach ($inv_paypal as $invId => $invest) {
                        if (in_array($invest->status, array(0, 1, 3))) {
                            $Data['paypal']['total']['fail'] += $invest->amount;
                            $Data['note'][] = "El aporte PayPal {$invId} no debería estar en estado '" . self::status($invest->status) . "'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
                        }
                    }
                }

                // A ver si tiene tpv
                // si estan pendientes, ejecutados o pagados al proyecto es una incidencia
                $inv_tpv = self::getList(array(
                    'methods' => self::METHOD_TPV,
                    'projects' => $project
                ), null, 0, 9999);
                if (!empty($inv_tpv)) {
                    // $Data['note'][] = "Los aportes de tpv son incidencias si están activos";
                    foreach ($inv_tpv as $invId => $invest) {
                        if ($invest->status == 1) {
                            $Data['tpv']['total']['fail'] += $invest->amount;
                            $Data['note'][] = "El aporte TPV {$invId} no debería estar en estado '" . self::status($invest->status) . "'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
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
                        'methods' => self::METHOD_CASH,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED
                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_CASH,
                        'projects' => $project,
                        'status' => self::STATUS_PENDING
                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_TPV,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED
                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_PAYPAL,
                        'projects' => $project
                    ), null, 0, 9999);
                    if (!empty($inv_paypal)) {
                        $Data['paypal']['first']['fail'] = 0;
                        foreach ($inv_paypal as $invId => $invest) {
                            if (in_array($invest->status, array(self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_PAID))) {
                                $Data['paypal']['first']['users'][$invest->user] = $invest->user;
                                $Data['paypal']['first']['invests']++;
                                $Data['paypal']['first']['amount'] += $invest->amount;
                            }
                        }
                        $Data['paypal']['total'] = $Data['paypal']['first'];
                    }

                    // DROP
                    $inv_drop = self::getList(array(
                        'methods' => self::METHOD_DROP,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED
                    ), null, 0, 9999);
                    if (!empty($inv_drop)) {
                        $Data['drop']['first']['fail'] = 0;
                        foreach ($inv_drop as $invId => $invest) {
                            $Data['drop']['first']['users'][$invest->user] = $invest->user;
                            $Data['drop']['first']['invests']++;
                            $Data['drop']['first']['amount'] += $invest->amount;
                        }
                        $Data['drop']['total'] = $Data['drop']['first'];
                    }

                    // POOL
                    $inv_pool = self::getList(array(
                        'methods' => self::METHOD_POOL,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED
                    ), null, 0, 9999);
                    if (!empty($inv_pool)) {
                        $Data['pool']['first']['fail'] = 0;
                        foreach ($inv_pool as $invId => $invest) {
                            $Data['pool']['first']['users'][$invest->user] = $invest->user;
                            $Data['pool']['first']['invests']++;
                            $Data['pool']['first']['amount'] += $invest->amount;
                        }
                        $Data['pool']['total'] = $Data['pool']['first'];
                    }

                } elseif ($act_eq === 'sum') {
                    // complicado: primero los de primera ronda, luego los de segunda ronda sumando al total
                    // calcular ultimo dia de primera ronda segun la fecha de pase
                    $passtime = strtotime($passed);
                    $last_day = date('Y-m-d', \mktime(0, 0, 0, date('m', $passtime), date('d', $passtime)-1, date('Y', $passtime)));

                    // CASH first
                    $inv_cash = self::getList(array(
                        'methods' => self::METHOD_CASH,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_until' => $last_day
                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_CASH,
                        'projects' => $project,
                        'status' => self::STATUS_PENDING,
                        'date_until' => $last_day
                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_TPV,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_until' => $last_day
                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_PAYPAL,
                        'projects' => $project,
                        'date_until' => $last_day
                    ), null, 0, 9999);
                    if (!empty($inv_paypal)) {
                        $Data['paypal']['first']['fail'] = 0;
                        foreach ($inv_paypal as $invId => $invest) {
                            if (in_array($invest->status, array(self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_PAID))) {
                                // a ver si cargo pendiente es incidencia...
                                if ($invest->status == self::STATUS_PENDING && ($p0 === 'first' || $p0 === 'all')) {
                                    $Data['paypal']['first']['fail'] += $invest->amount;
                                    $Data['note'][] = "El aporte paypal {$invId} no debería estar en estado '".self::status($invest->status)."'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
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
                        'methods' => self::METHOD_DROP,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_until' => $last_day
                    ), null, 0, 9999);
                    if (!empty($inv_drop)) {
                        $Data['drop']['first']['fail'] = 0;
                        foreach ($inv_drop as $invId => $invest) {
                            $Data['drop']['first']['users'][$invest->user] = $invest->user;
                            $Data['drop']['first']['invests']++;
                            $Data['drop']['first']['amount'] += $invest->amount;
                        }
                        $Data['drop']['total'] = $Data['drop']['first'];
                    }

                    // POOL first
                    $inv_pool = self::getList(array(
                        'methods' => self::METHOD_POOL,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_until' => $last_day
                    ), null, 0, 9999);
                    if (!empty($inv_pool)) {
                        $Data['pool']['first']['fail'] = 0;
                        foreach ($inv_pool as $invId => $invest) {
                            $Data['pool']['first']['users'][$invest->user] = $invest->user;
                            $Data['pool']['first']['invests']++;
                            $Data['pool']['first']['amount'] += $invest->amount;
                        }
                        $Data['pool']['total'] = $Data['pool']['first'];
                    }

                    // -- Los de segunda

                    // CASH  second
                    $inv_cash = self::getList(array(
                        'methods' => self::METHOD_CASH,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_from' => $passed

                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_CASH,
                        'projects' => $project,
                        'status' => self::STATUS_PENDING,
                        'date_from' => $passed

                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_TPV,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_from' => $passed

                    ), null, 0, 9999);
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
                        'methods' => self::METHOD_PAYPAL,
                        'projects' => $project,
                        'date_from' => $passed
                    ), null, 0, 9999);
                    if (!empty($inv_paypal)) {
                        $Data['paypal']['second']['fail'] = 0;
                        foreach ($inv_paypal as $invId => $invest) {
                            if (in_array($invest->status, array(self::STATUS_PENDING, self::STATUS_CHARGED, self::STATUS_PAID))) {
                                // a ver si cargo pendiente es incidencia...
                                if ($invest->status == self::STATUS_PENDING && $p0 === 'all') {
                                    $Data['paypal']['second']['fail'] += $invest->amount;
                                    $Data['paypal']['total']['fail'] += $invest->amount;
                                    $Data['note'][] = "El aporte paypal {$invId} no debería estar en estado '".self::status($invest->status)."'. <a href=\"/admin/invests/details/{$invId}\" target=\"_blank\">Abrir detalles</a>";
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
                        'methods' => self::METHOD_DROP,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_from' => $passed
                    ), null, 0, 9999);
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

                    // POOL second
                    $inv_pool = self::getList(array(
                        'methods' => self::METHOD_POOL,
                        'projects' => $project,
                        'status' => self::STATUS_CHARGED,
                        'date_from' => $passed
                    ), null, 0, 9999);
                    if (!empty($inv_pool)) {
                        $Data['pool']['second']['fail'] = 0;
                        foreach ($inv_pool as $invId => $invest) {
                            $Data['pool']['second']['users'][$invest->user] = $invest->user;
                            $Data['pool']['total']['users'][$invest->user] = $invest->user;
                            $Data['pool']['second']['invests']++;
                            $Data['pool']['total']['invests']++;
                            $Data['pool']['second']['amount'] += $invest->amount;
                        }
                        $Data['pool']['total']['amount'] += $Data['pool']['second']['amount'];
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

     public function getDetails() {

         $list = array();

         $values = array(':id' => $this->id);

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
     * @deprecated
     * ONLY FOR THE FORMER PROCESS SCRIPT
     * WILL RETURN ONLY PAYPAL WITH PREAPROVALS
     * Tratamiento de aportes pendientes en cron/execute
     */
    public static function getPending($project_id) {

        // @FIXME esta distinción de métodos de pago es MAL!
        // @TODO capa de pagos

        // $query = \Goteo\Core\Model::query("
        //     SELECT  *
        //     FROM  invest
        //     WHERE   invest.project = ?
        //     AND     (invest.status = 0
        //         OR (invest.method = 'tpv'
        //             AND invest.status = 1
        //         )
        //         OR (invest.method = 'cash'
        //             AND invest.status = 1
        //         )
        //     )
        //     AND (invest.campaign IS NULL OR invest.campaign = 0)
        //     ", array($project_id));

        $query = \Goteo\Core\Model::query("
            SELECT  *
            FROM  invest
            WHERE   invest.project = ?
            AND invest.method = 'paypal'
            AND invest.status = 0
            AND invest.campaign = 0
            ", array($project_id));

        return $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');
    }

    /**
     * @deprecated
     * ONLY FOR THE FORMER PROCESS SCRIPT
     * Retorna los aportes que no se han retornado correctamente (fallo en ceca por ejemplo)
     * @return [type] [description]
     */
    public static function getFailed($methods= null, $offset = 0, $limit = 100, $count = false) {
        return static::getList(['methods' => $methods,
                                'status' => self::STATUS_CHARGED,
                                'projectStatus' => Project::STATUS_UNFUNDED,
                                'types' => 'nopool'
                                ], null, $offset, $limit, $count);
    }

    /**
     * Keep updated any related data entities
     *
     * @return success boolean
     */
    public function keepUpdated() {

        // numero de proyectos aportados
        User::numInvested($this->user);

        // cantidad total aportada en goteo
        $amount = User::updateAmount($this->user);

        // nivel de meritocracia
        User::updateWorth($this->user, $amount);

        // proyecto
        self::invested($this->project); // conseguido
        self::invested($this->project, 'users'); // users amount
        self::numInvestors($this->project); // inversores

    }

     /**
     * Save a support message
     *
     * @return success boolean
     */
    public static function newSupportMessage($invest, $msg) {

        try {

        $sql = "INSERT INTO invest_msg (invest, msg)
                VALUES (:invest, :msg)";

        self::query($sql, array(':invest'=>$invest, ':msg'=> $msg));


        } catch (\PDOException $e) {
            $errors[] = "El mensaje ya ha sido asignado. Por favor, revise los datos." . $e->getMessage();
            return false;
        }

        return true;

    }

    /**
    * Return total funded money
    **/
    public static function getTotalMoneyFunded(){

        $status_published=[Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED];
        return self::getList(['status' => Invest::$RAISED_STATUSES, 'projectStatus' => $status_published], null, 0, 0, 'money');

    }
}
