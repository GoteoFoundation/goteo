<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Model;

    class Project extends \Goteo\Core\Controller {

        public function index($id = null, $show = 'home') {
            if ($id !== null) {
                return $this->view($id, $show);
            } else if (isset($_GET['create'])) {
                throw new Redirection("/project/create");
            } else {
                throw new Redirection("/discover");
            }
        }

        public function raw ($id) {
            $project = Model\Project::get($id);
            $project->check();
            \trace($project);
            die;
        }

        public function trash ($id) {
            $project = Model\Project::get($id);
            $project->trash();
            throw new Redirection("/dashboard");
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        public function edit ($id) {
            $project = Model\Project::get($id);

            // si no tenemos SESSION stepped es porque no venimos del create
            if (!isset($_SESSION['stepped']))
                $_SESSION['stepped'] = array(
                    'userProfile' => 'userProfile',
                     'userPersonal' => 'userPersonal',
                     'overview' => 'overview',
                     'costs' => 'costs',
                     'rewards' => 'rewards',
                     'supports' => 'supports'
                );

            if ($project->status != 1 && $_SESSION['user']->id == $project->owner) {
                // solo seguimiento estado, progreso
                // pasos preview, conseguido, recompensas
                
                $steps = array(
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'guide' => Text::get('guide-project-overview'),
                        'offtopic' => true
                    )
                );
                 
                 
            } else {
                // todos los pasos
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
            }
            
            $step = null;      
                        
            
            foreach ($_REQUEST as $k => $v) {                
                if (strncmp($k, 'view-step-', 10) === 0 && !empty($v) && !empty($steps[substr($k, 10)])) {
                    $step = substr($k, 10);
                }                
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                foreach ($steps as $id => &$data) {
                    
                    if (call_user_func_array(array($this, "process_{$id}"), array(&$project, &$errors))) {
                        // si un process devuelve true es que han enviado datos de este paso, lo añadimos a los pasados
                        if (!in_array($id, $_SESSION['stepped'])) {
                            $_SESSION['stepped'][$id] = $id;
                        }
                    }
                    
                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $project->save($errors);

                // si ha ocurrido algun error de proces (como p.ej. "no se ha podido guardar loqueseaa")
                /*
                 * Me follo la exception de si falla el save, ya veremos como mostrar esos errors
                if (!empty($errors))
                    throw new \Goteo\Core\Exception(implode('. ', $errors));
                 *
                 */

                // si estan enviando el proyecto a revisión
                if (isset($_POST['process_preview']) && isset($_POST['finish'])) {
                    $errors = array();
                    if ($project->ready($errors)) {
                        throw new Redirection("/dashboard?ok");
                    }
                }


            }

            //re-evaluar el proyecto
            $project->check();

            // vista por defecto, el primer paso por el que no ha pasado
            foreach ($steps as $id => $data) {

                if (empty($step) && !empty($project->errors[$id])) {
                    $step = $id;
                    break;
                }
            }

            if (empty($step)) {
                $step = 'preview';
            }

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

            // si
            // para cada paso, si no han pasado por el, quitamos errores y okleys de ese paso
            foreach ($steps as $id => $data) {
                if (!in_array($id, $_SESSION['stepped'])) {
                    unset($project->errors[$id]);
                    unset($project->okeys[$id]);
                }
            }


            
            // variables para la vista
            $viewData = array(
                'project' => $project,
                'steps' => $steps,
                'step' => $step
            );


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
                    if ($project->finishable) {
                        $success[] = Text::get('guide-project-success-minprogress');
                        $success[] = Text::get('guide-project-success-okfinish');
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

        public function create () {
            $project = new Model\Project;
            $project->create($_SESSION['user']->id);

            $_SESSION['stepped'] = array();
                throw new Redirection("/project/edit/{$project->id}");

            throw new \Goteo\Core\Exception('Fallo al crear un nuevo proyecto');
        }

        private function view ($id, $show) {
            $project = Model\Project::get($id);

            $viewData = array(
                    'project' => $project,
                    'show' => $show
                );

            //tenemos que tocar esto un poquito para gestionar los pasos al aportar
            if ($show == 'invest') {

                // si no está validado no puede aportar
                if (empty($_SESSION['user'])) {
                    throw new Redirection("/user/login");
                }

                $viewData['show'] = 'supporters';
                if (isset($_GET['confirm'])) {
                    if (\in_array($_GET['confirm'], array('ok', 'fail'))) {
                        $invest = $_GET['confirm'];
                    } else {
                        $invest = 'start';
                    }
                } else {
                    $invest = 'start';
                }
                $viewData['invest'] = $invest;
            }

            return new View('view/project/public.html.php', $viewData);
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
            if (!isset($_POST['process_userProfile'])) {
                return false;
            }

            $user = Model\User::get($project->owner);

            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'user_name'=>'name',
                'user_location'=>'location',
                'user_avatar'=>'avatar',
                'user_about'=>'about',
                'user_keywords'=>'keywords',
                'user_contribution'=>'contribution',
                'user_twitter'=>'twitter',
                'user_facebook'=>'facebook',
                'user_linkedin'=>'linkedin'
            );
                        
            foreach ($fields as $fieldPost=>$fieldTable) {
                if (isset($_POST[$fieldPost])) {
                    $user->$fieldTable = $_POST[$fieldPost];
                }
            }
            
            // Avatar
            if(!empty($_FILES['avatar_upload']['name'])) {
                $user->avatar = $_FILES['avatar_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['avatar-' . $user->avatar->id .  '-remove'])) {
                $user->avatar->remove('user');
                $user->avatar = '';
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
            $user = Model\User::flush();
            return true;
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */
        private function process_userPersonal(&$project, &$errors) {
            if (!isset($_POST['process_userPersonal'])) {
                return false;
            }

            // campos que guarda este paso
            $fields = array(
                'contract_name',
                'contract_nif',
                'phone',
                'address',
                'zipcode',
                'location',
                'country'
            );

            $personalData = array();

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $project->$field = $_POST[$field];
                    $personalData[$field] = $_POST[$field];
                }
            }

            // actualizamos estos datos en los personales del usuario
            if (!empty ($personalData)) {
                Model\User::setPersonal($project->owner, $personalData, true);
            }

            return true;
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        private function process_overview(&$project, &$errors) {
            if (!isset($_POST['process_overview'])) {
                return false;
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
                'name',
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
                if (isset($_POST[$field])) {
                    $project->$field = $_POST[$field];
                }
            }
            
            // tratar la imagen que suben
            if(!empty($_FILES['image_upload']['name'])) {
                $project->image = $_FILES['image_upload'];
            }

            // tratar las imagenes que quitan
            foreach ($project->gallery as $key=>$image) {
                if (!empty($_POST["gallery-{$image->id}-remove"])) {
                    $image->remove('project');
                    unset($project->gallery[$key]);
                }
            }

            // Media
            if (!empty($project->media)) {
                $project->media = new Model\Project\Media($project->media);
            }

            //categorias
            // añadir las que vienen y no tiene
            $tiene = $project->categories;
            if (isset($_POST['categories'])) {
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
            if (!isset($_POST['process_costs'])) {
                return false;
            }

            if (isset($_POST['resource'])) {
                $project->resource = $_POST['resource'];
            }
            
            //tratar costes existentes
            foreach ($project->costs as $key => $cost) {
                
                if (!empty($_POST["cost-{$cost->id}-remove"])) {
                    unset($project->costs[$key]);
                    continue;
                }

                if (isset($_POST['cost-' . $cost->id . '-cost'])) {
                    $cost->cost = $_POST['cost-' . $cost->id . '-cost'];
                    $cost->description = $_POST['cost-' . $cost->id .'-description'];
                    $cost->amount = $_POST['cost-' . $cost->id . '-amount'];
                    $cost->type = $_POST['cost-' . $cost->id . '-type'];
                    $cost->required = $_POST['cost-' . $cost->id . '-required'];
                    $cost->from = $_POST['cost-' . $cost->id . '-from'];
                    $cost->until = $_POST['cost-' . $cost->id . '-until'];
                }
            }

            //añadir nuevo coste
            if (!empty($_POST['cost-add'])) {
                
                $project->costs[] = new Model\Project\Cost(array(
                    'project' => $project->id,
                    'cost'  => 'Nueva tarea',
                    'type'  => 'task',
                    'from' => date('Y-m-d'),
                    'until' => date('Y-m-d')
                    
                ));
                
            }
           
            return true;
        }

        /*
         * Paso 5 - RETORNO
         */
        private function process_rewards(&$project, &$errors) {
            if (!isset($_POST['process_rewards'])) {
                return false;
            }

            //tratar retornos sociales
            foreach ($project->social_rewards as $k => $reward) {
                
                if (!empty($_POST["social_reward-{$reward->id}-remove"])) {
                    unset($project->social_rewards[$k]);
                    continue;
                }

                if (isset($_POST['social_reward-' . $reward->id . '-reward'])) {
                    $reward->reward = $_POST['social_reward-' . $reward->id . '-reward'];
                    $reward->description = $_POST['social_reward-' . $reward->id . '-description'];
                    $reward->icon = $_POST['social_reward-' . $reward->id . '-icon'];
                    $reward->license = $_POST['social_reward-' . $reward->id . '-license'];
                }
                
            }

            // retornos individuales
            foreach ($project->individual_rewards as $k => $reward) {
                
                if (!empty($_POST["individual_reward-{$reward->id}-remove"])) {
                    unset($project->individual_rewards[$k]);
                    continue;
                }

                if (isset($_POST['individual_reward-' . $reward->id .'-reward'])) {
                    $reward->reward = $_POST['individual_reward-' . $reward->id .'-reward'];
                    $reward->description = $_POST['individual_reward-' . $reward->id . '-description'];
                    $reward->icon = $_POST['individual_reward-' . $reward->id . '-icon'];
                    $reward->amount = $_POST['individual_reward-' . $reward->id . '-amount'];
                    $reward->units = $_POST['individual_reward-' . $reward->id . '-units'];
                }
                
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
            if (!isset($_POST['process_supports'])) {
                return false;
            }

            // tratar colaboraciones existentes
            foreach ($project->supports as $key => $support) {
                
                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["support-{$support->id}-remove"])) {
                    unset($project->supports[$key]);
                    continue;
                }

                if (isset($_POST['support-' . $support->id . '-support'])) {
                    $support->support = $_POST['support-' . $support->id . '-support'];
                    $support->description = $_POST['support-' . $support->id . '-description'];
                    $support->type = $_POST['support-' . $support->id . '-type'];
                }
                
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
            if (!isset($_POST['process_preview'])) {
                return false;
            }

            if (!empty($_POST['comment'])) {
                $project->comment = $_POST['comment'];
            }

            return true;
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}