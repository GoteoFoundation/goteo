<?php

namespace Goteo\Model\Project;

use DateTime;
use Goteo\Model\Project;

class ProjectFilters
{
    /**
     * Returns an array suitable for Project::getList($filters)
     */
    public function getFilters(string $filter, array $vars = []): array
    {
        $filters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED],
            'published_since' => (new DateTime('-6 month'))->format('Y-m-d')
        ];

        $filters['order'] = 'project.status ASC, project.published DESC, project.name ASC';
        if ($vars['q']) {
            $filters['global'] = $vars['q'];
            unset($filters['published_since']);
            $filters['status'] = [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED];
        }

        if ($vars['category']) {
            $filters['category'] = $vars['category'];
            unset($filters['published_since']);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
        }

        if ($vars['location'] || ($vars['latitude'] && $vars['longitude'])) {
            unset($filters['published_since']);
            $filters['location'] = new ProjectLocation([
                'location' => $vars['location'],
                'latitude' => $vars['latitude'],
                'longitude' => $vars['longitude'],
                'radius' => 300
            ]);
            $filters['status'] = [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED];
            $filters['order'] = 'Distance ASC, project.status ASC, project.published DESC, project.name ASC';
        }

        if ($filter === 'near') {
            // Nearby defined as 300Km distance
            // Any LocationInterface will do (UserLocation, ProjectLocation, ...)
            $filters['location'] = new ProjectLocation([
                'latitude' => $vars['latitude'],
                'longitude' => $vars['longitude'],
                'radius' => 300
            ]);
            $filters['order'] = 'Distance ASC, project.status ASC, project.published DESC, project.name ASC';
        } elseif ($filter === 'outdated') {
            $filters['type'] = 'outdated';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'project.days ASC, project.published DESC, project.name ASC';
        } elseif ($filter === 'promoted') {
            $filters['type'] = 'promoted';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'promote.order ASC, project.published DESC, project.name ASC';
        } elseif ($filter === 'popular') {
            $filters['type'] = 'popular';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'project.popularity DESC, project.published DESC, project.name ASC';
        } elseif ($filter === 'succeeded') {
            $filters['type'] = 'succeeded';
            $filters['status'] = [Project::STATUS_FUNDED, Project::STATUS_FULFILLED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            unset($filters['published_since']);
        } elseif ($filter === 'fulfilled') {
            $filters['status'] = [Project::STATUS_FULFILLED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            unset($filters['published_since']);
        } elseif ($filter === 'archived') {
            $filters['status'] = [Project::STATUS_UNFUNDED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            $filters['published_since'] = (new DateTime('-24 month'))->format('Y-m-d');
        } elseif ($filter === 'matchfunding') {
            $filters['type'] = 'matchfunding';
            unset($filters['published_since']);
        } elseif ($filter === 'recent') {
            $filters['type'] = 'recent';
        }

        if ($vars['review']) {
            $filters['status'] = [
                Project::STATUS_EDITING,
                Project::STATUS_REVIEWING,
                Project::STATUS_IN_CAMPAIGN,
                Project::STATUS_FUNDED,
                Project::STATUS_FULFILLED,
                Project::STATUS_UNFUNDED
            ];
            $filters['is_draft'] = true;
        }

        return $filters;
    }
}
