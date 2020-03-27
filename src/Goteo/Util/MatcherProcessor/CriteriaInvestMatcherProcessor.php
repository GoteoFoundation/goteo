<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\MatcherProcessor;

use Goteo\Util\MatcherProcessor\AbstractMatcherProcessor;
use Goteo\Util\MatcherProcessor\MatcherProcessorException;
use Goteo\Model\Matcher;
use Goteo\Library\Text;
use Goteo\Model\Invest;

/**
 * This Processor duplicates invests with some (customizable) limits
 */
class CriteriaInvestMatcherProcessor extends AbstractMatcherProcessor {
    protected $default_vars = [
    ];

    /**
     * Checks if this invests has to be matched and
     * returns the amount to be added
     */
    public function getAmount(&$error = '') {
        $invest = $this->getInvest();
        $project = $this->getProject();
        $matcher = $this->getMatcher();
        $vars = $this->getVars();
        $amount = $invest->amount;

        $invested = Invest::getList(['projects' => $project, 'types' => 'nondrop', 'status' => Invest::$ACTIVE_STATUSES], null, 0, 0, 'money');

        $investors = Invest::getList(['projects' => $project, 'types' => 'nondrop', 'status' => Invest::$ACTIVE_STATUSES], null, 0, 0, 'user');

        $invested = Invest::getList(['methods' => 'pool', 'status' => Invest::$ACTIVE_STATUSES, 'projects' => $project,'users' => $matcher->getUsers()], null, 0, 0, 'money');
        $has_reached_percent = ($vars['percent_of_donation'])? ($project->getAmountPercent() >= $vars['percent_of_donation']) : false;
        $has_reached_min_amount = ($vars['min_amount_per_project'])? ($vars['min_amount_per_project'] <= $invested) : false;
        $has_reached_donors = ($vars['min_number_of_donors']) ? ($vars['min_number_of_donors'] <= $investors) : true;

        if ($has_reached_percent && $has_reached_donors && !$invested) {
            $amount = $vars['donation_per_project'];
        } else if ($has_reached_min_amount && $has_reached_donors && !$invested) {
            $amount = $vars['donation_per_project'];
        } else {
            $amount = 0;
        }
        
        // Check if there's enough total to extract from user's pool
        if($matcher->getAvailableAmount() < $amount) {
            $error = 'Matcher funds exhausted';
            $amount = $matcher->getAvailableAmount();
        }
        
        return $amount;

    }

}
