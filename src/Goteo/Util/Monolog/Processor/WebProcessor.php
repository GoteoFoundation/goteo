<?php

namespace Goteo\Util\Monolog\Processor;

use Goteo\Application\Session;
use Goteo\Application\Lang;
use Goteo\Application\Cookie;
use Goteo\Library\Currency;
use Symfony\Component\HttpFoundation\Request;

class WebProcessor
{
    protected $request;
    private $token;
    private $session;
    private $uid;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        // Add session identifier

        return $this->processRecord($record);

    }

    public function processRecord(array $record)
    {
        if (null === $this->token) {
            $this->token = substr(uniqid(), -8);
        }
        if (null === $this->session) {
            try {
                $this->session = substr(Session::getId(), 0, 8);
            } catch (\RuntimeException $e) {
                $this->session = '????????';
            }
        }
        if (null === $this->uid) {
            if(Cookie::exists('uid')) {
                $this->uid = Cookie::get('uid');
            }
            if(empty($this->uid)) {
                $this->uid = $this->token;
                // die('uid '.$this->uid);
            }
            Cookie::store('uid', $this->uid, 15*50);
        }

        $record['extra']['uid'] = $this->uid;
        $record['extra']['session'] = $this->session;
        $record['extra']['token'] = $this->token;
        $record['extra']['ip'] = $this->request->getClientIp();
        $record['extra']['method'] = $this->request->getMethod();
        $record['extra']['user'] = Session::getUserId();
        $record['extra']['lang'] = Lang::current();
        $record['extra']['currency'] = Currency::current('id');
        if($shadowed_by = Session::get('shadowed_by')) {
            $record['extra']['shadowed_by'] = $shadowed_by[0];
        }

        return $record;
    }

    static function processObject(array $context = []) {
        $ctxt = [];
        foreach($context as $key => $value) {
            if(is_null($value)) continue;
            if(is_object($value)) {
                $clas = get_class($value);
                $key = strtolower(end(explode('\\',$clas)));
                // standarized properties
                if(property_exists($value, 'id')) {
                    $ctxt[$key] = $value->id;
                }
                foreach(['owner', 'method', 'amount', 'status', 'type', 'round', 'one_round', 'mincost', 'maxcost', 'published', 'passed', 'success', 'closed', 'days_left', 'mailing', 'template', 'massive', 'subject', 'scope', 'icon', 'license', 'currency', 'lang', 'node'] as $k ) {
                    if(property_exists($value, $k) && !is_null($value->$k)) {
                        $ctxt[$key . "_$k"] = $value->$k;
                    }
                }
                if(method_exists($value, 'getCall') && class_exists('\Goteo\Model\Call')) {
                    $call = $value->getCall();
                    if($call instanceOf \Goteo\Model\Call) {
                        $ctxt["call"] = $call->id;
                        foreach(['owner', 'amount', 'status', 'lang'] as $k ) {
                            if(property_exists($call, $k)) {
                                $ctxt["call_$k"] = $call->$k;
                            }
                        }
                    }
                }
            }
            else {
                $ctxt[$key] = $value;
            }
        }
        return $ctxt;
    }
}
