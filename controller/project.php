<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Library\Text,
        Goteo\Model;

    class Project extends \Goteo\Core\Controller {

        public function index($id = null) {

            if ($id !== null) {

                $project = Model\Project::get($id);

                if ($project !== false) {
                    include 'view/project/public.html.php';
                }

            }

            throw new Error(Error::NOT_FOUND);

        }

        public function manage($id = null) {
            Model\User::restrict();

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $_SESSION['current_project'] = $id;
                header('Location: /project/overview');
                die;
            }
        }

        /*
         * Paso cero de nuevo proyecto
         * @TODO : de nuevo el usuario no deberia llegar por la url sino por la session
         * pero aun no tenemos la validación de usuario...
         */

        public function create() {

            static $views = array(
                'overview',
                'costs',
                'rewards',
            );

            Model\User::restrict();

            if (empty($_SESSION['current_project'])) {

                $project = new Model\Project;

                if (!$project->create($_SESSION['user']->id)) {
                    throw new Error;
                }

                $_SESSION['current_project'] = $project->id;

            }

            if (isset($_POST['view']) && in_array($view, $views)) {
                $view = $_POST['view'];
            } else {
                $view = $views[0]; // @todo Default view
            }

            // Validate here
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            }

            include "view/project/{$view}.html.php";

        }

        /*
         * Paso 1 - PERFIL
         */

        public function user() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            $project = Model\Project::get($id);

            $user = Model\User::get($project->owner);

            $errors = array();

            if (isset($_POST['submit'])) {
                // el save solo se encarga de datos sensibles, no de esta información adicional...
                // tratar la imagen y ponerla en la propiedad avatar
                // __FILES__

                $user->name = $_POST['name'];
                $user->avatar = $_POST['avatar'];
                $user->about = $_POST['about'];
                $user->keywords = $_POST['keywords'];
                $user->contribution = $_POST['contribution'];
                $user->blog = $_POST['blog'];
                $user->twitter = $_POST['twitter'];
                $user->facebook = $_POST['facebook'];
                $user->linkedin = $_POST['linkedin'];

                $user->saveInfo($errors);

                //intereses
                // añadir los que vienen
                foreach ($_POST['interests'] as $int) {
                    if (!in_array($int, $user->interests)) {
                        $interest = new Model\User\Interest();

                        $interest->id = $int;
                        $interest->user = $user->id;

                        $interest->save($errors);
                    }
                }

                // quitar los que no vienen
                foreach ($user->interests as $int) {
                    if (!in_array($int, $_POST['interests'])) {
                        $interest = new Model\User\Interest();

                        $interest->id = $int;
                        $interest->user = $user->id;

                        $interest->remove($errors);
                    }
                }

                // si no saltamos tenemos que recargar
//        		  $user = Model\User::get($project->owner);

                header('Location: /project/register/');
                die;
            }

            $interests = Model\User\Interest::getAll();

            $guideText = Text::get('guide project user information');
            include 'view/project/user.html.php';
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */

        public function register() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $project = Model\Project::get($id);

                $errors = array();

                if (isset($_POST['submit'])) {

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

                    $project->save($errors);

                    if (empty($errors)) {
                        header('Location: /project/overview/');
                        die;
                    }
                }

                $guideText = Text::get('guide project contract information');
                include 'view/project/register.html.php';
            }
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        public function overview() {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $project = Model\Project::get($id);

                if (isset($_POST['submit'])) {

                    $errors = array();

                    // campos que guarda este paso
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
                        $project->$field = $_POST[$field];
                    }

                    //tratar imagen y ponerla en la propiedad image
                    $project->image = $_POST['image'];


                    $project->save($errors);

                    //categorias
                    // añadir las que vienen
                    foreach ($_POST['categories'] as $cat) {
                        if (!in_array($cat, $project->categories)) {
                            $category = new Model\Project\Category();

                            $category->id = $cat;
                            $category->project = $project->id;

                            $category->save($errors);
                        }
                    }

                    // quitar las que no vienen
                    foreach ($project->categories as $cat) {
                        if (!in_array($cat, $_POST['categories'])) {
                            $category = new Model\Project\Category();

                            $category->id = $cat;
                            $category->project = $project->id;

                            $category->remove($errors);
                        }
                    }


                    header('Location: /project/costs/');
                    die;
                }

                $currently = Model\Project::currentStatus();
                $categories = Model\Project\Category::getAll();

                $guideText = Text::get('guide project description');
                include 'view/project/overview.html.php';
            }
        }

        /*
         * Paso 4 - COSTES
         */

        public function costs() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $project = Model\Project::get($id);

                if (isset($_POST['submit'])) {

                    $errors = array();

                    $project->resource = $_POST['resource'];
                    $project->save($errors);

                    //tratar costes existentes
                    foreach ($project->costs as $cost) {
                        // primero mirar si lo estan quitando
                        if ($_POST['remove-cost' . $cost->id] == 1) {
                            $cost->remove($errors);
                            continue;
                        }

                        if (!empty($_POST['cost' . $cost->id])) {

                            $cost->cost = $_POST['cost' . $cost->id];
                            $cost->description = $_POST['cost-description' . $cost->id];
                            $cost->amount = $_POST['cost-amount' . $cost->id];
                            $cost->type = $_POST['cost-type' . $cost->id];
                            $cost->required = $_POST['cost-required' . $cost->id];
                            $cost->from = $_POST['cost-from' . $cost->id];
                            $cost->until = $_POST['cost-until' . $cost->id];

                            $cost->save($errors);
                        }
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

                    // si no saltamos tenemos que recargar
                    $project = Model\Project::get($id);
//                    header('Location: /project/rewards/');
//                    die;
                }
            }

            $types = Model\Project\Cost::types();

            $guideText = Text::get('guide project costs');
            include 'view/project/costs.html.php';
        }

        /*
         * Paso 5 - RETORNO
         */

        public function rewards() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $project = Model\Project::get($id);
