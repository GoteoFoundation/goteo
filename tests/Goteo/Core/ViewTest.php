<?php

namespace Goteo\Core\Tests;

use Goteo\Core\View,
    Goteo\Core\Redirection,
    Goteo\Core\View\Exception,
    Goteo\Application\Session,
    Goteo\Model\Project,
    Goteo\Model\Image,
    Goteo\Model\User;

class ViewTest extends \Goteo\TestCase {

    protected static $views = array();

    static function setUpBeforeClass() {
        self::$views = array(
            'admin/blog/list.html.php',
            'admin/commons/list.html.php',
            );

        $path = realpath(GOTEO_PATH . 'Resources/templates/legacy');

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $file => $object){
            if(strrpos($file, '.html.php', -9) !== false) {
                $file = substr($file, strlen($path) + 1);
                // print_r("[$file] " . strrpos($file, '.html.php', -9) . "\n");
                self::$views[] = $file;
            }
        }
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

    /**
     * Agressive test of all views
     * @return [type] [description]
     */
    /*public function testGoteoViews() {
        $project = new Project();
        $project->user = new User();
        $post = new \Goteo\Model\Blog\Post();
        $post->gallery = 'empty';
        $post->image = new Image();
        $call = new \Goteo\Model\Call();
        $call->logo =new Image();
        $vars = array(
            'project' => $project,
            'user' => $project->user,
            'post' => $post,
            'call' => $call
            );

        Session::setUser($project->user);
        foreach(self::$views as $view) {
            try {
                $v = new View($view, $vars);
                $this->assertInstanceOf('\Goteo\Core\View', $v);
                $out = $v->render();
                $this->assertInternalType('string', $out);
            }
            catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
                echo "\nLa vista [$view] lanza una exception de modelo!\nEsto no deberia hacerse aqui!\n";
            }
            catch(\Goteo\Core\Redirection $e) {
                echo "\nLa vista [$view] lanza una exception de redireccion!\nEsto no deberia hacerse aqui!\n";
            }
            // echo $out;
        }
    }
*/
}
