<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
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
                elseif (isset($_GET['enable']))
                    return $this->enable($id); // Re-habilitar la edición
                elseif (isset($_GET['publish']))
                    return $this->publish($id); // Publicarlo
                else
                    return $this->view($id);

            } else if (isset($_GET['create'])) {
                return $this->create();
            } else {
                throw new Redirection('/project/explore');
            }
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        private function edit ($id) {
            Model\User::restrict();  // esto dice @deprecated pero no dice que hay que usar en su vez
            //@TODO Verificar si tiene permisos para editar (usuario)
            $nodesign = false; // para usar el formulario de proyecto en Julian mode

            $project = Model\Project::get($id);

            //@TODO Verificar si tieme permiso para editar libremente
            if ($project->status != 1 && $_SESSION['user']->role_id != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/project/{$project->id}");


            $steps = array(
                'userProfile' => array(
                    'name' => 'Perfil',
                    'guide' => Text::get('guide-project-user-information'),
                    'offtopic' => true,
                    'errors' => $project->errors['userProfile']
                ),
                'userPersonal' => array(
                    'name' => 'Datos personales',
                    'guide' => Text::get('guide-project-contract-information'),
                    'offtopic' => true,
                    'errors' => $project->errors['userPersonal']
                ),
                'overview' => array(
                    'name' => 'Descripción',
                    'guide' => Text::get('guide-project-description'),
                    'errors' => $project->errors['overview']
                ),
                'costs'=> array(
                    'name' => 'Costes',
                    'guide' => Text::get('guide-project-costs'),
                    'errors' => $project->errors['costs']
                ),
                'rewards' => array(
                    'name' => 'Retornos',
                    'guide' => Text::get('guide-project-rewards'),
                    'errors' => $project->errors['rewards']
                ),
                'supports' => array(
                    'name' => 'Colaboraciones',
                    'guide' => Text::get('guide-project-support'),
                    'errors' => $project->errors['supports']
                ),
                'preview' => array(
                    'name' => 'Previsualizar',
                    'guide' => Text::get('guide-project-overview'),
                    'offtopic' => true,
                    'errors' => $project->errors
                )
            );

            // variables para la vista
            $viewData = array(
                            'project'=>$project,
                            'steps'=>$steps,
                            'nodesign'=>$nodesign
                        );

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

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//                echo '<pre>' . \print_r($_POST, 1) . '</pre>';
                $errors = array(); // errores de proceso, no de datos del proyecto
                foreach ($steps as $id => &$data) {
                    // necesitamos saber si vienen datos de este paso para no tratar posts vacios
                    if ($_POST['step'] == $id) {
                        call_user_func_array(array($this, "process_{$id}"), array(&$project, &$errors));
                        $data['errors'] = $project->errors[$id];
                    }
                    // y el paso que vamos a mostrar
                    if (!empty($_POST['view-step-'.$id]))
                        $step = $id;
                }

                if (!empty($errors))
                    throw new \Goteo\Core\Exception(implode('. ', $errors));

                // recalcular el progreso (los errores los ha puesto el process_ )
                $project->evaluate();
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
                "view/project/{$step}.html.php",
                $viewData
            );

            return $view;

        }

        private function create () {

            //@TODO Verificar que el usuario está validado
            Model\User::restrict();  // esto dice @deprecated pero no dice que hay que usar en su vez
            // sino, saltar a la página de login|register

            //@TODO Verificar si tienen permisos para crear nuevos proyectos
            $project = new Model\Project;
            $project->create($_SESSION['user']->id);
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

        // Finalizar para revision, ready le cambia el estado
        public function finish($id) {
            Model\User::restrict();  // esto dice @deprecated pero no dice que hay que usar en su vez
            //@TODO verificar si tienen el mínimo progreso para verificación y si está en estado edición
            $project = Model\Project::get($id);

            if ($project->status != 1)
                throw new Redirection("/project/{$project->id}");

            $errors = array();
            if ($project->ready($errors))
                throw new Redirection("/project/{$project->id}");
            
            throw new \Goteo\Core\Exception(implode(' ', $errors));
        }

        public function enable($id) {
            Model\User::restrict();  // esto dice @deprecated pero no dice que hay que usar en su vez
            //@TODO verificar si tiene permisos para rehabilitar la edición del proyecto (admin)
            if ($_SESSION['user']->role_id != 1) //@FIXME!! Piñonaco... ACL...
                throw new Redirection("/project/{$id}");

            $project = Model\Project::get($id);

            $errors = array();
            if ($project->enable($errors))
                throw new Redirection("/project/{$project->id}/?edit");

            throw new \Goteo\Core\Exception(implode(' ', $errors));
        }

        public function publish($id) {
            Model\User::restrict();  // esto dice @deprecated pero no dice que hay que usar en su vez
            //@TODO verificar si tiene permisos para publicar proyectos
            if ($_SESSION['user']->role_id != 1) //@FIXME!! Piñonaco... ACL...
                throw new Redirection("/project/{$id}");

            $project = Model\Project::get($id);

            $errors = array();
            if ($project->publish($errors))
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
        //-----------------------------------------------
        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$project, &$errors) {
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

            $user->saveInfo($errors);

            //intereses
//            echo '<pre>' . print_r($_POST['interests'], 1) . '</pre>'; //los que vienen
//            echo '<pre>' . print_r($user->interests, 1) . '</pre>'; //los que tiene
            // añadir los que vienen y no tiene
            foreach (array_diff($_POST['interests'],$user->interests) as $key=>$int) {
                $interest = new Model\User\Interest();

                $interest->id = $int;
                $interest->user = $user->id;

                $interest->save($errors);
                $user->interests[] = $interest;
            }

            // quitar los que tiene y no vienen
            foreach (array_diff($user->interests,$_POST['interests']) as $key=>$int) {
                $interest = new Model\User\Interest();

                $interest->id = $int;
                $interest->user = $user->id;

                if ($interest->remove($errors))
                    unset($user->interests[$key]);
            }

            //tratar webs existentes
            foreach ($user->webs as $key=>$web) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-web' . $web->id] == 1) {
                    if ($web->remove($errors))
                        unset($user->webs[$key]);
                    continue; // no tratar esta
                }

                // luego aplicar los cambios
                $web->user = $user->id;
                $web->url = $_POST['web' . $web->id];

                $web->save($errors);
            }

            //tratar nueva web
            if (!empty($_POST['nweb'])) {

                $web = new Model\User\Web();

                $web->id = '';
                $web->user = $user->id;
                $web->url = $_POST['nweb'];

                $web->save($errors);

                $user->webs[] = $web;
            }

            $user->check($project->errors['userProfile']); // checkea errores
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */
        private function process_userPersonal(&$project, &$errors) {
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

            $project->save($errors); // guarda los datos del proyecto
            $project->check('userPersonal'); // checkea errores
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        private function process_overview(&$project, &$errors) {
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
//            echo '<pre>' . print_r($_POST['categories'], 1) . '</pre>'; // A
//            echo '<pre>' . print_r($project->categories, 1) . '</pre>'; // B
            // añadir las que vienen y no tiene
            foreach (array_diff($_POST['categories'], $project->categories) as $key=>$cat) {
                $category = new Model\Project\Category();

                $category->id = $cat;
                $category->project = $project->id;

                $category->save($errors);
                $project->categories[] = $category;
            }

            // quitar las que tiene y no vienen
            foreach (array_diff($project->categories, $_POST['categories']) as $key=>$cat) {
                $category = new Model\Project\Category();

                $category->id = $cat;
                $category->project = $project->id;

                if ($category->remove($errors))
                    unset($project->categories[$key]);
            }

            $project->save($errors); // guarda los datos del proyecto
            $project->check('overview'); // checkea errores
        }

        /*
         * Paso 4 - COSTES
         */
        private function process_costs(&$project, &$errors) {
            $project->resource = $_POST['resource'];

            $project->save($errors); // guarda este dato del proyecto
            
            //tratar costes existentes
            foreach ($project->costs as $key=>$cost) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-cost' . $cost->id] == 1) {
                    if ($cost->remove($errors))
                        unset($project->costs[$key]);
                    continue; // no tratar este
                }

                $cost->cost = $_POST['cost' . $cost->id];
                $cost->description = $_POST['cost-description' . $cost->id];
                $cost->amount = $_POST['cost-amount' . $cost->id];
                $cost->type = $_POST['cost-type' . $cost->id];
                $cost->required = $_POST['cost-required' . $cost->id];
                $cost->from = $_POST['cost-from' . $cost->id];
                $cost->until = $_POST['cost-until' . $cost->id];

                $cost->save($errors);
            }

            //tratar nuevo coste
            if (!empty($_POST['ncost'])) {

                $cost = new Model\Project\Cost();

                $cost->id = '';
                $cost->project = $project->id;
                $cost->cost = $_POST['ncost'];
                $cost->description = $_POST['ncost-description'];
                $cost->amount = $_POST['ncost-amount'];
                $cost->type = $_POST['ncost-type'];
                $cost->required = $_POST['ncost-required'];
                $cost->from = $_POST['ncost-from'];
                $cost->until = $_POST['ncost-until'];

                $cost->save($errors);

                $project->costs[] = $cost;
            }

            $project->check('costs'); // checkea errores
        }

        /*
         * Paso 5 - RETORNO
         */
        private function process_rewards(&$project, &$errors) {
            //tratar retornos sociales
            foreach ($project->social_rewards as $key=>$reward) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-social_reward' . $reward->id] == 1) {
                    if ($reward->remove($errors))
                        unset($project->social_rewards[$key]);
                    continue; // no lo trata
                }

                $reward->reward = $_POST['social_reward' . $reward->id];
                $reward->description = $_POST['social_reward-description' . $reward->id];
                $reward->icon = $_POST['social_reward-icon' . $reward->id];
                $reward->license = $_POST['social_reward-license' . $reward->id];

                $reward->save($errors);
            }

            // retornos individuales
            foreach ($project->individual_rewards as $key=>$reward) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-individual_reward' . $reward->id] == 1) {
                    if ($reward->remove($errors))
                        unset($project->individual_rewards[$key]);
                    continue; // no tratar este
                }

                $reward->reward = $_POST['individual_reward' . $reward->id];
                $reward->description = $_POST['individual_reward-description' . $reward->id];
                $reward->icon = $_POST['individual_reward-icon' . $reward->id];
                $reward->amount = $_POST['individual_reward-amount' . $reward->id];
                $reward->units = $_POST['individual_reward-units' . $reward->id];

                $reward->save($errors);
            }



            // tratar nuevos retornos
            if (!empty($_POST['nsocial_reward'])) {
                $reward = new Model\Project\Reward();

                $reward->id = '';
                $reward->project = $project->id;
                $reward->reward = $_POST['nsocial_reward'];
                $reward->description = $_POST['nsocial_reward-description'];
                $reward->type = 'social';
                $reward->icon = $_POST['nsocial_reward-icon'];
                $reward->license = $_POST['nsocial_reward-license'];

                $reward->save($errors);

                $project->social_rewards[] = $reward;
            }

            if (!empty($_POST['nindividual_reward'])) {
                $reward = new Model\Project\Reward();

                $reward->id = '';
                $reward->project = $project->id;
                $reward->reward = $_POST['nindividual_reward'];
                $reward->description = $_POST['nindividual_reward-description'];
                $reward->type = 'individual';
                $reward->icon = $_POST['nindividual_reward-icon'];
                $reward->amount = $_POST['nindividual_reward-amount'];
                $reward->units = $_POST['nindividual_reward-units'];

                $reward->save($errors);

                $project->individual_rewards[] = $reward;
            }
        
            $project->check('rewards');
        }

        /*
         * Paso 6 - COLABORACIONES
         */
         private function process_supports(&$project, &$errors) {
            // tratar colaboraciones existentes
            foreach ($project->supports as $key=>$support) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-support' . $support->id] == 1) {
                    if ($support->remove($errors))
                        unset($project->supports[$key]);
                    continue; // no tratar este
                }

                $support->support = $_POST['support' . $support->id];
                $support->description = $_POST['support-description' . $support->id];
                $support->type = $_POST['support-type' . $support->id];
                
                $support->save($errors);
            }

            // tratar nueva colaboracion
            if (!empty($_POST['nsupport'])) {
                $support = new Model\Project\Support();

                $support->id = '';
                $support->project = $project->id;
                $support->support = $_POST['nsupport'];
                $support->description = $_POST['nsupport-description'];
                $support->type = $_POST['nsupport-type'];

                $support->save($errors);

                $project->supports[] = $support;
            }

            $project->check('supports');
        }

        /*
         * Paso 7 - PREVIEW
         * No hay nada que tratar porque aq este paso no se le envia nada por post
         */
        public function process_preview(&$project, &$errors) {
            $project->comment = $_POST['comment'];

            $project->save($errors); // guarda este dato del proyecto
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}