//                echo '<pre>' . print_r($project->social_rewards, 1) . '</pre>';
//                echo '<pre>' . print_r($project->individual_rewards, 1) . '</pre>';

                if (isset($_POST['submit'])) {

                    $errors = array();

                    //tratar retornos sociales
                    foreach ($project->social_rewards as $reward) {
                        // primero mirar si lo estan quitando
                        if ($_POST['remove-social_reward' . $reward->id] == 1) {
                            $reward->remove($errors);
                            continue;
                        }

                        $reward->reward = $_POST['social_reward' . $reward->id];
                        $reward->description = $_POST['social_reward-description' . $reward->id];
                        $reward->icon = $_POST['social_reward-icon' . $reward->id];
                        $reward->license = $_POST['social_reward-license' . $reward->id];

                        $reward->save($errors);
                    }

                    // retornos individuales
                    foreach ($project->individual_rewards as $reward) {
                        // primero mirar si lo estan quitando
                        if ($_POST['remove-individual_reward' . $reward->id] == 1) {
                            $reward->remove($errors);
                            continue;
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


                    // si no saltamos tenemos que recargar
                    $project = Model\Project::get($id);
//                    header('Location: /project/supports/');
//                    die;
                }

                $stypes = Model\Project\Reward::icons('social');
                $itypes = Model\Project\Reward::icons('individual');
                $licenses = Model\Project\Reward::licenses();

                $guideText = Text::get('guide project rewards');
                include 'view/project/rewards.html.php';
            }
        }

        /*
         * Paso 6 - COLABORACIONES
         */

        public function supports() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $project = Model\Project::get($id);
//                echo '<pre>' . print_r($project, 1) . '</pre>';

                if (isset($_POST['submit'])) {

//                    echo '<pre>' . print_r($_POST, 1) . '</pre>';
                    $errors = array();

                    // tratar colaboraciones existentes
                    foreach ($project->supports as $support) {
                        // primero mirar si lo estan quitando
                        if ($_POST['remove-support' . $support->id] == 1) {
                            $support->remove($errors);
                            continue;
                        }

                        if (!empty($_POST['support' . $support->id])) {
                            $support->support = $_POST['support' . $support->id];
                            $support->description = $_POST['support-description' . $support->id];
                            $support->type = $_POST['support-type' . $support->id];
                        }
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


                    // si no saltamos tenemos que recargar
                    $project = Model\Project::get($id);
//                    header('Location: /project/preview/');
//                    die;
                }

                $types = Model\Project\Support::types();

                $guideText = Text::get('guide project support');
                include 'view/project/supports.html.php';
            }
        }

        /*
         * Paso 7 - PREVIEW
         */

        public function preview() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
                $project = Model\Project::get($id);

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
        }

        /*
         * Paso 8 - Listo para revision
         *
         * Pasa el proyecto a estado "Pendiente de revisión"
         * Cambia el id temporal apor el idealiza del nombre del proyecto
         * 		(ojo que no se repita)
         * 		(ojo en las tablas relacionadas)
         */

        public function close() {
            Model\User::restrict();

            $id = $_SESSION['current_project'];

            if (!$id) {
                header('Location: /');
                die;
            } else {
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

}
