<?php

namespace Goteo\Core {

    class ACL {
        protected $resources = array();

        public function check($resource, $action) {
            $role = $_SESSION['user']->role;
            $resource = get_class_name($resource);
            $query = Model::query("
                SELECT
                    *
                FROM acl
                WHERE acl.role_id = :role
                AND acl.resource = :resource
                AND acl.action = :action
                ",
                array(
                    ':role'     => $role,
                    ':resource' => $resource,
                    ':action'   => $action
                )
            );
            if((bool) $query->rowCount() === false) {
                throw new Error(405);
            }
            return true;
        }

        public function allow() {}

        public function deny() {}

    }
}