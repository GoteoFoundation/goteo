<?php

namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Base extends AdminSubController {

    public function listAction($id = null, $subaction = null) {
        return array('template' => '');
    }

}
