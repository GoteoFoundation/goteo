<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Model;

    class Project extends \Goteo\Core\Controller {

        public function index($id = null) {
            if ($id !== null) {

                if (isset($_GET['edit']))
                    return $this->edit($id); //Editar
                elseif (isset($_GET['finish']))
                    return $this->finish($id); // Para revision
                elseif (isset($_GET['raw'])) {
                    $project = Model\Project::get($id);
                    \trace($project);
                    die;
                }
                else
                    return $this->view($id);

            } else if (isset($_GET['create'])) {
                return $this->create();
            } else {
                throw new Error(Error::NOT_FOUND);
            }
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        private function edit ($id) {
            //@TODO Verificar si tiene permisos para editar (usuario)
            $nodesign = true; // para usar el formulario de proyecto en Julian mode

            $project = Model\Project::get($id);
//            die ('<pre>' . print_r($project, 1) . '</pre>');
            //@TODO Verificar si tieme permiso para editar libremente
            if ($project->status != 1 && $_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/project/{$project->id}");

            if (!isset($_SESSION['stepped']))
                $_SESSION['stepped'] = array();

            $steps = array(
                'userProfile' => array(
                    'name' => Text::get('step-1'),
                    'title' => Text::get('step-userProfile'),
                    'guide' => Text::get('guide-project-user-information'),
                    'offtopic' => true
                ),
                'userPersonal' => array(
                    'name' => Text::get('step-2'),
                    'title' => Text::get('step-userPersonal'),
                    'guide' => Text::get('guide-project-contract-information'),
                    'offtopic' => true
                ),
                'overview' => array(
                    'name' => Text::get('step-3'),
                    'title' => Text::get('step-overview'),
                    'guide' => Text::get('guide-project-description')
                ),
                'costs'=> array(
                    'name' => Text::get('step-4'),
                    'title' => Text::get('step-costs'),
                    'guide' => Text::get('guide-project-costs')
                ),
                'rewards' => array(
                    'name' => Text::get('step-5'),
                    'title' => Text::get('step-rewards'),
                    'guide' => Text::get('guide-project-rewards')
                ),
                'supports' => array(
                    'name' => Text::get('step-6'),
                    'title' => Text::get('step-supports'),
                    'guide' => Text::get('guide-project-support')
                ),
                'preview' => array(
                    'name' => Text::get('step-7'),
                    'title' => Text::get('step-preview'),
                    'guide' => Text::get('guide-project-overview'),
                    'offtopic' => true
                )
            );
            
            $step = null;      
                        
            
            foreach ($_REQUEST as $k => $v) {                
                if (strncmp($k, 'view-step-', 10) === 0 && !empty($v) && !empty($steps[substr($k, 10)])) {
                    $step = substr($k, 10);
                }                
            }
            
            if (empty($step)) {
            
                // vista por defecto, el primer paso con errores
                if (!empty($project->errors['userProfile']))
                    $step = 'userProfile';
                elseif (!empty($project->errors['userPersonal']))
                    $step = 'userPersonal';
                elseif (!empty($project->errors['overview']))
                    $step = 'overview';
                elseif (!empty($project->errors['costs']))
                    $step = 'costs';
                elseif (!empty($project->errors['rewards']))
                    $step = 'rewards';
                elseif (!empty($project->errors['supports']))
                    $step = 'supports';
                else
                    $step = 'preview';
                
            }
            
            // variables para la vista
            $viewData = array(
                'project' => $project,
                'steps' => $steps,
                'nodesign' => $nodesign,
                'step' => $step
            );

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                foreach ($steps as $id => &$data) {
                    
                    if (call_user_func_array(array($this, "process_{$id}"), array(&$project, &$errors))) {
                        // si un process devuelve true es que han enviado datos de este paso, lo añadimos a los pasados
                        if (!in_array($id, $_SESSION['stepped']))
                            $_SESSION['stepped'][] = $id;
                    }
                    
                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $project->save($errors);

                // si ha ocurrido algun error de proces (como p.ej. "no se ha podido guardar loqueseaa")
                if (!empty($errors))
                    throw new \Goteo\Core\Exception(implode('. ', $errors));

                //re-evaluar el proyecto
                $project->evaluate();

                //si nos estan pidiendo el error de un campo, se lo damos
                if (!empty($_GET['errors'])) {
                    foreach ($project->errors as $paso) {
                        if (!empty($paso[$_GET['errors']])) {
                            return new View(
                                'view/project/errors.json.php',
                                array('errors'=>array($paso[$_GET['errors']]))
                            );
                        }
                    }
                }
            }
            
            

            // segun el paso añadimos los datos auxiliares para pintar
            switch ($step) {
                case 'userProfile':
                    $viewData['user'] = Model\User::get($project->owner);
                    $viewData['interests'] = Model\User\Interest::getAll();
                    break;
                
                case 'overview':
                    $viewData['currently'] = Model\Project::currentStatus();
                    $viewData['categories'] = Model\Project\Category::getAll();
                    break;

                case 'costs':
                    $viewData['types'] = Model\Project\Cost::types();
                    break;

                case 'rewards':
                    $viewData['stypes'] = Model\Project\Reward::icons('social');
                    $viewData['itypes'] = Model\Project\Reward::icons('individual');
                    $viewData['licenses'] = Model\Project\Reward::licenses();
                    break;

                case 'supports':
                    $viewData['types'] = Model\Project\Support::types();
                    break;
                
                case 'preview':
                    $success = array();
                    if (empty($project->errors)) {
                        $success[] = Text::get('guide-project-success-noerrors');
                    }
                    if ($project->progress > 80 && $project->status == 1) {
                        $success[] = Text::get('guide-project-success-minprogress');
                        $success[] = Text::get('guide-project-success-okfinish');
                        $viewData['finishable'] = true;
                    }
                    $viewData['success'] = $success;
                    break;
            }


            $view = new View (
                "view/project/edit.html.php",
                $viewData
            );

            return $view;

        }

        private function create () {
            //@TODO Verificar si tienen permisos para crear nuevos proyectos
            $project = new Model\Project;
            $project->create($_SESSION['user']->id);
            $_SESSION['stepped'] = array();
                throw new Redirection("/project/{$project->id}/?edit");

            throw new \Goteo\Core\Exception('Fallo al crear un nuevo proyecto');
        }

        private function view ($id) {
            $project = Model\Project::get($id);
            return new View(
                'view/project/public.html.php',
                array(
                    'project' => $project
                )
            );
        }

        /*
         * Finalizar para revision, ready le cambia el estado
         */
        public function finish($id) {
            //@TODO verificar si tienen el mínimo progreso para verificación y si está en estado edición
            $project = Model\Project::get($id);

            if ($project->status != 1)
                throw new Redirection("/project/{$project->id}");

            $errors = array();
            if ($project->ready($errors))
                throw new Redirection("/project/{$project->id}");
            
            throw new \Goteo\Core\Exception(implode(' ', $errors));
        }

        /*
         *  Explorar proyectos, por el momento mostrará todos los proyectos publicados
         */
         public function explore() {
            $projects = Model\Project::published();

            return new View (
                'view/explore.html.php',
                array(
                    'message' => 'Estos son los proyectos actualmente activos',
                    'projects' => $projects
                )
            );
         }

        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        // del save y remove de las tablas relacionadas se enmcarga el model/project
        // primero añadir y luego quitar para que no se pisen los indices
        // En vez del hidden step, va a comprobar que esté definido en el post el primer campo del proceso
        //-----------------------------------------------
        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$project, &$errors) {
            if (!isset($_POST['user_name']))
                return false;

            $user = Model\User::get($project->owner);

            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'user_name'=>'name',
                'user_avatar'=>'avatar',
                'user_about'=>'about',
                'user_keywords'=>'keywords',
                'user_contribution'=>'contribution',
                'user_twitter'=>'twitter',
                'user_facebook'=>'facebook',
                'user_linkedin'=>'linkedin'
            );
                        
            foreach ($fields as $fieldPost=>$fieldTable) {
                $user->$fieldTable = $_POST[$fieldPost];
            }
            
            // Avatar
            if(!empty($_FILES['avatar_upload']['name'])) {
                $user->avatar = $_FILES['avatar_upload'];
            }

            $user->interests = $_POST['user_interests'];

            //tratar webs existentes
            foreach ($user->webs as $i => &$web) {
                // luego aplicar los cambios
                
                if (isset($_POST['web-'. $web->id . '-url'])) {
                    $web->url = $_POST['web-'. $web->id . '-url'];
                }
                
                //quitar las que quiten
                if (!empty($_POST['web-' . $web->id .  '-remove'])) {
                    unset($user->webs[$i]);
                }                                                    
                
            }

            //tratar nueva web
            if (!empty($_POST['web-add'])) {                
                $user->webs[] = new Model\User\Web(array(
                    'url'   => 'http://'
                ));
            }

            /// este es el único save que se lanza desde un metodo process_
            $user->save($project->errors['userProfile']);
            return true;
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */
        private function process_userPersonal(&$project, &$errors) {
            if (!isset($_POST['contract_name']))
                return false;

            // campos que guarda este paso
            $fields = array(
                'contract_name',
                'contract_surname',
                'contract_nif',
                'contract_email',
                'phone',
                'address',
                'zipcode',
                'location',
                'country'
            );

            foreach ($fields as $field) {
                $project->$field = $_POST[$field];
            }

            return true;
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        private function process_overview(&$project, &$errors) {
            if (!isset($_POST['name']))
                return false;

            // campos que guarda este paso
            $fields = array(
                'name',
                'image',
                'description',
                'motivation',
                'about',
                'goal',
                'related',
                'keywords',
                'media',
                'currently',
                'project_location'
            );

            foreach ($fields as $field) {
                $project->$field = $_POST[$field];
            }

            //categorias
            // añadir las que vienen y no tiene
            $tiene = $project->categories;
            if (!empty($_POST['categories'])) {
                $viene = $_POST['categories'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$cat) {
                $category = new Model\Project\Category(array('id'=>$cat,'project'=>$project->id));
                $project->categories[] = $category;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$cat) {
                unset($project->categories[$key]);
            }

            $quedan = $project->categories; // truki para xdebug

            return true;
        }

        /*
         * Paso 4 - COSTES
         */
        private function process_costs(&$project, &$errors) {
            if (!isset($_POST['resource']))
                return false;

            $project->resource = $_POST['resource'];
            
            //tratar costes existentes
            foreach ($project->costs as $key => $cost) {
                
                if (!empty($_POST["cost-{$cost->id}-remove"])) {
                    unset($project->costs[$key]);
                    continue;
                }
                
                $cost->cost = $_POST['cost-' . $cost->id . '-cost'];
                $cost->description = $_POST['cost-' . $cost->id .'-description'];
                $cost->amount = $_POST['cost-' . $cost->id . '-amount'];
                $cost->type = $_POST['cost-' . $cost->id . '-type'];
                $cost->required = $_POST['cost-' . $cost->id . '-required'];
                $cost->from = $_POST['cost-' . $cost->id . '-from'];
                $cost->until = $_POST['cost-' . $cost->id . '-until'];                
                
            }

            //añadir nuevo coste
            if (!empty($_POST['cost-add'])) {
                
                $project->costs[] = new Model\Project\Cost(array(
                    'project' => $project->id,
                    'cost'  => 'Nuevo coste',
                    'type'  => 'task',
                    
                ));
                
            }
           
            return true;
        }

        /*
         * Paso 5 - RETORNO
         */
        private function process_rewards(&$project, &$errors) {
                        

            //tratar retornos sociales
            foreach ($project->social_rewards as $k => $reward) {
                
                if (!empty($_POST["social_reward-{$reward->id}-remove"])) {
                    unset($project->social_rewards[$k]);
                    continue;
                }
                
                $reward->reward = $_POST['social_reward-' . $reward->id . '-reward'];
                $reward->description = $_POST['social_reward-' . $reward->id . '-description'];
                $reward->icon = $_POST['social_reward-' . $reward->id . '-icon'];
                $reward->license = $_POST['social_reward-' . $reward->id . '-license'];
                
                
            }

            // retornos individuales
            foreach ($project->individual_rewards as $k => $reward) {
                
                if (!empty($_POST["individual_reward-{$reward->id}-remove"])) {
                    unset($project->individual_rewards[$k]);
                    continue;
                }
                
                $reward->reward = $_POST['individual_reward-' . $reward->id .'-reward'];
                $reward->description = $_POST['individual_reward-' . $reward->id . '-description'];
                $reward->icon = $_POST['individual_reward-' . $reward->id . '-icon'];
                $reward->amount = $_POST['individual_reward-' . $reward->id . '-amount'];
                $reward->units = $_POST['individual_reward-' . $reward->id . '-units'];
                
            }

            // tratar nuevos retornos
            if (!empty($_POST['social_reward-add'])) {                
                $project->social_rewards[] = new Model\Project\Reward(array(
                    'type'      => 'social',
                    'project'   => $project->id,
                    'reward'    => 'Nuevo retorno colectivo'                    
                ));
            }
            
            if (!empty($_POST['individual_reward-add'])) {                
                $project->individual_rewards[] = new Model\Project\Reward(array(
                    'type'      => 'individual',
                    'project'   => $project->id,
                    'reward'    => 'Nueva recompensa individual'                    
                ));
            }

            return true;
            
        }

        /*
         * Paso 6 - COLABORACIONES
         */
         private function process_supports(&$project, &$errors) {            

            // tratar colaboraciones existentes
            foreach ($project->supports as $key => $support) {
                
                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["support-{$support->id}-remove"])) {
                    unset($project->supports[$key]);
                    continue;
                }
                
                $support->support = $_POST['support-' . $support->id . '-support'];
                $support->description = $_POST['support-' . $support->id . '-description'];
                $support->type = $_POST['support-' . $support->id . '-type'];
                
            }
            
            // añadir nueva colaboracion
            if (!empty($_POST['support-add'])) {
                $project->supports[] = new Model\Project\Support(array(
                    'project'       => $project->id,
                    'support'       => 'Nueva colaboración',
                    'type'          => 'task',
                    'description'   => ''
                ));
            }

            return true;
        }

        /*
         * Paso 7 - PREVIEW
         * No hay nada que tratar porque aq este paso no se le envia nada por post
         */
        private function process_preview(&$project) {
            if (!isset($_POST['comment']))
                return false;

            $project->comment = $_POST['comment'];

            return true;
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}