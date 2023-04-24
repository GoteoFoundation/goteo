<?php

namespace Goteo\Util\Foil\Extension;

use Foil\Contracts\ExtensionInterface;
use Goteo\Model\Project;
use Goteo\Util\Widget\WidgetHelper;

class WidgetUtils implements ExtensionInterface
{
    private $args;

    public function provideFunctions(): array
    {
        return [
            'widget_project' => [$this, 'widgetProject'],
            'widget_project_tagmark' => [$this, 'projectTagmark']
        ];
    }

    public function projectTagmark(Project $project): string
    {
        return WidgetHelper::getProjectTagmark($project);
    }
    public function widgetProject(Project $project): Project
    {
        return WidgetHelper::getProjectWidget($project);
    }

    public function provideFilters()
    {
        return [];
    }

    public function setup(array $args = [])
    {
        $this->args = $args;
    }
}
