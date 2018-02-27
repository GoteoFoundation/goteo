<?php

namespace Goteo\Util\Monolog\Processor;

use Goteo\Application\Session;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Cookie;
use Goteo\Core\Model;
use Goteo\Application\Currency;
use Symfony\Component\HttpFoundation\Request;

class WebProcessor
{
    protected $request;
    private $token;
    private $session;
    private $uid;
    static private $fields = [
                'owner' => 'str',
                'name' => 'str',
                'title' => 'str',
                'method' => 'str',
                'amount' => 'int',
                'status' => 'int',
                'type' => 'int',
                'round' => 'int',
                'one_round' => 'int',
                'mincost' => 'int',
                'maxcost' => 'int',
                'date' => 'date',
                'published' => 'date',
                'passed' => 'date',
                'success' => 'date',
                'closed' => 'date',
                'days_left' => 'int',
                'mailing' => 'int',
                'template' => 'int',
                'massive' => 'int',
                'subject' => 'str',
                'scope' => 'str',
                'icon' => 'str',
                'license' => 'str',
                'currency' => 'str',
                'lang' => 'str',
                'node' => 'str'
    ];
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

        $record['extra']['mhash'] = Model::idealiza($record['message']);
        $record['extra']['uid'] = $this->uid;
        $record['extra']['session'] = $this->session;
        $record['extra']['token'] = $this->token;
        $record['extra']['ip'] = $this->request->getClientIp();
        $record['extra']['method'] = $this->request->getMethod();
        $record['extra']['user'] = (string)Session::getUserId();
        $record['extra']['lang'] = (string)Lang::current();
        $record['extra']['currency'] = (string)Currency::current('id');
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
                $clas = explode('\\',get_class($value));
                $key = strtolower(end($clas));
                // standarized properties
                if(property_exists($value, 'id')) {
                    $ctxt[$key] = $value->id;
                }
                foreach(self::$fields as $k => $type) {
                    if(property_exists($value, $k) && !is_null($value->$k)) {
                        if($type == 'str')      $val = (string) $value->$k;
                        elseif($type == 'int')  $val = (int) $value->$k;
                        else                    $val = $value->$k;

                        $ctxt[$key . "_$k"] = $val;
                    }
                }
                if(method_exists($value, 'getCall') && Config::get('calls_enabled')) {
                    $call = $value->getCall();
                    if($call instanceOf \Goteo\Model\Call) {
                        $ctxt = array_merge(self::processObject([$call]), $ctxt);
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
