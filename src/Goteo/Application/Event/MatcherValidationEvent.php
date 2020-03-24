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
use Goteo\Model\Matcher;
use Symfony\Component\HttpFoundation\Response;
use Goteo\Model\Questionnaire;

class MatcherValidationEvent extends \Goteo\Console\Event\FilterProjectEvent
{
    protected $scores; // Generic Object with all and
    protected $errors; // Array of errors and key of that error
    protected $fields; // Array of form fields with errors
    protected $keys = [ 'profile', /* 'personal', */ 'overview', 'criteria', 'configuration'];

    public function __construct(Matcher $matcher) {
        $this->matcher = $matcher;
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
        $owner = $this->matcher->getOwner();
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
        if(empty($owner->webs) && empty($owner->facebook) && empty($owner->twitter)) {
            $this->errors['profile'][] = 'profile_social';
            $res = ($total - 1) * $res / $total;
        }
        return $res;
    }

    public function getOverviewScore() {

        // 3. overview
        $overview = ['name', 'description', 'sphere', 'matcher_location', 'logo'];

        $total = count($overview);
        $count = 0;
        foreach($overview as $field) {
            if($field == 'sphere') {
                if (!empty($this->matcher->getMainSphere())) {
                    continue;
                }
            } elseif(!empty($this->matcher->{$field})) {
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

    public function getCriteriaScore() {
        // 4. images
        $res = 0;
        if (Questionnaire::getByMatcher($this->matcher->id))
            $res = 100;

        if($res) {
            $this->errors['criteria'][] = 'criteria';
        }
        return $res;

    }

    public function getConfigurationScore() {
        $res = 0;
        if (!empty($this->matcher->getVars())) {
            $res = 100;
        }

        if($res) {
            $this->errors['configuration'][] = 'configuration';
        }
        return $res;
    }

    public function getScores() {
        return $this->scores;
    }

    public function getErrors() {
        return $this->errors;
    }
}
