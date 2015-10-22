<?php


namespace Goteo\Library\Tests;

use Symfony\Component\Translation\Loader\ArrayLoader;
use Goteo\Library\Text;
use Goteo\Application\Lang;
use Goteo\TestCase;

class TextTest extends TestCase {
    protected $translator;
    protected $lang;

    protected $keys = [
            'test-lang-1' => 'This is a %s',
            'test-lang-2' => 'This is a %STRING%',
            'test-lang-3' => 'This is a %s and %s',
            'test-lang-4' => 'This is a %STRING% and %OTHER%'
            ];

    public function setUp() {
        $this->lang = Lang::current();
        $this->translator = Lang::translator();
        $this->translator->addLoader('array', new ArrayLoader());
        $this->translator->addResource('array', $this->keys, $this->lang);
    }

    public function testInstance() {

        $converter = new Text();

        $this->assertInstanceOf('\Goteo\Library\Text', $converter);
    }

    public function testLang() {

        $this->assertEquals($this->keys['test-lang-1'], Text::lang('test-lang-1'));
    }

    public function testLangOldParams() {

        $this->assertNotEquals($this->keys['test-lang-1'], Text::lang('test-lang-1', 'string'));
        $this->assertEquals('This is a string', Text::lang('test-lang-1', $this->lang, ['string']));
    }

    public function testLangParams() {

        $this->assertNotEquals($this->keys['test-lang-2'], Text::lang('test-lang-2', 'string'));
        $this->assertNotEquals('This is a string', Text::lang('test-lang-2', $this->lang, ['string']));
        $this->assertEquals('This is a string', Text::lang('test-lang-2', $this->lang, ['%STRING%' => 'string']));
    }

    public function testGet() {

        $this->assertNotEquals($this->keys['test-lang-3'], Text::get('test-lang-3', 'string', 'other'));
        $this->assertNotEquals($this->keys['test-lang-4'], Text::get('test-lang-3', [ '%STRING%' => 'string', '%OTHER%' => 'other']));
        $this->assertEquals('This is a string and other', Text::get('test-lang-3', 'string', 'other'));
        $this->assertEquals('This is a string and other', Text::get('test-lang-4', [ '%STRING%' => 'string', '%OTHER%' => 'other']));
    }

}
