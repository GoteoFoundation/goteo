<?php

namespace Goteo\Controller;

use Goteo\Application\View;
use Goteo\Core\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DonateLandingController extends Controller
{
    public function __construct() {
        View::setTheme('responsive');
    }

    public function indexAction(Request $request): Response
    {
        $src = $request->query->getAlpha('src');
        $detail = $request->query->getAlpha('detail');
        $view = $request->query->getAlpha('view', 'donate');

        return $this->viewResponse(
            "donate/{$view}/donate",
            ['no_donor_button' => 1]
        );
    }
}
