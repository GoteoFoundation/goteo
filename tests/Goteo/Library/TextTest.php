<?php

namespace Goteo\Library\Tests;

use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;
use PHPUnit_Framework_AssertionFailedError;

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
        $failures = [];
        $langs = Lang::getLangsAvailable();
        $originals = Text::getAll([], Config::get('sql_lang'));
        foreach($originals as $key => $ob) {
            foreach($langs as $lang => $parts) {

                $trans = Text::lang($key, $lang);
                // Skip non-translated strings
                if($trans === $key) continue;

                try {
                    // Check the existence of invalid chars
                    $this->assertFalse(strpos($trans, '\\r'), "Key [$key] in lang [$lang] has the invalid char [\\r]");
                    $this->assertFalse(strpos($trans, '\\t'), "Key [$key] in lang [$lang] has the invalid char [\\t]");
                } catch(PHPUnit_Framework_AssertionFailedError $e) {
                    $failures[] = $e->getMessage();
                }


                // Check that sentences with html tags are valid
                if(preg_match_all('/\<([^>]+)\>/', $ob->text, $matches)) {
                    sort($matches[0]);
                    try {
                        $this->assertNotFalse(preg_match_all('/\<([^>]+)\>/', $trans, $matches2), "Key [$key] contains incorrect HTML code");
                        sort($matches2[0]);
                        $this->assertEquals($matches[0], $matches2[0], "Key [$key] in lang [$lang] fails to include all original html tags: (" .implode(', ', $matches[0]). ") Having: (" . implode(", ", $matches2[0]) . ")");
                    } catch(PHPUnit_Framework_AssertionFailedError $e) {
                        $failures[] = $e->getMessage();
                    }
                }

                // if(strpos($ob->text, '<') !== false) {
                //     $this->assertTrue(check_html($trans), "Key [$key] in lang [$lang] has invalid html tags: [$trans]");
                //     // check that translations has the same tags as the original
                // }

                if($lang === Config::get('sql_lang')) continue;

                // Check for variables in the form %VAR%
                if(preg_match_all('/\%([A-Z0-9_-]+)\%/', $ob->text, $matches)) {
                    sort($matches[0]);
                    try {
                        $this->assertNotFalse(preg_match_all('/\%([A-Z0-9_-]+)\%/', $trans, $matches2), "Key [$key] contains incorrect variables defined as %VAR%");
                        sort($matches2[0]);
                        $this->assertEquals($matches[0], $matches2[0], "Key [$key] in lang [$lang] fails to include all variables: (" .implode(', ', $matches[0]). ") Having: (" . implode(", ", $matches2[0]) . ")");
                    } catch(PHPUnit_Framework_AssertionFailedError $e) {
                        $failures[] = $e->getMessage();
                    }
                }

                // Check for variables in the form %s %d
                if(preg_match_all('/\%([sd]+)/', $ob->text, $matches)) {
                    sort($matches[0]);
                    try {
                        $this->assertNotFalse(preg_match_all('/\%([sd]+)/', $trans, $matches2), "Key [$key] contains incorrect variables defined as %s");
                        sort($matches2[0]);
                        $this->assertEquals($matches[0], $matches2[0], "Key [$key] in lang [$lang] fails to include all variables: (" .implode(', ', $matches[0]). ") Having: (" . implode(", ", $matches2[0]) . ")");
                    } catch(PHPUnit_Framework_AssertionFailedError $e) {
                        $failures[] = $e->getMessage();
                    }
                }

            }
        }
         if($failures) {
            throw new PHPUnit_Framework_AssertionFailedError (
                count($failures)." translation assertions failed:\n\t".implode("\n\t", $failures)
            );
        }
    }

}
