<?php

namespace Goteo\Util\Origins;

use Symfony\Component\HttpFoundation\Request;

use Snowplow\RefererParser\Parser as RefererParser;
use UAParser\Parser as UAParser;

// https://github.com/snowplow/referer-parser/tree/master/php
class Parser {
    private $request;
    private $host;
    private $subdomains = [];

    public function __construct(Request $request, $host = null, array $subdomains = []) {
        $this->request = $request;
        $this->host = $host;
        $this->subdomains = $subdomains;
    }

    function getUA() {
        // Extracting UA elements
        // https://github.com/ua-parser/uap-php
        $parser = UAParser::create();
        $result = $parser->parse($this->request->headers->get('User-Agent'));
        return [
            'tag' => $result->ua->family,
            'category' => $result->os->family,
            'type' => 'ua'
            ];
    }

    function getReferer() {
        // Manual origin tracker
        if($ref = $this->request->query->get('ref')) {
            return [
                'tag' => $ref,
                'category' => 'campaign',
                'type' => 'referer'
                ];
        }

        // Extracting Referer elements
        // https://github.com/snowplow/referer-parser/tree/master/php

        $parser = new RefererParser();
        $ref = $this->request->headers->get('referer');
        $result = $parser->parse($ref, $this->request->getUri());
        $parts = explode("/", $this->request->getPathInfo());

        $referer = [
            'tag' => $result->getSource(),
            'category' => $result->getMedium(),
            'type' => 'referer'
            ];

        // Consider any subdomain as "internal"
        if($this->host) {
            $parsed = parse_url($ref);
            $ref_host = $parsed['host'];
            if(isset($parsed['port'])) $ref_host .= ':' . $parsed['port'];
            if($ref_host && preg_match('/' . preg_quote($this->host, '/') . '$/', $ref_host)) {
                $referer['category'] = 'internal';
            }
        }

        if($referer['category'] === 'internal') {
            $referer['tag'] = $parsed['path'];
            if($referer['tag'] == '/') {
                // Check subdomains equivalences for root paths
                foreach($this->subdomains as $sub => $paths) {
                    if(!is_array($paths)) $pahts = [$paths];
                    if($ref_host && preg_match('/' . preg_quote($sub, '/') . '$/', $ref_host)) {
                        $referer['tag'] = $paths[0];
                    }
                }
            }
        }
        // Tracked links form MailController as type "email"
        if($parts[1] === 'mail') {
            $referer['tag'] = 'Newsletter';
            $referer['category'] = 'email';
        }

        return $referer;
    }
}
