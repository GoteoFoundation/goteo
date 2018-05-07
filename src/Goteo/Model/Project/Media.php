<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Project {

    use \DOMDocument;
    use Goteo\Application\Lang;

    class Media {

        public
            $project,
            $url = '';

        public function __construct ($url) {
            $this->url = str_replace('http://', 'https://', $url);
        }

        protected static function getYouTubeCode ($video, $https = false, $autoplay=false) {

            if($autoplay)
                $cod_auto="&autoplay=1";
            return '<iframe width="100%" height="100%" style="max-width:none !important;" src="'
                   . ($https ? 'https' : 'http') . '://www.youtube.com/embed/'
                   . $video . '?wmode=Opaque'.$cod_auto.'" frameborder="0" allowfullscreen></iframe>';

        }

        protected static function getVimeoCode ($id, $https = false, $autoplay=false) {

            if($autoplay)
                $cod_auto=";autoplay=1";
            return '<iframe src="'
            . ($https ? 'https' : 'http') . '://player.vimeo.com/video/'
                   . $id . '?title=0&amp;byline=0&amp;portrait=0'.$cod_auto.'" width="100%" height="100%" frameborder="0" style="max-width:none !important;" webkitallowfullscreen mozallowfullscreen></iframe>';
        }

        protected static function getSlideshareCode ($id, $https = false) {

            return '<iframe src="'
            . ($https ? 'https' : 'http') . '://www.slideshare.net/slideshow/embed_code/'
                    . $id . '" width="100%" height="100%" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>';

        }

        protected static function getPreziCode ($id, $https = false) {

            return '<object id="prezi_'
                    . $id . '" name="prezi_'
                    . $id . '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="'
            . ($https ? 'https' : 'http') . '://prezi.com/bin/preziloader.swf"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="bgcolor" value="#ffffff"/><param name="flashvars" value="prezi_id='
                    . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"/><embed id="preziEmbed_'
                    . $id . '" name="preziEmbed_'
                    . $id . '" src="'
            . ($https ? 'https' : 'http') . '://prezi.com/bin/preziloader.swf" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="100%" bgcolor="#ffffff" flashvars="prezi_id='
                    . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"></embed></object>';

        }

        protected static function getBlipCode ($id, $https = false) {

            return '<iframe src="'
                    . ($https ? 'https' : 'http') . '://blip.tv/play/'
                    .$id.'.html" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
                <embed type="application/x-shockwave-flash" src="'
                    . ($https ? 'https' : 'http') . '://a.blip.tv/api.swf#'
                    .$id.'" style="display:none"></embed>';

        }

        protected static function getGissTvCode ($id, $https = false) {

            return '<iframe src="'
                    . ($https ? 'https' : 'http') . '://giss.tv/dmmdb/'.$id
                    .'" width="100%" height="100%" scrolling=no></iframe>';

        }

        public function getEmbedCode ($universalSubtitles = false, $lang = null, $autoplay = false) {
            if(empty($lang)) $lang = Lang::current();
            $https = true;

            $code = '';

            if (!empty($this->url)) {

                // por el momento, universal subtitles es incompatible con ssl
                if ($universalSubtitles && !$https) {
                    return '<script type="text/javascript" src="'
                    . ($https ? 'https' : 'http') . '://s3.amazonaws.com/s3.www.universalsubtitles.org/embed.js">
({
    "video_url": "'. trim($this->url) . '",
    "base_state": {"language": "'.$lang.'"},
    "video_config": {"width": "620", "height": "380"}
})
</script>';
                }


                switch (true) {

                    case trim($this->url) === '':
                        break;

                    case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtube\.com/watch[/\?\&\#$]#', $this->url)):
                        // Video de Youtube.com
                        if ($url = parse_url($this->url)) {
                            if (!empty($url['query'])) {
                                parse_str($url['query'], $query);
                                if (!empty($query['v'])) {
                                    $code = static::getYouTubeCode($query['v'], $https,$autoplay);
                                }
                            }
                        }
                        break;

                    case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtu\.be/(?<video>[^\#\&]+)#', $this->url, $yt)):
                        // URL corta de YouTube
                        $code = static::getYouTubeCode($yt['video'], $https,$autoplay);
                        break;

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?vimeo.com/(?<video>\d+)#', $this->url, $vm)):
                        // URL de Vimeo
                        $code = static::getVimeoCode($vm['video'], $https,$autoplay);
                        break;

                     case (preg_match('#^\[slideshare\sid\=(?<slide>\d+)#', $this->url, $sh)):
                        // URL de Slideshare
                        $code = static::getSlideshareCode($sh['slide'], $https);
                        break;

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?prezi.com/(?<slide>\w+)/#', $this->url, $pz)):
                        // URL de Prezi
                        $code = static::getPreziCode($pz['slide'], $https);
                        break;

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?blip.tv/play/(?<video>\w+).html#', $this->url, $bp)):
                        // URL de Blip.tv
                        $code = static::getBlipCode($bp['video'], $https);
                        break;

                     case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?giss.tv/dmmdb/(?<video>\w+)#', $this->url, $bp)):
                        // URL de Blip.tv
                        $code = static::getGissTvCode($bp['video'], $https);
                        break;

                    default:
                        // Mirar si es código HTML
                        $dom = new DOMDocument();
                        if (@$dom->loadHTML($this->url)) {
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
