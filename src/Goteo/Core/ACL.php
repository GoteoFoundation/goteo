<?php

namespace Goteo\Core {

    use Goteo\Model\User,
        Goteo\Application\Session;

    class ACL {
        protected $resources = array();

        public static function check ($url = \GOTEO_REQUEST_URI, $user = null, $node = \GOTEO_NODE) {

            $debug = false;

            $url = static::fixURL($url);

            if(is_null($user)) {
                if(!Session::isLogged()) {
                    // @FIXME: Ajuste para permitir un perfil público sin usuario registrado.
                    // (Es provisional hasta que se decida lo contrario)
                    $user = new User();
                    $user->id = '*';
                    $user->roles = array((object) array('id' => 'public', 'name' => 'Perfil público'));
                    $id = $user->id;
                } else {
                    $user = Session::getUser();
                    $id = $user->id;
                }
            } elseif($user instanceof User) {
                $id = $user->id;
            } else if($user = User::get($user)) {
                $id = $user->id;
            }
            $roles = $user->roles;
            array_walk($roles, function (&$role) { $role = $role->id; });

            $sql = "
                SELECT
                    acl.allow
                FROM acl
                WHERE (:node LIKE REPLACE(acl.node_id, '*', '%'))
                AND (:roles REGEXP REPLACE(acl.role_id, '*', '.'))
                AND (:user LIKE REPLACE(acl.user_id, '*', '%'))
                AND (:url LIKE REPLACE(acl.url, '*', '%'))
                ORDER BY acl.id DESC
                LIMIT 1
                ";

            $values = array(
                ':node'   => $node,
                ':roles'  => implode(', ', $roles),
                ':user'   => $id,
                ':url'    => $url
            );

            if ($debug) {
                echo \sqldbg($sql, $values);
                die;
            }

            //activamos la cache para este metodo
            $current_cache = \Goteo\Core\DB::cache();
            \Goteo\Core\DB::cache(true);

            $query = User::query($sql, $values);
            //comentado de momento asi se usa el cache corto (si esta definido), comentar con Julian si se puede usar el largo
            // $query->cacheTime(defined('SQL_CACHE_LONG_TIME') ? SQL_CACHE_LONG_TIME : 3600);
            $ret = (bool) $query->fetchColumn();

            //dejamos la cache como estaba
            \Goteo\Core\DB::cache($current_cache);

            return $ret;
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

            $query = User::query($sql, array(
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
                $sql = "SELECT id FROM acl WHERE user_id = :user AND url = :url AND allow = 1";
                $query = User::query($sql, $values);
                if ($query->rowCount() > 0)
                    return (bool) User::query("DELETE FROM acl WHERE user_id = :user AND url = :url", $values);
            }

            return static::addperms($url, $node, $role, $user, false);
        }

    }
}
