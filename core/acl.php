<?php

namespace Goteo\Core {

    use Goteo\Model\User;

    class ACL {
        protected $resources = array();

        public static function check ($url = \GOTEO_REQUEST_URI, $user = null, $node = \GOTEO_NODE) {
            $url = static::fixURL($url);

            if(is_null($user)) {
                if(!User::isLogged()) {
                    // @FIXME: Ajuste para permitir un perfil público sin usuario registrado.
                    // (Es provisional hasta que se decida lo contrario)
                    $user = new User();
                    $user->id = '*';
                    $user->roles = array((object) array('id' => 'public', 'name' => 'Perfil público'));
                    $id = $user->id;
                } else {
                    $user = $_SESSION['user'];
                    $id = $user->id;
                }
            } elseif($user instanceof User) {
                $id = $user->id;
            } else if($user = Model\User::get($user)) {
                $id = $user->id;
            }
            $roles = $user->roles;
            array_walk($roles, function (&$role) { $role = $role->id; });
            $query = Model::query("
                SELECT
                    acl.allow
                FROM acl
                WHERE (:node LIKE REPLACE(acl.node_id, '*', '%'))
                AND (:roles REGEXP REPLACE(acl.role_id, '*', '.'))
                AND (:user LIKE REPLACE(acl.user_id, '*', '%'))
                AND (:url LIKE REPLACE(acl.url, '*', '%'))
                ORDER BY acl.id DESC
                LIMIT 1
                ",
                array(
                    ':node'   => $node,
                    ':roles'  => implode(', ', $roles),
                    ':user'   => $id,
                    ':url'    => $url
                )
            );
            return (bool) $query->fetchColumn();
        }

        static protected function fixURL ($url) {

            return '/' . trim($url, "/\\ \t\n\r\0\x0B"). '/';
        }

        protected function addperms ($url, $node = \GOTEO_NODE, $role = '*', $user = '*', $allow = true) {

//            $url = static::fixURL($url);

            if($user instanceof User) {
                $user = $user->id;
            }

            $sql = "
            INSERT INTO			acl
            					(node_id, role_id, user_id, url, allow)
            VALUES				(:node, :role, :user, :url, :allow)
            ";

            $query = Model::query($sql, array(
                ':node'  => $node,
            	':role'  => $role,
                ':user'	 => $user,
                ':url'	 => $url,
                ':allow' => $allow

            ));

            return (bool) $query->rowCount();

        }

        public static function allow($url, $node = \GOTEO_NODE, $role = '*', $user = '*') {
            return static::addperms($url, $node, $role, $user, true);
        }

        public static function deny($url, $node = \GOTEO_NODE, $role = '*', $user = '*') {

            //si ya tiene un permiso, se elimina el permiso en vez de añadir una denegacion
            if (!empty($user) && $user != '*') {
                $values = array(
                    ':user' => $user,
                    ':url' => $url
                );
                $sql = "SELECT FROM acl WHERE user_id = :user AND url = :url AND allow = 1";
                $query = Model::query($sql, $values);
                if ($query->rowCount() > 0)
                    return (bool) Model::query("DELETE FROM acl WHERE user_id = :user AND url = :url", $values);
            }

            return static::addperms($url, $node, $role, $user, false);
        }

    }
}