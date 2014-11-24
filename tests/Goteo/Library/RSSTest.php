<?php


namespace Goteo\Tests;

use Goteo\Library\RSS,
    \FeedWriter;

class RSSTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $rss = new RSS();

        $this->assertInstanceOf('\Goteo\Library\RSS', $rss);

        $config = array('title' => 'Feed test', 'link' => 'http://test.com', 'description' => 'Test descrition');
        $data = array('posts' => array(1 => (object)array('id' => 1, 'title' => 'title post', 'date' => time())));

        $feed = RSS::get($config, $data, 'RSS1');
        $this->assertInstanceOf('\FeedWriter\RSS1', $feed);

        $feed = RSS::get($config, $data, 'ATOM');
        $this->assertInstanceOf('\FeedWriter\ATOM', $feed);

        $feed = RSS::get($config, $data);
        $this->assertInstanceOf('\FeedWriter\RSS2', $feed);

        return $feed;
    }


}
