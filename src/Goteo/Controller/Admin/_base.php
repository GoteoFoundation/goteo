<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Base extends AdminSubController {

    public function listAction($id = null, $subaction = null) {
        return array('template' => '');
    }

}
