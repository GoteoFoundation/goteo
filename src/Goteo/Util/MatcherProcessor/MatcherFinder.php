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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MatcherFinder {
    protected $container = null;
    protected $processors = [];
    protected $listeners = [];

    public function __construct(ContainerBuilder $container) {
        $this->container = $container;
    }

    public function addProcessor($processor) {
        if(!class_exists($processor)) {
            throw new MatcherFinderException("[$processor] is not a valid class");
        }
        $reflect = new \ReflectionClass($processor);
        if(!$reflect->implementsInterface('Goteo\Util\MatcherProcessor\MatcherProcessorInterface')) {
            throw new MatcherFinderException("[$processor] should implement [Goteo\Util\MatcherProcessor\MatcherProcessorInterface]");
        }
        $this->processors[] = $processor;

        if($listeners = $processor::getAppEventListeners()) {
            foreach($listeners as $listener => $arguments) {
                $this->addListenerToDispatcher('dispatcher', $listener, $arguments);
            }
        }
        if($listeners = $processor::getConsoleEventListeners()) {
            foreach($listeners as $listener => $arguments) {
                $this->addListenerToDispatcher('console_dispatcher', $listener, $arguments);
            }
        }
        return $this;
    }


    /**
     * Adds to the dispatcher the listeners defined in the processor
     * @param [type] $listener [description]
     */
    public function addListenerToDispatcher($dispatcher = 'dispatcher', $listener, $arguments = []) {
        $sc = $this->container;
        if(!isset($this->listeners[$dispatcher]) || !is_array($this->listeners[$dispatcher])) $this->listeners[$dispatcher] = [];

        $index = count($this->listeners[$dispatcher]);
        $id = "matcher.$dispatcher.listener.$index";
        if(in_array($listener, $this->listeners[$dispatcher])) return;

        $sc->register($id, $listener)
           ->setArguments(array_map(function($arg) {return new Reference($arg);}, $arguments));

        $sc->getDefinition($dispatcher)
           ->addMethodCall('addSubscriber', array(new Reference($id)));

        $this->listeners[$dispatcher][$id] = $listener;
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
