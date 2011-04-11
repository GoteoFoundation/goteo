<?php

namespace Goteo\Core {

    class ACL {
        protected $resources = array();

        public function check($resource, $action) {
            if(!\Goteo\Model\User::isLogged()) {
                throw new Redirection('/user/login');
            }
            $role = $_SESSION['user']->role;
            $resource = get_class_name($resource);
            $query = Model::query("
                SELECT
                    acl.allow
                FROM acl
                WHERE (acl.role_id = :role OR acl.role_id IS NULL)
                AND (acl.resource = :resource OR acl.resource LIKE '*')
                AND (acl.action = :action OR acl.action LIKE '*')
                ",
                array(
                    ':role'     => $role,
                    ':resource' => $resource,
                    ':action'   => $action
                )
            );
            return (bool) $query->fetchColumn();
        }

        public function allow() {}

        public function deny() {}

    }
}