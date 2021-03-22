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

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\View;
use Goteo\Model\Banner;
use Goteo\Model\Project;
use Goteo\Model\Stories;
use Goteo\Model\Node;
use Goteo\Util\Stats\Stats;

// para sacar el contenido de about

class IndexController extends DiscoverController
{

    public function __construct()
    {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function indexAction(Request $request)
    {
        $limit = 24;
        $filters = $this->getProjectFilters('promoted');
        $projects = Project::getList($filters, null, 0, $limit);
        $total_projects = Project::getList($filters, null, 0, 0, true);

        $stories = Stories::getList(['active' => true]);

        $channels = Node::getAll(['status' => 'active', 'type' => 'channel']);

        $banners = Banner::getAll(true);

        $stats = Stats::create('home_stats');

        $sponsors = [];
        foreach(Sponsor::getTypes() as $type) {
            $sponsors[$type] = Sponsor::getList(['type' => $type]);
        }

        return $this->viewResponse('home/index', [
            'banners'   => $banners,
            'projects'  => $projects,
            'total_projects'  => $total_projects,
            'limit'     => $limit,
            'limit_add' => 12, // Limit for javascript on addSlick
            'stories'   => $stories,
            'channels'  => $channels,
            'stats'     => $stats,
            'sponsors'  => $sponsors
        ]);
    }

}
