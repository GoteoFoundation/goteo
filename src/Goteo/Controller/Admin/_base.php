<?php

namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Base extends AdminSubController {

    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {
        $ret = array();

        return $ret;
    }

}
