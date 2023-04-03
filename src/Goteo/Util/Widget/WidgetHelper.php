<?php

namespace Goteo\Util\Widget;

use Goteo\Application\Lang;
use Goteo\Model\Image;
use Goteo\Model\Project;
use Goteo\Model\Project\Category;
use Goteo\Model\Project\Reward;
use Goteo\Model\User;

class WidgetHelper
{

    public static function getProjectWidget(Project $project, $lang = null): Project
    {
        if(empty($lang)) $lang = Lang::current();
        $Widget = new Project();
        $Widget->id = (!empty($project->project)) ? $project->project : $project->id;
        $Widget->status = $project->status;
        $Widget->name = $project->name;
        $Widget->subtitle = $project->subtitle;
        $Widget->owner = $project->owner;
        $Widget->description = $project->description;
        $Widget->published = $project->published;
        $Widget->created = $project->created;
        $Widget->updated = $project->updated;
        $Widget->success = $project->success;
        $Widget->closed = $project->closed;
        $Widget->node = $project->node;
        $Widget->project_location = $project->project_location;
        $Widget->social_commitment = $project->social_commitment;

        $Widget->noinvest = $project->noinvest;
        $Widget->days_round1 = (!empty($project->days_round1)) ? $project->days_round1 : 40;
        $Widget->days_round2 = (!empty($project->days_round2)) ? $project->days_round2 : 40;
        $Widget->one_round = $project->one_round;
        $Widget->days_total = ($project->one_round) ? $Widget->days_round1 : ($Widget->days_round1 + $Widget->days_round2);

        $Widget->image = Image::get($project->image);

        $Widget->amount = $project->amount;
        $Widget->invested = $project->amount; // compatibilidad, ->invested no debe usarse
        $Widget->num_investors = $project->num_investors;

        // @TODO : hay que hacer campos calculados conn traducción para esto
        $Widget->cat_names = Category::getNames($Widget->id, 2, $lang);
        $Widget->rewards = Reward::getWidget($Widget->id, $lang);

        if(!empty($project->mincost) && !empty($project->maxcost)) {
            $Widget->mincost = $project->mincost;
            $Widget->maxcost = $project->maxcost;
        } else {
            $calc = Project::calcCosts($project->project);
            $Widget->mincost = $calc->mincost;
            $Widget->maxcost = $calc->maxcost;
        }

        $Widget->user = new User();
        $Widget->user->id = $project->user_id;
        $Widget->user->name = $project->user_name;
        $Widget->user->gender = $project->user_gender;
        $Widget->user->email = $project->user_email;
        $Widget->user->lang = $project->user_lang;

        // calcular dias sin consultar sql
        $Widget->days = $project->days;

        $Widget->setDays(); // esto hace una consulta para el número de días que le faltaan segun configuración

        return $Widget;
    }
    public static function getProjectTagmark(Project $project): string
    {
        $tagmark = '';

        switch ($project->status) {
            case Project::STATUS_FUNDED:
                $tagmark = 'gotit';
                break;
            case Project::STATUS_IN_CAMPAIGN:
                if ($project->amount >= $project->maxcost)
                    $tagmark = 'onrun';
                elseif ($project->round == Project::ROUND_TWO)
                    $tagmark = 'onrun-keepiton';
                elseif ($project->round == Project::ROUND_ONE && $project->amount >= $project->mincost)
                    $tagmark = 'keepiton';
                break;
            case Project::STATUS_FULFILLED:
                $tagmark = 'success';
                break;
            case Project::STATUS_UNFUNDED:
                $tagmark = 'fail';
                break;
        }

        return $tagmark;
    }

}
