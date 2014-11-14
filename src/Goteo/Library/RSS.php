<?php
namespace Goteo\Library {

use \FeedWriter;

	/*
	 * Clase para usar La libreria FeedWriter https://github.com/mibe/FeedWriter
	 */
    class RSS {

        public static function get($config, $data, $gformat = null) {

            $formats = array('RSS1', 'RSS2', 'ATOM');
            if(!in_array($gformat, array('RSS1', 'RSS2', 'ATOM'))) $gformat = 'RSS2';
            $clas = "FeedWriter\\$gformat";
            $feed = new $clas();

            $feed->setTitle($config['title']);
            $feed->setLink($config['link']);
            $feed->setDescription($config['description']);
            if($config['image']) $feed->setImage($config['title'], $config['link'], $config['image']);
            if($config['date']) $feed->setDate($config['date']);
            $feed->setChannelElement('language', 'ES-ES');
            // $feed->setChannelElement('author', 'Goteo');
            if($config['selfLink']) $feed->setSelfLink($config['selfLink']);

            //channel

            foreach ($data['tags'] as $tagId => $tagName) {
                $feed->setChannelElement('category', $tagName, null, true);
            }
            // $feed->addCdataEncoding(array('category'));


            foreach ($data['posts'] as $postId => $post) {
                // print_r($post);die;
                $item = $feed->createNewItem();
                $item->addElementArray(array(
                    'title' => $post->title,
                    'link' => $post->link,
                    'description' => $post->text
                    )
                );

                if($gformat === 'ATOM') $item->setContent(html_entity_decode(strip_tags($post->text)));

                $item->setDate($post->date);
                $item->addElement('guid', $post->link
                    // si acaso cuando tenga un slug como dios manda
                    //, array('isPermaLink'=>'true')
                    );

                foreach ($post->tags as $tagId => $tagName) {
                    $item->addElement('category', $tagName, null, false, true);
                }

                $feed->addItem($item);
            }

            return $feed;
        }

	}

}
