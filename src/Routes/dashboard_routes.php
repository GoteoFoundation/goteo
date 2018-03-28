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
$dash->add('dashboard-messages', new Route(
    '/messages',
    array('_controller' => 'Goteo\Controller\DashboardController::messagesAction',
        )
));
$dash->add('dashboard-mailing', new Route(
    '/mailing',
    array('_controller' => 'Goteo\Controller\DashboardController::mailingAction',
        )
));
$dash->add('dashboard-rewards', new Route(
    '/rewards',
    array('_controller' => 'Goteo\Controller\DashboardController::myRewardsAction',
        )
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

// join action for a project in when selected for a matcher
$dash->add('dashboard-ajax-matcher-action', new Route(
    '/ajax/matchers/{mid}/{action}/{pid}',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::joinMatcherAction')
));


// Projects editing
// Summary
$dash->add('dashboard-project-summary', new Route(
    '/project/{pid}/summary',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::summaryAction',
        )
));
// Old Route from menu
$dash->add('dashboard-project-empty', new Route(
    '/project',
    array('_controller' => function() {
        return new RedirectResponse('/dashboard/projects');
    })
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

// Project edit (profile)
$dash->add('dashboard-project-profile', new Route(
    '/project/{pid}/profile',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::profileAction',
        )
));

// Project edit (personal)
// Not used for the moment
// $dash->add('dashboard-project-personal', new Route(
//     '/project/{pid}/personal',
//     array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::personalAction',
//         )
// ));

// Project edit (main)
$dash->add('dashboard-project-main', new Route(
    '/project/{pid}/overview',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::overviewAction',
        )
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

// Project costs editing
$dash->add('dashboard-project-costs', new Route(
    '/project/{pid}/costs',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::costsAction',
        )
));

// Project rewards editing
$dash->add('dashboard-project-rewards', new Route(
    '/project/{pid}/rewards',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::rewardsAction',
        )
));

// Project campaign editing
$dash->add('dashboard-project-campaign', new Route(
    '/project/{pid}/campaign',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::campaignAction',
        )
));

// Send project to review
$dash->add('dashboard-project-apply', new Route(
    '/project/{pid}/apply',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::applyAction',
        )
));

// Delete project
$dash->add('dashboard-project-delete', new Route(
    '/project/{pid}/delete',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::deleteAction',
        )
));




// Project translate index
$dash->add('dashboard-project-translate', new Route(
    '/project/{pid}/translate',
    array('_controller' => 'Goteo\Controller\Dashboard\TranslateProjectDashboardController::translateAction')
));
// Project translate overview
$dash->add('dashboard-project-translate-overview', new Route(
    '/project/{pid}/translate/overview/{lang}',
    array('_controller' => 'Goteo\Controller\Dashboard\TranslateProjectDashboardController::overviewTranslateAction',
        'lang' => null
        )
));
// Project translate costs
$dash->add('dashboard-project-translate-costs', new Route(
    '/project/{pid}/translate/costs/{lang}',
    array('_controller' => 'Goteo\Controller\Dashboard\TranslateProjectDashboardController::costsTranslateAction',
        'lang' => null
        )
));
// Project translate rewards
$dash->add('dashboard-project-translate-rewards', new Route(
    '/project/{pid}/translate/rewards/{lang}',
    array('_controller' => 'Goteo\Controller\Dashboard\TranslateProjectDashboardController::rewardsTranslateAction',
        'lang' => null
        )
));
// Project translate supports
$dash->add('dashboard-project-translate-supports', new Route(
    '/project/{pid}/translate/supports/{lang}',
    array('_controller' => 'Goteo\Controller\Dashboard\TranslateProjectDashboardController::supportsTranslateAction',
        'lang' => null
        )
));



// Project updates editing list
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
    '/project/{pid}/updates/new',
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
// Project updates translate
$dash->add('dashboard-project-updates-translate', new Route(
    '/project/{pid}/updates/{uid}/{lang}',
    array('_controller' => 'Goteo\Controller\Dashboard\TranslateProjectDashboardController::updatesTranslateAction',
        'lang' => null
        )
));


