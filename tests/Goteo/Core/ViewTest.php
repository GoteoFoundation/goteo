<?php

namespace Goteo\Tests;

use \Goteo\Core\View,
    \Goteo\Core\View\Exception;

class ViewTest extends \PHPUnit_Framework_TestCase {

    protected static $views = array();

    static function setUpBeforeClass() {
        self::$views = array(
            'admin/blog/list.html.php',
            'admin/commons/list.html.php',
            );

        $path = realpath(GOTEO_PATH . '/view');

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $file => $object){
            if(strrpos($file, '.html.php', -9) !== false) {
                $file = substr($file, strlen($path) + 1);
                // print_r("[$file] " . strrpos($file, '.html.php', -9) . "\n");
                self::$views[] = $file;
            }
        }
        //some views uses it
        define('NODE_ID', GOTEO_NODE);
    }

    public function testInstance() {
        $test = new View(__FILE__);
        $this->assertInstanceOf('\Goteo\Core\View', $test);
        $test = new Exception();
        $this->assertInstanceOf('\Goteo\Core\View\Exception', $test);
        try {
            $test = new View('i-dont-exists.php');
        }
        catch(Exception $e) {
            $this->assertInstanceOf('\Goteo\Core\View\Exception', $e);
        }

    }

    public function testView() {

    }
    public function testGoteoViews() {
        foreach(self::$views as $view) {
            $v = new View($view);
            $this->assertInstanceOf('\Goteo\Core\View', $v);
            $out = $v->render();
            // echo $out;
            $this->assertInternalType('string', $out);
        }
    }
}
