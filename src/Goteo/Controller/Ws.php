<?php

namespace Goteo\Controller {

    use Goteo\Model;

    class Ws extends \Goteo\Core\Controller {

        public function get_home_post($id) {
            $Post = Model\Post::get($id);

            header ('HTTP/1.1 200 Ok');
            echo <<< EOD
<h3>{$Post->title}</h3>
<div class="embed">{$Post->media->getEmbedCode()}</div>
<div class="description">{$Post->text}</div>
EOD;
            die;
        }

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


        public function get_template_content($id) {
            $Template = \Goteo\Model\Template::get($id);

            header ('HTTP/1.1 200 Ok');
            echo $Template->title . '#$#$#' . $Template->text;
            die;
        }

        /*
         * Marcar recompensa cumplida
         */
        public function fulfill_reward($project, $user) {

            if (Model\Project::isMine($project, $user)) {
                $parts = explode('-', $_POST['token']);
                if ($parts[0] == 'ful_reward') {
                    if (Model\Invest::setFulfilled($parts[1], $parts[2])) {
                        header ('HTTP/1.1 200 Ok');
                        echo 'Recompensa '.$_POST['token'].' marcada como cumplida por '.$user;
                        die;
                    } else {
                        header ('HTTP/1.1 200 Ok');
                        die;
                    }
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
