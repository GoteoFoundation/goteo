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

    public static function sanitize($text) {
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y',
            'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', 'ª' => 'a', 'º' => 'o', 'ẃ' => 'w', 'Ẃ' => 'Ẃ', 'ẁ' => 'w', 'Ẁ' => 'Ẃ', '€' => 'eur',
            'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ś' => 's', 'Ś' => 'S', 'ẅ' => 'w', 'Ẅ' => 'W',
            '!' => '', '¡' => '', '?' => '', '¿' => '', '@' => '', '^' => '', '|' => '', '#' => '', '~' => '',
            '%' => '', '$' => '', '*' => '', '+' => '', '.' => '-', '`' => '', '´' => '', '’' => '', '”' => '-', '“' => '-',
        );
        // Clean modern un-supported UTF8 chars
        $text = utf8_encode(utf8_decode($text));
        return preg_replace('/[^\x20-\x7e]*/', '', $text);
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
        if($ref = static::sanitize($this->request->query->get('ref'))) {
            return [
                'tag' => $ref,
                'category' => 'campaign',
                'type' => 'referer'
                ];
        }

        // Extracting Referer elements
        // https://github.com/snowplow/referer-parser/tree/master/php

        $parser = new RefererParser();
        $ref = static::sanitize($this->request->headers->get('referer'));
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
