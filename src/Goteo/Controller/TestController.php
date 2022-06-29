<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;

class TestController extends BaseSymfonyController {

    public function testAction(): Response
    {
        dump("TEST ACTION on pure Symfony controller");

        return $this->renderFoilTemplate('about/librejs');
    }
}
