<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Model\User,
        Goteo\Model\User\UserLocation;

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
            $Template = \Goteo\Library\Template::get($id);

            header ('HTTP/1.1 200 Ok');
            echo $Template->title . '#$#$#' . $Template->text;
            die;
        }

        /**
         * JSON endpoint to retrieve/establish the user's location
         *
         * @param type $user id usuario
         *
         * //@TODO: grabar la localidad y asignar al usuario
         * //@TODO: verificar que la localidad no existe
         *
         */
        public function geolocate($type) {
            //Return current status
            header ('HTTP/1.1 200 Ok');
            header ('Content-Type: application/json');

            $return = array('success' => false, 'msg' => '');
            $errors = array();
            //
            if($type === 'user' && User::isLogged()) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    //Handles user localization
                    if($_POST['lat'] && $_POST['lng']) {
                        if ($loc = UserLocation::addUserLocation(array(
                            'user' => isset($_SESSION['user']) ? $_SESSION['user']->id : '',
                            'ip'   => \myip(),
                            'city' => $_POST['city'],
                            'region' => $_POST['region'],
                            'country' => $_POST['country'],
                            'country_code' => $_POST['country_code'],
                            'lng'  => $_POST['lng'],
                            'lat'  => $_POST['lat'],
                            'method' => $_POST['method'],
                            'valid' => 1
                        ), $errors)) {
                            $return['msg'] = 'Location successfully added for user';
                            $return['location'] = $loc;
                            $return['success'] = true;
                        } else {
                            $return['msg'] = implode(',', $errors);
                        }
                    }
                    else {
                        //Just changes some properties (locable, info)
                        foreach($_POST as $key => $value) {
                            if($key === 'locable' || $key === 'info') {
                                if(UserLocation::setProperty($_SESSION['user']->id, $key, $value, $errors)) {
                                    $return['msg'] = 'Property succesfully changed for user';
                                    $return['success'] = true;
                                }
                                else {
                                    $return['msg'] = implode(',', $errors);
                                }
                            }
                        }
                    }
                }
                //GET method just returns user info
                elseif ($loc = UserLocation::get($_SESSION['user']->id)) {
                    $return['location'] = $loc;
                    $return['success'] = true;
                }
                else {
                    $return['msg'] = 'User has no location';
                }
            }
            else {
                $return['msg'] = 'Type must be defined (user)';
            }
            echo json_encode($return);
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
