<?php

namespace Goteo\Controller\Manage {

    use Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Application\Message,
        Goteo\Model,
        Goteo\Model\Mail;

    class Projects {

        public static function process ($action = 'list', $id = null, $subaction = null, $filters = array()) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])) {

                $projData = Model\Project::getMedium($_POST['id']);
                if (empty($projData->id)) {
                    Message::error('El proyecto id "'.$_POST['id'].'" no existe o está corrupto');
                    throw new Redirection('/manage/projects');
                }

                if (isset($_POST['save-accounts'])) {

                    $accounts = Model\Project\Account::get($projData->id);
                    $accounts->bank = $_POST['bank'];
                    $accounts->paypal = $_POST['paypal'];
                    if ($accounts->save($errors)) {
                        throw new Redirection('/manage/projects');
                    } else {
                        Message::error(implode('<br />', $errors));
                    }

                }

            }

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            if (isset($id)) {
                $project = Model\Project::get($id);
            }

            // poner y quitar flags
            if ($action == 'setflag') {
                Model\Contract::setStatus($id, array($subaction => 1));

                // aviso al impulsor cuando se activa el 'listo para imprimir'
                if ($subaction == 'ready') {

                    $mailHandler = new Mail();

                    $mailHandler->to = $project->user->email;
                    $mailHandler->toName = $project->user->name;
                    $mailHandler->reply = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
                    $mailHandler->replyName = GOTEO_MAIL_NAME;
                    $mailHandler->subject = 'Contrato listo para imprimir';
                    $mailHandler->content = \Goteo\Controller\Dashboard\Projects::prepare_content('ready');
                    $mailHandler->html = true;
                    $mailHandler->template = 11;
                    if ($mailHandler->send($errors)) {
                        Message::info('Se le ha enviado a '.$project->user->email.' el contenido de "Contrato listo para imprimir" ');
                    } else {
                        Message::error('FALLO al enviar mail de "Contrato listo para imprimir". <br />Mandarselo a mano.<br />Errores: '.implode('<br />', $errors));
                    }

                    unset($mailHandler);
                }



                throw new Redirection('/manage/projects/#'.$id);
            }

            if ($action == 'unsetflag') {
                Model\Contract::setStatus($id, array($subaction => 0));

                /*
                 * Ya no hacemos esto porque no guardamos el documento del contrato
                 *
                // si están quitando pdf, eliminamos el registro de contrato y archivo
                if ($subaction == 'pdf') {
                    list($num, $cdate) = Model\Contract::getNum($id);
                    $pdf_name = 'contrato-goteo_'.$num.'-'.$cdate.'.pdf';
                    $filename = Model\Contract\Document::$dir . $id . '/' . $pdf_name;
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                }
                */

                throw new Redirection('/manage/projects/#'.$id);
            }

            if ($action == 'create') {

                $contract = Model\Contract::get($id);
                Model\Contract::create($id);
                throw new Redirection('/manage/projects/#'.$id);
            }

            if ($action == 'preview') {

                $contract = Model\Contract::get($id);

                return new View(
                    'manage/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'preview',
                        'contract' => $contract
                    )
                );
            }

            if ($action == 'report') {
                // informe financiero
                // Datos para el informe de transacciones correctas
                $Data = Model\Invest::getReportData($project->id, $project->status, $project->round, $project->passed);
                $account = Model\Project\Account::get($project->id);

                return new View(
                    'manage/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'report',
                        'project' => $project,
                        'account' => $account,
                        'Data' => $Data
                    )
                );
            }

            if ($action == 'accounts') {

                $accounts = Model\Project\Account::get($project->id);

                // cambiar fechas
                return new View(
                    'manage/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'accounts',
                        'project' => $project,
                        'accounts' => $accounts
                    )
                );
            }


            if (!empty($filters['filtered'])) {
                $projects = static::getList($filters);
            } else {
                $projects = array();
            }
            $nodes = Model\Node::getList();
            $status = Model\Project::status();
            $projectStatus = Model\Project::procStatus(); // estado del proceso de campaña (1a, 2a, compeltada)
            $contractStatus = Model\Contract::procStatus(); // estado del proceso de contrato
            $orders = array(
                'name' => 'Nombre',
                'date' => 'Fecha de publicación (recientes primero)',
                'adate' => 'Fecha de publicación (antiguos primero)',
                'number' => 'Número de contrato (mayor a menor)',
                'anumber' => 'Número de contrato (menor a mayor)'
            );

            return new View(
                'manage/index.html.php',
                array(
                    'folder' => 'projects',
                    'file' => 'list',
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
                    'nodes' => $nodes,
                    'projectStatus' => $projectStatus,
                    'contractStatus' => $contractStatus,
                    'orders' => $orders
                )
            );

        }


        /**
         * Saca una lista completa de proyectos
         *
         * @param string node id
         * @return array of project instances
         */
        public static function getList(&$filters = array()) {
            $projects = array();

            $values = array();
            $joined = false; // si hay join con contract

            // los filtros
            $sqlFilter = $sqlJoin = "";
            if ($filters['status'] > -1) {
                $sqlFilter .= " AND project.status = :status";
                $values[':status'] = $filters['status'];
            }
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND project.owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['name'])) {
                $sqlJoin .= "INNER JOIN user ON user.id = project.owner";
                $sqlFilter .= " AND (user.name LIKE :user OR user.email LIKE :user)";
                $values[':user'] = "%{$filters['name']}%";
            }
            if (!empty($filters['proj_name'])) {
                $sqlFilter .= " AND project.name LIKE :name";
                $values[':name'] = "%{$filters['proj_name']}%";
            }
            if (!empty($filters['node'])) {
                $sqlFilter .= " AND project.node LIKE :node";
                $values[':node'] = $filters['node'];
            }

            // filtro estado de campaña
            if (!empty($filters['projectStatus'])) {
                if ($filters['projectStatus'] == 'all') // En campaña o financiados
                  $sqlFilter .= " AND project.status IN (3, 4)";

                if ($filters['projectStatus'] == 'first') // En primera ronda
                  $sqlFilter .= " AND project.status = 3 AND project.passed IS NULL";

                if ($filters['projectStatus'] == 'second')// En segunda ronda
                  $sqlFilter .= " AND project.status = 3 AND project.passed IS NOT NULL";

                if ($filters['projectStatus'] == 'completed') // Campaña completada
                  $sqlFilter .= " AND project.status = 4";
            }


            // filtro estado de contrato
            if (!empty($filters['contractStatus'])) {
                switch ($filters['contractStatus']) {
                    case 'all': // Tengan o no contrato generado
                        $sqlFilter .= " AND (contract_status.contract IS NULL OR contract_status.closed = 0)
                        ";
                        break;

                    case 'noreg': // Sin registro de contrato
                        $sqlJoin .= "LEFT JOIN contract ON contract.project = project.id";
                        $sqlFilter .= " AND contract.project IS NULL
                        ";
                        $joined = true;
                        break;

                    case 'onform': // Editando datos
                        $sqlJoin .= "INNER JOIN contract ON contract.project = project.id";
                        $sqlFilter .= " AND (contract.project IS NOT NULL OR contract_status.owner = 0)
                        ";
                        $joined = true;
                        break;

                    default:
                        // aqui hay que filtrar hasta ese estado específico pero los posteriores a cero
                        // excepto el flag de pago adelantado
                          $sqlFilter .= " AND contract_status.{$filters['contractStatus']} = 1
                          ";

                          // sacamos los estados posteriores
                          $nexts = Model\Contract::nextStatus($filters['contractStatus']);

                          if (!empty($nexts)) foreach ($nexts as $next) {
                              $sqlFilter .= " AND contract_status.{$next} = 0
                              ";
                          }

                        break;
                }
            }

            /*
            if ($filters['prepay'] == 1) {
                $sqlFilter .= " AND contract_status.prepay = 1";
            } elseif (!isset($filters['prepay'])) {
                $filters['prepay'] = 0;
            }
             */

            //el Order
            $sqlOrder = '';
            switch ($filters['order']) {
                case 'adate': // por fecha, antiguos primero
                    $sqlOrder .= " ORDER BY project.published ASC";
                break;
                case 'name': // por nombre
                    $sqlOrder .= " ORDER BY project.name ASC";
                break;
                case 'number': // por numero, más nuevos primero
                    if ($filters['contractStatus'] == 'noreg') {
                        // ya hay un filtro para "Sin registro de contrato"
                        $sqlOrder .= " ORDER BY project.published DESC";
                        $filters['order'] = 'date';
                    } elseif (!$joined) {
                        // solo con registro de contrato
                        $sqlJoin .= "INNER JOIN contract ON contract.project = project.id";
                        $sqlOrder .= " ORDER BY contract.number DESC";
                    } else {
                        $sqlOrder .= " ORDER BY contract.number DESC";
                    }
                break;
                case 'anumber': // por numero, más antiguos primero
                    if ($filters['contractStatus'] == 'noreg') {
                        // ya hay un filtro para "Sin registro de contrato"
                        $sqlOrder .= " ORDER BY project.published DESC";
                        $filters['order'] = 'date';
                    } elseif (!$joined) {
                        // solo con registro de contrato
                        $sqlJoin .= "INNER JOIN contract ON contract.project = project.id";
                        $sqlOrder .= " ORDER BY contract.number ASC";
                    } else {
                        $sqlOrder .= " ORDER BY contract.number DESC";
                    }
                break;
                case 'date': // por fecha, recientes primero
                default:
                    $sqlOrder .= " ORDER BY project.published DESC";
                break;
            }

            // la select
            // @Javier , esto habría que optimizarlo igual que el Project::GetList
            // no se usa exactamente porque aqui necesita join con datos de contrato
            $sql = "SELECT
                        project.id
                    FROM project
                    $sqlJoin
                    LEFT JOIN contract_status
                        ON contract_status.contract = project.id
                    WHERE project.id != ''
                        $sqlFilter
                        $sqlOrder
                    LIMIT 999
                    ";

            /*
            var_dump($values);
            echo $sql;
            die;
            */

            $query = Model\Project::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $proj) {
                $the_proj = Model\Project::getMedium($proj->id);
                $the_proj->contract = Model\Contract::get($proj->id);

                // si aun no tiene fechas hay que calcularlas
                $the_date = strtotime($the_proj->published);
                if (empty($the_proj->passed)) {
                    $the_proj->passed = date('Y-m-d', mktime(0, 0, 0, date('m', $the_date), date('d',$the_date)+$the_proj->days_round1, date('Y', $the_date)));
                }
                if (empty($the_proj->success)) {
                    $the_proj->success = date('Y-m-d', mktime(0, 0, 0, date('m', $the_date), date('d',$the_date)+$the_proj->days_total, date('Y', $the_date)));
                }

                // preparamos los flags
                if (empty($the_proj->contract)) {
                    $flags = array('noreg' => 1); // no tiene registro
                } elseif (empty($the_proj->contract->status)) {
                    $flags = array('onform' => 1); // tiene registro pero sin estados
                } else {
                    $flags = (array) $the_proj->contract->status;
                    // si no está cerrado es que está editando
                    if ($flags['owner'] == 0) {
                        $flags['onform'] = 1;
                    }
                }
                $the_proj->flags = $flags;

                // y el número
                list($cNum, $cDate) = Model\Contract::getNum($the_proj->id, $the_proj->published);
                $the_proj->cName = "P-{$cNum}-{$cDate}";

                // incidencias, importe total
                $sum = Model\Invest::getList(array(
                        'projects' => $the_proj->id,
                        'issue' => 'show'
                    ), null, 0, 0, 'money');

                $the_proj->issues = $sum;
                // y si estas incidencias hacen peligrar el mínimo

                $projects[] = $the_proj;
            }
            return $projects;
        }


    }

}
