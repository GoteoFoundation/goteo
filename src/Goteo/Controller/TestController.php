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

use Goteo\Core\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends Controller {

    /**
     * @Route("/test", name="about-test")
     */
    public function testAction(): Response
    {
        dump("TEST ACTION");
        return $this->viewResponse('about/librejs');
    }
}
