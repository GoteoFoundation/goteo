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

use Goteo\Application\AppEvents;
use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Application\Currency;
use Goteo\Payment\Method\PaymentMethodInterface;
use Goteo\Payment\Method\PoolPaymentMethod;


abstract class AbstractMatcherProcessor implements MatcherProcessorInterface {
    protected $matcher;
    protected $project; // Context project to check
    protected $invest; // Context Invest to multiply
    protected $method; // Context payment method
    protected $default_vars = []; // default variables

    public function __construct(Matcher $matcher) {
        $this->setMatcher($matcher);
    }

    /**
     * Default implementation is just the name of the class simplified
     */
    static public function getId() {
        $parts = explode('\\', get_called_class());
        return preg_replace('/(matcherprocessor$)/', '', strtolower(array_pop($parts)));
    }

    /**
     * Default implementation is the name of the class made human readable
     */
    static public function getName() {
        $parts = explode('\\', get_called_class());
        $name = preg_replace('/(MatcherProcessor$)/', '', array_pop($parts));
        return preg_replace('/(?!^)[[:upper:]]+/',' \0', $name);
    }

    static public function getDesc() {
        return '';
    }

    static public function is(Matcher $matcher) {
        return static::getId() === $matcher->processor;
    }

    static public function create(Matcher $matcher) {
        if(static::is($matcher)) {
            return new static($matcher);
        }
        throw new MatcherProcessorException("Matcher [{$matcher->id}] is not valid for this processor");
    }

    static public function getVarLabels() {
        return [];
    }

    public function getVars() {
        $vars = $this->default_vars;
        if($matcher = $this->getMatcher()) {
            if($custom = $matcher->getVars()) {
                return $custom + $vars;
            }
        }
        return $vars;
    }

    /**
     * Returns and array of users and how much has to extracted of each pool for a certain amount
     * ponderates between each user according to the total amount existing in their pool
     * @param  int $total total amount to be shared between user's pool
     * @return array        [user_id => amount]
     */
    public function getUserAmounts($total) {
        $users = $this->getMatcher()->getUsers();
        $total_amount = $this->getMatcher()->getTotalAmount();
        $list = [];
        $calculated = 0;
        $pools = [];
        foreach($users as $user) {
            $pool = $user->getPool()->amount;
            $share = $pool / $total_amount;
            $amount = floor($share * $total);
            if($amount > $pool) $amount = $pool;
            $pools[$user->id] = $pool - $amount;

            $list[$user->id] = $amount;
            $calculated += $amount;
        }

        if($missing = max(0, $total - $calculated)) {
            // Check if some is missing to achieve the total
            foreach($list as $u => $a) {
                if($pools[$u] >= $missing) {
                    $list[$u] = $a + $missing;
                    break;
                }
            }
        }
        // print_r($list);
        return $list;
    }

    /**
     * Generic implementation of the list of invests by using $this->getAmount()
     * @return [type] [description]
     */
    public function getInvests() {
        $matcher = $this->getMatcher();
        $invest = $this->getInvest();
        $project = $this->getProject();
        $vars = $this->getVars();

        // Ensure is enough amount
        $error = '';
        if($amount = $this->getAmount($error)) {

            // Check if there's enough total to extract from user's pool
            if($matcher->getTotalAmount() < $amount) {
                throw new MatcherProcessorException("Not enough amount to match");
            }

            $list = [];
            foreach($this->getUserAmounts($amount) as $user_id => $user_amount) {
                $list[] = new Invest([
                    'amount'    => $user_amount,
                    'user'      => $user_id,
                    'currency' => Currency::current(),
                    'currency_rate' => Currency::rate(),
                    'project'   => $project->id,
                    'method'    => PoolPaymentMethod::getId(),
                    'status'    => Invest::STATUS_CHARGED,
                    'invested'  => date('Y-m-d'),
                    'anonymous' => false,
                    'resign'    => true,
                    'campaign'  => true,
                    'drops'     => $invest ? $invest->id : null,
                    'matcher'   => $matcher->id
                ]);
            }
            return $list;

        }
        throw new MatcherProcessorException("No amount for this matcher: " . $error);
    }

    public function setMatcher(Matcher $matcher) {
        $this->matcher = $matcher;
        return $this;
    }

    public function getMatcher() {
        return $this->matcher;
    }

    public function setProject(Project $project) {
        $this->project = $project;
        return $this;
    }

    public function getProject() {
        if( ! $this->project instanceOf Project ) {
            throw new MatcherProcessorException('No project defined in matcher');
        }
        return $this->project;
    }

    public function setInvest(Invest $invest) {
        $this->invest = $invest;
        return $this;
    }

    public function getInvest() {
        return $this->invest;
    }

    public function setMethod(PaymentMethodInterface $method) {
        $this->method = $method;
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }


    /**
     * Defaults event listeners for this processor
     * MailingMatcherListener: Sends mails on project matcher status changes
     * InvestMatcherListener: Processes the matchfunding payments on invests events
     */
    static public function getAppEventListeners() {
        return [
            'Goteo\Application\EventListener\MailingMatcherListener' => ['logger'],
            'Goteo\Application\EventListener\InvestMatcherListener'  => ['paylogger']
        ];
    }

    static public function getConsoleEventListeners() {
        return [
            'Goteo\Application\EventListener\MailingMatcherListener' => ['console_logger']
        ];
    }

}
