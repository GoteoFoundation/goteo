<?php

namespace Goteo\Util\Tests;

use Goteo\Util\Parsers\UrlLang;
use Goteo\Application\Config;
use Symfony\Component\HttpFoundation\Request;

class UrlLangTest extends \PHPUnit\Framework\TestCase {

    protected function getParser($url, $host="http://example.com") {
        $request = Request::create("$host$url");
        return new UrlLang($request);
    }

    public function testInstance() {
        $parser = $this->getParser("/");

        $this->assertInstanceOf('\Goteo\Util\Parsers\UrlLang', $parser);

        return $parser;
    }

    /**
     * @depends testInstance
     * */
    public function testSkipSessionManagement($parser) {
        $this->assertFalse($parser->skipSessionManagement());

        $parser = $this->getParser("/test/something");
        $this->assertFalse($parser->skipSessionManagement());

        Config::set('session.skip', "/test");
        $this->assertTrue($parser->skipSessionManagement());

        Config::set('session.skip', ["/test"]);
        $this->assertTrue($parser->skipSessionManagement());

    }

    /**
     * @depends testInstance
     * */
    public function testDomainLang($parser) {
        $this->assertEquals("example.com", $parser->getHost(""));

        $parser = $this->getParser("/test/something");
        $this->assertEquals("example.com", $parser->getHost(""));

        Config::set('url.url_lang', "example.com");
        $this->assertEquals("example.com", $parser->getHost(""));
        $this->assertEquals("en.example.com", $parser->getHost("en"));

        Config::set("lang", "es");
        $this->assertEquals("www.example.com", $parser->getHost("es"));
        $this->assertEquals("en.example.com", $parser->getHost("en"));

        $parser = $this->getParser("/", "http://www.example.com");
        $this->assertEquals("en.example.com", $parser->getHost("en"));

        return $parser;
    }

    /**
     * @depends testDomainLang
     * */
    public function testRedirectSkip($parser) {
        $parser = $this->getParser("/api");
        $this->assertEquals("example.com", $parser->getHost("en"));

        $parser = $this->getParser("/api", "http://www.example.com");
        $this->assertEquals("www.example.com", $parser->getHost("en"));

        $parser = $this->getParser("/password-recovery", "http://ca.example.com");
        $this->assertEquals("ca.example.com", $parser->getHost("en"));
    }

    /**
     * @depends testDomainLang
     * */
    public function testFixedRedirection($parser) {
        $parser = $this->getParser("/login");
        $this->assertEquals("example.com", $parser->getHost("en"));

        $parser = $this->getParser("/login", "http://www.example.com");
        $this->assertEquals("example.com", $parser->getHost("en"));

        $parser = $this->getParser("/password-reset", "http://ca.example.com");
        $this->assertEquals("example.com", $parser->getHost("en"));
    }

    /**
     * @depends testDomainLang
     * */
    public function testSubdomains($parser) {
        $parser = $this->getParser("/api", "http://www.sub.example.com");
        $this->assertEquals("www.sub.example.com", $parser->getHost("en"));
        $parser = $this->getParser("/login", "http://www.sub.example.com");
        $this->assertEquals("sub.example.com", $parser->getHost("en"));

        $parser = $this->getParser("/", "http://www.sub.example.com");
        $this->assertEquals("sub.example.com", $parser->getHost("en"));

        Config::set('url.url_lang', "sub.example.com");

        $parser = $this->getParser("/api", "http://www.sub.example.com");
        $this->assertEquals("www.sub.example.com", $parser->getHost("en"));
        $parser = $this->getParser("/login", "http://www.sub.example.com");
        $this->assertEquals("sub.example.com", $parser->getHost("en"));

        $parser = $this->getParser("/", "http://www.sub.example.com");
        $this->assertEquals("en.sub.example.com", $parser->getHost("en"));

        $parser = $this->getParser("/test/something", "http://en.sub.example.com");
        $this->assertEquals("www.sub.example.com", $parser->getHost("es"));
        $this->assertEquals("ca.sub.example.com", $parser->getHost("ca"));
    }
}
