<?php

namespace Goteo\Controller\Manage {

    use Goteo\Core\View,
        Goteo\Library\Message,
        Goteo\Model;

    class Projects {

        public static function process ($action = 'list', $id = null, $filters = array()) {
            
            $log_text = null;
            $errors = array();

            if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])) {

                $projData = Model\Project::get($_POST['id']);
                if (empty($projData->id)) {
                    Message::Error('El proyecto '.$_POST['id'].' no existe');
                    break;
                }

                if (isset($_POST['save-accounts'])) {

                    $accounts = Model\Project\Account::get($projData->id);
                    $accounts->bank = $_POST['bank'];
                    $accounts->bank_owner = $_POST['bank_owner'];
                    $accounts->paypal = $_POST['paypal'];
                    $accounts->paypal_owner = $_POST['paypal_owner'];
                    $accounts->allowpp = $_POST['allowpp'];
                    if ($accounts->save($errors)) {
                        Message::Info('Se han actualizado las cuentas del proyecto '.$projData->name);
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

            if ($action == 'manage') {

                $contract = Model\Contract::get($id);

                return new View(
                    'view/manage/index.html.php',
                    array(
                        'folder' => 'projects',
                        'file' => 'manage',
                        'contract' => $contract
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
            //    segun campos de contract_status
                /*
                <option value="all"<?php echo ($filters['contractStatus'] == 'all') ? ' selected="selected"' : ''; ?>>En cualquier estado</option>
                <option value="none"<?php echo ($filters['contractStatus'] == 'none') ? ' selected="selected"' : ''; ?>>Sin rellenar</option>
                <option value="filled"<?php echo ($filters['contractStatus'] == 'filled') ? ' selected="selected"' : ''; ?>>Contrato rellenado</option>
                <option value="sended"<?php echo ($filters['contractStatus'] == 'sended') ? ' selected="selected"' : ''; ?>>Contrato enviado</option>
                <option value="checked"<?php echo ($filters['contractStatus'] == 'checked') ? ' selected="selected"' : ''; ?>>Contrato revisado</option>
                <option value="ready"<?php echo ($filters['contractStatus'] == 'ready') ? ' selected="selected"' : ''; ?>>Documento generado</option>
                 */
            
            
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
