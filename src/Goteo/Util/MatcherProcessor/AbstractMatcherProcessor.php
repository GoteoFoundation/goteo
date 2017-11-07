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

use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Payment\Method\PaymentMethodInterface;

abstract class AbstractMatcherProcessor implements MatcherProcessorInterface {
    protected $matcher;
    protected $project; // Context project to check
    protected $invest; // Context Invest to multiply
    protected $method; // Context payment method

    public function __construct(Matcher $matcher) {
        $this->setMatcher($matcher);
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

    static public function create(Matcher $matcher) {
        if(static::is($matcher)) {
            return new static($matcher);
        }
        throw new MatcherProcessorException("Matcher [{$matcher->id}] is not valid for this processor");
    }

}
