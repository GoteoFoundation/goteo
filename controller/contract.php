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
            if ($contract->project_user == $_SESSION['user']->id)  // es el dueño del proyecto
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
                if (!empty($contract->status->pdf)) {
                    $viewData['pdf'] = ''; // coger el get contents del archivo y sacarlo talcual
                } else {
                    // montar el contenido del pdf con los datops del contrato
                }

                return new View('view/contract/view.html.php', $viewData);
            } else {
                // no lo puede ver y punto
                throw new Redirection("/");
            }
        }

        public function raw ($id) {
            $contract = Model\Contract::get($id);
            // temporal para testeo, si no tiene contrato lo creamos
            if (!$contract) {
                if (Model\Contract::create($id)) {
                    $contract = Model\Contract::get($id);
                } else {
                    die ('fallo al crear el registro de contrato');
                }
            }
            die(\trace($contract));
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
        public function edit ($id, $step = 'promoter') {
            $contract = Model\Contract::get($id);
            
            // aunque pueda acceder edit, no lo puede editar si los datos ya se han dado por cerrados
            if ($contract->project_user != $_SESSION['user']->id // no es su proyecto
                && $contract->status->owner
                && !isset($_SESSION['user']->roles['gestor']) // no es un gestor
                && !isset($_SESSION['user']->roles['superadmin']) // no es superadmin
                ) {
                // le mostramos el pdf
                throw new Redirection('/contract/'.$id);
            }

            // checkeamos errores
            $contract->check();
            
            // todos los pasos, entrando en datos del promotor por defecto
            $steps = array(
                'promoter' => array(
                    'name' => Text::get('contract-step-promoter'),
                    'title' => 'Title Promotor',
                    'class' => 'first-on on',
                    'num' => ''
                ),
                'entity' => array(
                    'name' => Text::get('contract-step-entity'),
                    'title' => 'Title Entidad',
                    'class' => 'on-on on',
                    'num' => ''
                ),
                'accounts' => array(
                    'name' => Text::get('contract-step-accounts'),
                    'title' => 'Title Cuentas',
                    'class' => 'on-on on',
                    'num' => ''
                ),
                'documents' => array(
                    'name' => Text::get('contract-step-documents'),
                    'title' => 'Title Documentos',
                    'class' => 'on-off on',
                    'num' => ''
                ),
                'final' => array(
                    'name' => Text::get('contract-step-final'),
                    'title' => 'Title Revisión',
                    'class' => 'off-last off',
                    'num' => ''
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
                
                // checkeamos de nuevo
                $contract->check();
            }
            
            if (!empty($errors)) {
                Message::Error(implode('<br />', $errors));
            }

            // variables para la vista
            $viewData = array(
                'contract' => $contract,
                'steps' => $steps,
                'step' => $step
            );

            $view = new View (
                "view/contract/edit.html.php",
                $viewData
            );

            return $view;

        }

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
                'birthdate',
                'address',
                'location',
                'region',
                'zipcode',
                'country'
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
            }

            return true;
        }

        /*
         * Entidad
         */
        private function process_entity(&$contract, &$errors) {
            if (!isset($_POST['process_entity'])) {
                return false;
            }

            // campos que guarda este paso. Verificar luego. 
            $fields = array(
                'office',
                'entity_name',
                'entity_cif',
                'entity_address',
                'entity_location',
                'entity_region',
                'entity_zipcode',
                'entity_country',
                'reg_name',
                'reg_date',
                'reg_number',
                'reg_id'
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
            }

            return true;
        }

        /*
         * Cuentas
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
//                'paypal', no modificamos la cuenta paypal
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
         * Documentación
         */
        private function process_documents(&$contract, &$errors) {
            if (!isset($_POST['process_documents'])) {
                return false;
            }

            // tratar el que suben
            if(!empty($_FILES['doc_upload']['name'])) {
                // procesarlo aqui con el submodelo Contract\Doc
                $newdoc = new Model\Contract\Document(
                        array('contract' => $contract->project)
                    );
                $newdoc->setFile($_FILES['doc_upload']);
                if ($newdoc->save($errors)) {
                    $contract->docs[] = $newdoc;
                }
            }

            // tratar el que quitan
            foreach ($contract->docs as $key=>$doc) {
                if (!empty($_POST["docs-{$doc->id}-remove"])) {
                    if ($doc->remove($errors)) {
                        unset($contract->docs[$key]);
                    }
                }
            }
            
            // y los campos de descripción
            $fields = array(
                'project_description',
                'project_invest',
                'project_return'
            );

            foreach ($fields as $field) {
                $contract->$field = $_POST[$field];
            }
            
            
            
            return true;
        }

        /*
         * Paso final, revisión y cierre
         */
        private function process_final(&$contract, &$errors) {
            if (!isset($_POST['process_final'])) {
                return false;
            }

            // este paso solo cambia el campo de cerrado (y flag de cerrado por impulsor)
            if (isset($_POST['finish'])) {
                // marcar en el registro de gestión, "datos de contrato" cerrados
                if (Model\Contract::setStatus($contract->project, array('owner'=>true))) {
                    Message::Info('El formulario de contrato ha sido cerrado para revisión');

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($contract->project);
                    $log->populate('Impulsor da por cerrados los datos del contrato (dashboard)', '/admin/projects', \vsprintf('%s ha cerrado los datos del contrato del proyecto %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('project', $contract->project_name, $contract->project)
                            )));
                    $log->doAdmin('user');
                    unset($log);

                    $contract->status = Model\Contract::getStatus($contract->project);
                    
                    return true;

                } else {
                    Message::Error('Ha habido algún error al cerrar los datos de contrato');
                    return false;
                }
            }            
            
            return true;
        }

        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}