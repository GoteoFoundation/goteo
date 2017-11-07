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

class MatcherFinder {
    protected $processors = [];

    public function addProcessor($processor) {
        if(!class_exists($processor)) {
            throw new MatcherFinderException("[$processor] is not a valid class");
        }
        $reflect = new \ReflectionClass($processor);
        if(!$reflect->implementsInterface('Goteo\Util\MatcherProcessor\MatcherProcessorInterface')) {
            throw new MatcherFinderException("[$processor] should implement [Goteo\Util\MatcherProcessor\MatcherProcessorInterface]");
        }
        $this->processors[] = $processor;
        return $this;
    }

    /**
     * Returns a processor if found, null otherwise
     * @param  Matcher $matcher matcher campaing object
     * @return mixed           The matcher or null
     */
    public function getProcessor(Matcher $matcher) {
        foreach($this->processors as $processor) {
            if($processor::is($matcher)) return $processor::create($matcher);
        }

        return null;
    }
}
