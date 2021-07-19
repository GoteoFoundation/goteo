<?php

namespace Goteo\Util\Tests;

use Goteo\Util\Origins\Parser;
use Symfony\Component\HttpFoundation\Request;

class ParserTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $fakeRequest = Request::create('/', 'GET');
        $ob = new Parser($fakeRequest);

        $this->assertInstanceOf('\Goteo\Util\Origins\Parser', $ob);

        return $ob;

    }

    public function testUa() {
        $uas = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
                => ['Chrome', 'Windows'],
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
                 => ['Chrome', 'Windows'],
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0'
                => ['Firefox', 'Windows'],
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
                => ['Chrome', 'Mac OS X'],
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
                => ['Chrome', 'Windows'],
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
                => ['Chrome', 'Mac OS X'],
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0'
                => ['Firefox', 'Ubuntu']
        ];
        foreach($uas as $ua => $parts) {
            $r = Request::create('/', 'GET', [], [], [], ['HTTP_USER_AGENT' => $ua]);
            $ob = new Parser($r);
            $ua = $ob->getUA();
            $this->assertEquals($parts[0], $ua['tag']);
            $this->assertEquals($parts[1], $ua['category']);
        }
    }

    public function testReferer() {
        $domains = [
            'subdomain1.goteo.test' => ['/blog'],
            'subdomain2.goteo.test' => ['/channel/mychannel']
        ];
        $refs = [
            'https://goteo.test/' => ['https://facebook.com/', 'Facebook', 'social'],
            'https://goteo.test/1' => ['https://twitter.com/', 'Twitter', 'social'],
            'https://goteo.test/2' => ['https://google.com/', 'Google', 'search'],
            'https://goteo.test/3' => ['https://bing.com/', 'Bing', 'search'],
            'https://goteo.test/4' => ['https://subdomain.goteo.test/', '/', 'internal'],
            'https://goteo.test/5' => ['https://subdomain.goteo.test/path', '/path', 'internal'],
            'https://goteo.test/6' => ['https://subdomain1.goteo.test/', '/blog', 'internal'],
            'https://goteo.test/7' => ['https://subdomain2.goteo.test/', '/channel/mychannel', 'internal'],
            'https://goteo.test/8' => ['https://subdomain2.goteo.test/project', '/project', 'internal'],
            'https://goteo.test/9?ref=fbadd' => ['https://subdomain2.goteo.test/project', 'fbadd', 'campaign'],
            'https://goteo.test/mail/9' => ['https://subdomain2.goteo.test/project', 'Newsletter', 'email'],

        ];

        foreach($refs as $url => $parts) {
            $r = Request::create($url, 'GET', [], [], [], ['HTTP_REFERER' => $parts[0]]);
            $ob = new Parser($r, 'goteo.test', $domains);
            $referer = $ob->getReferer();
            $this->assertEquals($parts[1], $referer['tag']);
            $this->assertEquals($parts[2], $referer['category']);

        }
    }
}
