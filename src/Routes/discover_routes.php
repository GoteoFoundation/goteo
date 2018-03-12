<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


$discover = new RouteCollection();

//// DISCOVER /////

// Legacy redirections
$discover->add('discover-results', new Route(
    '/results/{category}/{name}',
    array(
        'category' => null,
        'name' => null,
        '_controller' => function($category, Request $request) {
            $qs = [];
            if($request->query->has('query')) $qs['q'] = $request->query->get('query');
            if($category) $qs['category'] = $category;
            $loc = '/discover' . ($qs ? '?'.http_build_query($qs) : '');
            return new RedirectResponse($loc);
  })
));

$discover->add('discover-view', new Route(
    '/view/{type}',
    array(
        'type' => '', // default value
        '_controller' => function ($type) {
            if($type === 'archive') $type = 'archived';
            elseif($type === 'outdate') $type = 'outdated';
            elseif($type === 'success') $type = 'succeeded';
            return new RedirectResponse("/discover/$type");
    })
));


// AJAX search
$discover->add('discover-ajax', new Route(
    '/ajax',
    array('_controller' => 'Goteo\Controller\DiscoverController::ajaxSearchAction',
        )
));

// New all-in-one controller
$discover->add('discover', new Route(
    '/{filter}',
    array(
        'filter' => '',
        '_controller' => 'Goteo\Controller\DiscoverController::searchAction'
    )
));



return $discover;
