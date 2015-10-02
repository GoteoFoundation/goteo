<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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

            return array('key' => $key, 'user' => $user->id);
        }

    }

}
