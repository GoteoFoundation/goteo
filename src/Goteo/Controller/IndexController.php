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

use Goteo\Application\View;
use Goteo\UseCase\Home\HomeUseCase;
use Symfony\Component\HttpFoundation\Response;


class IndexController extends BaseSymfonyController
{
    public function __construct()
    {
        parent::__construct();
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function indexAction(): Response
    {
        $useCase = new HomeUseCase();
        $response = $useCase->execute();

        return $this->renderFoilTemplate('home/index', [
            'response' => $response,
        ]);
    }
}
