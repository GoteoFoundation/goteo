<?php
namespace Goteo\Library {

    require_once 'library/rss/FeedWriter.php';  // Libreria para creacion de rss

	/*
	 * Clase para usar La libreria FeedWriter
	 */
    class Rss {

        public static function get($config, $data, $gformat = null) {
            
            $feed = new \FeedWriter(
                                $config['title'],
                                $config['description'],
                                $config['link'],
                                $config['indent'],
                                true,
                                null,
                                true
                    );

            // debug
            $feed->debug = true;

            //format
            $format = \RSS_2_0;
            if (isset($gformat)){
                foreach ($feed->getFeedFormats() as $cFormat) {
                    if ($cFormat[0] == $gformat) {
                        $format = $cFormat[1];
                    }

                }
            }

            //channel
//            $feed->set_image('Goteo.org', SITE_URL . '/images/logo.jpg');
            $feed->set_language('ES-ES'); // segun \LANG
            $feed->set_date(\date('Y-m-d\TH:i:s').'Z', DATE_UPDATED);
            $feed->set_author(null, 'Goteo');
            $feed->set_selfLink(SITE_URL . '/rss');

            foreach ($data['tags'] as $tagId => $tagName) {
                $feed->add_category($tagName);
            }


            date_default_timezone_set('UTC');

            foreach ($data['posts'] as $postId=>$post) {

                // fecha
                $postDate = explode('-', $post->date);
                $date = \mktime(0, 0, 0, $postDate[1], $postDate[0], $postDate[2]);

                //item $postId
                $feed->add_item($post->title, $post->text, SITE_URL . '/blog/' . $post->id);
                $feed->set_date(\date(DATE_ATOM, $date), DATE_PUBLISHED);

                foreach ($post->tags as $tagId => $tagName) {
                    $feed->add_category($tagName);
                }

                // html output
                $feed->set_feedConstruct($format);
                $feed->feed_construct->construct['itemTitle']['type'] = 'html';
                $feed->feed_construct->construct['itemContent']['type'] = 'html';
            }

            return $feed->getXML($format);
        }

	}
	
}