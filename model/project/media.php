<?php

namespace Goteo\Model\Project {

    use \DOMDocument;

    class Media {

        public
            $project,
            $url = '';

        public function __construct ($url) {
            $this->url = $url;
        }

        protected static function getYouTubeCode ($video, $https = false) {

            return '<iframe width="100%" height="100%" src="'
                   . ($https ? 'https' : 'http') . '://www.youtube.com/embed/'
                   . $video . '" frameborder="0" allowfullscreen></iframe>';

        }

        protected static function getVimeoCode ($id, $https = false) {

            return '<iframe src="http://player.vimeo.com/video/'
                   . $id . '?title=0&amp;byline=0&amp;portrait=0" width="100%" height="100%" frameborder="0"></iframe>';

        }

        protected static function getSlideshareCode ($id, $https = false) {

            return '<iframe src="http://www.slideshare.net/slideshow/embed_code/'
                    . $id . '" width="100%" height="100%" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>';

        }

        protected static function getPreziCode ($id, $https = false) {

            return '<object id="prezi_'
                    . $id . '" name="prezi_'
                    . $id . '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="http://prezi.com/bin/preziloader.swf"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="bgcolor" value="#ffffff"/><param name="flashvars" value="prezi_id='
                    . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"/><embed id="preziEmbed_'
                    . $id . '" name="preziEmbed_'
                    . $id . '" src="http://prezi.com/bin/preziloader.swf" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="100%" bgcolor="#ffffff" flashvars="prezi_id='
                    . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"></embed></object>';

        }

        public function getEmbedCode () {

            $code = '';

            if (!empty($this->url)) {

                switch (true) {

                    case trim($this->url) === '':
                        break;

                    case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtube\.com/watch[/\?\&\#$]#', $this->url)):
                        // Video de Youtube.com
                        if ($url = parse_url($this->url)) {
                            if (!empty($url['query'])) {
                                parse_str($url['query'], $query);
                                if (!empty($query['v'])) {
                                    $code = static::getYouTubeCode($query['v'], $url['scheme'] === 'https');
                                }
                            }
                        }
                        break;

                    case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtu\.be/(?<video>[^\#\&]+)#', $this->url, $yt)):
                        // URL corta de YouTube
                        $code = static::getYouTubeCode($yt['video'], !empty($yt['https']));
                        break;

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?vimeo.com/(?<video>\d+)#', $this->url, $vm)):
                        // URL de Vimeo
                        $code = static::getVimeoCode($vm['video'], !empty($vm['https']));
                        break;

                     case (preg_match('#^\[slideshare\sid\=(?<slide>\d+)#', $this->url, $sh)):
                        // URL de Slideshare
                        $code = static::getSlideshareCode($sh['slide']);
                        break;

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?prezi.com/(?<slide>\w+)/#', $this->url, $pz)):
                        // URL de Slideshare
                        $code = static::getPreziCode($pz['slide']);
                        break;

                    default:
                        // Mirar si es código HTML
                        $dom = new DOMDocument();
                        if ($dom->loadHTML($this->url)) {
                            $code = $media;
                        }

                }


            }

            return $code;

        }

        public function __toString () {
            return $this->url;
        }



    }

}