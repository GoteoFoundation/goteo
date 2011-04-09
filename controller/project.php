<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Model;

    class Project extends \Goteo\Core\Controller {


        private function edit ($id) {

            $project = Model\Project::get($id);

            $steps = array(
                'userProfile' => array(
                    'name' => 'Perfil',
                    'guide' => $guideText = Text::get('guide project user information'),
                    'errors' => $project->errors['userProfile']
                ),
                'userPersonal' => array(
                    'name' => 'Datos personales',
                    'guide' => Text::get('guide project contract information'),
                    'errors' => $project->errors['userPersonal']
                ),
                'overview' => array(
                    'name' => 'Descripción',
                    'guide' => Text::get('guide project description'),
                    'errors' => $project->errors['overview']
                ),
                'costs'=> array(
                    'name' => 'Costes',
                    'guide' => Text::get('guide project costs'),
                    'errors' => $project->errors['costs']
                ),
                'rewards' => array(
                    'name' => 'Retornos',
                    'guide' => Text::get('guide project rewards'),
                    'errors' => $project->errors['rewards']
                ),
                'supports' => array(
                    'name' => 'Colaboraciones',
                    'guide' => Text::get('guide project support'),
                    'errors' => $project->errors['supports']
                ),
                'preview' => array(
                    'name' => 'Previsualizar',
                    'guide' => '',
                    'errors' => $project->errors
                )
            );

            // vista por defecto
            if (!empty($project->errors['userProfile']))
                $view = 'userProfile';
            elseif (!empty($project->errors['userPersonal']))
                $view = 'userPersonal';
            elseif (!empty($project->errors['overview']))
                $view = 'overview';
            elseif (!empty($project->errors['costs']))
                $view = 'costs';
            elseif (!empty($project->errors['rewards']))
                $view = 'rewards';
            elseif (!empty($project->errors['supports']))
                $view = 'supports';
            else
                $view = 'preview';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                foreach ($steps as $id => &$data) {
                    call_user_func_array(array($this, "process_{$id}"), array($project));
                    $data['errors'] = $project->errors[$id];
                    if (!empty($_POST['view-step-'.$id]))
                        $view = $id;
                }

                $project->save();
                $project->evaluate();
            }

            // datos que necesita para pintar, creo que esto lo tendria que cargar cada vista
            //userProfile
            $interests = Model\User\Interest::getAll();

            //overview
            $currently = Model\Project::currentStatus();
            $categories = Model\Project\Category::getAll();

            //costs
            $types = Model\Project\Cost::types();

            //rewards
            $stypes = Model\Project\Reward::icons('social');
            $itypes = Model\Project\Reward::icons('individual');
            $licenses = Model\Project\Reward::licenses();

            //supports
            $types = Model\Project\Support::types();


            include "view/project/{$view}.html.php";

        }

        private function create () {
            
            $project = new Model\Project;
            $project->create($_SESSION['user']->id);

            if ($project->save()) {
                throw new Redirection("/project/{$project->id}/?edit");
            }

            throw new Error;
        }

        private function view ($id) {
            $project = Model\Project::get($id);
            include 'view/project/public.html.php';
        }

        public function index($id = null) {
            
            if ($id !== null) {

                if (isset($_GET['edit'])) {
                    return $this->edit($id);
                } elseif (isset($_GET['edit'])) {
                    return $this->finish($id);
                } else {
                    return $this->view($id);
                }
                
            } else if (isset($_GET['create'])) {
                return $this->create();                
            } else {
                throw new Error(Error::NOT_FOUND);
            }          
            
        }

        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$project) {

            $user = Model\User::get($project->owner);

            // el save solo se encarga de datos sensibles, no de esta información adicional...
            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'name',
                'avatar',
                'about',
                'keywords',
                'contribution',
                'blog',
                'twitter',
                'facebook',
                'linkedin'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field]))
                    $user->$field;
            }

            $user->saveInfo();


            //intereses, si viene en el post
            if (isset($_POST['interests'])) {
                // añadir los que vienen
                foreach ($_POST['interests'] as $int) {
                    if (!in_array($int, $user->interests)) {
                        $interest = new Model\User\Interest();

                        $interest->id = $int;
                        $interest->user = $user->id;

                        $interest->save();
                    }
                }

                // quitar los que no vienen
                foreach ($user->interests as $int) {
                    if (!in_array($int, $_POST['interests'])) {
                        $interest = new Model\User\Interest();

                        $interest->id = $int;
                        $interest->user = $user->id;

                        $interest->remove();
                    }
                }
            }

            $user->check($project->errors['userProfile']);
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */

        private function process_userPersonal(&$project) {
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
                if (isset($_POST[$field]))
                    $project->$field = $_POST[$field];
            }

            $project->check('userPersonal');
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        private function process_overview(&$project) {
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
                if (isset($_POST[$field]))
                    $project->$field = $_POST[$field];
            }

            //categorias, si viene el campo
            if (isset($_POST['categories'])) {
                // añadir las que vienen
                foreach ($_POST['categories'] as $cat) {
                    if (!in_array($cat, $project->categories)) {
                        $category = new Model\Project\Category();

                        $category->id = $cat;
                        $category->project = $project->id;

                        $category->save();
                    }
                }

                // quitar las que no vienen
                foreach ($project->categories as $cat) {
                    if (!in_array($cat, $_POST['categories'])) {
                        $category = new Model\Project\Category();

                        $category->id = $cat;
                        $category->project = $project->id;

                        $category->remove();
                    }
                }
            }

            $project->check('overview');
        }

        /*
         * Paso 4 - COSTES
         */
        private function process_costs(&$project) {
            if (isset($_POST['resource']))
                $project->resource = $_POST['resource'];

            //tratar costes existentes
            foreach ($project->costs as $cost) {
                // primero mirar si lo estan quitando
                if (isset($_POST['remove-cost' . $cost->id]) && $_POST['remove-cost' . $cost->id] == 1) {
                    $cost->remove();

                    //@TODO como lo quito??
                    
                    continue;
                }

                if (isset($_POST['cost' . $cost->id])) {
                    $cost->cost = $_POST['cost' . $cost->id];
                    $cost->description = $_POST['cost-description' . $cost->id];
                    $cost->amount = $_POST['cost-amount' . $cost->id];
                    $cost->type = $_POST['cost-type' . $cost->id];
                    $cost->required = $_POST['cost-required' . $cost->id];
                    $cost->from = $_POST['cost-from' . $cost->id];
                    $cost->until = $_POST['cost-until' . $cost->id];

                    $cost->save();
                }
            }

            //tratar nuevo coste
            if (isset($_POST['ncost']) && !empty($_POST['ncost'])) {

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

                $cost->save();

                $project->costs[] = $cost;
            }

            $project->check('costs');
        }

        /*
         * Paso 5 - RETORNO
         */

        private function process_rewards(&$project) {
            //tratar retornos sociales
            foreach ($project->social_rewards as $reward) {
                // primero mirar si lo estan quitando
                if (isset($_POST['remove-social_reward' . $reward->id]) && $_POST['remove-social_reward' . $reward->id] == 1) {
                    $reward->remove();
                    continue;
                }

                if (isset($_POST['social_reward' . $reward->id])) {
                    $reward->reward = $_POST['social_reward' . $reward->id];
                    $reward->description = $_POST['social_reward-description' . $reward->id];
                    $reward->icon = $_POST['social_reward-icon' . $reward->id];
                    $reward->license = $_POST['social_reward-license' . $reward->id];

                    $reward->save();
                }
            }

            // retornos individuales
            foreach ($project->individual_rewards as $reward) {
                // primero mirar si lo estan quitando
                if (isset($_POST['remove-individual_reward' . $reward->id]) && $_POST['remove-individual_reward' . $reward->id] == 1) {
                    $reward->remove();
                    continue;
                }

                if (isset($_POST['individual_reward' . $reward->id])) {
                    $reward->reward = $_POST['individual_reward' . $reward->id];
                    $reward->description = $_POST['individual_reward-description' . $reward->id];
                    $reward->icon = $_POST['individual_reward-icon' . $reward->id];
                    $reward->amount = $_POST['individual_reward-amount' . $reward->id];
                    $reward->units = $_POST['individual_reward-units' . $reward->id];

                    $reward->save();
                }
            }



            // tratar nuevos retornos
            if (isset($_POST['nsocial_reward']) && !empty($_POST['nsocial_reward'])) {
                $reward = new Model\Project\Reward();

                $reward->id = '';
                $reward->project = $project->id;
                $reward->reward = $_POST['nsocial_reward'];
                $reward->description = $_POST['nsocial_reward-description'];
                $reward->type = 'social';
                $reward->icon = $_POST['nsocial_reward-icon'];
                $reward->license = $_POST['nsocial_reward-license'];

                $reward->save();

                $project->social_rewards[] = $reward;
            }

            if (isset($_POST['nindividual_reward']) && !empty($_POST['nindividual_reward'])) {
                $reward = new Model\Project\Reward();

                $reward->id = '';
                $reward->project = $project->id;
                $reward->reward = $_POST['nindividual_reward'];
                $reward->description = $_POST['nindividual_reward-description'];
                $reward->type = 'individual';
                $reward->icon = $_POST['nindividual_reward-icon'];
                $reward->amount = $_POST['nindividual_reward-amount'];
                $reward->units = $_POST['nindividual_reward-units'];

                $reward->save();

                $project->individual_rewards[] = $reward;
            }
        
            $project->check('rewards');
        }

        /*
         * Paso 6 - COLABORACIONES
         */
         private function process_supports(&$project) {
            // tratar colaboraciones existentes
            foreach ($project->supports as $support) {
                // primero mirar si lo estan quitando
                if ($_POST['remove-support' . $support->id] == 1) {
                    $support->remove();
                    continue;
                }

                if (!empty($_POST['support' . $support->id])) {
                    $support->support = $_POST['support' . $support->id];
                    $support->description = $_POST['support-description' . $support->id];
                    $support->type = $_POST['support-type' . $support->id];
                }
                $support->save();
            }

            // tratar nueva colaboracion
            if (!empty($_POST['nsupport'])) {
                $support = new Model\Project\Support();

                $support->id = '';
                $support->project = $project->id;
                $support->support = $_POST['nsupport'];
                $support->description = $_POST['nsupport-description'];
                $support->type = $_POST['nsupport-type'];

                $support->save();

                $project->supports[] = $support;
            }

            $project->check('supports');
        }

        /*
         * Paso 7 - PREVIEW
         */

        public function process_preview() {
            $finish = false;
            $errors = $project->errors;
            if (empty($errors)) {
                $success[] = Text::get('guide project success noerrors');
            }
            if ($project->progress > 80 && $project->status == 1) {
                $success[] = Text::get('guide project success minprogress');
                $success[] = Text::get('guide project success okfinish');
                $finish = true;
            }

            $guideText = Text::get('guide project overview');
            include 'view/project/preview.html.php';
        }

        /*
         * Paso 8 - Listo para revision
         *
         * Pasa el proyecto a estado "Pendiente de revisión"
         * Cambia el id temporal apor el idealiza del nombre del proyecto
         * 		(ojo que no se repita)
         * 		(ojo en las tablas relacionadas)
         */

        public function finish($id) {

            $project = Model\Project::get($id);

            if ($project->ready()) {
                unset($_SESSION['current_project']);
                header('Location: /dashboard');
                die;
            } else {
                header('Location: /project/preview');
                die;
            }
        }

    }

}