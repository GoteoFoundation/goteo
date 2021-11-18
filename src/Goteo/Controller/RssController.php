<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Core\Controller;
use Goteo\Model;
use Symfony\Component\HttpFoundation\Request;

class RssController extends Controller {

    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);
    }

    public function indexAction(Request $request, $lang = '') {
        date_default_timezone_set('UTC');

        if(preg_match('/^[a-z]{2,2}+$/', $lang)) {
            Lang::set($lang);
        }
        $lang = Lang::current();

        $url = $request->getSchemeAndHttpHost();
        // usaremos FeedWriter
        $gformat = strtoupper($request->query->get('format'));

        // le preparamos los datos y se los pasamos
        if(!in_array($gformat, array('RSS1', 'RSS2', 'ATOM'))) $gformat = 'RSS2';
        $class = "FeedWriter\\$gformat";
        $feed = new $class();

        $feed->setTitle('Goteo RSS');
        $feed->setLink($url);
        $feed->setDescription('Blog Goteo.org rss');
        $feed->setImage('Goteo RSS', $url, $url . '/goteo_logo.png');
        $feed->setDate(date('Y-m-d\TH:i:s').'Z');
        $feed->setChannelElement('language', Lang::getLocale());
        $feed->setSelfLink($url . '/rss' . ($lang !== Config::get('lang') ? "/$lang" : ''));

        $tags = Model\Blog\Post\Tag::getAll();
        foreach ($tags as $tagName) {
            $feed->setChannelElement('category', $tagName, null, true);
        }

        $blog = Model\Blog::get(Config::get('node'), 'node');

        foreach ($blog->posts as $post) {
            $link = $url . '/blog/' . $post->getSlug();
            $item = $feed->createNewItem();
            $text = $post->type === 'md' ? $this->getService('app.md.parser')->text($post->text) : $post->text;
            $item->addElementArray(array(
                'title' => $post->title,
                'link' => $link,
                'description' => $text
                )
            );

            if($gformat === 'ATOM') {
                $item->setContent(html_entity_decode(strip_tags($text)));
            }

            $item->setDate($post->date);
            $item->addElement('guid', $link);

            foreach ($post->tags as $tagName) {
                $item->addElement('category', $tagName, null, false, true);
            }

            $feed->addItem($item);
        }

        return $this->rawResponse($feed->generateFeed(), $feed->getMimeType());
    }
}
