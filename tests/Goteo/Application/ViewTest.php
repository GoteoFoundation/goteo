<?php


namespace Goteo\Application\Tests;

use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Model\User;

class ViewTest extends \PHPUnit_Framework_TestCase {
    private static $views;

    static function setUpBeforeClass() {
        $folders = View::getFolders();
        foreach($folders as $key => $path) {
            $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
            foreach($objects as $file => $object){
                if(strrpos($file, '.php', -4) !== false) {
                    $file = substr($file, strlen($path) + 1);
                    self::$views[] = $file;
                }
            }
        }
    }

    public function testInstance() {

        $ob = new View();

        $this->assertInstanceOf('\Goteo\Application\View', $ob);

        $this->assertInstanceOf('\Foil\Engine', View::getEngine());

    }

/*
    public function testTemplates() {

        $project = new Project();
        $project->user = new User();

        $vars = ['project' => $project, 'user' => $project->user];
        foreach(self::$views as $view) {

            $html = View::render($view, $vars, false);
            print_r($html);
        }
    }
    */
}
