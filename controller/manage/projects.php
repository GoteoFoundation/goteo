<?php

namespace Goteo\Controller\Manage {

    use Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Message,
        Goteo\Model;

    class Projects {

        public static function process ($action = 'list', $id = null, $filters = array()) {
            
            $errors = array();

            if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])) {

                $projData = Model\Project::getMedium($_POST['id']);
                if (empty($projData->id)) {
                    Message::Error('El proyecto id "'.$_POST['id'].'" no existe o está corrupto');
                    throw new Redirection('/manage/projects');
                }

                if (isset($_POST['save-accounts'])) {

                    $accounts = Model\Project\Account::get($projData->id);
                    $accounts->bank = $_POST['bank'];
                    $accounts->paypal = $_POST['paypal'];
                    if ($accounts->save($errors)) {
                        throw new Redirection('/manage/projects');
                    } else {
                        Message::Error(implode('<br />', $errors));
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

            if ($action == 'preview') {

                $contract = Model\Contract::get($id);

                return new View(
                    'view/manage/index.html.php',
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

                return new View(
                    'view/manage/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'report',
                        'project' => $project,
                        'Data' => $Data
                    )
                );
            }

            if ($action == 'accounts') {

                $accounts = Model\Project\Account::get($project->id);

                // cambiar fechas
                return new View(
                    'view/manage/index.html.php',
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
            $status = Model\Project::status();
            $orders = array(
                'name' => 'Nombre',
                'date' => 'Fecha de publicación (recientes primero)',
                'adate' => 'Fecha de publicación (antiguos primero)',
                'number' => 'Número de contrato (mayor a menor)',
                'anumber' => 'Número de contrato (menor a mayor)'
            );

            return new View(
                'view/manage/index.html.php',
                array(
                    'folder' => 'projects',
                    'file' => 'list',
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
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
        public static function getList($filters = array()) {
            $projects = array();

            $values = array();

            // los filtros
            $sqlFilter = "";
            if ($filters['status'] > -1) {
                $sqlFilter .= " AND status = :status";
                $values[':status'] = $filters['status'];
            } else {
                $sqlFilter .= " AND status > 2 AND passed IS NOT NULL AND passed != '0000-00-00' AND status < 5";
            }
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND owner IN (SELECT id FROM user WHERE (name LIKE :user OR email LIKE :user))";
                $values[':user'] = "%{$filters['name']}%";
            }
            if (!empty($filters['proj_name'])) {
                $sqlFilter .= " AND name LIKE :name";
                $values[':name'] = "%{$filters['proj_name']}%";
            }

            // filtro estado de campaña
            if (!empty($filters['projectStatus'])) {
                if ($filters['projectStatus'] == 'all') // En campaña o financiados
                  $sqlFilter .= "";
                
                if ($filters['projectStatus'] == 'goingon') // En primera ronda
                  $sqlFilter .= "";
                    
                if ($filters['projectStatus'] == 'passed')// Pasado la primera ronda
                  $sqlFilter .= "";
                    
                if ($filters['projectStatus'] == 'succed') // Terminado la segunda ronda
                  $sqlFilter .= "";
            }

                
            // filtro estado de contrato 
            if (!empty($filters['contractStatus'])) {
                if ($filters['contractStatus'] == 'all') // Tengan o no contrato generado
                  $sqlFilter .= "";
                
                if ($filters['contractStatus'] == 'none') // En primera ronda
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'filled')// Registro de contrato generado pero no cerrado
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'sended') // Datos cerrados
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'checked') // Datos en revision
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'ready') // Documento generado
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'received') // Sobre recibido
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'payed') // Pagos realizados
                  $sqlFilter .= "";
                    
                if ($filters['contractStatus'] == 'finished') // Contrato cumplido
                  $sqlFilter .= "";
            }
            
            //el Order
            if (!empty($filters['order'])) {
                switch ($filters['order']) {
                    case 'date':
                        $sqlOrder .= " ORDER BY published DESC";
                    break;
                    case 'adate':
                        $sqlOrder .= " ORDER BY published ASC";
                    break;
                    case 'name':
                        $sqlOrder .= " ORDER BY name ASC";
                    break;
                    case 'number':
                        $sqlOrder .= " ORDER BY contract.number DESC";
                    break;
                    case 'anumber':
                        $sqlOrder .= " ORDER BY contract.number ASC";
                    break;
                }
            }

            // la select
            $sql = "SELECT 
                        id
                    FROM project
                    WHERE id != ''
                        $sqlFilter
                        $sqlOrder
                    LIMIT 999
                    ";

            $query = Model\Project::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $the_proj = Model\Project::getMedium($proj['id']);
                $the_proj->contract = Model\Contract::get($proj['id']);
                $projects[] = $the_proj;
            }
            return $projects;
        }
        

    }

}
