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

use Goteo\Application\Config;
use Goteo\Model\Project;
use Stripe\StripeClient;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class SubscriptionsAdminController extends AbstractAdminController
{
    public static string $label = 'admin-subscriptions';

    private StripeClient $stripe;

    public function __construct()
    {
        parent::__construct();

        $this->stripe = new StripeClient(Config::get('payments.stripe.secretKey'));
    }

    public static function getGroup(): string
    {
        return 'activity';
    }

    public static function getRoutes(): array
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
        ];
    }

    public function listAction(Request $request)
    {
        $searchFilters = [sprintf('created<%d', time())];
        $filters = $request->query->all();

        if (!empty($filters)) {
            foreach ($request->query->all() as $key => $value) {
                if (empty($value)) continue;

                switch ($key) {
                    case 'statuses':
                        if ($value === "Todos los estados") break;
                        $searchFilters[] = "status:'$value'";
                        break;
                    case 'projects':
                        $searchFilters[] = "metadata['project']:'$value'";
                        break;
                    case 'name':
                        $searchFilters[] = "metadata['user']:'$value'";
                        break;
                }
            }
        }

        $statuses = array_column(array_map(function ($status) {
            return ['name' => $status, 'id' => $status];
        }, [
            Subscription::STATUS_ACTIVE,
            Subscription::STATUS_CANCELED,
            Subscription::STATUS_INCOMPLETE,
            Subscription::STATUS_INCOMPLETE_EXPIRED,
            Subscription::STATUS_PAST_DUE,
            Subscription::STATUS_PAUSED,
            Subscription::STATUS_TRIALING,
            Subscription::STATUS_UNPAID
        ]), 'name', 'id');

        $total_projects = Project::getList(['status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED]], null, 0, 0, true);
        $projects = Project::getList(['status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED]], null, 0, $total_projects);
        $projects = array_column($projects, 'name', 'id');
        
        $result = $this->stripe->subscriptions->search([
            'query' => implode(" AND ", $searchFilters)
        ]);

        $subscriptions = [];
        foreach ($result->data as $result) {
            $subscriptions[] = $result->toArray();
        }

        return $this->viewResponse('admin/subscriptions/list', [
            'list' => $subscriptions,
            'result' => $result,
            'projects' => $projects,
            'statuses' => $statuses,
            'filters' => $filters
        ]);
    }
}
