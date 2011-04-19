<?php

namespace Goteo\Core {

    use Goteo\Model\User;

    class ACL {
        protected $resources = array();

        /**
         * @deprecated
         * Este deprecated es para que elimines todas las llamadas que encuentres en tu código.
         * La razón es porque esté método lo llamará únicamente el dispatcher, librándonos de tener que llamarlo cada vez en el código.
         *
         * @param type string	$url	Recurso
         * @param type string	$user	Usuario
         */
        public static function check ($url = \GOTEO_REQUEST_URI, User $user = null) {
            
            return true;
            if(is_null($user)) {
                if(!User::isLogged()) {
                    return false;
                }
                else {
                    $user = $_SESSION['user'];
                }
            }
            $query = Model::query("
                SELECT
                    acl.allow
                FROM acl
                WHERE (acl.node_id = :node OR acl.node_id IS NULL)
                AND (acl.role_id = :role OR acl.role_id IS NULL)
                AND (acl.user_id = :user OR acl.user_id IS NULL)
                AND (REPLACE(acl.url, '*', '%') LIKE :url OR acl.url LIKE '*')
                ORDER BY acl.id DESC
                LIMIT 1
                ",
                array(
                    ':node'		=> NULL,
                    ':role'     => $user->role,
                    ':user'		=> $user->id,
                    ':url'      => $url
                )
            );
            return (bool) $query->fetchColumn();
        }

        public function allow() {}

        public function deny() {}

    }
}