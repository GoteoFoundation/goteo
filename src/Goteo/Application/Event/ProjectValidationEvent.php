<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\Event;

use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Model\Project;
use Goteo\Model\Image;
use Symfony\Component\HttpFoundation\Response;

class ProjectValidationEvent extends \Goteo\Console\Event\FilterProjectEvent
{
    protected $scores; // Generic Object with all and
    protected $errors; // Array of errors and key of that error
    protected $fields; // Array of form fields with errors
    protected $keys = [ 'profile', /* 'personal', */ 'overview', 'images', 'costs', 'rewards', 'campaign' ];

    public function __construct(Project $project) {
        $this->project = $project;
        $this->reset();
    }

    public function reset() {
        $this->scores = new \stdClass;
        $this->errors = [];
        $this->fields = [];
        foreach($this->getKeys() as $key) {
            $this->scores->{$key} = 0;
            $this->errors[$key] = [];
            $this->fields[$key] = [];
        }
        $this->scores->global = 0;
        return $this;
    }

    public function setKeys(array $keys) {
        $this->keys = $keys;
        return $this;
    }

    public function getKeys() {
        return $this->keys;
    }

    public function setFields(array $fields) {
        $this->fields = $fields;
        return $this;
    }

    public function getFields() {
        return $this->fields;
    }

    public function calculate() {
        $total = $count = 0;
        foreach($this->getKeys() as $key) {
            $func = 'get' .ucfirst($key) . 'Score';
            if(is_callable([$this, $func])) {
                $this->scores->{$key} = call_user_func([$this, $func]);
                $total += (int)$this->scores->{$key};
                $count ++;
            }
        }
        $this->scores->global = round($total / $count);
        return $this;
    }

    public function getProfileScore() {
        // 1. profile
        $profile = [ 'name', 'gender', 'about' ];
        $total = count($profile);
        $count = 0;
        $owner = $this->project->getOwner();
        foreach($profile as $field) {
            if(!empty($owner->{$field})) {
                continue;
            }
            $this->fields['profile'][] = $field;
            $count++;
        }
        if($count > 0) {
            $this->errors['profile'][] = 'profile';
        }
        $res = round(100 * ($total - $count)/$total);
        if(empty($owner->webs) && empty($owner->facebook) && empty($owner->twitter)&& empty($owner->instagram)) {
            $this->errors['profile'][] = 'profile_social';
            $res = ($total - 1) * $res / $total;
        }
        return $res;
    }

    public function getPersonalScore() {
        // 2. personal
        $personal = [ 'phone' ];
        $count = 0;
        $total = count($personal);
        foreach($personal as $field) {
            if(!empty($this->project->{$field})) {
                continue;
            }
            $this->fields['personal'][] = $field;
            $count++;
        }
        if($count > 0) {
            $this->errors['personal'][] = 'personal';
        }
        return round(100 * ($total - $count)/$total);
    }

    public function getOverviewScore() {

        // 3. overview
        $overview = ['name', 'subtitle', 'lang', 'currency',
        // 'media',
         'description', 'project_location', 'related', 'about', 'motivation', 'scope', 'social_commitment', 'social_commitment_description'];

        $total = count($overview);
        $count = 0;
        foreach($overview as $field) {
            if($field === 'description') {
                if(preg_match('/^\s*\S+(?:\s+\S+){79,}\s*$/', $this->project->{$field})) {
                    continue;
                }
            } elseif(!empty($this->project->{$field})) {
                continue;
            }
            $this->fields['overview'][] = $field;
            $count++;
        }
        if($count > 0) {
            $this->errors['overview'][] = 'overview';
        }
        return round(100 * ($total - $count)/$total);
    }

    public function getImagesScore() {
        // 4. images
        $res = 0;
        if($this->project->image instanceOf Image) {
            if($this->project->image->id) {
                $res = 100;
            }
        }
        if($res < 100) {
            $this->errors['images'][] = 'images';
        }
        return $res;

    }

    public function getCostsScore() {
        // 5. costs
        $costs = ['cost', 'description', 'amount', 'type'];
        $count1 = 0;
        $requireds = 0;
        foreach($this->project->costs as $cost) {
            $count2 = 0;
            foreach($costs as $field) {
                if($field === 'amount') {
                    if(is_numeric($cost->{$field})) {
                        continue;
                    }
                } elseif(!empty($cost->{$field})) {
                    continue;
                }
                $this->fields['costs'][] = $field;
                $count2++;
            }
            if($count2) {
                $count1++;
            }
            $requireds += $cost->required;
        }
        if($count1 > 0) {
            $this->errors['costs'][] = 'costs';
        }

        $res = 0;
        $total = count($this->project->costs);
        if($total > 0) {
            $res = round(100 * ($total - $count1)/$total);
        }
        if($requireds == $total || $requireds == 0) {
            $this->errors['costs'][] = 'costs_required';
            $res /= 2;
        }
        return $res;
    }

    public function getRewardsScore() {
        // 6. rewards
        $rewards = ['reward', 'description', 'amount', 'type'];
        $count1 = 0;
        $requireds = 0;
        foreach($this->project->individual_rewards as $reward) {
            $count2 = 0;
            foreach($rewards as $field) {
                if($field === 'amount') {
                    if((int) $reward->{$field} > 0) {
                        continue;
                    } else {
                        $this->errors['rewards'][] = 'rewards_empty_amount';
                    }
                } elseif(!empty($reward->{$field})) {
                    continue;
                }
                $this->fields['rewards'][] = $field;
                $count2++;
            }
            if($count2) {
                $count1++;
            }
            $requireds += $reward->required;
        }
        $res = 100;
        $total = count($this->project->individual_rewards);
        if($count1 > 0) {
            $this->errors['rewards'][] = 'rewards';
            $res = round(100 * ($total - $count1)/$total);
        }
        // rewards required >= 1, default 3
        $rewards_required = abs(intval(Config::get('rewards.required'))) ?: 3;
        if($total < $rewards_required) {
            $this->errors['rewards'][] = 'rewards_required';
            $res *= $total / $rewards_required;
        }
        return $res;
    }

    public function getCampaignScore() {
        // 6. campaign
        $campaign = [ 'phone' ];
        $count = 0;
        $total = count($campaign);
        foreach($campaign as $field) {
            if(!empty($this->project->{$field})) {
                continue;
            }
            $this->fields['campaign'][] = $field;
            $count++;
        }
        if($count > 0) {
            $this->errors['campaign'][] = 'campaign';
        }
        return round(100 * ($total - $count)/$total);
    }

    public function getScores() {
        return $this->scores;
    }

    public function getErrors() {
        return $this->errors;
    }
}
