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

$dash = new RouteCollection();

$dash->add('dashboard-activity', new Route(
    '/activity',
    array('_controller' => 'Goteo\Controller\DashboardController::activityAction',
        )
));
// Old route Redirection
$dash->add('dashboard-activity-sumary', new Route(
    '/activity/summary',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/activity");
    })
));

// Virtual wallet
$dash->add('dashboard-wallet', new Route(
    '/wallet',
    array('_controller' => 'Goteo\Controller\DashboardController::walletAction',
        )
));

// AJAX utils
$dash->add('dashboard-ajax-projects-interests', new Route(
    '/ajax/projects/interests',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::projectsInterestsAction',
        )
));
$dash->add('dashboard-ajax-projects-mine', new Route(
    '/ajax/projects/mine',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::projectsMineAction',
        )
));
$dash->add('dashboard-ajax-projects-invested', new Route(
    '/ajax/projects/invested',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::projectsInvestedAction',
        )
));
$dash->add('dashboard-ajax-projects-materials-table', new Route(
    '/ajax/projects/{id}/materials-table',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::projectMaterialsTableAction',
        )
));

// Projects editing
// Summary
$dash->add('dashboard-project-summary', new Route(
    '/project/{pid}/summary',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::summaryAction',
        )
));
// Old Route from menu
$dash->add('dashboard-project-summary-redirect', new Route(
    '/projects/summary',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::summaryAction',
        'pid' => null
        )
));
// Redirect if no summary
$dash->add('dashboard-project-summary-redirect', new Route(
    '/project/{pid}',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::summaryAction'
        )
));
// Route from menu
$dash->add('dashboard-projects', new Route(
    '/projects',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::indexAction')
));


// Project image editing
$dash->add('dashboard-project-images', new Route(
    '/project/{pid}/images',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::imagesAction',
        )
));
// Route from menu (if exists)
$dash->add('dashboard-project-images-redirect', new Route(
    '/projects/images',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::imagesAction',
        'pid' => null
        )
));

// Project updates editing
$dash->add('dashboard-project-updates', new Route(
    '/project/{pid}/updates',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::updatesAction',
        )
));
// Route from menu (if exists)
$dash->add('dashboard-project-updates-redirect', new Route(
    '/projects/updates',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::updatesAction',
        'pid' => null
        )
));
// New update
$dash->add('dashboard-project-updates-new', new Route(
    '/project/{pid}/updates/_',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::updatesEditAction',
        'uid' => null
        )
));
// Edit update
$dash->add('dashboard-project-updates-edit', new Route(
    '/project/{pid}/updates/{uid}',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::updatesEditAction',
        )
));

$dash->add('dashboard-project-analytics', new Route(
    '/project/{pid}/analytics',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::analyticsAction',
        )
));
// Route from menu
$dash->add('dashboard-project-analytics-redirect', new Route(
    '/projects/analytics',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::analyticsAction',
        'pid' => null
        )
));

// Materials editing
$dash->add('dashboard-project-materials', new Route(
    '/project/{pid}/materials',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::materialsAction',
        )
));

// Route from menu
$dash->add('dashboard-project-materials-redirect', new Route(
    '/projects/materials',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::materialsAction',
        'pid' => null
        )
));

// Settings
$dash->add('dashboard-settings', new Route(
    '/settings',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::indexAction',
        )
));

// Settings
$dash->add('dashboard-settings-preferences', new Route(
    '/settings/preferences',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::preferencesAction',
        )
));

// Settings
$dash->add('dashboard-settings-apikey', new Route(
    '/settings/apikey',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::apikeyAction',
        )
));

return $dash;
