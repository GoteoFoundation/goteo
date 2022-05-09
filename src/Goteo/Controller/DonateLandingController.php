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
        $source = $request->query->getAlpha('source');
        $detail = $request->query->getAlpha('detail');

        // TODO: Handle with new View Admin
        $view = 'donate';
        if ($source && $detail) {
            $view = 'journal';
        }

        return $this->viewResponse(
            "donate/{$view}/donate",
            ['no_donor_button' => 1]
        );
    }
}
