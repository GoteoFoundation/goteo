<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Library;

    class Rss extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador index
            \Goteo\Core\DB::cache(true);
        }

        public function index () {
            date_default_timezone_set('UTC');

            // sacamos su blog
            $blog = Model\Blog::get(\GOTEO_NODE, 'node');

            $tags = Model\Blog\Post\Tag::getAll();

            /*
            echo '<pre>'.print_r($tags, true).'</pre>';
            echo '<pre>'.print_r($blog->posts, true).'</pre>';
            die;
             *
             */

            // al ser xml no usaremos vista
            // usaremos FeedWriter

            // configuracion
            $url = SITE_URL;
            if(substr($url, 0, 2) === '//') $url = (HTTPS_ON ? 'https:' : 'http:') . $url;
            $config = array(
                'title' => 'Goteo Rss',
                'description' => 'Blog Goteo.org rss',
                'link' => $url,
                'image' => $url . '/myicon.png',
                'selfLink' => $url . '/rss',
                'date' => \date('Y-m-d\TH:i:s').'Z',


            );

            foreach($blog->posts as $post) {
                $post->link = $url . '/blog/' . $post->id;
            }

            $data = array(
                'tags' => $tags,
                'posts' => $blog->posts
            );

            // le preparamos los datos y se los pasamos
            $feed = Library\RSS::get($config, $data, strtoupper($_GET['format']));
            $feed->printFeed();
        }

    }

}
