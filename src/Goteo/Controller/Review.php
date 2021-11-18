<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application;
use Goteo\Application\Session;
use Goteo\Core\Controller;
use Goteo\Core\Redirection;
use Goteo\Core\View;
use Goteo\Library\Feed;
use Goteo\Library\Text;
use Goteo\Model;
use Goteo\Model\Page;

class Review extends Controller {

    /*
     *  Muy guarro para poder moverse mientras desarrollamos
     */
    public function index () {

        $page = Page::get('review');
        $user = Session::getUser();
        $reviews = Model\Review::assigned($user->id);

        if ($page) {
            $message = str_replace('%USER_NAME%', $user->name, $page->parseContent());
        }

        if (count($reviews) == 0) {
            $message = 'No tienes asignada ninguna revisión de proyectos';
        }

        return new View ('review/index.html.php', [
            'message' => $message,
            'menu'    => self::menu(),
            'section' => 'activity',
            'option'  => 'summary',
            'reviews' => $reviews
        ]);
    }

    /*
     * Sección, Mi actividad
     * Opciones:
     *      'summary' resumen de los proyectos que tengo actualmente asignados para revisar
     */
    public function activity ($option = 'summary', $action = 'list') {

        $user = Session::getUser();
        $reviews = Model\Review::assigned($user->id);

        if (count($reviews) == 0) {
            $message = 'No tienes asignada ninguna revisión de proyectos';
        }

        return new View ('review/index.html.php', [
            'menu'    => self::menu(),
            'message' => $message,
            'section' => __FUNCTION__,
            'option'  => $option,
            'action'  => $action,
            'reviews' => $reviews,
            'errors'  => $errors,
            'success' => $success
        ]);
    }

    /*
     * Seccion, Mis revisiones asignadas
     * Opciones:
     *      'summary' resumen de la revision del proyecto de trabajo: comentarios de administrador para esta revision y enlaces del proyecto
     *      'evaluate' marcar los puntos de criterios y comentarios de evaluacion y mejoras
     *                  para cada seccion de criterios
     *      'comments' resumen de comentarios de todos los revisores
     */
    public function reviews ($option = 'summary', $action = 'list', $id = null) {

        $user = Session::getUser();
        $errors = array();
        $reviews = Model\Review::assigned($user->id);

        // si no hay proyectos asignados no tendria que estar aqui
        if (count($reviews) == 0) {
            throw new Redirection('/review/activity');
        }

        $review = $_SESSION['review'];

        if ($action == 'ready' && !empty($id)) {
            $ready = new Model\User\Review(array(
                                'user' => $user->id,
                                'id'   => $id
                            ));
            if ($ready->ready($errors)) {
                $message = 'Se ha dado por terminada tu revisión';
                $review = Model\Review::getData($review->id);

                // Evento Feed
                $log = new Feed();
                $log->setTarget($review->project, 'project');
                $log->populate('revisión cerrada (revisor)', '/review/reviews',
                    \vsprintf('El revisor %s ha %s la revisión de %s', array(
                        Feed::item('user', Session::getUser()->name, Session::getUserId()),
                        Feed::item('relevant', 'Finalizado'),
                        Feed::item('project', $review->name, $review->project)
                    ))
                );
                $log->doAdmin('admin');
                unset($log);
            }
        }

        if (empty($review)) {
            $review = $reviews[0];
        }

        if ($action == 'select' && !empty($_POST['review'])) {
            // otra revisión de trabajo
            $review = Model\Review::getData($_POST['review']);
        } elseif ($action == 'open' && !empty($id)) {
            // otra revisión de trabajo por url
            $review = Model\Review::getData($id);
        }

        $_SESSION['review'] = $review;

        if ($option == 'evaluate') {
            if ($review->ready == 1) {
                Application\Message::info(Text::get('review-closed-alert'));
            } else {
                Application\Message::info(Text::get('review-ajax-alert'));
            }
        }

        $viewData = array(
            'menu'    => self::menu(),
            'message' => $message,
            'section' => __FUNCTION__,
            'option'  => $option,
            'action'  => $action,
            'reviews' => $reviews,
            'review'  => $review,
            'errors'  => $errors,
            'success' => $success
        );

        if ($option == 'evaluate' || $option == 'report') {
            $viewData['evaluation'] = Model\Review::getEvaluation($review->id, $user->id);
        }

        return new View ('review/index.html.php', $viewData);
    }

    /*
     * Seccion, Mi historial
     * Opciones:
     *      'summary' resumen de los revisados anteriormente
     */
    public function history ($option = 'summary', $id = null) {

        // tratamos el post segun la opcion y la acion
        // sacamos las revisiones realizadas

        $user = Session::getUser();
        $reviews = Model\Review::history($user->id);

        if (count($reviews) == 0) {
            $message = 'No hay revisiones anteriores';
        }

        $viewData = array(
            'menu'    => self::menu(),
            'message' => $message,
            'section' => __FUNCTION__,
            'option'  => $option,
            'action'  => $action,
            'reviews' => $reviews,
            'errors'  => $errors,
            'success' => $success
        );

        if ($option == 'details') {
            if (!empty($id)) {
                $viewData['review']     = $reviews[$id];
                $viewData['evaluation'] = Model\Review::getEvaluation($id, $user->id);
            } else {
                $viewData['options'] = 'summary';
            }
        }

        return new View (
            'review/index.html.php',
            $viewData
        );
    }

    private static function menu(): array
    {
        return [
            'activity' => [
                'label'   => 'Mi actividad',
                'options' => [
                    'summary' => 'Resumen'
                ]
            ],
            'reviews' => [
                'label' => 'Mis revisiones',
                'options' => [
                    'summary'  => 'Resumen',
                    'evaluate' => 'Evaluar',
                    'report'   => 'Informe'
                ]
            ],
            'history' => [
                'label'   => 'Mi historial',
                'options' => [
                    'summary'  => 'Listado'
                ]
            ]
        ];
    }

}
