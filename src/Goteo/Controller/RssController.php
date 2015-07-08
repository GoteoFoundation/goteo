<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use FeedWriter;
use Goteo\Model;

class RssController extends \Goteo\Core\Controller {

    public function __construct() {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);
    }

    public function indexAction ($lang = '', Request $request) {
        date_default_timezone_set('UTC');

        if($lang) Lang::set($lang);
        $lang = Lang::current();

        $url = $request->getSchemeAndHttpHost();
        // usaremos FeedWriter
        $gformat = strtoupper($request->query->get('format'));

        // le preparamos los datos y se los pasamos

        $formats = array('RSS1', 'RSS2', 'ATOM');
        if(!in_array($gformat, array('RSS1', 'RSS2', 'ATOM'))) $gformat = 'RSS2';
        $clas = "FeedWriter\\$gformat";
        $feed = new $clas();

        $feed->setTitle('Goteo RSS');
        $feed->setLink($url);
        $feed->setDescription('Blog Goteo.org rss');
        $feed->setImage('Goteo RSS', $url, $url . '/goteo_logo.png');
        $feed->setDate(date('Y-m-d\TH:i:s').'Z');
        $feed->setChannelElement('language', Lang::getLocale());
        // $feed->setChannelElement('author', 'Goteo');
        $feed->setSelfLink($url . '/rss' . ($lang !== Config::get('lang') ? "/$lang" : ''));

        $tags = Model\Blog\Post\Tag::getAll();
        foreach ($tags as $tagId => $tagName) {
            $feed->setChannelElement('category', $tagName, null, true);
        }
        // $feed->addCdataEncoding(array('category'));

        // sacamos su blog
        $blog = Model\Blog::get(Config::get('node'), 'node');

        foreach ($blog->posts as $postId => $post) {
            $link = $url . '/blog/' . $post->id;
            // print_r($post);die;
            $item = $feed->createNewItem();
            $item->addElementArray(array(
                'title' => $post->title,
                'link' => $link,
                'description' => $post->text
                )
            );

            if($gformat === 'ATOM') {
                $item->setContent(html_entity_decode(strip_tags($post->text)));
            }

            $item->setDate($post->date);
            $item->addElement('guid', $link
                // si acaso cuando tenga un slug como dios manda
                //, array('isPermaLink'=>'true')
                );

            foreach ($post->tags as $tagId => $tagName) {
                $item->addElement('category', $tagName, null, false, true);
            }

            $feed->addItem($item);
        }

        return $this->rawResponse($feed->generateFeed(), $feed->getMimeType());

    }

}


