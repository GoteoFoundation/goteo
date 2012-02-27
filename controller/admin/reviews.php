<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Model;

    class Reviews {

        public static function process ($action = 'list', $id = null) {

            $filters = array();
            $fields = array('status', 'checker');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $filter = "?status={$filters['status']}&checker={$filters['checker']}";

            $success = array();
            $errors  = array();

            switch ($action) {
                case 'add':
                case 'edit':

                    // el get se hace con el id del proyecto
                    $review = Model\Review::get($id);

                    $project = Model\Project::getMini($review->project);

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                        // instancia
                        $review->id         = $_POST['id'];
                        $review->project    = $_POST['project'];
                        $review->to_checker = $_POST['to_checker'];
                        $review->to_owner   = $_POST['to_owner'];

                        if ($review->save($errors)) {
                            switch ($action) {
                                case 'add':
                                    $success[] = 'Revisión iniciada correctamente';

                                    // Evento Feed
                                    $log = new Feed();
                                    $log->populate('valoración iniciada (admin)', '/admin/reviews',
                                        \vsprintf('El admin %s ha %s la valoración de %s', array(
                                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                            Feed::item('relevant', 'Iniciado'),
                                            Feed::item('project', $project->name, $project->id)
                                    )));
                                    $log->doAdmin('admin');
                                    unset($log);

                                    break;
                                case 'edit':
                                    $success[] = 'Datos editados correctamente';
                                    break;
                            }

                            throw new Redirection('/admin/reviews/' . $filter);
                        }
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'reviews',
                            'file'   => 'edit',
                            'action' => $action,
                            'review' => $review,
                            'project'=> $project,
                            'success'=> $success,
                            'errors' => $errors
                        )
                    );

                    break;
                case 'close':
                    // el get se hace con el id del proyecto
                    $review = Model\Review::getData($id);

                    // marcamos la revision como completamente cerrada
                    if (Model\Review::close($id, $errors)) {
                        $message = 'La revisión se ha cerrado';

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('valoración finalizada (admin)', '/admin/reviews',
                            \vsprintf('El admin %s ha dado por %s la valoración de %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Finalizada'),
                                Feed::item('project', $review->name, $review->project)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    }
                    break;
                case 'unready':
                    // se la reabrimos para que pueda seguir editando
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $user_rev = new Model\User\Review(array(
                            'id' => $id,
                            'user' => $user
                        ));
                        $user_rev->unready($errors);
                    }
                    break;
                case 'assign':
                    // asignamos la revision a este usuario
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $assignation = new Model\User\Review(array(
                            'id' => $id,
                            'user' => $user
                        ));
                        if ($assignation->save($errors)) {

                            $userData = Model\User::getMini($user);
                            $reviewData = Model\Review::getData($id);

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('asignar revision (admin)', '/admin/reviews',
                                \vsprintf('El admin %s ha %s a %s la revisión de %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', 'Asignado'),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('project', $reviewData->name, $reviewData->project)
                            )));
                            $log->setTarget($userData->id, 'user');
                            $log->doAdmin('admin');
                            unset($log);

                        }
                    }
                    break;
                case 'unassign':
                    // se la quitamos a este revisor
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $assignation = new Model\User\Review(array(
                            'id' => $id,
                            'user' => $user
                        ));
                        if ($assignation->remove($errors)) {

                            $userData = Model\User::getMini($user);
                            $reviewData = Model\Review::getData($id);

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('Desasignar revision (admin)', '/admin/reviews',
                                \vsprintf('El admin %s ha %s a %s la revisión de %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', 'Desasignado'),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('project', $reviewData->name, $reviewData->project)
                            )));
                            $log->setTarget($userData->id, 'user');
                            $log->doAdmin('admin');
                            unset($log);

                        }
                    }
                    break;
                case 'report':
                    // mostramos los detalles de revision
                    // ojo que este id es la id del proyecto, no de la revision
                    $review = Model\Review::get($id);
                    $review = Model\Review::getData($review->id);

                    $evaluation = array();

                    foreach ($review->checkers as $user=>$user_data) {
                        $evaluation[$user] = Model\Review::getEvaluation($review->id, $user);
                    }


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'reviews',
                            'file' => 'report',
                            'review'     => $review,
                            'evaluation' => $evaluation
                        )
                    );
                    break;
            }

            $projects = Model\Review::getList($filters);
            $status = array(
                'open' => 'Abiertas',
                'closed' => 'Cerradas'
            );
            $checkers = Model\User::getAll(array('role'=>'checker'));

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'reviews',
                    'file' => 'list',
                    'message' => $message,
                    'projects' => $projects,
                    'filters' => $filters,
                    'status' => $status,
                    'checkers' => $checkers,
                    'errors' => $errors
                )
            );
            
        }

    }

}
