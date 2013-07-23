<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Library\Message,
        Goteo\Library\Feed,
        Goteo\Model;

//@TODO: ACL, cerrado para todos y se abre al impulsor 
    //      o bien, abierto y se verifica por código
    
    
    
    class Contract extends \Goteo\Core\Controller {

        /**
         * La vista por defecto del contrato ES el pdf
         * 
         * @param string(50) $id del proyecto
         * @return \Goteo\Core\View   Pdf
         */
        public function index($id = null) {
            
            $contract = Model\Contract::get($id); // datos del contrato
            $project  = Model\Project::get($id, null); // datos del proyecto

            // solamente se puede ver si....
            // Es un admin, es el impulsor
            // 
            $grant = false;
            if ($contract->owner == $_SESSION['user']->id)  // es el dueño del proyecto
                $grant = true;
            elseif (ACL::check('/contract/edit/'.$id))  // puede editar el proyecto
                $grant = true;
            elseif (ACL::check('/contract/edit/todos'))  // es un admin
                $grant = true;

            // si lo puede ver
            if ($grant) {
                $viewData = array(
                        'contract' => $contract,
                        'project' => $project
                    );

                // si existe el archivo físico lo mostramos
                // si no existe, lo generamos con los datos actuales
                return new View('view/contract/view.html.php', $viewData);

            } else {
                // no lo puede ver y punto
                throw new Redirection("/");
            }
        }

        public function raw ($id) {
            $contract = Model\Contract::get($id, LANG);
            \trace($contract);
            die;
        }

        // los contratos no se pueden eliminar... ¿o sí?
        public function delete ($id) {
            /*
            $contract = Model\Contract::get($id);
            $errors = array();
            if ($contract->delete($errors)) {
                Message::Info("Has borrado los datos del proyecto '<strong>{$contract->name}</strong>' correctamente");
                if ($_SESSION['contract']->id == $id) {
                    unset($_SESSION['contract']);
                }
            } else {
                Message::Info("No se han podido borrar los datos del proyecto '<strong>{$contract->name}</strong>'. Error:" . implode(', ', $errors));
            }
             */
            throw new Redirection("/dashboard/projects/contract");
        }

        //Aunque no esté en estado edición un admin siempre podrá editar los datos de contrato
        public function edit ($id) {
            $contract = Model\Contract::get($id, null);

            // aunque pueda acceder edit, no lo puede editar si los datos ya se han dado por cerrados
            if ($contract->owner != $_SESSION['user']->id // no es su proyecto
                && $contract->status->editable
                && !isset($_SESSION['user']->roles['gestor']) // no es un gestor
                && !isset($_SESSION['user']->roles['superadmin']) // no es superadmin
                ) {
                // le mostramos el pdf
                throw new Redirection('/contract/'.$id);
            }

            // todos los pasos, entrando en userProfile por defecto
            $step = 'accounts';

            $steps = array(
                'promoter' => array(
                    'name' => 'Promotor',
                    'title' => 'Promotor'
                ),
                'accounts' => array(
                    'name' => 'Cuentas',
                    'title' => 'Cuentas'
                ),
                'additionals' => array(
                    'name' => 'Detalles',
                    'title' => 'Detalles'
                ),
                'legal'=> array(
                    'name' => 'Legales',
                    'title' => 'Legales'
                )
            );
            
                        
            
            foreach ($_REQUEST as $k => $v) {                
                if (strncmp($k, 'view-step-', 10) === 0 && !empty($v) && !empty($steps[substr($k, 10)])) {
                    $step = substr($k, 10);
                }                
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                foreach ($steps as $id => &$data) {
                    
                    if (call_user_func_array(array($this, "process_{$id}"), array(&$contract, &$errors))) {
                        // ok
                    }
                    
                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $contract->save($errors);
            }

            //checkeo de campos oblogatorios
            $contract->check();

            // variables para la vista
            $viewData = array(
                'contract' => $contract,
                'steps' => $steps,
                'step' => $step
            );


            // segun el paso añadimos los datos auxiliares para pintar
            switch ($step) {
                // datos del promotor
                case 'promoter': // cambiar luego a promoter
                    // si no tiene registro de contrato, cargamos los datos personales del usuario
                    // si tiene registro de contrato, cargamos de ahí
                    break;
                
                // cuentas
                case 'accounts':
                    // cargamos los datos de las cuentas del proyecto (ver dashboard)
//                    $viewData['accounts'] = Model\Contract::currentStatus();
                    break;

                // adicionales
                case 'additionals':
                    break;

                // otros datos legales
                case 'legals':
                    break;

            }

            $view = new View (
                "view/contract/edit.html.php",
                $viewData
            );

            return $view;

        }

        /* Solo desde admin, para inicializar el registro del contrato
         * 
         * Pone numero de contrato
         * los datos del proyecto y del impulsor
         * 
         * 
         * 
         */
        public function create () {

            
            
            
            $contract = new Model\Contract(
                            array(
                                'id'=>'a'
                            )
                        );
            
            throw new Redirection("/contract/edit/{$contract->id}");
        }

        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        // del save y remove de las tablas relacionadas se enmcarga el model/contract
        // primero añadir y luego quitar para que no se pisen los indices
        //-----------------------------------------------
        
        /*
         * Los campos que tenemos en la tabla son:
                `id`, 
                `project`, 
                `number`, 
                `date`, 
                `type`, 
                `name`, 
                `nif`, 
                `office`, 
                `address`, 
                `location`, 
                `region`, 
                `country`, 
                `entity_name`, 
                `entity_cif`, 
                `entity_address`, 
                `entity_location`, 
                `entity_region`, 
                `entity_country`, 
                `reg_name`, 
                `reg_number`, 
                `reg_id`, 
                `project_name`, 
                `project_url`, 
                `project_owner`, 
                `project_user`, 
                `project_profile`, 
                `project_description`, 
                `bank`, 
                `bank_owner`, 
                `paypal`, 
                `paypal_owner`                
         * 
         */
        
        
        
        
        /*
         * Promotor
         */
        private function process_promoter(&$contract, &$errors) {
            if (!isset($_POST['process_promoter'])) {
                return false;
            }

            // campos que guarda este paso. Verificar luego. 
            $fields = array(
                'type',
                'name',
                'nif',
                'office',
                'address',
                'location',
                'region',
                'country',
                'entity_name',
                'entity_cif',
                'entity_address',
                'entity_location',
                'entity_region',
                'entity_country'
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
            }

            return true;
        }

        /*
         * Cuentas
         * Dualidad: En 
         * 
         * 
         */
        private function process_accounts(&$contract, &$errors) {
            if (!isset($_POST['process_accounts'])) {
                return false;
            }

            // también en la tabla de cuentas
            $accounts = Model\Project\Account::get($contract->project);
            
            $fields = array(
                'bank',
                'bank_owner',
                'paypal',
                'paypal_owner'
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
                $accounts->$field = $_POST[$field];
            }
            
            $accounts->save($errors);
            
            return true;
        }

        /*
         * Datos adicionales, verificar luego
         * Descripción del proyecto (para contrato)
         * datos de registro,
         * 
         */

        private function process_additionals(&$contract, &$errors) {
            if (!isset($_POST['process_additionals'])) {
                return false;
            }

            $fields = array(
                'birthdate',
                'project_description',
                'reg_name',
                'reg_number',
                'reg_id'
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
            }
            
            return true;
        }

        /*
         * Legales
         */
        private function process_legals(&$contract, &$errors) {
            if (!isset($_POST['process_legals'])) {
                return false;
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
            }
            
            return true;
        }

        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}