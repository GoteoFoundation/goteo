<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Project;

use \DOMDocument;
use Exception;
use Goteo\Application\Lang;

class Media {

    public $project;
    public $url = '';

    private const PEERTUBE_OFFICIAL_URL = "framatube.org";
    private const PEERTUBE_PLATAFORMESS_URL = "peertube.plataformess.org";
    private const PEERTUBE_XY_SPACE_URL = "tube.xy-space.de";
    private const PEERTUBE_LAGUIXETA_URL = "peertube.laguixeta.cat";

    public function __construct ($url) {
        $this->url = str_replace('http://', 'https://', $url);
    }

    protected static function getYouTubeCode ($video, $autoplay=false): string
    {
        if($autoplay)
            $cod_auto="&autoplay=1";
        return "<iframe width='100%' height='100%' style='max-width:none !important;'
                    src='https://www.youtube.com/embed/$video?wmode=Opaque$cod_auto'
                    frameborder='0' allowfullscreen></iframe>";
    }

    protected static function getVimeoCode ($id, $autoplay=false): string
    {
        if($autoplay)
            $cod_auto=";autoplay=1";
        return "<iframe src='https://player.vimeo.com/video/$id?title=0&amp;byline=0&amp;portrait=0$cod_auto'
                    width='100%' height='100%' frameborder='0' style='max-width:none !important;'
                    webkitallowfullscreen mozallowfullscreen></iframe>";
    }

    protected static function getSlideshareCode ($videoId): string
    {
        return "<iframe src='https://www.slideshare.net/slideshow/embed_code/$videoId'
                    width='100%' height='100%' marginwidth='0' marginheight='0'
                    frameborder='0' scrolling='no'></iframe>";
    }

    protected static function getPreziCode ($id): string
    {
        return '<object id="prezi_'
                . $id . '" name="prezi_'
                . $id . '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="'
                . 'https://prezi.com/bin/preziloader.swf"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="bgcolor" value="#ffffff"/><param name="flashvars" value="prezi_id='
                . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"/><embed id="preziEmbed_'
                . $id . '" name="preziEmbed_'
                . $id . '" src="'
                . 'https://prezi.com/bin/preziloader.swf" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="100%" bgcolor="#ffffff" flashvars="prezi_id='
                . $id . '&amp;lock_to_path=0&amp;color=ffffff&amp;autoplay=no&amp;autohide_ctrls=0"></embed></object>';
    }

    protected static function getBlipCode ($videoId): string
    {
        return "<iframe src='https://blip.tv/play/$videoId.html'
                    width='100%' height='100%' frameborder='0' allowfullscreen></iframe>
                    <embed type='application/x-shockwave-flash'
                        src='https://a.blip.tv/api.swf#$videoId' style='display:none'></embed>";
    }

    protected static function getGissTvCode ($videoId): string
    {
        return "<iframe src='https://giss.tv/dmmdb/$videoId'
                    width='100%' height='100%' scrolling='no'></iframe>";
    }

    protected static function getPeerTubeCode($videoId, $baseDomain, $autoplay = false): string
    {
        if ($autoplay)
            $autoplayParameter = "&autoplay=1";
        return "<iframe src='https://$baseDomain/videos/embed/$videoId?warningTitle=0$autoplayParameter'
                    allowfullscreen=''
                    sandbox='allow-same-origin allow-scripts allow-popups'
                    width='560'
                    height='315'
                    frameborder='0'></iframe>";
    }

    public function getEmbedCode ($universalSubtitles = false, $lang = null, $autoplay = false)
    {
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
                    if ($url = parse_url($this->url)) {
                        if (!empty($url['query'])) {
                            parse_str($url['query'], $query);
                            if (!empty($query['v'])) {
                                $code = static::getYouTubeCode($query['v'], $autoplay);
                            }
                        }
                    }
                    break;

                case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?youtu\.be/(?<video>[^\#\&]+)#', $this->url, $yt)):
                    $code = static::getYouTubeCode($yt['video'], $autoplay);
                    break;

                 case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?vimeo.com/(?<video>\d+)#', $this->url, $vm)):
                    $code = static::getVimeoCode($vm['video'], $autoplay);
                    break;

                 case (preg_match('#^\[slideshare\sid\=(?<slide>\d+)#', $this->url, $sh)):
                    $code = static::getSlideshareCode($sh['slide']);
                    break;

                 case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?prezi.com/(?<slide>\w+)/#', $this->url, $pz)):
                    $code = static::getPreziCode($pz['slide']);
                    break;

                 case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?blip.tv/play/(?<video>\w+).html#', $this->url, $bp)):
                    $code = static::getBlipCode($bp['video']);
                    break;

                 case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?giss.tv/dmmdb/(?<video>\w+)#', $this->url, $bp)):
                    $code = static::getGissTvCode($bp['video']);
                    break;

                case (preg_match('#^(http(?<https>s)?://)?(?:www\.)?((framatube|peertube(\.plataformess|\.laguixeta))(.org|.cat|tube\.xy-space\.de))/(w|videos/watch)/(?<video>[a-zA-Z0-9\-]+)#', $this->url, $pt)):
                    $baseDomain = $this->getPeerTubeBaseDomainUrl($this->url);
                    $code = static::getPeerTubeCode($pt['video'], $baseDomain, $autoplay);
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

    /**
     * @throws Exception
     */
    private function getPeerTubeBaseDomainUrl(string $url): string
    {
        if (str_contains($url, self::PEERTUBE_OFFICIAL_URL)) {
            return self::PEERTUBE_OFFICIAL_URL;
        } else if (str_contains($url, self::PEERTUBE_PLATAFORMESS_URL)) {
            return self::PEERTUBE_PLATAFORMESS_URL;
        } else if (str_contains($url, self::PEERTUBE_XY_SPACE_URL)) {
            return self::PEERTUBE_XY_SPACE_URL;
        } else if (str_contains($url, self::PEERTUBE_LAGUIXETA_URL)) {
            return self::PEERTUBE_LAGUIXETA_URL;
        }

        throw new Exception("Media video URL not matched!");
    }

    public function __toString () {
        return $this->url;
    }

}