// Project supports list
$dash->add('dashboard-project-supports', new Route(
    '/project/{pid}/supports',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::supportsAction',
        )
));
// Project supports item editing
$dash->add('dashboard-project-supports-edit', new Route(
    '/project/{pid}/supports/{sid}',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::supportsEditAction',
        )
));


// Project invests list
$dash->add('dashboard-project-invests', new Route(
    '/project/{pid}/invests',
    array('_controller' => 'Goteo\Controller\Dashboard\ProjectDashboardController::investsAction',
        )
));

// Analytics
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
     array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings/profile");
    })
));

// Settings (profile)
$dash->add('dashboard-settings-profile', new Route(
    '/settings/profile',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::profileAction',
        )
));

// Settings (profile translatgions)
$dash->add('dashboard-settings-profile-trans', new Route(
    '/settings/profile/{lang}',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::profileTranslateAction',
        )
));

// Settings (preferences)
$dash->add('dashboard-settings-preferences', new Route(
    '/settings/preferences',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::preferencesAction',
        )
));

// Settings (personal data)
$dash->add('dashboard-settings-personal', new Route(
    '/settings/personal',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::personalAction',
        )
));

// Settings (access data)
$dash->add('dashboard-settings-access', new Route(
    '/settings/access',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::accessAction',
        )
));

// Settings (API key)
$dash->add('dashboard-settings-apikey', new Route(
    '/settings/apikey',
    array('_controller' => 'Goteo\Controller\Dashboard\SettingsDashboardController::apikeyAction',
        )
));

// Redirection old routes
$dash->add('dashboard-old-sumary', new Route(
    '/activity/summary',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/activity");
    })
));
$dash->add('dashboard-old-apikey', new Route(
    '/activity/apikey',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings/apikey");
    })
));
$dash->add('dashboard-old-profile', new Route(
    '/profile',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings");
    })
));
$dash->add('dashboard-old-profile-2', new Route(
    '/profile/profile',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings");
    })
));
$dash->add('dashboard-old-preferences', new Route(
    '/profile/preferences',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings/preferences");
    })
));
$dash->add('dashboard-old-location', new Route(
    '/profile/location',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings");
    })
));
$dash->add('dashboard-old-personal', new Route(
    '/profile/personal',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings/personal");
    })
));
$dash->add('dashboard-old-access', new Route(
    '/profile/access',
    array('_controller' => function () {
        return new RedirectResponse("/dashboard/settings/access");
    })
));

$dash->add('dashboard-old-projects', new Route(
    '/projects/{zone}/{action}',
    array('_controller' => function ($zone = null, $action = null) {
        $project = $_SESSION['project'];
        if($zone == 'shared-materials') $zone = 'materials';
        if($zone == 'messengers') $zone = 'supports';
        if($zone == 'overview') $zone = 'summary';
        if($project)
            return new RedirectResponse("/dashboard/project/$project/$zone");
        else
            return new RedirectResponse("/dashboard/projects");
    },
    'zone' => null,
    'action' => null,
    )
));

$dash->add('dashboard-old-translate', new Route(
    '/translates/{zone}/{action}',
    array('_controller' => function ($zone = null, $action = null) {
        $project = $_SESSION['translate_project'];
        $call = $_SESSION['translate_call'];
        $node = $_SESSION['translate_node'];
        $type = $_SESSION['translate_type'];
        if(empty($type)) $type = $zone;
        // print_r($project);die("[$type]");
        if($type === 'project' && is_object($project)) {
            return new RedirectResponse("/dashboard/project/" . $project->id . '/translate');
        }
        if($type === 'call') {
            return new RedirectResponse("/dashboard/calls");
        }
        // TODO: nodes
        return new RedirectResponse("/dashboard/settings/profile");
    },
    'zone' => null,
    'action' => null,
    )
));

return $dash;
