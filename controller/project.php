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
                elseif (isset($_GET['enable']))
                    return $this->enable($id); // Re-habilitar la edición
                elseif (isset($_GET['publish']))
                    return $this->publish($id); // Publicarlo
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
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
            //@TODO Verificar si tiene permisos para editar (usuario)
            $nodesign = true; // para usar el formulario de proyecto en Julian mode

            $project = Model\Project::get($id);
//            die ('<pre>' . print_r($project, 1) . '</pre>');
            //@TODO Verificar si tieme permiso para editar libremente
            if ($project->status != 1 && $_SESSION['user']->role != 1) // @FIXME!!! este piñonaco porque aun no tenemos el jodido ACL listo :(
                throw new Redirection("/project/{$project->id}");


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
//                die ('<pre>' . \print_r($_POST, 1) . '</pre>');
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                $procesar = $_POST['step'];
                foreach ($steps as $id => &$data) {
                    // tengo que poner este hidden porque sino el objeto se RE-llena con datos vacios que no le llegan por post
                    // solo quiero substituir en los datos lo que llegue por post, lo demás que quede tal cual al hacer save
                    if ($procesar == $id) {
                        call_user_func_array(array($this, "process_{$id}"), array(&$project, &$errors));
                    }
                    // y el paso que vamos a mostrar
                    if (!empty($_POST['view-step-'.$id]))
                        $viewData['step'] = $step = $id;
                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $project->save($errors);

                // si ha ocurrido algun error de proces (como p.ej. "no se ha podido guardar loqueseaa")
                if (!empty($errors))
                    throw new \Goteo\Core\Exception(implode('. ', $errors));

                //re-evaluar el proyecto
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
                "view/project/edit.html.php",
                $viewData
            );

            return $view;

        }

        private function create () {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
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
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
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
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
            //@TODO verificar si tiene permisos para rehabilitar la edición del proyecto (admin)
            if ($_SESSION['user']->role != 1) //@FIXME!! Piñonaco... ACL...
                throw new Redirection("/project/{$id}");

            $project = Model\Project::get($id);

            $errors = array();
            if ($project->enable($errors))
                throw new Redirection("/project/{$project->id}/?edit");

            throw new \Goteo\Core\Exception(implode(' ', $errors));
        }

        public function publish($id) {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }
            //@TODO verificar si tiene permisos para publicar proyectos
            if ($_SESSION['user']->role != 1) //@FIXME!! Piñonaco... ACL...
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
        // del save y remove de las tablas relacionadas se enmcarga el model/project
        // primero añadir y luego quitar para que no se pisen los indices
        //-----------------------------------------------
        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$project, &$errors) {
            return true;
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

            $user->interests = $_POST['interests'];

            //tratar webs existentes
            foreach ($user->webs as $key=>&$web) {
                // luego aplicar los cambios
                $web->url = $_POST['web' . $web->id];
            }

            //tratar nueva web
            if (!empty($_POST['nweb'])) {
                $web = new Model\User\Web();

                $web->id = '';
                $web->user = $user->id;
                $web->url = $_POST['nweb'];

                $user->webs[] = $web;
            }

            //quitar las que quiten
            foreach ($user->webs as $key=>$web) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-web' . $web->id] == 1)
                    unset($user->webs[$key]);
            }

            /// este es el único save que se lanza desde un metodo process_
            $user->save($project->errors['userProfile']); 
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

            $quedan = $project->categories;
        }

        /*
         * Paso 4 - COSTES
         */
        private function process_costs(&$project, &$errors) {
            $project->resource = $_POST['resource'];
            
            //tratar costes existentes
            foreach ($project->costs as $key=>$cost) {
                $cost->cost = $_POST['cost' . $cost->id];
                $cost->description = $_POST['cost-description' . $cost->id];
                $cost->amount = $_POST['cost-amount' . $cost->id];
                $cost->type = $_POST['cost-type' . $cost->id];
                $cost->required = $_POST['cost-required' . $cost->id];
                $cost->from = $_POST['cost-from' . $cost->id];
                $cost->until = $_POST['cost-until' . $cost->id];
            }

            //añadir nuevo coste
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

                $project->costs[] = $cost;
            }

            // quitar los que quiten
            $costes = $project->costs;
            foreach ($project->costs as $key=>$cost) {
                $este = $_POST['remove-cost' . $cost->id];
                if (!empty($este))
                    unset($project->costs[$key]);
            }
            $costes = $project->costs;
        }

        /*
         * Paso 5 - RETORNO
         */
        private function process_rewards(&$project, &$errors) {

            //tratar retornos sociales
            foreach ($project->social_rewards as $key=>$reward) {
                $reward->reward = $_POST['social_reward' . $reward->id];
                $reward->description = $_POST['social_reward-description' . $reward->id];
                $reward->icon = $_POST['social_reward-icon' . $reward->id];
                $reward->license = $_POST['social_reward-license' . $reward->id];
            }

            // retornos individuales
            foreach ($project->individual_rewards as $key=>$reward) {
                $reward->reward = $_POST['individual_reward' . $reward->id];
                $reward->description = $_POST['individual_reward-description' . $reward->id];
                $reward->icon = $_POST['individual_reward-icon' . $reward->id];
                $reward->amount = $_POST['individual_reward-amount' . $reward->id];
                $reward->units = $_POST['individual_reward-units' . $reward->id];
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

                $project->individual_rewards[] = $reward;
            }

            // quitar los retornos colectivos
            foreach ($project->social_rewards as $key=>$reward) {
                $este = $_POST['remove-social_reward' . $reward->id];
                if (!empty($este))
                    unset($project->social_rewards[$key]);
            }

            // quitar las recompensas individuales
            foreach ($project->individual_rewards as $key=>$reward) {
                $este = $_POST['remove-individual_reward' . $reward->id];
                if (!empty($este))
                    unset($project->individual_rewards[$key]);
            }

        }

        /*
         * Paso 6 - COLABORACIONES
         */
         private function process_supports(&$project, &$errors) {

            // tratar colaboraciones existentes
            foreach ($project->supports as $key=>$support) {
                $support->support = $_POST['support' . $support->id];
                $support->description = $_POST['support-description' . $support->id];
                $support->type = $_POST['support-type' . $support->id];
            }

            // añadir nueva colaboracion
            if (!empty($_POST['nsupport'])) {
                $support = new Model\Project\Support();

                $support->id = '';
                $support->project = $project->id;
                $support->support = $_POST['nsupport'];
                $support->description = $_POST['nsupport-description'];
                $support->type = $_POST['nsupport-type'];

                $project->supports[] = $support;
            }

            // quitar las colaboraciones marcadas para quitar
            foreach ($project->supports as $key=>$support) {
                if ($_POST['remove-support' . $support->id] == 1) 
                    unset($project->supports[$key]);
            }

        }

        /*
         * Paso 7 - PREVIEW
         * No hay nada que tratar porque aq este paso no se le envia nada por post
         */
        private function process_preview(&$project) {
            $project->comment = $_POST['comment'];
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}