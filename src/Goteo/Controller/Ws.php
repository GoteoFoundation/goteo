<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller {

    use Goteo\Model;

    class Ws extends \Goteo\Core\Controller {

        public function get_faq_order($section) {
            $next = Model\Faq::next($section);

            header ('HTTP/1.1 200 Ok');
            echo $next;
            die;
        }

        public function get_criteria_order($section) {
            $next = Model\Criteria::next($section);

            header ('HTTP/1.1 200 Ok');
            echo $next;
            die;
        }

        public function set_review_criteria($user, $review) {
            // comprobar que tiene asignada esta revision
            if (Model\User\Review::is_legal($user, $review)) {

                $score = new Model\User\Review (array (
                                'user'   => $user,
                                'id' => $review
                ));

                $parts = explode('-', $_POST['campo']);
                if ($parts[0] == 'criteria') {
                    $criteria = $parts[1];
                } else {
                    header ('HTTP/1.1 400 Bad request');
                    die;
                }
                $value = $_POST['valor'];

                // puntuamos
                if ($score->setScore($criteria, $value)) {
                    $result = 'Ok';
                } else {
                    $result = 'fail';
                }

                // recalculamos
                $new_score = $score->recount();

                header ('HTTP/1.1 200 Ok');
                echo $new_score->score.'/'.$new_score->max;
                /*
                echo "Usuario: $user<br />";
                echo "Revision: $review<br />";
                echo "Criterio: {$criteria}<br />";
                echo "Valor: {$value}<br />";
                echo "Resulta: $result<br />";
                echo "<pre>".print_r($new_score, true)."</pre>";
                 *
                 */
                die;
            } else {
                header ('HTTP/1.1 403 Forbidden');
                die;
            }
        }

        public function set_review_comment($user, $review) {
            // comprobar que tiene asignada esta revision
            if (Model\User\Review::is_legal($user, $review)) {

                $comment = new Model\User\Review (array (
                                'user'   => $user,
                                'id' => $review
                ));

                $parts = explode('-', $_POST['campo']);
                if (in_array($parts[0], array('project', 'owner', 'reward')) &&
                    in_array($parts[1], array('evaluation', 'recommendation'))) {
                    $section = $parts[0];
                    $field   = $parts[1];

                    $text = $_POST['valor'];

                    if ($comment->setComment($section, $field, $text)) {
                        $result = 'Grabado';
                    } else {
                        $result = 'Error';
                    }

                    header ('HTTP/1.1 200 Ok');
                    echo $result;
                    die;

                } else {
                    header ('HTTP/1.1 400 Bad request');
                    die;
                }

            } else {
                header ('HTTP/1.1 403 Forbidden');
                die;
            }
        }



    }

}
