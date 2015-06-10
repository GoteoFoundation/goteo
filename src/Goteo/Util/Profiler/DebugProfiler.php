<?php

namespace Goteo\Util\Profiler;

use Symfony\Component\HttpKernel\Event;
use Symfony\Component\Stopwatch\Stopwatch;

use Goteo\Core\CacheStatement;
use Goteo\Application\App;
use Goteo\Application\View;

class DebugProfiler {
    protected $events = array();
    protected $controllers = array();
    protected $stopwatch = array();
    static protected $instance = null;

    public function __construct() {
        View::addFolder(__DIR__ . '/templates/', 'profiler');
        $this->stopwatch = new Stopwatch();
    }

    public function addKernelEvent(Event\KernelEvent $event) {
        $last_event = end($this->events);
        if($last_event) {
            $last_event->stopwatch = $this->stopwatch->stop('event');
        }
        // New counter
        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start('event');
        $ob = new \stdClass;
        $ob->class = get_class($event);;
        $ob->event = $event;
        $ob->controllers = $ob->requests = $ob->responses = array();

        if(method_exists($event, 'getController')) {
            $ob->controllers[] = $event->getController();
        }
        if(method_exists($event, 'getRequest')) {
            $ob->requests[] = $event->getRequest();
        }
        if(method_exists($event, 'getResponse')) {
            $ob->responses[] = $event->getResponse();
        }

        $this->events[] = $ob;
    }

    public function render($view, $with_vars = true) {
        // SQL stats
        $queries = array(
            'total_replica_non_cached' => CacheStatement::$query_stats['replica'][0],
            'total_master_non_cached' => CacheStatement::$query_stats['master'][0],
            'total_replica_cached' => CacheStatement::$query_stats['replica'][1],
            'total_master_cached' => CacheStatement::$query_stats['master'][1],
            'total_replica' => CacheStatement::$query_stats['replica'][0] + CacheStatement::$query_stats['replica'][1],
            'total_master' => CacheStatement::$query_stats['master'][0] + CacheStatement::$query_stats['master'][1],
            'time_replica' => CacheStatement::$query_stats['replica'][2],
            'time_master' => CacheStatement::$query_stats['master'][2],
            'sql_replica_non_cached' => CacheStatement::$queries['replica'][0],
            'sql_replica_cached' => CacheStatement::$queries['replica'][1],
            'sql_master_non_cached' => CacheStatement::$queries['master'][0],
            'sql_master_cached' => CacheStatement::$queries['master'][1],
            );
        $queries['total'] = $queries['total_replica'] + $queries['total_master'];
        $queries['total_non_cached'] = $queries['total_replica_non_cached'] + $queries['total_master_non_cached'];
        $queries['total_cached'] = $queries['total_replica_cached'] + $queries['total_master_cached'];
        $queries['time'] = $queries['time_replica'] + $queries['time_master'];
        $queries['sql_long_queries'] = array();
        foreach($queries['sql_master_non_cached'] + $queries['sql_replica_non_cached'] as $i => $sql) {
            if($sql[3] > 0.01) $queries['sql_long_queries'][intval($sql[3] . $i)] = $sql;
        }
        krsort($queries['sql_long_queries']);

        // Kernel events
        $events = array();
        foreach($this->events as $i => $event) {
            $events[$i]['class'] = $event->class;
            if( ! $event->stopwatch ) {
                $event->stopwatch = $this->stopwatch->stop('event');
            }
            $events[$i]['time'] = $event->stopwatch->getDuration();
            $events[$i]['memory'] = $event->stopwatch->getMemory();
            $events[$i]['controllers'] = $event->controllers;
            $events[$i]['requests'] = $event->requests;
            $events[$i]['responses'] = $event->responses;
        }

        return View::render("profiler::$view", $with_vars ? [
            'errors' => App::getErrors(),
            'queries' => $queries,
            'events' => $events
            ] : []);
    }

    static public function getInstance() {
        if( ! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function addEvent(Event\KernelEvent $event) {
        self::getInstance()->addKernelEvent($event);
    }

    static public function getHeadContent() {
        return self::getInstance()->render('header', false);
    }

    static public function getBodyContent() {
        return self::getInstance()->render('body');
    }
}
