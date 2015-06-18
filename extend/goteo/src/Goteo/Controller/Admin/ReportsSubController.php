<?php
/**
 * Estadísticas e informes
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Library\Reporting,
    Goteo\Library\Currency,
    Goteo\Library\Cacher,
    Goteo\Model;

class ReportsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nuevo Destacado',
      'move' => 'Moviendo a otro Nodo el proyecto',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe Financiero del proyecto',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Destacado',
      'translate' => 'Traduciendo Destacado',
      'reorder' => 'Ordenando los padrinos en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Informe Impulsores',
      'admins' => 'Asignando administradores del Canal',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configuración de campaña del proyecto',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
      'keywords' => 'Palabras clave',
      'view' => 'Apadrinamientos',
      'info' => 'Información de contacto',
      'send' => 'Comunicación enviada',
      'init' => 'Iniciando un nuevo envío',
      'activate' => 'Iniciando envío',
      'detail' => 'Viendo destinatarios',
      'dates' => 'Fechas del proyecto',
      'accounts' => 'Cuentas del proyecto',
      'images' => 'Imágenes del proyecto',
      'assign' => 'Asignando a una Convocatoria el proyecto',
      'open_tags' => 'Asignando una agrupación al proyecto',
      'rebase' => 'Cambiando Id de proyecto',
      'consultants' => 'Cambiando asesor del proyecto',
      'paypal' => 'Informe PayPal',
      'geoloc' => 'Informe usuarios Localizados',
      'calls' => 'Informe Convocatorias',
      'donors' => 'Informe Donantes',
      'top' => 'Top Cofinanciadores',
      'currencies' => 'Actuales ratios de conversión',
    );


    static protected $label = 'Informes';


    protected $filters = array (
      'report' => '',
      'date_from' => '',
      'date_until' => '',
      'year' => '2014',
      'status' => '',
      'user' => '',
      'top' => 'numproj',
      'limit' => 25,
    );


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function currenciesAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('currencies', $id, $this->filters, $subaction));
    }


    public function topAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('top', $id, $this->filters, $subaction));
    }


    public function donorsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('donors', $id, $this->filters, $subaction));
    }


    public function callsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('calls', $id, $this->filters, $subaction));
    }


    public function projectsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('projects', $id, $this->filters, $subaction));
    }


    public function geolocAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('geoloc', $id, $this->filters, $subaction));
    }


    public function paypalAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('paypal', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {

        switch ($action)  {
            case 'resetpdf':
                if (!empty($id)) {
                    Model\User\Donor::resetPdf($id);
                }
                return $this->redirect('/admin/reports/donors');

                break;

            case 'donors':

                if (empty($filters['year'])) $filters['year'] = Model\User\Donor::currYear();

                $data = Model\User\Donor::getList($filters);

                return array(
                        'folder' => 'reports',
                        'file'   => 'donors',
                        'filters'=> $filters,
                        'data'   => $data
                );

                break;

            case 'top':

                $data = self::top($filters);

                return array(
                        'folder' => 'reports',
                        'file'   => 'top',
                        'filters'=> $filters,
                        'data'   => $data
                );

                break;

            case 'projects':

                $data = self::projects($filters, $id);

                return array(
                        'folder' => 'reports',
                        'file'   => 'projects',
                        'data'   => $data,
                        'filters'   => $filters
                );

                break;

            case 'calls':

                $data = self::calls($filters, $id);

                return array(
                        'folder' => 'reports',
                        'file'   => 'calls',
                        'data'   => $data,
                        'filters'   => $filters
                );

                break;

            // calculamos lo que debería haber ahora en PayPal:
            case 'paypal':

                $data = self::paypal();

                return array(
                        'folder' => 'reports',
                        'file'   => 'paypal',
                        'data'   => $data
                );

                break;

            case 'geoloc':

                $data = self::geoloc();

                return array(
                        'folder' => 'reports',
                        'file'   => 'geoloc',
                        'data'   => $data
                );

                break;

            case 'currencies':

                $data = Currency::getAll();

                return array(
                        'folder' => 'reports',
                        'file'   => 'currencies',
                        'data'   => $data
                );

                break;
        }

        $reports = Reporting::getList();

        if (!empty($filters['report'])) {
            $data = Reporting::getReport($filters['report'], $filters);
        } else {
            $data = null;
        }


        return array(
                'folder'  => 'reports',
                'file'    => 'list',
                'reports' => $reports,
                'filters' => $filters,
                'data'    => $data
        );

    }

    private static function top($filters = array()) {

        // lista de proyectos que han pasado la primera ronda
        $data = array();

        $sql = "SELECT
                  user.id as id,
                  user.name as name,
                  user.email as email,
                  SUM(invest.amount) as amount,
                  COUNT(DISTINCT(invest.project)) as numproj
                FROM invest
                INNER JOIN user
                  ON user.id = invest.user
                WHERE invest.status IN ('0', '1', '3')
                GROUP BY invest.user
                ";

        // filtros: orden y límite
        if (!empty($filters['top'])) {
            $sql .= " ORDER BY ".$filters['top']. " DESC";
        }

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ".$filters['limit'];
        }


        $query = Model\User::query($sql);
        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
            $data[] = $item;
        }

        return $data;

    }


    private static function projects($filters = array(), $id = null) {


        if (empty($id)) {
            // si no tenemos id,
            // lista de proyectos que han pasado la primera ronda
            $data = array();

            // filtro
            switch ($filters['status']) {
                case 'first':
                    $sqlFilter = "WHERE project.status = 3 AND (project.passed IS NULL OR project.passed = '0000-00-00')";
                    break;
                case 'second':
                    $sqlFilter = "WHERE project.status = 3 AND (project.passed IS NOT NULL OR project.passed != '0000-00-00')";
                    break;
                case 'completed':
                    $sqlFilter = "WHERE project.status IN (4, 5)";
                    break;
                default:
                    $sqlFilter = "WHERE project.status IN (3, 4, 5)";
            }

            $sql = "SELECT
                        project.id as id,
                        project.name as name,
                        date_format(project.published, '%d/%m/%Y') as init,
                        date_format(project.passed, '%d/%m/%Y') as fin_1a,
                        date_format(project.success, '%d/%m/%Y') as fin_2a
                    FROM project
                    $sqlFilter
                    ORDER BY project.published DESC
                    ";

            $query = Model\Invest::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $item) {
                $data[$item['id']] = $item;
            }

        } else {
            // si tenemos id, sacamos los datos de ese proyecto
            $sql = "SELECT
                        IF (project.contract_entity, 'Juridica', 'Fisica') as persona,
                        project.entity_name as entidad,
                        project.entity_cif as cif,
                        concat(project.address, ', ', project.zipcode, ', ', project.location, ', ', project.country) as dir_fiscal,
                        IF(project.secondary_address,
                            concat(project.post_address, ', ', project.post_zipcode, ', ', project.post_location, ', ', project.post_country),
                            ''
                            ) as dir_postal,
                        project.contract_name as responsable,
                        project.contract_nif as nif_responsable,
                        project.contract_email as email_responsable,
                        project.phone as telefono,
                        project_account.paypal as paypal,
                        project_account.bank as ccc,
                        project.name as nombre_proyecto,
                        project.status as proj_status,
                        user.email as email_usuario,
                        date_format(project.published, '%d/%m/%Y') as inicio_campaña,
                        date_format(project.passed, '%d/%m/%Y') as final_1a_ronda,
                        date_format(project.success, '%d/%m/%Y') as final_2a_ronda
                    FROM project
                    INNER JOIN user ON user.id = project.owner
                    LEFT JOIN project_account ON project_account.project = project.id
                    WHERE project.id = :id
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data = $query->fetchObject();


            ////// campos calculados /////
            // Importe que aparece en el termómetro
            $sql = "SELECT  SUM(amount) as amount
                        FROM    invest
                        WHERE   project = :id
                        AND     invest.status IN ('0', '1', '3', '4')
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->total = $query->fetchColumn();

            // Dinero perdido por incidencias no resueltas
            $sql = "SELECT  SUM(amount) as amount
                        FROM    invest
                        WHERE   project = :id
                        AND     invest.issue = 1
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->issues = $query->fetchColumn();


            // Dinero enviado al proyecto (92% de lo cobrado correcto y pagado al proyecto)
            $sql = "SELECT  SUM(amount) as amount
                        FROM    invest
                        WHERE   project = :id
                        AND     invest.status IN ('1', '3')
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $total = $query->fetchColumn();
            $data->project_total = $total * 0.92;

            // por banco
            $sql = "SELECT  SUM(amount) as amount
                        FROM    invest
                        WHERE   project = :id
                        AND     invest.status IN ('1', '3')
                        AND     invest.method IN ('tpv')
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $tpv = $query->fetchColumn();
            $data->project_tpv = $tpv * 0.92;
            $data->fee_tpv = $tpv * 0.008;

            // los manuales se añaden al banco pero no a la comision
            $sql = "SELECT  SUM(amount) as amount
                        FROM    invest
                        WHERE   project = :id
                        AND     invest.status IN ('1', '3')
                        AND     invest.method IN ('cash')
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $cash = $query->fetchColumn();
            $data->project_tpv += $cash * 0.92;


            // por paypal
            $sql = "SELECT  SUM(amount) as amount, COUNT(id) as num
                        FROM    invest
                        WHERE   project = :id
                        AND     invest.status IN ('1', '3')
                        AND     invest.method IN ('paypal')
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $paypal = $query->fetchObject();
            $data->project_paypal = $paypal->amount * 0.92;
            $data->fee_paypal = $paypal->amount * 0.034 + $paypal->num * 0.35;

            $data->fee_total = $data->fee_tpv + $data->fee_paypal;
            $data->project_total = $data->project_total - $data->fee_total;
            $data->project_tpv = $data->project_tpv - $data->fee_total;


            // Num total de donantes con información rellenada que renunciaron a recompensa: $data->num_resign
            $sql = "SELECT  COUNT(DISTINCT(invest.user))
                        FROM    invest
                        INNER JOIN invest_address
                            ON invest_address.invest = invest.id
                            AND invest_address.name != ''
                            AND invest_address.nif != ''
                        WHERE project = :id
                        AND invest.status IN ('0', '1', '3')
                        AND (invest.issue IS NULL OR invest.issue = 0)
                        AND (invest.resign IS NOT NULL AND invest.resign = 1)
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->num_resign = $query->fetchColumn();

            // Num de donantes con información rellenada de más de 100 euros aportados<br />
            // (independientemente de si fue en una o varias aportaciones): $data->num_resign100
            $sql = "SELECT  COUNT(invest.user),
                        	SUM(invest.amount) as amount
                        FROM    invest
                        WHERE project = :id
                        AND invest.status IN ('0', '1', '3')
                        AND (invest.issue IS NULL OR invest.issue = 0)
                        AND (invest.resign IS NOT NULL AND invest.resign = 1)
                        GROUP BY invest.user
                        HAVING amount >= 100
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->num_resign100 = $query->fetchColumn(0);

            // Num de usuarios que no  marcaron ninguna recompensa pero tampoco donacion: $data->num_noresign
            $sql = "SELECT  COUNT(invest.user)
                        FROM    invest
                        LEFT JOIN invest_reward
                            ON invest_reward.invest = invest.id
                        WHERE project = :id
                        AND invest.status IN ('0', '1', '3')
                        AND (invest.issue IS NULL OR invest.issue = 0)
                        AND (invest.resign IS NULL OR invest.resign = 0)
                        AND invest_reward.reward IS NULL
                        GROUP BY invest.user
                    ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->num_noresign = $query->fetchColumn();

        }

        return $data;

    }


    private static function calls($filters = array(), $id = null) {


        if (empty($id)) {
            // si no tenemos id,
            // lista de convocatorias
            $data = array();

            // filtro de estado del proceso de convocatoria
            switch ($filters['status']) {
                case 'search':
                    $sqlFilter = "WHERE project.status = 3 AND (project.passed IS NULL OR project.passed = '0000-00-00')";
                    break;
                case 'init':
                    $sqlFilter = "WHERE project.status = 3 AND (project.passed IS NOT NULL OR project.passed != '0000-00-00')";
                    break;
                case 'completed':
                    $sqlFilter = "WHERE project.status IN (4, 5)";
                    break;
                default:
                    $sqlFilter = "WHERE project.status IN (3, 4, 5)";
            }

            $data = array();
            /*
            $sql = "SELECT
                project.id as id,
                project.name as name,
                date_format(project.published, '%d/%m/%Y') as init,
                date_format(project.passed, '%d/%m/%Y') as fin_1a,
                date_format(project.success, '%d/%m/%Y') as fin_2a
            FROM project
            $sqlFilter
            ORDER BY project.published DESC
            ";

            $query = Model\Invest::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $item) {
                $data[$item['id']] = $item;
            }
            */
        } else {

            $data = array();
            /// @TODO sacadatos
            /*
            // si tenemos id, sacamos los datos de ese proyecto
            $sql = "SELECT
                IF (project.contract_entity, 'Juridica', 'Fisica') as persona,
                project.entity_name as entidad,
                project.entity_cif as cif,
                concat(project.address, ', ', project.zipcode, ', ', project.location, ', ', project.country) as dir_fiscal,
                IF(project.secondary_address,
                    concat(project.post_address, ', ', project.post_zipcode, ', ', project.post_location, ', ', project.post_country),
                    ''
                    ) as dir_postal,
                project.contract_name as responsable,
                project.contract_nif as nif_responsable,
                project.contract_email as email_responsable,
                project.phone as telefono,
                project_account.paypal as paypal,
                project_account.bank as ccc,
                project.name as nombre_proyecto,
                project.status as proj_status,
                user.email as email_usuario,
                date_format(project.published, '%d/%m/%Y') as inicio_campaña,
                date_format(project.passed, '%d/%m/%Y') as final_1a_ronda,
                date_format(project.success, '%d/%m/%Y') as final_2a_ronda
            FROM project
            INNER JOIN user ON user.id = project.owner
            LEFT JOIN project_account ON project_account.project = project.id
            WHERE project.id = :id
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data = $query->fetchObject();


            ////// campos calculados /////
            // Importe que aparece en el termómetro
            $sql = "SELECT  SUM(amount) as amount
                FROM    invest
                WHERE   project = :id
                AND     invest.status IN ('0', '1', '3', '4')
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->total = $query->fetchColumn();

            // Dinero perdido por incidencias no resueltas
            $sql = "SELECT  SUM(amount) as amount
                FROM    invest
                WHERE   project = :id
                AND     invest.issue = 1
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->issues = $query->fetchColumn();


            // Dinero enviado al proyecto (92% de lo cobrado correcto y pagado al proyecto)
            $sql = "SELECT  SUM(amount) as amount
                FROM    invest
                WHERE   project = :id
                AND     invest.status IN ('1', '3')
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $total = $query->fetchColumn();
            $data->project_total = $total * 0.92;

            // por banco
            $sql = "SELECT  SUM(amount) as amount
                FROM    invest
                WHERE   project = :id
                AND     invest.status IN ('1', '3')
                AND     invest.method IN ('tpv')
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $tpv = $query->fetchColumn();
            $data->project_tpv = $tpv * 0.92;
            $data->fee_tpv = $tpv * 0.008;

            // los manuales se añaden al banco pero no a la comision
            $sql = "SELECT  SUM(amount) as amount
                FROM    invest
                WHERE   project = :id
                AND     invest.status IN ('1', '3')
                AND     invest.method IN ('cash')
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $cash = $query->fetchColumn();
            $data->project_tpv += $cash * 0.92;


            // por paypal
            $sql = "SELECT  SUM(amount) as amount, COUNT(id) as num
                FROM    invest
                WHERE   project = :id
                AND     invest.status IN ('1', '3')
                AND     invest.method IN ('paypal')
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $paypal = $query->fetchObject();
            $data->project_paypal = $paypal->amount * 0.92;
            $data->fee_paypal = $paypal->amount * 0.034 + $paypal->num * 0.35;

            $data->fee_total = $data->fee_tpv + $data->fee_paypal;
            $data->project_total = $data->project_total - $data->fee_total;
            $data->project_tpv = $data->project_tpv - $data->fee_total;


            // Num total de donantes con información rellenada que renunciaron a recompensa: $data->num_resign
            $sql = "SELECT  COUNT(DISTINCT(invest.user))
                FROM    invest
                INNER JOIN invest_address
                    ON invest_address.invest = invest.id
                    AND invest_address.name != ''
                    AND invest_address.nif != ''
                WHERE project = :id
                AND invest.status IN ('0', '1', '3')
                AND (invest.issue IS NULL OR invest.issue = 0)
                AND (invest.resign IS NOT NULL AND invest.resign = 1)
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->num_resign = $query->fetchColumn();

            // Num de donantes con información rellenada de más de 100 euros aportados<br />
            // (independientemente de si fue en una o varias aportaciones): $data->num_resign100
            $sql = "SELECT  COUNT(invest.user),
                    SUM(invest.amount) as amount
                FROM    invest
                WHERE project = :id
                AND invest.status IN ('0', '1', '3')
                AND (invest.issue IS NULL OR invest.issue = 0)
                AND (invest.resign IS NOT NULL AND invest.resign = 1)
                GROUP BY invest.user
                HAVING amount >= 100
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->num_resign100 = $query->fetchColumn(0);

            // Num de usuarios que no  marcaron ninguna recompensa pero tampoco donacion: $data->num_noresign
            $sql = "SELECT  COUNT(invest.user)
                FROM    invest
                LEFT JOIN invest_reward
                    ON invest_reward.invest = invest.id
                WHERE project = :id
                AND invest.status IN ('0', '1', '3')
                AND (invest.issue IS NULL OR invest.issue = 0)
                AND (invest.resign IS NULL OR invest.resign = 0)
                AND invest_reward.reward IS NULL
                GROUP BY invest.user
            ";
            $query = Model\Invest::query($sql, array(':id' => $id));
            $data->num_noresign = $query->fetchColumn();

            */

        }

        return $data;

    }


    //TODO: añadir proyectos
    private static function geoloc() {

        $old_cache = \Goteo\Core\DB::cache();
        \Goteo\Core\DB::cache(true);

        $stats = new Model\Location\LocationStats(new Model\User\UserLocation, new Model\User);
        $total = Model\User::dbCount();
        $data = array(
            'date'          => date('Ymd'),
            'report'        => 'geoloc',
            'registered'    => $total,
            'no-location'   => $total - Model\User::dbCount(array('location' => ''), '!='),
            'located'       => $stats->countLocated(),
            'unlocated'     => $stats->countUnlocated(),
            'unlocable'     => $stats->countUnlocable(),
            'not-spain'     => $stats->countFiltered('country_code', 'ES', true),
            'by-region'     => $stats->countGroupFiltered('region', 'country_code', 'ES'),
            'by-country'    => $stats->countGroupCountries(),
            'by-node'       => array()
        );


        // por nodo (no exactamente geoloc....)
        $nodes = Model\Node::getList();
        foreach ($nodes as $nodeId => $nodeName) {
            $data['by-node'][$nodeName] = Model\User::dbCount(array('node' => $nodeId));
        }

        \Goteo\Core\DB::cache($old_cache);
        return $data;

    }


    /**
     * Este método devuelve los datos de aportes PayPal
     *
     * @FIXME: Queda pendiente una mejora en este informe para que sea util
     *
     *
     * @return \stdClass $data
     */
    private static function paypal() {


        $data = new \stdClass;

        /*
         * Aportes en estado preapproval:
         *      En paypal aun no hay nada de estos
         */

        /* Aportes en estado cobrado por goteo:
         *      En paypal debería haber el 100% de estos
         *      (menos comision)
         */
        $sql = "
                    SELECT SUM(amount) as amount, COUNT(id) as num
                    FROM invest
                    WHERE method = 'paypal' AND status = 1
                ";
        $query = Model\Invest::query($sql);
        $charged = $query->fetchObject();
        $charged->fee = $charged->amount * 0.034 + $charged->num * 0.35; // @FIXME este cálculo de tarifa PayPal no es exacto
        $charged->net = $charged->amount - $charged->fee;
        $charged->goteo = $charged->net;
        $data->charged = $charged;

        /* Aportes en estado pagado al proyecto:
         *      En paypal debería haber el 8% de estos
         *      (menos comision)
         */
        $sql = "
                    SELECT SUM(amount) as amount, COUNT(id) as num
                    FROM invest
                    WHERE method = 'paypal' AND status = 3
                ";
        $query = Model\Invest::query($sql);
        $paid = $query->fetchObject();
        $paid->fee = $paid->amount * 0.034 + $paid->num * 0.35; // @FIXME este cálculo de tarifa PayPal no es exacto
        $paid->net = $paid->amount - $paid->fee;
        $paid->goteo = $paid->net * \GOTEO_FEE / 100;
        // Por ahora GOTEO_FEE es el mismo 8%
        // cuando el porcentaje sea diferente por proyecto habrá que calcular la comisión por separado
        //

        $data->paid = $paid;

        $data->total = $charged->goteo + $paid->goteo;

        return $data;

    }


}
