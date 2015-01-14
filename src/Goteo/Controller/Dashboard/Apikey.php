<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model;

    class Apikey {

        public static function get ($user, $action = 'view', &$errors = array()) {

            $errors = array();

            if ($action == 'generate') {
                $key = md5($user.date('dMYHis'));
                $apikey = new Model\User\Apikey(array(
                    'user_id' => $user->id,
                    'key' => $key
                ));
                $apikey->save($errors);

            } else {
                $key = Model\User\Apikey::get($user->id);
            }

            return $key;
        }

    }

}
