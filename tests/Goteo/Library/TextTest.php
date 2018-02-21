<?php

namespace Goteo\Library\Tests;

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;

class TextTest extends TestCase {
	protected $translator;
	protected $lang;

	protected $keys = [
		'test-lang-1' => 'This is a %s',
		'test-lang-2' => 'This is a %STRING%',
		'test-lang-3' => 'This is a %s and %s',
		'test-lang-4' => 'This is a %STRING% and %OTHER%',
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
		$this->assertNotEquals($this->keys['test-lang-4'], Text::get('test-lang-3', ['%STRING%' => 'string', '%OTHER%' => 'other']));
		$this->assertEquals('This is a string and other', Text::get('test-lang-3', 'string', 'other'));
		$this->assertEquals('This is a string and other', Text::get('test-lang-4', ['%STRING%' => 'string', '%OTHER%' => 'other']));
	}

	/**
	 * Validate consistency between languages translations by checking all the translations
     * have the same vars (%VAR% or %s)
	 */
	public function testLangTranslations() {
        // Lang::set('es');
        // echo Lang::current().']';
        // print_r(Text::get('feed-admin-invest-cancelled'));
        // print_r(Text::lang('feed-admin-invest-cancelled', 'as'));print_r(Text::getErrors());die;
        $langs = Lang::getLangsAvailable();
        $originals = Text::getAll([], Config::get('sql_lang'));
        foreach($originals as $key => $ob) {
            // Check for variables in the form %VAR%
            if(preg_match_all('/\%([A-Z0-9_-]+)\%/', $ob->text, $matches)) {
                sort($matches[0]);
                foreach($langs as $lang => $parts) {
                    if($lang === Config::get('sql_lang')) continue;
                    $trans = Text::lang($key, $lang);
                    if($trans === $key) continue;
                    $this->assertNotFalse(preg_match_all('/\%([A-Z0-9_-]+)\%/', $trans, $matches2));
                    sort($matches2[0]);
                    $this->assertEquals($matches[0], $matches2[0], "Key [$key] in lang [$lang] fails to include all variables: (" .implode(', ', $matches[0]). ") Having: (" . implode(", ", $matches2[0]) . ")");
                }
            }
            // Check for variables in the form %s %d
            if(preg_match_all('/\%([sd]+)/', $ob->text, $matches)) {
                sort($matches[0]);
                foreach($langs as $lang => $parts) {
                    if($lang === Config::get('sql_lang')) continue;
                    $trans = Text::lang($key, $lang);
                    if($trans === $key) continue;
                    $this->assertNotFalse(preg_match_all('/\%([sd]+)/', $trans, $matches2));
                    sort($matches2[0]);
                    $this->assertEquals($matches[0], $matches2[0], "Key [$key] in lang [$lang] fails to include all variables: (" .implode(', ', $matches[0]). ") Having: (" . implode(", ", $matches2[0]) . ")");
                }
            }

        }
    }

}
